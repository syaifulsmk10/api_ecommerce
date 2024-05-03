<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\cart;
use App\Models\category;
use App\Models\discount;
use App\Models\product;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Role::create([
            "name" => "admin",
        ]);

        Role::create([
            "name" => "siswa",
        ]);

         User::create([
        "name" => "admin",
        "username" => "admin",
        "email" => "admin@admin.com",
        "password" => bcrypt("admin_password"),
        "role_id" => 1
    ]);

        User::create([
            "name" => "user",
            "username" => "user",
             "email" => "user@user.com",
            "password" => bcrypt("user_password"),
            "role_id" => 2, 
        ]);


       $category1 = Category::create(['name' => 'elektronik']);
        $category2 = Category::create(['name' => 'school']);

        $product1 = Product::create([
            'name' => 'Laptop',
            'image' => 'laptop.jpg',
            'desc' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
            'price' => 1000,
            'category_id' => $category1->id,
            'stock' => 10
        ]);

        $product2 = Product::create([
            'name' => 'book',
            'image' => 'book.jpg',
            'desc' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
            'price' => 1000,
            'category_id' => $category2->id,
            'stock' => 20
        ]);

        $discount1 = Discount::create([
            'discount_type' => 1, 
            'product_id' => $product1->id,
            'discount_value' => 10, 
            'time_start' => Carbon::now(),
            'time_end' => Carbon::now()->addDays(10)
        ]);

        $discount2 = Discount::create([
            'discount_type' => 2, 
            'product_id' => $product2->id,
            'discount_value' => 10, 
            'coupon_code' => 'aaa',
            'time_start' => Carbon::now(),
            'time_end' => Carbon::now()->addDays(10)
        ]);

        $discountedPriceProduct1 = $product1->price - ($product1->price * ($discount1->discount_value / 100));
        $discountedPriceProduct2 = $product2->price -  ($product1->price * ($discount1->discount_value / 100));

        Cart::create([
            'product_id' => $product1->id,
            'discount_id' => $discount1->id,
            'quantity' => 2,
            'total_price' => $discountedPriceProduct1 * 2
        ]);

        Cart::create([
            'product_id' => $product2->id,
            'discount_id' => $discount2->id,
            'quantity' => 3,
            'total_price' => $discountedPriceProduct2 * 3
        ]);
    }
}
