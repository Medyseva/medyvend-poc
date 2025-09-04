<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDrugsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drugs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('generic_name')->nullable();
            $table->string('manufacturer')->nullable();
            $table->string('category')->nullable();
            $table->string('dosage_form')->nullable();
            $table->string('strength')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->string('barcode')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('requires_prescription')->default(false);
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
        Schema::dropIfExists('drugs');
    }
}
