<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyVoucherSale extends Model
{
    protected $table = 'daily_voucher_sales';

    protected $fillable = [
        'sale_date',
        'total_transactions',
        'total_amount',
    ];
}
