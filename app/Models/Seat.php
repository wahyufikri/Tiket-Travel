<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{
    protected $table = 'seats';

    protected $fillable = [
        'schedule_id',
        'seat_number',
        'is_booked',
    ];

    /**
     * Relasi ke Schedule (jadwal perjalanan)
     */
    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }
}
