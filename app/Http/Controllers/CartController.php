<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;
        $cartItems = Cart::where('user_id', $userId)->with('product')->get();

        return response()->json($cartItems);
    }

    public function store(Request $request)
    {
        $userId = $request->user()->id;
        $cartItem = Cart::create([
            'user_id' => $userId,
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
        ]);

        return response()->json($cartItem, 201);
    }

    public function update(Request $request, Cart $cart)
    {
        $cart->update($request->only(['quantity']));

        return response()->json($cart);
    }

    public function destroy(Cart $cart)
    {
        $cart->delete();

        return response()->json(null, 204);
    }
}
