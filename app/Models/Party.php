<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Party extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'notes',
    ];

    public function ledgerEntries()
    {
        return $this->hasMany(PartyLedgerEntry::class);
    }
}

