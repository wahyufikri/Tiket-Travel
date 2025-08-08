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
        'passenger_name'
    ];

    /**
     * Relasi ke Schedule
     */
    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    /**
     * Relasi ke Seat
     */
    public function seat()
    {
        return $this->belongsTo(Seat::class);
    }

    /**
     * Relasi ke RouteStop untuk titik naik
     */
    public function fromStop()
    {
        return $this->belongsTo(Stop::class, 'from_stop_id');
    }

    /**
     * Relasi ke RouteStop untuk titik turun
     */
    public function toStop()
    {
        return $this->belongsTo(Stop::class, 'to_stop_id');
    }
}
