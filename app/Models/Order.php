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
        'midtrans_response',
        'expired_at',
        'verified_by',
    ];
    protected $casts = [
        'expired_at' => 'datetime',
    ];

    // Relasi ke customer (pemesan)
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function stops()
    {
        return $this->hasMany(Stop::class, 'order_id');
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

    public function payment()
{
    return $this->hasOne(Payment::class);
}

public function booking()
{
    return $this->hasOne(Booking::class);

}
public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
