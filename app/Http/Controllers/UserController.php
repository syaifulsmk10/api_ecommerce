<?php

namespace App\Http\Controllers;

use App\Models\product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
     public function postLogin(Request $request)
    {
        $validate = $request->validate([
            "name" => "required",
            "password" => "required",
        ]);
        $token = User::where("name", $request->name)->first()->createToken('auth')->plainTextToken;

        if (!Auth::attempt($validate)) return response()->json([
            'message' => 'wrong username or password',
            'data' => $validate
        ], 404);

        if (Auth::user()->role_id == 1) return response()->json([
            'message' => 'success Login Admin',
            'data' => $validate,
            'token' => $token
        ], 200);

        return response()->json([
            'message' => 'success Login User',
            'data' => $validate,
            'token' => $token
        ], 200);
    }

    public function registerUser(Request $request)
    {
        $user = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => bcrypt($request->password),
            "role_id" => 2
        ]);

        return response()->json([
            'message' => 'success register admin',
            'data' => $user
        ], 200);
    }

    public function getUser(Request $request)
        {
            $user = $request->user();
               
        if ($user->role_id == 1) {
        return response()->json([
            'message' => 'success',
            'data' => [
                'name' => $user->name,
                'role' => 'admin',
            ]
        ], 200);
    } else {
        $products = product::all(); 
        // $cartItems = $user->cartItems(); 
    
        return response()->json([
            'message' => 'success',
            'data' => [
                'name' => $user->name,
                'role' => 'user',
                'products' => $products,
                // 'cart' => $cartItems
            ]
        ], 200);
        }
    }


    public function index(){
    {
        $roles = User::all();

        return response()->json([
            "data" => $roles
        ], 200);
    }
    }

    public function create(Request $request){
         $user = User::create([
            'name' => $request->name,
            'password' => $request->password,
            'email' => $request->email,
            'roles_id' => 2,
        ]);

        return response()->json([
            'message' => 'store user',
            'data' => $user
        ], 200);
    }

    public function update(Request $request, $id){

        $user = User::find($id);
        if ($request->password == null){
            $user->update([
                'name' => $request->name,
                'password' => $user->password,
                'roles_id' => $request->roles_id
            ]);
        }else{
            $user->update([
                 'name' => $request->name,
                'password' => $user->password,
                'roles_id' => $request->roles_id
            ]);
        }

        return response()->json([
                'message' => 'success update user',
                'data' => $user
            ], 200);
    }

    public function destroy($id)
    {
        $user = User::find($id);
        $user->delete();
        return response()->json([
            'message' => 'success delete user',
            'data' => $user
        ], 200);
    }
}
