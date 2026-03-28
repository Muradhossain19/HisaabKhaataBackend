<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function index()
    {
        return response()->json(['data' => PaymentMethod::orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name' => 'required|string', 'type' => 'nullable|string']);
        $pm = PaymentMethod::create($data);
        return response()->json($pm, 201);
    }

    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $data = $request->validate(['name' => 'required|string', 'type' => 'nullable|string']);
        $paymentMethod->update($data);
        return response()->json($paymentMethod);
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        $paymentMethod->delete();
        return response()->json(null, 204);
    }
}
