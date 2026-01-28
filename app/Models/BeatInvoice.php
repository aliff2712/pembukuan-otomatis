<?php

namespace App\Models;

use App\Models\Journal;
use App\Models\BeatSubscriptionStaging;
use Illuminate\Database\Eloquent\Model;

class BeatInvoice extends Model
{
    protected $fillable = [
        'import_batch_id',
        'staging_id',

        'customer_name',
        'pppoe',
        'package_name',

        'total_amount',

        'billing_day',
        'period_month',
        'period_year',

        'status',
    ];

    protected $casts = [
        'total_amount' => 'integer',
    ];

    public function staging()
    {
        return $this->belongsTo(BeatSubscriptionStaging::class, 'staging_id');
    }

        public function isPaid(): bool
        {
            return $this->status === 'paid';
        }
        public function journal()
        {
            return $this->hasOne(Journal::class, 'reference_id')->where('reference_type', 'BeatInvoice');
        }

        public function payments()
        {
            return $this->hasMany(Payment::class, 'invoice_id');
        }

        public function getPaidAmountAttribute(): int
        {
            return $this->payments()->sum('amount');
        }

        public function getOutstandingAmountAttribute(): int
        {
            return max(0, $this->total_amount - $this->paid_amount);
        }

        public function getStatusAttribute(): string
        {
            if ($this->paid_amount <= 0) {
                return 'unpaid';
            }

            if ($this->paid_amount < $this->total_amount) {
                return 'partial';
            }

            return 'paid';
        }


}
