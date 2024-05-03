<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Discount;
use App\Models\Cart;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
            'quantity' => 'required',
            'coupon_code' => 'nullable'
        ]);

        $product = Product::findOrFail($request->product_id);
        $quantity = $request->quantity;
        $coupon_code = $request->coupon_code;

        $discount = Discount::where('product_id', $product->id)
            ->where(function ($query) {
                $query->whereNull('coupon_code')
                    ->orWhere('time_start', '<=', Carbon::now())
                    ->where('time_end', '>=', Carbon::now());
            })
            ->first();

        if ($discount) {
            if ($discount->discount_type == 1) {
                $discountedPrice = $product->price - ($product->price * ($discount->discount_value / 100));
            } else {
                $discountedPrice = $product->price;
            }
        }


        $totalPrice = $discountedPrice * $quantity;

        cart::create([
            // "user_id" => Auth::user()->id,
            "product_id" => $product->id,
            "quantity" => $quantity,
            "total_price" => $totalPrice,
            "discount_id" => $discount ? $discount->id : null
        ]);

        return response()->json(['message' => 'Product added to cart successfully.'], 200);
    }



//     public function applyCoupon(Request $request)
// {

//         $request->validate([
//         'cart_id' => 'required|exists:carts,id',
//         'coupon_code' => 'required|string|exists:discounts,coupon_code'
//     ]);


//         $cart = Cart::findOrFail($request->cart_id);
//     $coupon_code = $request->coupon_code;

//         $discount = Discount::where('coupon_code', $coupon_code)
//                         ->where('product_id', $cart->product_id)
//                         ->where('time_start', '<=', Carbon::now())
//                         ->where('time_end', '>=', Carbon::now())
//                         ->first();

//         if ($discount) {
//         if ($discount->discount_type == 2) { 
//             $discountedPrice = $cart->product->price - ($cart->product->price * ($discount->discount_value / 100));
//         } 
//         $cart->total_price = $discountedPrice * $cart->quantity;
//         $cart->discount_id = $discount->id;
//         $cart->save();
//         return response()->json(['message' => 'Coupon applied successfully.'], 200);
//     } else {
//         return response()->json(['message' => 'Coupon is not valid for this product.'], 400);
//     }
// }


    public function applyCoupon(Request $request)
    {
         $cart = Cart::findOrFail($request->cart_id);
    $coupon_code = $request->coupon_code;

       $discount = Discount::where('coupon_code',  $coupon_code)
                        ->whereDate('time_start', '<=', now())
                        ->whereDate('time_end', '>=', now())
                        ->first();


        if (!$discount) {
            return response()->json(['message' => 'Invalid or expired voucher code'], 400);
        }
        $discountId = $discount->id;
       
        $discountValue = $discount->discount_value;

        $discountedPrice = $cart->product->price - ($cart->product->price * ($discount->discount_value / 100));
         $cart->total_price = $discountedPrice * $cart->quantity;
          $cart->discount_id = $discount->id;
        $cart->save();

        // Cart::where('discount_id')
        //     ->update([
        //         'discount_id' => $discountId,
        //         'total_price' => $discountedPrice
        //     ]);

        return response()->json(['message' => 'Voucher code applied successfully'], 200);
    }


    public function getCart()
    {
        $cart = Cart::where("user_id", Auth::user()->id)->get();

        $total_harga = 0;

        foreach ($cart as $carts) {
            $total_harga += $carts->total_price;
        }

        return response()->json([
            "cart" => $cart,
            "total_harga" => $total_harga
        ]);
    }
}