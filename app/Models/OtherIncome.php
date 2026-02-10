<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtherIncome extends Model
{
    protected $fillable = [
        'income_date',
        'description',
        'notes',
        'amount',
        'status',
        'posted_journal_id',
        'created_by',
    ];

    protected $casts = [
        'income_date' => 'date',
        'amount' => 'integer',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function postedJournal()
    {
        return $this->belongsTo(Journal::class, 'posted_journal_id');
    }

    public function isPosted(): bool
    {
        return $this->status === 'posted';
    }
}
