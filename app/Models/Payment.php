<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'invoice_id',
        'payment_date',
        'amount',
        'method',      // cash | bank
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
}
