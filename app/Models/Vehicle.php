<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $table = 'vehicles'; // Nama tabel yang sesuai dengan database Anda

    protected $fillable = [
        'vehicle_name','license_plate', 'type', 'color', 'capacity', 'year', 'status','seat_configuration','current_location'
    ];

    public function drivers()
{
    return $this->belongsToMany(Driver::class);
}

}
