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

        try {

            $validated = $request->validate([
                'product_id' => 'required',
                'quantity' => 'required|numeric|min:1',
            ]);

            $userId = $request->userId;
            $cartItem = Cart::create([
                'user_id' => $userId,
                'product_id' => $validated['product_id'],
                'quantity' => $validated['quantity'],
            ]);

            return response()->json($cartItem, 201);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response()->json(['error' => 'Server Error'], 500);
        }
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
