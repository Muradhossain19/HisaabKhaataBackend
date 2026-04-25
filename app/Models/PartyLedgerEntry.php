<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartyLedgerEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'party_id',
        'direction',
        'amount',
        'date',
        'note',
    ];

    protected $casts = [
        'date' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function party()
    {
        return $this->belongsTo(Party::class);
    }
}

