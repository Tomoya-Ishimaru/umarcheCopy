<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
        'goodsPrice'
    ];

    public function Product()
    {
        return $this->belongsTo(Product::class);
    }
    public function User()
    {
        return $this->belongsTo(User::class);
    }
}
