<?php

namespace App\Models;

use App\Models\Journal;
use App\Models\BeatInvoice;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'invoice_id',
        'payment_date',
        'amount',
        'method',
        'reference',
        'note',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'integer',
    ];

    public function invoice()
    {
        return $this->belongsTo(BeatInvoice::class, 'invoice_id');
    }

    public function journal()
    {
        return $this->morphOne(Journal::class, 'journalable');
    }
}
