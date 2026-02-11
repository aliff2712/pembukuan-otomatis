<?php

namespace App\Http\Controllers;

use App\Models\BeatInvoice;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;


class BeatInvoiceController extends Controller
{
    public function index()
    {
        // Filter berdasarkan status jika ada
        if (request()->filled('status')) {
            $query = BeatInvoice::byPaymentStatus(request('status'));
        } else {
            $query = BeatInvoice::query();
        }

        // search by customer name or pppoe or package
        if (request()->filled('search')) {
            $s = request('search');
            $query->where(function($q) use ($s) {
                $q->where('customer_name', 'like', "%{$s}%")
                  ->orWhere('pppoe', 'like', "%{$s}%")
                  ->orWhere('package_name', 'like', "%{$s}%");
            });
        }

        $invoices = $query->orderBy('period_year', 'desc')
            ->orderBy('period_month', 'desc')
            ->paginate(20)
            ->withQueryString();

        // Get stats
        $stats = BeatInvoice::getStats();

        return view('beat-invoices.index', compact('invoices', 'stats'));
    }
    
    public function show($id)
    {
        $invoice = BeatInvoice::with(['staging', 'payments'])->findOrFail($id);

        return view('beat-invoices.show', compact('invoice'));
    }
public function getUnpaid()
{
    $unpaid = BeatInvoice::select('beat_invoices.*')
        ->selectRaw('COALESCE(SUM(payments.amount),0) as paid_amount')
        ->leftJoin('payments', 'payments.invoice_id', '=', 'beat_invoices.id')
        ->groupBy('beat_invoices.id')
        ->havingRaw('beat_invoices.total_amount > COALESCE(SUM(payments.amount),0)')
        ->get()
        ->map(function($inv){
            return [
                'id' => $inv->id,
                'customer_name' => $inv->customer_name,
                'total_amount' => $inv->total_amount,
                'paid_amount' => $inv->paid_amount,
                'outstanding' => $inv->total_amount - $inv->paid_amount,
            ];
        });

    return response()->json($unpaid);
}

}
