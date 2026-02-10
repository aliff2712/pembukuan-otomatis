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
        $query = BeatInvoice::query();

        // search by customer name or pppoe or package
        if (request()->filled('search')) {
            $s = request('search');
            $query->where(function($q) use ($s) {
                $q->where('customer_name', 'like', "%{$s}%")
                  ->orWhere('pppoe', 'like', "%{$s}%")
                  ->orWhere('package_name', 'like', "%{$s}%");
            });
        }

        if (request()->filled('status')) {
            $query->where('status', request('status'));
        }

        $invoices = $query->orderBy('period_year', 'desc')
            ->orderBy('period_month', 'desc')
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total' => BeatInvoice::count(),
            'total_amount' => (int) BeatInvoice::sum('total_amount'),
            'unpaid_count' => BeatInvoice::whereRaw('total_amount > COALESCE((select SUM(amount) from payments where payments.invoice_id = beat_invoices.id),0)')->count(),
        ];

        return view('beat-invoices.index', compact('invoices', 'stats'));
    }
    
    public function show($id)
    {
        $invoice = BeatInvoice::with(['staging', 'payments'])->findOrFail($id);

        return view('beat-invoices.show', compact('invoice'));
    }

    public function getUnpaid()
    {
        $unpaid = BeatInvoice::get()->map(function($inv) {
            return [
                'id' => $inv->id,
                'customer_name' => $inv->customer_name,
                'total_amount' => $inv->total_amount,
                // 'paid_amount' => $inv->paid_amount ?? $inv->payments()->sum('amount'),
                'outstanding' => $inv->outstanding_amount,
            ];
        })->filter(function($i){
            return $i['outstanding'] > 0;
        })->values();

        return response()->json($unpaid);
    }
}
