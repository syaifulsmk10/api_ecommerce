<?php

namespace App\Http\Controllers;

use App\Models\rating;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rating = rating::all();

        return response()->json([
            "data" => $rating
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $rating = rating::create($request->all());

         return response()->json([
            "data" => $rating
        ]);
 
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(rating $rating)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(rating $rating)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, rating $rating)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(rating $rating)
    {
        //
    }


    public function average($product_id)
    {
        $rating = rating::where('product_id', $product_id)->avg('rating');

        return response()->json([
            "data" => $rating,
        ]);
    }
}
