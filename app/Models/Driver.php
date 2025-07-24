<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    protected $table = 'drivers'; // Nama tabel yang sesuai dengan database Anda

    protected $fillable = [
        'name',
        'phone_number',
        'address',
        'status',
        'current_location', // Tambahkan ini jika Anda ingin menyimpan lokasi saat ini
    ];

    public function vehicles()
{
    return $this->belongsToMany(Vehicle::class);
}
}
