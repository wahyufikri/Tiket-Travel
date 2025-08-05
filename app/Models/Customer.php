<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Illuminate\Database\Eloquent\Model;

class Customer extends Authenticatable
{
    use Notifiable;
    protected $fillable = ['name', 'email', 'phone','password'];


    protected $hidden = [
        'password',
        'remember_token',
    ];
    // Relasi ke daftar pesanan
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
