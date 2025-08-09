<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    protected $table = 'routes'; // Nama tabel yang sesuai dengan database Anda

    protected $fillable = [
        'origin','destination','duration_minutes'
    ];
    // Route.php
protected $casts = [
    'duration_minutes' => 'integer',
];

public function stops()
{
    return $this->hasMany(Stop::class);
}

public function stopPrices()
{
    return $this->hasMany(StopPrice::class);
}



}
