<?php

namespace App\Http\Controllers;

use App\Models\VendingMachineInventory;
use App\Models\VendingDispenseRecord;
use App\Models\Drug;
use App\Models\VendingMachine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Jobs\DispatchMedsToVendtrails;

class VendingInventoryController extends Controller
{
    public function index(Request $request)
    {
        $machineId = $request->get('machine_id', 1);
        
        $inventories = VendingMachineInventory::with(['drug', 'vendingMachine'])
            ->when($machineId, function($query) use ($machineId) {
                return $query->where('vending_machine_id', $machineId);
            })
            ->orderBy('slot_row')
            ->orderBy('slot_column')
            ->get();

        $machines = VendingMachine::where('machine_is_active', true)->get();

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'data' => $inventories
            ]);
        }

        return view('admin.inventory.index', compact('inventories', 'machines', 'machineId'));
    }

    public function show($id)
    {
        $item = VendingMachineInventory::with(['drug', 'vendingMachine'])
            ->findOrFail($id);
        
        return response()->json([
            'status' => 'success',
            'data' => $item
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vending_machine_id' => 'required|exists:vending_machine,id',
            'drug_id' => 'required|exists:drugs,id',
            'slot_row' => 'required|integer',
            'slot_column' => 'required|integer',
            'stock_quantity' => 'required|integer|min:0',
            'threshold_quantity' => 'nullable|integer|min:0',
            'expiry_date' => 'nullable|date|after:today',
            'batch_number' => 'nullable|string|max:100',
        ]);

        // Check if slot is already occupied
        $existingSlot = VendingMachineInventory::where([
            'vending_machine_id' => $validated['vending_machine_id'],
            'slot_row' => $validated['slot_row'],
            'slot_column' => $validated['slot_column']
        ])->first();

        if ($existingSlot) {
            return response()->json([
                'status' => 'error',
                'message' => 'Slot is already occupied'
            ], 422);
        }

        $validated['last_restocked_at'] = now();
        $item = VendingMachineInventory::create($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Inventory added successfully',
                'data' => $item->load(['drug', 'vendingMachine'])
            ], 201);
        }

        return redirect()->route('inventory.index')
            ->with('success', 'Inventory added successfully');
    }

    public function update(Request $request, $id)
    {
        $item = VendingMachineInventory::findOrFail($id);
        
        $validated = $request->validate([
            'stock_quantity' => 'sometimes|integer|min:0',
            'threshold_quantity' => 'nullable|integer|min:0',
            'expiry_date' => 'nullable|date',
            'batch_number' => 'nullable|string|max:100',
        ]);

        // Update last_restocked_at if stock_quantity is being increased
        if (isset($validated['stock_quantity']) && $validated['stock_quantity'] > $item->stock_quantity) {
            $validated['last_restocked_at'] = now();
        }

        $item->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Inventory updated successfully',
                'data' => $item->load(['drug', 'vendingMachine'])
            ]);
        }

        return redirect()->route('inventory.index')
            ->with('success', 'Inventory updated successfully');
    }

    public function destroy($id)
    {
        $item = VendingMachineInventory::findOrFail($id);
        $item->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Inventory item deleted successfully'
        ]);
    }

    public function getMeds(Request $request)
    {
        $machineId = $request->get('machine_id', 1);
        
        $inventories = VendingMachineInventory::where('vending_machine_id', $machineId)
            ->with('drug:id,name')
            ->get()
            ->map(function ($item) {
                // Calculate consumed count from dispense records
                $consumed = VendingDispenseRecord::where([
                    'drug_id' => $item->drug_id,
                    'machine_id' => $item->vending_machine_id,
                    'status' => 'successful'
                ])->count();

                $remaining = max(0, $item->stock_quantity - $consumed);

                return [
                    'id' => $item->id,
                    'rack_sequence' => 'R' . $item->slot_row . 'C' . $item->slot_column,
                    'tablet_name' => $item->drug->name ?? 'Unknown',
                    'capacity_count' => $item->threshold_quantity ?? 0,
                    'loaded_count' => $item->stock_quantity,
                    'consumed_count' => $consumed,
                    'remaining_count' => $remaining,
                    'drug_id' => $item->drug_id,
                    'expiry_date' => $item->expiry_date ? $item->expiry_date->format('Y-m-d') : null,
                    'batch_number' => $item->batch_number,
                    'is_low_stock' => $item->isLowStock(),
                    'is_expired' => $item->isExpired()
                ];
            });

        return response()->json([
            'data' => $inventories,
            'message' => 'Success! Data fetched Successfully!',
            'status' => 200
        ]);
    }

    public function dispenseMeds(Request $request)
    {
        $validated = $request->validate([
            'prescription_id' => 'required|integer',
            'invoice_id' => 'required|integer',
            'selected_tablets' => 'required|array',
            'selected_tablets.*.drug_id' => 'required|integer|exists:drugs,id',
            'selected_tablets.*.quantity' => 'nullable|integer|min:1',
            'transaction_ref' => 'required|string',
            'machine_id' => 'nullable|integer|exists:vending_machine,id',
            'company_num' => 'nullable|integer'
        ]);

        $machineId = $validated['machine_id'] ?? 1;
        $companyNum = $validated['company_num'] ?? 2;

        // Check if vending already completed
        $existingRecords = VendingDispenseRecord::where('invoice_id', $validated['invoice_id'])
            ->where('status', 'successful')
            ->exists();

        if ($existingRecords) {
            Log::channel('vending_trails')->warning("Attempt to re-dispense for invoice {$validated['invoice_id']} which is already completed.");
            return response()->json([
                'status' => 'error',
                'message' => 'Medicines already dispensed for this invoice.'
            ], 422);
        }

        Log::channel('vending_trails')->info("Dispense request received for invoice ID: {$validated['invoice_id']}");

        $instructions = [];

        foreach ($validated['selected_tablets'] as $tablet) {
            $drugId = $tablet['drug_id'];
            $quantity = $tablet['quantity'] ?? 1;

            $slot = VendingMachineInventory::where('drug_id', $drugId)
                ->where('vending_machine_id', $machineId)
                ->where('stock_quantity', '>', 0)
                ->first();

            if ($slot) {
                if ($slot->stock_quantity < $quantity) {
                    return response()->json([
                        'status' => 'error',
                        'message' => "Insufficient stock for drug ID {$drugId}. Available: {$slot->stock_quantity}, Requested: {$quantity}"
                    ], 422);
                }

                $instructions[] = [
                    'row_address' => (int) $slot->slot_row,
                    'column_address' => (string) $slot->slot_column,
                    'qty' => $quantity
                ];

                // Create dispense record
                VendingDispenseRecord::create([
                    'prescription_id' => $validated['prescription_id'],
                    'invoice_id' => $validated['invoice_id'],
                    'drug_id' => $drugId,
                    'vle_id' => auth()->id(),
                    'machine_id' => $machineId,
                    'status' => VendingDispenseRecord::STATUS_PENDING,
                    'transaction_ref' => $validated['transaction_ref'],
                ]);

                Log::channel('vending_trails')->info("Inserted record for drug ID {$drugId} into vending_dispense_records.");
            } else {
                Log::channel('vending_trails')->warning("No vending slot found for drug ID: {$drugId} on machine: {$machineId}");
                return response()->json([
                    'status' => 'error',
                    'message' => "No available slot found for drug ID: {$drugId}"
                ], 422);
            }
        }

        if (empty($instructions)) {
            Log::channel('vending_trails')->error("No valid instructions generated. Aborting dispense for invoice: {$validated['invoice_id']}");
            return response()->json([
                'status' => 'error',
                'message' => 'No instructions generated for selected tablets.'
            ], 422);
        }

        Log::channel('vending_trails')->debug("Dispatching vending job with instructions", [
            'invoice' => $validated['invoice_id'],
            'instructions' => $instructions
        ]);

        // Dispatch job for async processing
        DispatchMedsToVendtrails::dispatch(
            $validated['invoice_id'],
            $validated['prescription_id'],
            $machineId,
            $companyNum,
            $instructions
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Dispensing job has been queued. Please check status for completion.'
        ]);
    }

    public function lowStockReport()
    {
        $lowStockItems = VendingMachineInventory::with(['drug', 'vendingMachine'])
            ->whereColumn('stock_quantity', '<=', 'threshold_quantity')
            ->orWhere('stock_quantity', 0)
            ->orderBy('stock_quantity', 'asc')
            ->get();

        return view('admin.inventory.low-stock', compact('lowStockItems'));
    }

    public function expiryReport()
    {
        $expiringItems = VendingMachineInventory::with(['drug', 'vendingMachine'])
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<=', now()->addDays(30))
            ->orderBy('expiry_date', 'asc')
            ->get();

        return view('admin.inventory.expiry-report', compact('expiringItems'));
    }
}