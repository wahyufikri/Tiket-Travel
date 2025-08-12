<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'schedule_id',
        'order_id',
        'seat_id',
        'from_stop_id',
        'to_stop_id',
        'passenger_name',
        'phone_number', // Tambahkan ini kalau belum ada (untuk nomor WA)
    ];

    // Cast agar jadwal bisa langsung pakai Carbon
    protected $casts = [
        'departure_time' => 'datetime', // opsional jika kolom ada di booking, biasanya di schedule
    ];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function seat()
    {
        return $this->belongsTo(Seat::class);
    }

    public function fromStop()
    {
        return $this->belongsTo(Stop::class, 'from_stop_id');
    }

    public function toStop()
    {
        return $this->belongsTo(Stop::class, 'to_stop_id');
    }

    /**
     * Accessor untuk waktu keberangkatan lewat relasi schedule
     */
    public function getDepartureTimeAttribute()
    {
        return $this->schedule ? $this->schedule->departure_time : null;
    }
}
