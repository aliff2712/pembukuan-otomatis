<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\JournalEntry;
use Illuminate\Http\Request;
use App\Models\ChartOfAccount;
use App\Services\ExpenseService;
use Illuminate\Routing\Controller;

class ExpenseController extends Controller
{
    /**
     * FORM INPUT EXPENSE
     */
    public function create()
    {
        return view('expenses.create', [
            'expenseAccounts' => ChartOfAccount::where('account_type', 'expense')->get(),
            'cashAccounts' => ChartOfAccount::where('account_type', 'asset')
                ->where('is_cash', true)
                ->get(),
        ]);
    }


    /**
     * SIMPAN EXPENSE → JOURNAL → LEDGER
     */
    public function store(Request $request, ExpenseService $expenseService)
    {
        $data = $request->validate([
            'expense_date' => ['required', 'date'],
            'expense_coa_id' => ['required', 'exists:chart_of_accounts,id'],
            'cash_coa_id' => ['required', 'exists:chart_of_accounts,id'],
            'amount' => ['required', 'numeric', 'min:1'],
            'description' => ['nullable', 'string'],
        ]);

        $expense = $expenseService->record($data);

        return redirect()
            ->route('expenses.show', $expense->id)
            ->with('success', 'Pengeluaran berhasil dicatat');
    }

    /**
     * TRACE EXPENSE (YANG KEMARIN)
     */
    public function show(Expense $expense)
    {
        $journal = JournalEntry::where('source_type', 'expense')
            ->where('source_id', $expense->id)
            ->with(['lines.coa'])
            ->first();

        if (!$journal) {
            abort(500, 'Journal for this expense not found');
        }

        return view('expenses.show', compact('expense', 'journal'));
    }
}
