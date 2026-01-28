<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeatSubscriptionStaging extends Model
{
    protected $fillable = [
        'import_batch_id',
        'raw_id',

        'customer_name',
        'phone',
        'pppoe',
        'package_name',
        'area',
        'address',

        'base_price',
        'extra_fee_1',
        'extra_fee_2',

        'billing_day',
        'period_month',
        'period_year',

        'extra_note',
        'admin_by',

        'status',
        'error_reason',
    ];

    public function invoice()
    {
        return $this->hasOne(BeatInvoice::class, 'staging_id');
    }

    public function getTotalAmountAttribute(): int
    {
        return
            ($this->base_price ?? 0)
            + ($this->extra_fee_1 ?? 0)
            + ($this->extra_fee_2 ?? 0);
    }
}
