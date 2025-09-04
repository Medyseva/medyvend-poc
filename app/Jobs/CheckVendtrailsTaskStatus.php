<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Invoice;
use App\Models\VendingDispenseRecord;
use App\Models\VendingMachineInventory;
use App\Services\VendTrailsService;
use Illuminate\Support\Facades\Log;

class CheckVendtrailsTaskStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $taskNum;
    protected $invoiceId;
    protected $machineNum;
    protected $attempts = 0;
    protected $maxAttempts = 20; // Maximum attempts before giving up

    public $tries = 25;
    public $timeout = 120;

    public function __construct($taskNum, $invoiceId, $machineNum = 1)
    {
        $this->taskNum = $taskNum;
        $this->invoiceId = $invoiceId;
        $this->machineNum = $machineNum;
    }

    public function handle(VendTrailsService $vendtrails)
    {
        $this->attempts++;
        Log::channel('vending_trails')->info("🔍 Checking task_num {$this->taskNum} for invoice {$this->invoiceId} (Attempt: {$this->attempts}/{$this->maxAttempts})");

        try {
            $isCompleted = $vendtrails->checkMachineStatus($this->taskNum, $this->machineNum);

            if ($isCompleted) {
                $this->handleTaskCompletion();
            } else {
                $this->handleTaskInProgress();
            }
        } catch (\Exception $ex) {
            Log::channel('vending_trails')->error("Error checking task status for task_num {$this->taskNum}: " . $ex->getMessage());
            
            if ($this->attempts >= $this->maxAttempts) {
                $this->handleTaskFailure("Max attempts reached with errors");
            } else {
                // Retry with exponential backoff
                $delay = min(300, 30 * pow(2, $this->attempts - 1)); // Max 5 minutes
                self::dispatch($this->taskNum, $this->invoiceId, $this->machineNum)
                    ->delay(now()->addSeconds($delay));
            }
        }
    }

    protected function handleTaskCompletion()
    {
        Log::channel('vending_trails')->info("✅ Task {$this->taskNum} completed successfully for invoice {$this->invoiceId}");

        // Update invoice status if using Invoice model
        if (class_exists('App\Models\Invoice')) {
            Invoice::where('id', $this->invoiceId)->update([
                'vending_status' => 'vending_completed'
            ]);
        }

        // Get all processing records for this invoice
        $dispenseRecords = VendingDispenseRecord::where('invoice_id', $this->invoiceId)
            ->where('status', VendingDispenseRecord::STATUS_PROCESSING)
            ->get();

        foreach ($dispenseRecords as $record) {
            // Mark record as successful
            $record->update([
                'status' => VendingDispenseRecord::STATUS_SUCCESSFUL,
                'dispensed_at' => now(),
            ]);

            // Update inventory
            $this->updateInventoryStock($record);

            Log::channel('vending_trails')->info("✅ Marked dispense record {$record->id} as successful for Drug ID {$record->drug_id}");
        }

        Log::channel('vending_trails')->info("✅ All records for Invoice {$this->invoiceId} marked as completed");
    }

    protected function handleTaskInProgress()
    {
        if ($this->attempts >= $this->maxAttempts) {
            $this->handleTaskFailure("Maximum attempts exceeded - task may have failed");
            return;
        }

        Log::channel('vending_trails')->info("⏳ Task {$this->taskNum} still in progress. Retrying in 30s (Attempt: {$this->attempts}/{$this->maxAttempts})");
        
        // Continue checking with exponential backoff
        $delay = min(120, 30 + ($this->attempts * 5)); // Increase delay slightly each time
        self::dispatch($this->taskNum, $this->invoiceId, $this->machineNum)
            ->delay(now()->addSeconds($delay));
    }

    protected function handleTaskFailure($reason)
    {
        Log::channel('vending_trails')->error("❌ Task {$this->taskNum} failed for invoice {$this->invoiceId}. Reason: {$reason}");

        // Update invoice status if using Invoice model
        if (class_exists('App\Models\Invoice')) {
            Invoice::where('id', $this->invoiceId)->update([
                'vending_status' => 'vending_failed'
            ]);
        }

        // Mark all processing records as failed
        VendingDispenseRecord::where('invoice_id', $this->invoiceId)
            ->where('status', VendingDispenseRecord::STATUS_PROCESSING)
            ->update([
                'status' => VendingDispenseRecord::STATUS_FAILED
            ]);
    }

    protected function updateInventoryStock($record)
    {
        $inventory = VendingMachineInventory::where('vending_machine_id', $record->machine_id)
            ->where('drug_id', $record->drug_id)
            ->first();

        if ($inventory) {
            if ($inventory->stock_quantity > 0) {
                $inventory->decrement('stock_quantity');
                Log::channel('vending_trails')->info("📦 Decreased stock for Drug ID {$record->drug_id} on Machine ID {$record->machine_id}. New stock: " . ($inventory->stock_quantity - 1));
            } else {
                Log::channel('vending_trails')->warning("⚠️ Stock already at 0 for Drug ID {$record->drug_id} on Machine ID {$record->machine_id}");
            }
        } else {
            Log::channel('vending_trails')->warning("⚠️ No inventory found for Drug ID {$record->drug_id} on Machine ID {$record->machine_id}");
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::channel('vending_trails')->error("CheckVendtrailsTaskStatus job failed for task {$this->taskNum}: " . $exception->getMessage());
        $this->handleTaskFailure("Job failed with exception: " . $exception->getMessage());
    }
}