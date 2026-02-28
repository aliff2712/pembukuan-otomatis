<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\JournalEntry;
use App\Models\JournalLine;
use Carbon\Carbon;
use Illuminate\Http\Request;

class JournalEntryController extends Controller
{
    /**
     * Display a listing of journal entries
     */
    public function index(Request $request)
    {
        $query = JournalEntry::query();

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('journal_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('journal_date', '<=', $request->date_to);
        }

        // Filter by source type
        if ($request->filled('source_type')) {
            $query->where('source_type', $request->source_type);
        }

        // Search by description or reference no
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('reference_no', 'like', "%{$search}%");
            });
        }

        // Filter by month & year (quick filter)
        if ($request->filled('month') && $request->filled('year')) {
            $query->whereMonth('journal_date', $request->month)
                  ->whereYear('journal_date', $request->year);
        }

        $entries = $query->with('lines.coa')
            ->orderBy('journal_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(20);

        // Summary statistics
        $stats = [
            'total_entries' => JournalEntry::count(),
            'this_month' => JournalEntry::whereMonth('journal_date', now()->month)
                ->whereYear('journal_date', now()->year)
                ->count(),
            'total_debit' => JournalEntry::sum('total_debit'),
            'total_credit' => JournalEntry::sum('total_credit'),
        ];

        // Source types for filter
        $sourceTypes = JournalEntry::select('source_type')
            ->distinct()
            ->pluck('source_type')
            ->toArray();

        return view('journal-entries.index', compact('entries', 'stats', 'sourceTypes'));
    }

    /**
     * Display the specified journal entry with details
     */
    public function show($id)
    {
        $entry = JournalEntry::with(['lines' => function($query) {
            $query->orderBy('id');
        }])->findOrFail($id);

        // Calculate totals
        $totalDebit = $entry->lines->sum('debit');
        $totalCredit = $entry->lines->sum('credit');
        $isBalanced = abs($totalDebit - $totalCredit) < 0.01; // Allow small floating point differences

        return view('journal-entries.show', compact('entry', 'totalDebit', 'totalCredit', 'isBalanced'));
    }

    /**
     * Show journal entries by date (daily ledger)
     */
    public function daily(Request $request)
    {
        $date = $request->get('date', now()->toDateString());
        
        $entries = JournalEntry::with('lines')
            ->whereDate('journal_date', $date)
            ->orderBy('id')
            ->get();

        $summary = [
            'date' => $date,
            'total_entries' => $entries->count(),
            'total_debit' => $entries->sum('total_debit'),
            'total_credit' => $entries->sum('total_credit'),
        ];

        return view('journal-entries.daily', compact('entries', 'summary', 'date'));
    }

    /**
     * Export journal entries to Excel/CSV
     */
    public function export(Request $request)
    {
        $query = JournalEntry::query();

        // Apply same filters as index
        if ($request->filled('date_from')) {
            $query->where('journal_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('journal_date', '<=', $request->date_to);
        }

        if ($request->filled('source_type')) {
            $query->where('source_type', $request->source_type);
        }

        $entries = $query->with('lines')->orderBy('journal_date')->get();

        // Prepare CSV data
        $csvData = [];
        $csvData[] = ['Date', 'Description', 'Source', 'Reference', 'Account Code', 'Account Name', 'Debit', 'Credit'];

        foreach ($entries as $entry) {
            foreach ($entry->lines as $line) {
                $csvData[] = [
                    $entry->journal_date,
                    $entry->description,
                    $entry->source_type,
                    $entry->reference_no,
                    $line->account_code,
                    $line->account_name,
                    $line->debit,
                    $line->credit,
                ];
            }
        }

        // Generate CSV
        $filename = 'journal_entries_' . now()->format('Y-m-d_His') . '.csv';
        
        $handle = fopen('php://temp', 'r+');
        foreach ($csvData as $row) {
            fputcsv($handle, $row);
        }
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Get journal summary by account (for reports)
     */
    public function summaryByAccount(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->startOfMonth()->toDateString());
        $dateTo = $request->get('date_to', now()->endOfMonth()->toDateString());

        $summary = JournalLine::join('journal_entries', 'journal_entries.id', '=', 'journal_lines.journal_entry_id')
            ->whereBetween('journal_entries.journal_date', [$dateFrom, $dateTo])
            ->selectRaw('
                journal_lines.account_code,
                journal_lines.account_name,
                SUM(journal_lines.debit) as total_debit,
                SUM(journal_lines.credit) as total_credit,
                (SUM(journal_lines.debit) - SUM(journal_lines.credit)) as balance
            ')
            ->groupBy('journal_lines.account_code', 'journal_lines.account_name')
            ->orderBy('journal_lines.account_code')
            ->get();

        return view('journal-entries.summary', compact('summary', 'dateFrom', 'dateTo'));
    }

    /**
     * API endpoint for getting journal entries (AJAX)
     */
    public function api(Request $request)
    {
        $query = JournalEntry::query();

        if ($request->filled('source_type')) {
            $query->where('source_type', $request->source_type);
        }

        if ($request->filled('source_id')) {
            $query->where('source_id', $request->source_id);
        }

        $entries = $query->with('lines')
            ->orderBy('journal_date', 'desc')
            ->limit(50)
            ->get();

        return response()->json($entries);
    }

    
}