<?php

namespace App\Http\Controllers;

use App\Models\discount;
use App\Models\product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $product = product::all();
        return response()->json([
            "message" => "success get product",
            "data" => $product
        ]);
    }

    //     public function create(Request $request)
// {
//     $productData = $request->input('product');
//     $discountData = $request->input('discount');

    //     // Simpan produk
//     $product = new Product();
//     $product->name = $productData['name'];
//     $product->image = $productData['image'];
//     $product->desc = $productData['desc'];
//     $product->price = $productData['price'];
//     $product->category_id = $productData['category_id'];
//     $product->stock = $productData['stock'];
//     $product->save();

    //     // Simpan diskon
//     $discount = new Discount();
//     $discount->discount_type = $discountData['discount_type'];
//     $discount->product_id = $product->id;
//     $discount->discount_value = $discountData['discount_value'];
//     $discount->time_start = $discountData['time_start'];
//     $discount->time_end = $discountData['time_end'];
//     $discount->save();

    //     return response()->json(['message' => 'Product and discount created successfully'], 201);
// }

    public function create(Request $request)
    {
        // $image = $request->file("image")->move(public_path(), "tes.png");

    //      try {
    //     $product = Product::create([
    //         'name' => $request->name,
    //         'image' => $request->image,
    //         'desc' => $request->desc,
    //         'price' => $request->price,
    //         'category_id' => $request->category_id,
    //         'stock' => $request->stock
    //     ]);
    // } catch (\Exception $e) {
    //     return response()->json([
    //         "message" => "Failed to store product: " . $e->getMessage()
    //     ], 400);
    // }

    
        $product = Product::create([
            'name' => $request->name,
            'image' => $request->image,
            'desc' => $request->desc,
            'price' => $request->price,
            'category_id' => $request->category_id,
            'stock' => $request->stock
        ]);

        if(!$product){
             return response()->json([
                "message" => "Failed to create product"
            ], 400);
        }
        
         if ($request->has('discount_type') && $request->has('discount_value') && $request->has('time_start') && $request->has('time_end')) {
                $discount = Discount::create([
                    'discount_type' => $request->discount_type,
                    'product_id' => $product->id,
                    'discount_value' => $request->discount_value,
                    'time_start' => $request->time_start,
                    'time_end' => $request->time_end
                ]);

            }

            return response()->json([
                "message" => "Success creating product",
                "product" => $product,
                "discount" => $discount ?? null
            ], 201);




        // if ($product) {
        //    
        // } else {
        //     return response()->json([
        //         "message" => "Failed to create product"
        //     ], 400);
        // }
    }

      public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        if ($request->name) {
            $product->name = $request->name;
        }

        if ($request->image) {
            $product->image = $request->image;
        }

        if ($request->desc) {
            $product->desc = $request->desc;
        }

        if ($request->price) {
            $product->price = $request->price;
        }

        if ($request->category_id) {
            $product->category_id = $request->category_id;
        }

        if ($request->stock) {
            $product->stock = $request->stock;
        }

        $product->save();

        return response()->json(['message' => 'Product updated successfully'], 200);
    }
        


    public function destroy($id)
    {

        
        $product = Product::find($id);
        

        if (!$product) {
            return response()->json([
                "message" => "Product not found"
            ], 404);
        }

        Discount::where('product_id', $id)->delete();

        $product->delete();


        return response()->json([
            "message" => "Product and its discount deleted successfully"
        ], 200);
    }
}