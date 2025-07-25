<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderPassenger extends Model
{
    protected $fillable = ['order_id', 'name', 'seat_number'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}

