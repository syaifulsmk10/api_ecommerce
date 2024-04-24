<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class discount extends Model
{
    use HasFactory;

     protected $fillable = ['discount_type', 'product_id', 'coupon_code', 'discount_value', 'time_start', 'time_end'];

    protected $dates = ['time_start', 'time_end'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}