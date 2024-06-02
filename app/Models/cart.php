<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    

 protected $fillable = ['product_id', 'discount_id', 'quantity', 'total_price',  'user_id'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }

      public function user()
    {
        return $this->belongsTo(User::class);
    }

}
