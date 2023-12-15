<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $payment = Payment::with('order')->get();
        return response()->json(['payments' => $payment]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:order,id',
            'amount' => 'required|numeric',
            'payment_method' => 'required|in:cash,gcash,paymaya',
        ]);

        $payment = Payment::create($request->all());
        return response()->json(['message' => 'Payment created successfully', 'payment' => $payment]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'order_id' => 'exists:order,id',
            'amount' => 'numeric',
            'payment_method' => 'in:cash,gcash,paymaya',
        ]);

        $payment = Payment::findOrFail($id);
        $payment->update($request->all());

        return response()->json(['message' => 'Payment updated successfully', 'payment' => $payment]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $payment = Payment::findOrFail($id);
        $payment->delete();

        return response()->json(['message' => 'Payment deleted successfully']);
    }
}