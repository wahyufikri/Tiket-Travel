<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StopPrice extends Model
{
    protected $table = 'stop_prices'; // Nama tabel yang sesuai dengan database Anda
    protected $fillable = ['route_id', 'from_stop_id', 'to_stop_id', 'price'];

    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    public function fromStop()
    {
        return $this->belongsTo(Stop::class, 'from_stop_id');
    }

    public function toStop()
    {
        return $this->belongsTo(Stop::class, 'to_stop_id');
    }
}
