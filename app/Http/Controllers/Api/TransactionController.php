<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::query();
        $user = $request->user();
        if ($user) {
            $query->where(function ($q) use ($user) {
                $q->whereNull('user_id')->orWhere('user_id', $user->id);
            });
        }
        if ($request->filled('from')) {
            $query->where('date', '>=', $request->input('from'));
        }
        if ($request->filled('to')) {
            $query->where('date', '<=', $request->input('to'));
        }
        $data = $query->orderBy('date', 'desc')->paginate(50);
        return response()->json($data);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id' => 'nullable|string',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric',
            'currency' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'payment_method_id' => 'nullable|exists:payment_methods,id',
            'date' => 'nullable|date',
            'note' => 'nullable|string',
            'attachments' => 'nullable|array',
        ]);

        $data['is_synced'] = true;
        $user = $request->user();
        if ($user) $data['user_id'] = $user->id;
        $t = Transaction::create($data);
        return response()->json($t, 201);
    }

    public function bulk(Request $request)
    {
        $payload = $request->validate(['transactions' => 'required|array']);
        $results = [];
        $user = $request->user();

        DB::beginTransaction();
        try {
            foreach ($payload['transactions'] as $item) {
                $clientId = $item['client_id'] ?? null;
                $data = [
                    'user_id' => $user ? $user->id : null,
                    'client_id' => $clientId,
                    'type' => $item['type'] ?? 'expense',
                    'amount' => $item['amount'] ?? 0,
                    'currency' => $item['currency'] ?? 'BDT',
                    'category_id' => $item['category_id'] ?? null,
                    'payment_method_id' => $item['payment_method_id'] ?? null,
                    'date' => $item['date'] ?? now(),
                    'note' => $item['note'] ?? null,
                    'attachments' => $item['attachments'] ?? null,
                    'is_synced' => true,
                ];
                $t = Transaction::create($data);
                $results[] = ['client_id' => $clientId, 'server_id' => $t->id, 'transaction' => $t];
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'bulk_failed', 'message' => $e->getMessage()], 500);
        }

        return response()->json(['data' => $results]);
    }

    public function show(Transaction $transaction)
    {
        $user = request()->user();
        if ($user && $transaction->user_id && $transaction->user_id !== $user->id) {
            return response()->json(['message' => 'Not found'], 404);
        }
        return response()->json($transaction);
    }

    public function update(Request $request, Transaction $transaction)
    {
        $user = $request->user();
        if ($user && $transaction->user_id && $transaction->user_id !== $user->id) {
            return response()->json(['message' => 'Not found'], 404);
        }
        $data = $request->validate([
            'type' => 'in:income,expense',
            'amount' => 'numeric',
            'currency' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'payment_method_id' => 'nullable|exists:payment_methods,id',
            'date' => 'nullable|date',
            'note' => 'nullable|string',
            'attachments' => 'nullable|array',
        ]);
        $transaction->update($data);
        return response()->json($transaction);
    }

    public function destroy(Transaction $transaction)
    {
        $user = request()->user();
        if ($user && $transaction->user_id && $transaction->user_id !== $user->id) {
            return response()->json(['message' => 'Not found'], 404);
        }
        $transaction->delete();
        return response()->json(null, 204);
    }
}
