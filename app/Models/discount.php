<?php

namespace App\Models;

use App\Enums\DiscountType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class discount extends Model
{
    use HasFactory;

     protected $fillable = ['discount_type', 'product_id', 'coupon_code', 'discount_value', 'time_start', 'time_end'];

    protected $dates = ['time_start', 'time_end'];

    public function product()
    {
         return $this->belongsTo(Product::class)->onDelete('cascade');
    }

     public function carts()
       {
        return $this->hasMany(Cart::class)->onDelete('cascade');
    }

    public function getDiscountTypeAttribute($value) {
        $enum = DiscountType::fromValue((int) $value);

        return $enum->key;
    }
}
