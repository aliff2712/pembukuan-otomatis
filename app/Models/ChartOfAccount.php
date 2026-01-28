<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class ChartOfAccount extends Model
{
    protected $fillable = [
        'account_code',
        'account_name',
        'account_type',
        'is_cash', // ← INI YANG HILANG
    ];

    protected $casts = [
        'is_cash' => 'boolean',
    ];
}
