<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'invoice' => 'array',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
