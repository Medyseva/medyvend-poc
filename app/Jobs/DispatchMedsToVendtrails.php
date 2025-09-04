<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Models\VendingDispenseRecord;
use App\Models\Invoice;
use App\Services\VendTrailsService;

class DispatchMedsToVendtrails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $invoiceId;
    public $prescriptionId;
    public $machineNum;
    public $companyNum;
    public $instructions;

    public $tries = 3;
    public $timeout = 300;
    public $retryAfter = 60;

    public function __construct($invoiceId, $prescriptionId, $machineNum, $companyNum, $instructions)
    {
        $this->invoiceId = $invoiceId;
        $this->prescriptionId = $prescriptionId;
        $this->machineNum = $machineNum ?? 1;
        $this->companyNum = $companyNum ?? 2;
        $this->instructions = $instructions;
    }

    public function handle(VendTrailsService $vendtrails)
    {
        Log::channel('vending_trails')->info("DispatchMedsToVendtrails job started for Invoice ID: {$this->invoiceId}");

        try {
            // Update status to processing
            VendingDispenseRecord::where('invoice_id', $this->invoiceId)
                ->where('status', VendingDispenseRecord::STATUS_PENDING)
                ->update(['status' => VendingDispenseRecord::STATUS_PROCESSING]);

            $taskNum = $vendtrails->sendInstructionToMachine(
                $this->machineNum,
                $this->companyNum,
                $this->instructions
            );

            Log::channel('vending_trails')->info("Instruction sent successfully for Invoice ID: {$this->invoiceId}. Received Task Number: {$taskNum}");

            // Update invoice status (if using Invoice model)
            if (class_exists('App\Models\Invoice')) {
                Invoice::where('id', $this->invoiceId)->update([
                    'vending_status' => 'vending_in_process'
                ]);
                Log::channel('vending_trails')->info("Invoice {$this->invoiceId} updated to 'vending_in_process'.");
            }

            // Update dispense records with task number
            VendingDispenseRecord::where('invoice_id', $this->invoiceId)
                ->where('status', VendingDispenseRecord::STATUS_PROCESSING)
                ->update([
                    'task_number' => $taskNum
                ]);

            // Dispatch status checking job
            CheckVendtrailsTaskStatus::dispatch($taskNum, $this->invoiceId, $this->machineNum)
                ->delay(now()->addSeconds(15));

            Log::channel('vending_trails')->info("Dispense records for Invoice {$this->invoiceId} updated with Task Number {$taskNum}. Status check job scheduled.");

        } catch (\Exception $ex) {
            Log::channel('vending_trails')->error("Error in DispatchMedsToVendtrails for Invoice ID: {$this->invoiceId}. Error: " . $ex->getMessage());
            
            // Update records as failed
            VendingDispenseRecord::where('invoice_id', $this->invoiceId)
                ->whereIn('status', [VendingDispenseRecord::STATUS_PENDING, VendingDispenseRecord::STATUS_PROCESSING])
                ->update(['status' => VendingDispenseRecord::STATUS_FAILED]);

            throw $ex;
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::channel('vending_trails')->error("DispatchMedsToVendtrails job failed for Invoice ID: {$this->invoiceId}. Error: " . $exception->getMessage());
        
        // Mark all related records as failed
        VendingDispenseRecord::where('invoice_id', $this->invoiceId)
            ->whereIn('status', [VendingDispenseRecord::STATUS_PENDING, VendingDispenseRecord::STATUS_PROCESSING])
            ->update(['status' => VendingDispenseRecord::STATUS_FAILED]);
    }
}