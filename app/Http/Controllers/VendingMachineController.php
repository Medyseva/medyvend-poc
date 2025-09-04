<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\VendTrailsService;
use App\Models\VendingMachine;
use App\Models\VendingMachineInventory;

class VendingMachineController extends Controller
{
    protected $vendingService;

    public function __construct(VendTrailsService $vendingService)
    {
        $this->vendingService = $vendingService;
    }

    public function index()
    {
        $machines = VendingMachine::with(['inventories' => function($query) {
            $query->with('drug');
        }])->get();

        return view('admin.machines.index', compact('machines'));
    }

    public function generateToken(Request $request)
    {
        $request->validate([
            'machine_num' => 'required|integer',
            'company_num' => 'nullable|integer'
        ]);

        try {
            $token = $this->vendingService->generateNewToken(
                $request->machine_num,
                $request->company_num ?? 1
            );
            
            return response()->json([
                'status' => 'success',
                'message' => 'Token generated successfully',
                'token' => $token
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getMachineDetails(Request $request)
    {
        $request->validate([
            'machine_num' => 'required|integer'
        ]);

        $machine = VendingMachine::where('machine_num', $request->machine_num)
            ->with('inventories.drug')
            ->first();

        if (!$machine) {
            return response()->json([
                'status' => 'error',
                'message' => 'Machine not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $machine
        ]);
    }

    public function sendInstructionToMachine(Request $request)
    {
        $request->validate([
            'machine_num' => 'required|integer',
            'company_num' => 'required|integer',
            'instructions' => 'required|array'
        ]);

        try {
            $taskNum = $this->vendingService->sendInstructionToMachine(
                $request->machine_num,
                $request->company_num,
                $request->instructions
            );

            return response()->json([
                'status' => 'success',
                'task_num' => $taskNum,
                'message' => 'Instructions sent successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function checkMachineStatus(Request $request)
    {
        $request->validate([
            'task_num' => 'required|integer',
            'machine_num' => 'required|integer'
        ]);

        try {
            $isCompleted = $this->vendingService->checkMachineStatus(
                $request->task_num,
                $request->machine_num
            );

            return response()->json([
                'status' => 'success',
                'completed' => $isCompleted,
                'message' => $isCompleted ? 'Task completed successfully' : 'Task still in progress'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function machineHardReset(Request $request)
    {
        $request->validate([
            'machine_num' => 'required|integer'
        ]);

        try {
            $response = $this->vendingService->hardResetMachine($request->machine_num);

            return response()->json([
                'status' => 'success',
                'message' => $response
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'machine_id' => 'required|unique:vending_machine',
            'machine_num' => 'required|integer|unique:vending_machine',
            'machine_name' => 'required|string|max:255',
            'machine_lat' => 'nullable|numeric',
            'machine_long' => 'nullable|numeric',
            'machine_type' => 'nullable|string',
            'machine_max_rows' => 'nullable|integer',
            'machine_max_column' => 'nullable|integer',
            'machine_ip' => 'nullable|ip',
            'machine_is_active' => 'boolean'
        ]);

        $machine = VendingMachine::create($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Machine created successfully',
                'data' => $machine
            ], 201);
        }

        return redirect()->route('machines.index')
            ->with('success', 'Machine created successfully');
    }

    public function show($id)
    {
        $machine = VendingMachine::with(['inventories.drug', 'dispenseRecords'])
            ->findOrFail($id);

        return view('admin.machines.show', compact('machine'));
    }

    public function edit($id)
    {
        $machine = VendingMachine::findOrFail($id);
        return view('admin.machines.edit', compact('machine'));
    }

    public function update(Request $request, $id)
    {
        $machine = VendingMachine::findOrFail($id);

        $validated = $request->validate([
            'machine_name' => 'required|string|max:255',
            'machine_lat' => 'nullable|numeric',
            'machine_long' => 'nullable|numeric',
            'machine_type' => 'nullable|string',
            'machine_max_rows' => 'nullable|integer',
            'machine_max_column' => 'nullable|integer',
            'machine_ip' => 'nullable|ip',
            'machine_is_active' => 'boolean'
        ]);

        $machine->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Machine updated successfully',
                'data' => $machine
            ]);
        }

        return redirect()->route('machines.index')
            ->with('success', 'Machine updated successfully');
    }

    public function destroy($id)
    {
        $machine = VendingMachine::findOrFail($id);
        $machine->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Machine deleted successfully'
        ]);
    }
}