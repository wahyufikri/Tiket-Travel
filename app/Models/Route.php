<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    protected $table = 'routes'; // Nama tabel yang sesuai dengan database Anda

    protected $fillable = [
        'origin','destination','price','duration_minutes'
    ];
    // Route.php
protected $casts = [
    'duration_minutes' => 'integer',
];

}
