<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class product extends Model
{
    use HasFactory;

    
    protected $fillable = ['name', 'image', 'desc', 'price', 'category_id', 'stock'];

    public function cart()
    {
       return $this->hasMany(Cart::class)->onDelete('cascade');
    }


    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function discounts()
    {
       return $this->hasMany(Discount::class)->onDelete('cascade');
    }

    
}
