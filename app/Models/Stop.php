<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Stop extends Model
{
    use HasFactory;
    protected $table = 'route_stops'; // Nama tabel yang sesuai dengan database Anda

    protected $fillable = [
        'route_id',
        'stop_order',
        'stop_name', // Harga dari stop sebelumnya
    ];

    // Relasi ke tabel routes
    public function route()
    {
        return $this->belongsTo(Route::class);
    }
    // Stop.php
public function fromPrices()
{
    return $this->hasMany(StopPrice::class, 'from_stop_id');
}

public function toPrices()
{
    return $this->hasMany(StopPrice::class, 'to_stop_id');
}

}
