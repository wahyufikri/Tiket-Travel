<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'payment_method',
        'payment_proof',
        'paid_at',
        'verified_by',
        'status',
    ];

    protected $dates = ['paid_at'];

    // Relasi ke order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
