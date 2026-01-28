<?php

namespace App\Services;

use App\Models\BeatInvoice;
use Carbon\Carbon;

class ARAgingService
{
    public function summary(): array
    {
        $today = Carbon::today();

        $buckets = [
            '0_30'   => 0,
            '31_60'  => 0,
            '61_90'  => 0,
            '90_plus'=> 0,
        ];

        $invoices = BeatInvoice::query()
            ->where('status', 'unpaid')
            ->get();

        foreach ($invoices as $invoice) {
            if (!$invoice->billing_year || !$invoice->billing_month || !$invoice->billing_day) {
                continue; // skip data rusak
            }

            $billingDate = Carbon::create(
                $invoice->billing_year,
                $invoice->billing_month,
                $invoice->billing_day
            );

            $days = $billingDate->diffInDays($today);

            match (true) {
                $days <= 30  => $buckets['0_30']   += $invoice->amount,
                $days <= 60  => $buckets['31_60']  += $invoice->amount,
                $days <= 90  => $buckets['61_90']  += $invoice->amount,
                default      => $buckets['90_plus']+= $invoice->amount,
            };
        }

        return $buckets;
    }
}
