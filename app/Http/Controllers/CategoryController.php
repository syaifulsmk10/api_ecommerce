<?php

namespace App\Http\Controllers;

use App\Models\category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(){
        $category = category::all();
        return response()->json([
            "message" => "success get category",
            "data" => $category
        ]);
    }
}
