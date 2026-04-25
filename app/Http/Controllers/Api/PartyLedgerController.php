<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Party;
use App\Models\PartyLedgerEntry;
use Illuminate\Http\Request;

class PartyLedgerController extends Controller
{
    public function index(Request $request, Party $party)
    {
        $entries = PartyLedgerEntry::query()
            ->where('party_id', $party->id)
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'party' => [
                'id' => $party->id,
                'name' => $party->name,
                'phone' => $party->phone,
            ],
            'entries' => $entries,
        ]);
    }

    public function store(Request $request, Party $party)
    {
        $data = $request->validate([
            'direction' => 'required|in:you_get,you_give',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'nullable|date',
            'note' => 'nullable|string',
        ]);

        $user = $request->user();
        if ($user) $data['user_id'] = $user->id;
        $data['party_id'] = $party->id;
        if (!isset($data['date'])) $data['date'] = now();

        $entry = PartyLedgerEntry::create($data);
        return response()->json($entry, 201);
    }
}

