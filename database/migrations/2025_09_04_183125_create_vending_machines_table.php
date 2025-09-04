<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendingMachinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vending_machine', function (Blueprint $table) {
            $table->id();
            $table->string('machine_id')->unique();
            $table->integer('machine_num')->unique();
            $table->string('machine_name');
            $table->decimal('machine_lat', 10, 7)->nullable();
            $table->decimal('machine_long', 10, 7)->nullable();
            $table->string('machine_auth_key')->nullable();
            $table->string('machine_type')->nullable();
            $table->integer('machine_max_rows')->nullable();
            $table->integer('machine_max_column')->nullable();
            $table->string('machine_qr_url')->nullable();
            $table->boolean('machine_is_active')->default(true);
            $table->date('doa')->nullable();
            $table->string('doa_status')->nullable();
            $table->string('fault_status')->nullable();
            $table->string('machine_uuid')->nullable();
            $table->string('machine_ip')->nullable();
            $table->string('machine_mac')->nullable();
            $table->timestamp('machine_last_ping')->nullable();
            $table->text('vending_auth_token')->nullable();
            $table->text('vendtrails_access_token')->nullable();
            $table->text('vendtrails_refresh_token')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vending_machine');
    }
}
