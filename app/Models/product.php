<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class product extends Model
{
    use HasFactory;

    
    protected $fillable = ['name', 'image', 'desc', 'price', 'category_id', 'stock'];
    
    protected $appends = ['product_rating', 'discounted_price',];
    

    public function cart()
    {
       return $this->hasMany(Cart::class)->onDelete('cascade');
    }


    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function discount()
    {
       return $this->hasOne(Discount::class, 'product_id', 'id');
    }

    public function ratings()
    {
        return $this->hasMany(rating::class);
    }

        public function getImageAttribute($value)
        {
            return env('APP_URL') . $value;
        }

        public function getProductRatingAttribute()
        {
            return $this->ratings->avg("rating") ?: 0;
        }

       public function getDiscountedPriceAttribute()
    {
        if ($this->discount) {
            return $this->price - ($this->price * ($this->discount->discount_value / 100));
        } else {
            return $this->price;
        }
    }



}
