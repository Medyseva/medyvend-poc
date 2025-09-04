<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendingDispenseRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'prescription_id',
        'invoice_id',
        'drug_id',
        'vle_id',
        'machine_id',
        'status',
        'transaction_ref',
        'task_number',
        'dispensed_at',
    ];

    protected $casts = [
        'dispensed_at' => 'datetime',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SUCCESSFUL = 'successful';
    const STATUS_FAILED = 'failed';

    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function drug()
    {
        return $this->belongsTo(Drug::class);
    }

    public function vle()
    {
        return $this->belongsTo(User::class, 'vle_id');
    }

    public function machine()
    {
        return $this->belongsTo(VendingMachine::class, 'machine_id');
    }

    public function isCompleted()
    {
        return $this->status === self::STATUS_SUCCESSFUL;
    }

    public function isFailed()
    {
        return $this->status === self::STATUS_FAILED;
    }

    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }
}