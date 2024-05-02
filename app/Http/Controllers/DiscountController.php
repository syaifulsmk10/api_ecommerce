<?php

namespace App\Http\Controllers;

use App\Models\discount;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
     public function index(){
        $discount = discount::all();
        return response()->json([
            "message" => "success get discount",
            "data" => $discount
        ]);
    }

    public function create(Request $request){
        $discount = discount::create([
        'name' => $request->name,
        'image' => $request->image,
        'desc' => $request->desc,
        'price' => $request->price,
        'category_id' => $request->category_id,
        'stock' => $request->stock
        ]);

         if ($discount) {
            return response()->json([
                "message" => "success create discount",
                "Data" => $discount
            ], 200);
        } else {
            return response()->json([
                "message" => "failed create discount"
            ], 400); 
        }

    }

     public function update(Request $request)
    {
        $data = discount::findOrFail($request->id);
        $updateData = $data->update([
            'name' => $request->name,
            'image' => $request->image,
            'desc' => $request->desc,
            'price' => $request->price,
            'category_id' => $request->category_id,
            'stock' => $request->stock
        ]);

         if ($updateData) {
            return response()->json([
                "message" => "Berhasil mengupdate data",
                "Data" => $data
            ],200);
        } else {
            return response()->json([
                "message" => "Gagal mengupdate data"
            ], 400); // Mengembalikan status 400 jika gagal membuat buku
        }

    }

     public function destroy(Request $request)
    {
        $dataToDelete = discount::findOrFail($request->id);
         $deleteProced = $dataToDelete->delete();

         if(!$deleteProced) return response()->json([
           "Message" => "Gagal Menghapus Data!"
         ],400);

         return response()->json([
            "Message" => "Berhasil Menghapus Data!"
         ],200);
    }
}
