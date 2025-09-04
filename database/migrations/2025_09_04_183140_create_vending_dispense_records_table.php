<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendingDispenseRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vending_dispense_records', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('prescription_id')->nullable();
            $table->bigInteger('invoice_id')->nullable();
            $table->foreignId('drug_id')->constrained('drugs')->onDelete('cascade');
            $table->bigInteger('vle_id')->nullable();
            $table->foreignId('machine_id')->constrained('vending_machine', 'id')->onDelete('cascade');
            $table->enum('status', ['pending', 'processing', 'successful', 'failed'])->default('pending');
            $table->string('transaction_ref')->nullable();
            $table->string('task_number')->nullable();
            $table->timestamp('dispensed_at')->nullable();
            $table->timestamps();
            
            $table->index(['invoice_id', 'status']);
            $table->index(['prescription_id', 'status']);
            $table->index(['machine_id', 'drug_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vending_dispense_records');
    }
}
