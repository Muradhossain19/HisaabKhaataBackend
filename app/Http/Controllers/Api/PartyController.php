<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Party;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PartyController extends Controller
{
    public function index(Request $request)
    {
        $query = Party::query();

        // Phase-1: keep user_id optional; once backend-hardening is done, enforce user scoping.
        $user = $request->user();
        if ($user) {
            $query->where(function ($q) use ($user) {
                $q->whereNull('user_id')->orWhere('user_id', $user->id);
            });
        }

        $parties = $query->orderBy('name')->get();

        // Compute due balance per party = sum(you_get) - sum(you_give)
        $dueMap = DB::table('party_ledger_entries')
            ->select('party_id', DB::raw("SUM(CASE WHEN direction='you_get' THEN amount ELSE -amount END) as due"))
            ->groupBy('party_id')
            ->pluck('due', 'party_id');

        $data = $parties->map(function ($p) use ($dueMap) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'phone' => $p->phone,
                'due' => (float) ($dueMap[$p->id] ?? 0),
            ];
        });

        return response()->json(['data' => $data]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:64',
            'notes' => 'nullable|string',
        ]);

        $user = $request->user();
        if ($user) $data['user_id'] = $user->id;

        $party = Party::create($data);
        return response()->json($party, 201);
    }

    public function update(Request $request, Party $party)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:64',
            'notes' => 'nullable|string',
        ]);

        $party->update($data);
        return response()->json($party);
    }

    public function destroy(Party $party)
    {
        $party->delete();
        return response()->json(null, 204);
    }
}

