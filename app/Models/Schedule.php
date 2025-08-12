<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $table = 'schedules'; // Nama tabel yang sesuai dengan database Anda

    protected $fillable = [
        'route_id',
        'vehicle_id',
        'driver_id',
        'departure_date',
        'departure_time',
        'available_seats',
        'status',
        'arrival_time' // Tambahkan ini jika Anda ingin menyimpan waktu kedatangan,
    ];
    protected $casts = [
    'departure_time' => 'datetime',
];

    // Schedule.php
    public function route()
    {
        return $this->belongsTo(Route::class, 'route_id');
    }
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }
    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }
    public function seats()
    {
        return $this->hasMany(Seat::class);
    }
    

}
