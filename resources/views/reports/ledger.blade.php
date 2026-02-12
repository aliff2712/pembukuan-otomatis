@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto p-6">
    <h1 class="text-xl font-bold mb-4">Ledger - Bulanan ({{ $month }} / {{ $year }})</h1>

    <table class="w-full border-collapse border">
        <thead>
            <tr class="bg-gray-100">
                <th class="border p-2 text-left">Kode Akun</th>
                <th class="border p-2 text-left">Nama Akun</th>
                <th class="border p-2 text-right">Debit</th>
                <th class="border p-2 text-right">Kredit</th>
                <th class="border p-2 text-right">Saldo</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $r)
            <tr>
                <td class="border p-2">{{ $r->account_code }}</td>
                <td class="border p-2">{{ $r->account_name }}</td>
                <td class="border p-2 text-right">{{ number_format($r->debit) }}</td>
                <td class="border p-2 text-right">{{ number_format($r->credit) }}</td>
                <td class="border p-2 text-right">{{ number_format($r->balance) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
