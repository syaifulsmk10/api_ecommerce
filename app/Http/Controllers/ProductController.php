<?php

namespace App\Http\Controllers;

use App\Models\discount;
use App\Models\product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $product = product::with('discount', 'ratings')->orderByDesc("created_at");

        if($request->category_id){
            $product = $product->where("category_id", $request->category_id);
        }

        $product = $product->paginate(10)->map(function ($product) {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'image' => $product->image,
            'desc' => $product->desc,
            'price' => $product->price,
            'category_id' => $product->category_id,
            'stock' => $product->stock,
            'created_at' => $product->created_at,
            'updated_at' => $product->updated_at,
            'product_rating' => $product->product_rating,
            'discounted_price' => $product->discounted_price,
            'discount_value' => $product->discount ? $product->discount->discount_value : 0,
        ];
    });

    return response()->json([
        "data" => $product
    ]);
}


    public function create(Request $request)
    {
       

    $imagePath = $request->file("image")->move(public_path(), $request->file("image")->getClientOriginalName());
        $imagename = $request->file("image")->getClientOriginalName();

        if(!$imagePath){
            return response()->json([
                "message" => "Failed to upload image"
            ], 400);
        }

            $product = Product::create([
                'name' => $request->name,
                'image' => $imagename,
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

    $data = $request->only(['name', 'image', 'desc', 'price', 'category_id', 'stock']);

    $product->update($data);

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