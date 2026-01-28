<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Payment;
use App\Services\PaymentPostingService;

class PaymentPost extends Command
{
    protected $signature = 'payment:post {payment_id}';
    protected $description = 'Post payment to ledger & mark invoice as paid';

    public function handle(PaymentPostingService $service): int
    {
        $paymentId = $this->argument('payment_id');

        $payment = Payment::with('invoice')->find($paymentId);

        if (! $payment) {
            $this->error("Payment ID {$paymentId} not found");
            return Command::FAILURE;
        }

        try {
            $service->post($payment);
        } catch (\Throwable $e) {
            $this->error('Failed to post payment: ' . $e->getMessage());
            return Command::FAILURE;
        }

        $this->info("Payment {$paymentId} successfully posted");
        return Command::SUCCESS;
    }
}
