<?php

namespace App\Http\Controllers;

use App\Models\discount;
use App\Models\product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $product = product::orderByDesc("created_at");

        if($request->category_id){
            $product = $product->where("category_id", $request->category_id);
        }

        $product = $product->get();


        return response()->json([
            "message" => "success get product",
            "data" => $product
        ]);
    }



    public function create(Request $request)
    {
        
    // $imagePath = $request->file("image")->move(public_path(), "tes.png");
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

    $imagePath = $request->file("image")->move(public_path(), $request->file("image")->getClientOriginalName());

        if(!$imagePath){
            return response()->json([
                "message" => "Failed to upload image"
            ], 400);
        }

            $product = Product::create([
                'name' => $request->name,
                'image' => $imagePath,
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

            $discount = null;

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
                "discount" => $discount
            ], 201);

    }

      public function update(Request $request, $id)
{
    $product = Product::findOrFail($id);

    if ($request->has('name')) {
        $product->name = $request->name;
        // Simpan perubahan hanya jika ada perubahan pada atribut 'name'
        $product->save();
    }

    return response()->json($product, 200);
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