<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transactions';

    protected $fillable = [
        'type',
        'order_id',
        'title',
        'amount',
        'category_id',
        'transaction_date',
        'payment_method',
        'description',
    ];

    /**
     * Relasi ke Order (jika income dari order online)
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function category()
    {
        return $this->belongsTo(TransactionCategory::class, 'category_id');
    }


}
