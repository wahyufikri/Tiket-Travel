<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'schedule_id',
        'order_code',
        'seat_quantity',
        'total_price',
        'payment_status',
        'order_status',
    ];

    // Relasi ke customer (pemesan)
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // Relasi ke jadwal perjalanan
    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    // Relasi ke daftar penumpang dan kursi
    public function passengers()
    {
        return $this->hasMany(OrderPassenger::class);
    }

    // Optional: Scope untuk status tertentu (kalau kamu mau)
    public function scopeMenunggu($query)
    {
        return $query->where('order_status', 'menunggu');
    }

    public function scopeLunas($query)
    {
        return $query->where('payment_status', 'lunas');
    }
}
