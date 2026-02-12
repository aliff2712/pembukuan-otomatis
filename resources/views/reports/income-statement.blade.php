@extends('layouts-main.app')

@section('content')
<div class="max-w-4xl mx-auto p-6">
    <h1 class="text-xl font-bold mb-4">Income Statement (Laporan Laba Rugi)</h1>

    <form method="get" class="mb-4 flex gap-2">
        <input type="date" name="from" value="{{ $from ?? '' }}" class="border p-2" />
        <input type="date" name="to" value="{{ $to ?? '' }}" class="border p-2" />
        <button class="bg-blue-600 text-white px-3 py-2">Filter</button>
    </form>

    <table class="w-full border-collapse border">
        <thead>
            <tr class="bg-gray-100">
                <th class="border p-2 text-left">Tipe</th>
                <th class="border p-2 text-left">Kode</th>
                <th class="border p-2 text-left">Nama Akun</th>
                <th class="border p-2 text-right">Saldo</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $row)
            <tr>
                <td class="border p-2">{{ ucfirst($row->account_type) }}</td>
                <td class="border p-2">{{ $row->account_code }}</td>
                <td class="border p-2">{{ $row->account_name }}</td>
                <td class="border p-2 text-right">{{ number_format($row->balance) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="font-semibold bg-gray-50">
                <td class="border p-2">Total Revenue</td>
                <td class="border p-2" colspan="2"></td>
                <td class="border p-2 text-right">{{ number_format($totals['revenue'] ?? 0) }}</td>
            </tr>
            <tr class="font-semibold bg-gray-50">
                <td class="border p-2">Total Expense</td>
                <td class="border p-2" colspan="2"></td>
                <td class="border p-2 text-right">{{ number_format($totals['expense'] ?? 0) }}</td>
            </tr>
            <tr class="font-bold bg-gray-200">
                <td class="border p-2">Net Profit / (Loss)</td>
                <td class="border p-2" colspan="2"></td>
                <td class="border p-2 text-right">{{ number_format($net) }}</td>
            </tr>
        </tfoot>
    </table>
</div>
@endsection
