@extends('layouts-main.app')

@section('title', 'Voucher Sale Detail')
@section('page-title', 'Voucher Sale Detail')

@section('content')
<!-- Back Button -->
<div class="mb-3">
    <a href="{{ route('voucher-sales.index') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left"></i> Back to List
    </a>
</div>

<!-- Main Info Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-ticket-alt"></i> Voucher Sale Information
        </h6>
        <div>
            <span class="badge bg-secondary">{{ $sale->source }}</span>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <td width="180"><strong>ID:</strong></td>
                        <td>{{ $sale->id }}</td>
                    </tr>
                    <tr>
                        <td><strong>Sale Date:</strong></td>
                        <td>
                            <i class="far fa-calendar-alt"></i>
                            {{ \Carbon\Carbon::parse($sale->sale_date)->format('l, d F Y') }}
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Source:</strong></td>
                        <td>
                            <span class="badge bg-info">{{ $sale->source }}</span>
                        </td>
                    </tr>
                   
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <td width="180"><strong>Total Transactions:</strong></td>
                        <td>
                            <span class="badge bg-success" style="font-size: 1rem;">
                                {{ number_format($sale->total_transactions) }} transactions
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Total Amount:</strong></td>
                        <td>
                            <h4 class="text-primary mb-0">
                                Rp {{ number_format($sale->total_amount, 0, ',', '.') }}
                            </h4>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Average per Transaction:</strong></td>
                        <td>
                            Rp {{ number_format($sale->total_transactions > 0 ? $sale->total_amount / $sale->total_transactions : 0, 0, ',', '.') }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <hr>

        <!-- Timestamps -->
        <div class="row">
            <div class="col-md-6">
                <small class="">
                    <i class="far fa-clock"></i> Created: 
                    {{ \Carbon\Carbon::parse($sale->created_at)->format('d M Y H:i:s') }}
                </small>
            </div>
            <div class="col-md-6">
                <small class="">
                    <i class="far fa-clock"></i> Last Updated: 
                    {{ \Carbon\Carbon::parse($sale->updated_at)->format('d M Y H:i:s') }}
                    ({{ \Carbon\Carbon::parse($sale->updated_at)->diffForHumans() }})
                </small>
            </div>
        </div>
    </div>
</div>

<!-- Related Journal Entry -->
@if($journalEntry)
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-book"></i> Related Journal Entry
        </h6>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <p class="mb-1"><strong>Journal Date:</strong> {{ $journalEntry->journal_date }}</p>
                <p class="mb-1"><strong>Description:</strong> {{ $journalEntry->description }}</p>
            </div>
            <div class="col-md-6 text-end">
                <p class="mb-1"><strong>Reference No:</strong> {{ $journalEntry->reference_no ?? '-' }}</p>
                <a href="{{ route('journal-entries.show', $journalEntry->id) }}" 
                    class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-eye"></i> View Journal Entry
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-sm table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Account Code</th>
                        <th>Account Name</th>
                        <th class="text-end">Debit</th>
                        <th class="text-end">Credit</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $lines = \DB::table('journal_lines')
                            ->leftJoin('chart_of_accounts as coa', 'coa.id', '=', 'journal_lines.coa_id')
                            ->where('journal_entry_id', $journalEntry->id)
                            ->select('journal_lines.*', 'coa.account_code', 'coa.account_name')
                            ->get();
                    @endphp
                    @foreach($lines as $line)
                        <tr>
                            <td>{{ $line->account_code ?? $line->coa_id }}</td>
                            <td>{{ $line->account_name ?? '-' }}</td>
                            <td class="text-end">
                                {{ $line->debit > 0 ? 'Rp ' . number_format($line->debit, 0, ',', '.') : '-' }}
                            </td>
                            <td class="text-end">
                                {{ $line->credit > 0 ? 'Rp ' . number_format($line->credit, 0, ',', '.') : '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th colspan="2" class="text-end">Total:</th>
                        <th class="text-end">Rp {{ number_format($journalEntry->total_debit, 0, ',', '.') }}</th>
                        <th class="text-end">Rp {{ number_format($journalEntry->total_credit, 0, ',', '.') }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@else
<div class="card shadow mb-4">
    <div class="card-body text-center ">
        <i class="fas fa-info-circle fa-3x mb-3"></i>
        <p>No journal entry found for this voucher sale.</p>
        <small>Journal entries are created automatically during import process.</small>
    </div>
</div>
@endif

<!-- Actions -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-cog"></i> Actions
        </h6>
    </div>
    <div class="card-body">
        <button type="button" class="btn btn-danger" onclick="voidSale({{ $sale->id }}, '{{ $sale->sale_date }}')">
            <i class="fas fa-trash"></i> Void This Sale
        </button>
        <a href="{{ route('voucher-sales.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>
</div>
@endsection

@push('scripts')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function voidSale(id, date) {
    Swal.fire({
        title: 'Void Voucher Sale?',
        html: `Are you sure you want to void voucher sale for date: <strong>${date}</strong>?<br><br>This action cannot be undone and will delete the associated journal entry.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e74a3b',
        cancelButtonColor: '#858796',
        confirmButtonText: 'Yes, void it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/voucher-sales/${id}`;
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            
            form.appendChild(csrfToken);
            form.appendChild(methodField);
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endpush