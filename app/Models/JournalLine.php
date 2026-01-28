<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JournalLine extends Model
{
    protected $fillable = [
        'journal_entry_id',
        'account_code',
        'account_name',
        'debit',
        'credit'
    ];
     public function coa()
    {
        return $this->belongsTo(ChartOfAccount::class, 'coa_id');
    }
}

