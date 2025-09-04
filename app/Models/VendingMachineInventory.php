<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendingMachineInventory extends Model
{
    protected $table = 'vending_machine_inventory';
    use HasFactory;

    protected $fillable = [
        'vending_machine_id',
        'drug_id',
        'slot_row',
        'slot_column',
        'stock_quantity',
        'threshold_quantity',
        'expiry_date',
        'batch_number',
        'last_restocked_at',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'last_restocked_at' => 'datetime',
    ];

    public function vendingMachine()
    {
        return $this->belongsTo(VendingMachine::class, 'vending_machine_id');
    }

    public function drug()
    {
        return $this->belongsTo(Drug::class, 'drug_id');
    }

    public function isLowStock()
    {
        return $this->stock_quantity <= $this->threshold_quantity;
    }

    public function isExpired()
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }
}