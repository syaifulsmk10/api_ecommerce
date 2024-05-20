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
            'quantity' => 'nullable',
            'coupon_code' => 'nullable'
        ]);

        $product = Product::findOrFail($request->product_id);
        $quantity = 1;
        $discountedPrice = $product->price;

        $discount = Discount::where('product_id', $product->id)
            ->where(function ($query) {
                $query->whereNull('coupon_code')
                    ->orWhere('time_start', '<=', Carbon::now())
                    ->where('time_end', '>=', Carbon::now());
            })
            ->first();

        if($product->stock < 1){
            return response()->json([
                "message" => "product finished"
            ]);
        }   
 

        if ($discount) {
            if ($discount->discount_type == "Product") {
                $discountedPrice = $product->price - ($product->price * ($discount->discount_value / 100));
            } else {
                $discountedPrice = $product->price;
            }
        }
        
        $totalPrice = $discountedPrice * $quantity;
        $product->save();


        cart::create([
            "user_id" => Auth::user()->id,
            "product_id" => $product->id,
            "quantity" => $quantity,
            "total_price" => $totalPrice,
            "discount_id" => $discount ? $discount->id : null,
           
        ]);

        return response()->json(['message' => 'Product added to cart successfully.'], 200);
    }


    
public function updateQuantity(Request $request, $id)
{
  
 $cart = Cart::find($id);
$cart->quantity = $request->input('quantity');

$totalPrice = 0;

if ($cart->discount_id == null) {
    $totalPrice = $cart->product->price;
} else {
    if (!$cart->discount->discount_id === '1') {
        $totalPrice = $cart->product->price;
       
    } else {
         $totalPrice = $cart->product->price - ($cart->product->price * ($cart->discount->discount_value / 100));
          
    }
}

$totalpricee = $cart->quantity * $totalPrice;

$cart->total_price = $totalpricee;

$cart->save();
    


     $totalpri = 0;
        $carts = Cart::all();
        foreach ($carts as $cartsItem) {
            $totalpri += $cartsItem->total_price;
        }

    return response()->json([
        'message' => 'Quantity updated successfully',
        'totalpricee' => $totalpricee,
        'totalpri' => $totalpri
    ]);

}

    public function getCoupon(Request $request)
    {
        //  $cart = Cart::findOrFail($request->cart_id);
    $coupon_code = $request->coupon_code;

       $discount = Discount::where('coupon_code',  $coupon_code)
                        ->first();


        if (!$discount || !(Carbon::now()->lessThanOrEqualTo(Carbon::parse($discount->time_end)->endOfDay()))) {
            return response()->json(['message' => 'Invalid or expired voucher code'], 400);
        }
     
     return response()->json([
            'data' => $discount,
        ], 200);
    }


    public function getCart()
    {
        $cart = Cart::where("user_id", Auth::user()->id)->get();


        $cartItems = [];
        $totalpri = 0;
       

        foreach ($cart as $cartsItem) {
            $name = $cartsItem->product->name;
            $image = $cartsItem->product->image;
            $desc = $cartsItem->product->desc;
            $quantity =$cartsItem->quantity;

            if($cartsItem->discount_id ==  1){
                $price = $cartsItem->product->price - ($cartsItem->product->price * ($cartsItem->discount->discount_value/ 100 ));
            }else{

                $price = $cartsItem->product->price;
            }
           
            $totalpri += $cartsItem->total_price;    
            
             $cartItems[] = [
            "name" => $name,
            "image" => $image,
            "desc" => $desc,
            "quantity" => $quantity,
            "price" => $price
        ];
        }

    

        return response()->json([
            "cartItems" =>  $cartItems,
             "totalpri" => $totalpri,
        ]);
    }

    public function checkOut(){
        $cart = cart::where("user_id", Auth::user()->id)->where("status", 1)->get();

        foreach($cart as $carts){
            if(!$carts || $carts->product->stock < $carts->quantity){
                 return response()->json(['status' => 'Stock tidak mencukupi untuk produk: ' . $carts->product->name], 400);
            } 
      
        }

        foreach($cart as $cartitem){

        $cartitem->product->stock -= $cartitem->quantity;
        $cartitem->product->save();
        
        $cartitem->status = 2;
        $cartitem->save();
        }
              
         return response()->json(['status' => 'Berhasil Checkout']);
                    
    }


    public function destroy($id){
        $cart = cart::find($id);
        $cart->delete();

        return response()->json([
            "message" => "cart delete success"
        ]);
    }


    public function destroyAll(){
        cart::truncate();

         return response()->json([
            "message" => "cart delete all success"
        ]);

    }
}