<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MidtransLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'event_type',
        'payload',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
