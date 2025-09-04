<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Drug extends Model
{
    use HasFactory;
    
    protected $table = "drugs";
    protected $guarded = ['id'];
    
    public function vendingInventories()
    {
        return $this->hasMany(VendingMachineInventory::class, 'drug_id');
    }

    public function isInVending()
    {
        return $this->vendingInventories()->exists();
    }

    public function getAvailableInVending($machineId = null)
    {
        $query = $this->vendingInventories()
            ->where('stock_quantity', '>', 0)
            ->where(function ($q) {
                $q->whereNull('expiry_date')
                  ->orWhere('expiry_date', '>', now());
            });

        if ($machineId) {
            $query->where('vending_machine_id', $machineId);
        }

        return $query->sum('stock_quantity');
    }

    public function dispenseRecords()
    {
        return $this->hasMany(VendingDispenseRecord::class, 'drug_id');
    }
}