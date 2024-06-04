<?php

namespace App\Http\Controllers;

use App\Models\cart;
use App\Models\Discount;
use App\Models\product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
     public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
            'quantity' => 'nullable',
            'coupon_code' => 'nullable'
        ]);

        $product = product::findOrFail($request->product_id);
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
       $cartItem = Cart::where('user_id', Auth::user()->id)->where('status', 1)
        ->where('product_id', $product->id)
        ->first();

    if ($cartItem) {
        // If the product is already in the cart, update the quantity and total price
        $cartItem->quantity += $quantity;
        $cartItem->total_price += $totalPrice;
        $cartItem->save();
    } else {
        // If the product is not in the cart, create a new cart item
        cart::create([
            "user_id" => Auth::user()->id,
            "product_id" => $product->id,
            "quantity" => $quantity,
            "total_price" => $totalPrice,
            "discount_id" => $discount ? $discount->id : null,
        ]);
    }

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
        $carts = Cart::where('user_id', Auth::user()->id)->where('status',1)->get();
        foreach ($carts as $cartsItem) {
            $totalpri += $cartsItem->total_price;
        }

    return response()->json([
        'message' => 'Quantity updated successfully',
        'totalpricee' => $totalpricee,
        'totalpri' => $totalpri
    ]);

}

    // public function getCoupon(Request $request)
    // {

    // $coupon_code = $request->coupon_code;

    //    $discount = Discount::where('coupon_code',  $coupon_code)
    //                     ->first();


    //     if (!$discount || !(Carbon::now()->lessThanOrEqualTo(Carbon::parse($discount->time_end)->endOfDay()))) {
    //         return response()->json(['message' => 'Invalid or expired voucher code'], 400);
    //     }
     
    //  return response()->json([
    //         'data' => $discount,
    //     ], 200);
    // }


    public function getCart(Request $request)
    {
              
     
    $cart = Cart::where("user_id", Auth::user()->id)->where("status",1)->get();
//     if ($cart) {
//     $discounttype = Discount::where('id', $cart->id)->get();
//     dd($discounttype);
// } 

        $cartItems = [];
        $totalprice = 0;
        $totalpricee = 0;
        $discountprice = 0;
       
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

            $totalprice += $cartsItem->quantity * $price;

            $couponCode = $request->coupon_code;

                if ($couponCode) {
                    $discount = Discount::where('coupon_code', $couponCode)->first();
                    
                    if (!$discount || !(Carbon::now()->lessThanOrEqualTo(Carbon::parse($discount->time_end)->endOfDay()))) {
                    return response()->json(['message' => 'Invalid or expired voucher code'], 400);}

                $totalpricee = $totalprice - ($totalprice * $discount->discount_value/100);
                $discountprice = $totalprice * ($discount->discount_value / 100);

            
                } else {
                $totalpricee = $totalprice;
                $discountprice = 0;
                }

             $cartItems[] = [
            "name" => $name,
            "image" => $image,
            "desc" => $desc,
            "quantity" => $quantity,
            "price" => $price,
        ];
        }

        return response()->json([
            "cartItems" =>  $cartItems,
             "totalpricee" => $totalpricee,
             "discountprice" => $discountprice,
        
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
