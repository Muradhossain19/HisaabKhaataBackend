<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'client_id', 'type', 'amount', 'currency', 'category_id', 'payment_method_id', 'date', 'note', 'attachments', 'is_synced'
    ];

    protected $casts = [
        'attachments' => 'array',
        'date' => 'datetime',
        'is_synced' => 'boolean',
    ];
}
