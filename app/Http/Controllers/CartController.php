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

    public function update(Request $request, $id)
    {
        $user = $request->user();
        $cartItem = Cart::where('user_id', $user->id)->where('id', $id)->firstOrFail();

        $validated = $request->validate([
            'quantity' => 'required|numeric|min:1',
            // If you also want to allow price updates, include it in validation
            'prod_price' => 'sometimes|numeric',
        ]);

        $cartItem->quantity = $validated['quantity'];
        // If the price is included in the request, update it
        if (isset($validated['prod_price'])) {
            $cartItem->prod_price = $validated['prod_price'];
        }
        $cartItem->save();

        return response()->json($cartItem);
    }


    public function destroy(Cart $cart)
    {
        $cart->delete();

        return response()->json(null, 204);
    }

    public function getAllProductInCart(string $user_id)
    {
        $cartItems = Cart::where('user_id', $user_id)->with('product')->get();

        if (!$cartItems) {
            return response()->json([
                'message' => 'Cart is empty'
            ]);
        }
        return response()->json($cartItems);
    }
}
