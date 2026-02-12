@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6">
    <h1 class="text-xl font-bold mb-4">Balance Sheet (Neraca) — Per {{ $date }}</h1>

    <form method="get" class="mb-4 flex gap-2">
        <input type="date" name="date" value="{{ $date }}" class="border p-2" />
        <button class="bg-blue-600 text-white px-3 py-2">Tampilkan</button>
    </form>

    @php
        $groups = collect($rows)->groupBy('account_type');
    @endphp

    <h2 class="mt-4 font-semibold">Assets</h2>
    <table class="w-full border-collapse border mb-4">
        <tbody>
            @foreach($groups->get('asset', []) as $r)
            <tr>
                <td class="border p-2">{{ $r->account_code }} - {{ $r->account_name }}</td>
                <td class="border p-2 text-right">{{ number_format($r->balance) }}</td>
            </tr>
            @endforeach
            <tr class="font-semibold bg-gray-100">
                <td class="border p-2">Total Assets</td>
                <td class="border p-2 text-right">{{ number_format($totals['asset'] ?? 0) }}</td>
            </tr>
        </tbody>
    </table>

    <h2 class="mt-4 font-semibold">Liabilities</h2>
    <table class="w-full border-collapse border mb-4">
        <tbody>
            @foreach($groups->get('liability', []) as $r)
            <tr>
                <td class="border p-2">{{ $r->account_code }} - {{ $r->account_name }}</td>
                <td class="border p-2 text-right">{{ number_format($r->balance) }}</td>
            </tr>
            @endforeach
            <tr class="font-semibold bg-gray-100">
                <td class="border p-2">Total Liabilities</td>
                <td class="border p-2 text-right">{{ number_format($totals['liability'] ?? 0) }}</td>
            </tr>
        </tbody>
    </table>

    <h2 class="mt-4 font-semibold">Equity</h2>
    <table class="w-full border-collapse border mb-4">
        <tbody>
            @foreach($groups->get('equity', []) as $r)
            <tr>
                <td class="border p-2">{{ $r->account_code }} - {{ $r->account_name }}</td>
                <td class="border p-2 text-right">{{ number_format($r->balance) }}</td>
            </tr>
            @endforeach
            <tr class="font-semibold bg-gray-100">
                <td class="border p-2">Total Equity</td>
                <td class="border p-2 text-right">{{ number_format($totals['equity'] ?? 0) }}</td>
            </tr>
        </tbody>
    </table>
</div>
@endsection
