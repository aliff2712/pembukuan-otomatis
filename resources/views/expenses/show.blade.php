<h2>Transaksi Pengeluaran</h2>

<ul>
    <li>Tanggal: {{ $expense->expense_date }}</li>
    <li>Akun Beban: {{ $expense->expenseAccount->name }}</li>
    <li>Kas/Bank: {{ $expense->cashAccount->name }}</li>
    <li>Nominal: {{ number_format($expense->amount, 2) }}</li>
    <li>Keterangan: {{ $expense->description }}</li>
</ul>
<h3>Journal Entry</h3>

<ul>
    <li>Tanggal Journal: {{ $journal->journal_date }}</li>
    <li>Keterangan: {{ $journal->description }}</li>
    <li>Source: Expense #{{ $expense->id }}</li>
</ul>
<h3>Journal Lines</h3>

<table border="1" cellpadding="6">
    <thead>
        <tr>
            <th>Akun</th>
            <th>Debit</th>
            <th>Kredit</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($journal->lines as $line)
        <tr>
            <td>{{ $line->coa->name }}</td>
            <td>{{ number_format($line->debit, 2) }}</td>
            <td>{{ number_format($line->credit, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<h3>Ledger</h3>

<ul>
    <li>
        <a href="{{ route('ledger.show', $expense->expense_coa_id) }}">
            Lihat Ledger {{ $expense->expenseAccount->name }}
        </a>
    </li>
    <li>
        <a href="{{ route('ledger.show', $expense->cash_coa_id) }}">
            Lihat Ledger {{ $expense->cashAccount->name }}
        </a>
    </li>
</ul>
