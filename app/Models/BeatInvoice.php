<?php

namespace App\Models;

use App\Models\Journal;
use App\Models\BeatSubscriptionStaging;
use Illuminate\Database\Eloquent\Model;

class BeatInvoice extends Model
{
    protected $fillable = [
        // 'import_batch_id',
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
    public function getComputedStatusAttribute()
{
    $paid = $this->payments()->sum('amount');

    if ($paid == 0) return 'unpaid';
    if ($paid < $this->total_amount) return 'partial';
    return 'paid';
}

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

    /**
     * Scope untuk filter berdasarkan payment status
     */
    public function scopeByPaymentStatus($query, $status)
    {
        return $query
            ->leftJoin('payments', 'payments.invoice_id', '=', 'beat_invoices.id')
            ->select(
                'beat_invoices.id',
                'beat_invoices.total_amount',
                'beat_invoices.customer_name',
                'beat_invoices.pppoe',
                'beat_invoices.package_name',
                'beat_invoices.billing_day',
                'beat_invoices.period_month',
                'beat_invoices.period_year'
            )
            ->selectRaw('COALESCE(SUM(payments.amount), 0) as paid_amount')
            ->groupBy(
                'beat_invoices.id',
                'beat_invoices.total_amount',
                'beat_invoices.customer_name',
                'beat_invoices.pppoe',
                'beat_invoices.package_name',
                'beat_invoices.billing_day',
                'beat_invoices.period_month',
                'beat_invoices.period_year'
            )
            ->when($status === 'unpaid', function($q) {
                return $q->havingRaw('COALESCE(SUM(payments.amount), 0) = 0');
            })
            ->when($status === 'partial', function($q) {
                return $q->havingRaw(
                    'COALESCE(SUM(payments.amount), 0) > 0 AND '
                    .'COALESCE(SUM(payments.amount), 0) < beat_invoices.total_amount'
                );
            })
            ->when($status === 'paid', function($q) {
                return $q->havingRaw(
                    'COALESCE(SUM(payments.amount), 0) >= beat_invoices.total_amount'
                );
            });
    }
    
    /**
     * Get stats untuk dashboard
     */
    public static function getStats()
    {
        return [
            'total' => self::count(),
            'total_amount' => (int) self::sum('total_amount'),
            'paid_count' => self::byPaymentStatus('paid')->count(),
            // 'partial_count' => self::byPaymentStatus('partial')->count(),
            'unpaid_count' => self::byPaymentStatus('unpaid')->count(),
        ];
    }
}