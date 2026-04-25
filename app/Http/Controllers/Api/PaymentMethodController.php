<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function index(Request $request)
    {
        $query = PaymentMethod::query();
        $user = $request->user();
        if ($user) {
            $query->where(function ($q) use ($user) {
                $q->whereNull('user_id')->orWhere('user_id', $user->id);
            });
        }
        return response()->json(['data' => $query->orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name' => 'required|string', 'type' => 'nullable|string']);
        $user = $request->user();
        if ($user) $data['user_id'] = $user->id;
        $pm = PaymentMethod::create($data);
        return response()->json($pm, 201);
    }

    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $user = $request->user();
        if ($user && $paymentMethod->user_id && $paymentMethod->user_id !== $user->id) {
            return response()->json(['message' => 'Not found'], 404);
        }
        $data = $request->validate(['name' => 'required|string', 'type' => 'nullable|string']);
        $paymentMethod->update($data);
        return response()->json($paymentMethod);
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        $user = request()->user();
        if ($user && $paymentMethod->user_id && $paymentMethod->user_id !== $user->id) {
            return response()->json(['message' => 'Not found'], 404);
        }
        $paymentMethod->delete();
        return response()->json(null, 204);
    }
}
