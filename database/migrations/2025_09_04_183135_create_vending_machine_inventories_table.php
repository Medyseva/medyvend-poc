<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendingMachineInventoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vending_machine_inventory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vending_machine_id')->constrained('vending_machine')->onDelete('cascade');
            $table->foreignId('drug_id')->constrained('drugs')->onDelete('cascade');
            $table->integer('slot_row');
            $table->integer('slot_column');
            $table->integer('stock_quantity')->default(0);
            $table->integer('threshold_quantity')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('batch_number')->nullable();
            $table->timestamp('last_restocked_at')->nullable();
            $table->timestamps();
            
            $table->unique(['vending_machine_id', 'slot_row', 'slot_column'], 'unique_machine_slot');
            $table->index(['vending_machine_id', 'drug_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vending_machine_inventory');
    }
}
