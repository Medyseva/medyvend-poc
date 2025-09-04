<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendingMachine extends Model
{
    protected $table = 'vending_machine';
    use HasFactory;
    protected $fillable = [
        'id',
        'machine_id',
        'vending_auth_token',
        'machine_num',
        'machine_name',
        'machine_lat',
        'machine_long',
        'machine_auth_key',
        'machine_type',
        'machine_max_rows',
        'machine_max_column',
        'machine_qr_url',
        'machine_is_active',
        'doa',
        'doa_status',
        'fault_status',
        'machine_uuid',
        'machine_ip',
        'machine_mac',
        'machine_last_ping',
        'created_at',
        'updated_at',
        'vendtrails_access_token',
        'vendtrails_refresh_token',
    ];

    public function inventories()
    {
        return $this->hasMany(VendingMachineInventory::class, 'vending_machine_id');
    }

    public function dispenseRecords()
    {
        return $this->hasMany(VendingDispenseRecord::class, 'machine_id');
    }
}