@extends('layouts.app')

@section('title', 'Edit Expense')
@section('page-title', 'Edit Expense')

@section('content')
<!-- Back Button -->
<div class="mb-3">
    <a href="{{ route('expenses.show', $expense->id) }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left"></i> Back to Detail
    </a>
</div>

<!-- Form Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-edit"></i> Edit Expense Information
        </h6>
    </div>
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i>
                <strong>Validation Error!</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form method="POST" action="{{ route('expenses.update', $expense->id) }}">
            @csrf
            @method('PUT')

            <div class="row">
                <!-- Expense Date -->
                <div class="col-md-6 mb-3">
                    <label for="expense_date" class="form-label">
                        <i class="far fa-calendar"></i> Expense Date <span class="text-danger">*</span>
                    </label>
                    <input type="date" 
                        class="form-control @error('expense_date') is-invalid @enderror" 
                        id="expense_date" 
                        name="expense_date" 
                        value="{{ old('expense_date', $expense->expense_date) }}"
                        required>
                    @error('expense_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Amount -->
                <div class="col-md-6 mb-3">
                    <label for="amount" class="form-label">
                        <i class="fas fa-money-bill-wave"></i> Amount <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" 
                            class="form-control @error('amount') is-invalid @enderror" 
                            id="amount" 
                            name="amount" 
                            value="{{ old('amount', $expense->amount) }}"
                            placeholder="0"
                            min="0"
                            step="1"
                            required>
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-text">
                        <span id="amountText" class="text-muted"></span>
                    </div>
                </div>

                <!-- Expense Account -->
                <div class="col-md-6 mb-3">
                    <label for="expense_coa_id" class="form-label">
                        <i class="fas fa-tags"></i> Expense Account (Beban) <span class="text-danger">*</span>
                    </label>
                    <select class="form-control @error('expense_coa_id') is-invalid @enderror" 
                        id="expense_coa_id" 
                        name="expense_coa_id" 
                        required>
                        <option value="">-- Select Expense Account --</option>
                        @foreach($expenseAccounts as $account)
                            <option value="{{ $account->id }}" 
                                {{ old('expense_coa_id', $expense->expense_coa_id) == $account->id ? 'selected' : '' }}>
                                {{ $account->account_code }} - {{ $account->account_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('expense_coa_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">
                        Akun kategori pengeluaran (5xxx - Expense)
                    </div>
                </div>

                <!-- Cash/Bank Account -->
                <div class="col-md-6 mb-3">
                    <label for="cash_coa_id" class="form-label">
                        <i class="fas fa-university"></i> Paid From (Kas/Bank) <span class="text-danger">*</span>
                    </label>
                    <select class="form-control @error('cash_coa_id') is-invalid @enderror" 
                        id="cash_coa_id" 
                        name="cash_coa_id" 
                        required>
                        <option value="">-- Select Cash/Bank Account --</option>
                        @foreach($cashAccounts as $account)
                            <option value="{{ $account->id }}" 
                                {{ old('cash_coa_id', $expense->cash_coa_id) == $account->id ? 'selected' : '' }}>
                                {{ $account->account_code }} - {{ $account->account_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('cash_coa_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">
                        Akun sumber pembayaran (kas atau bank)
                    </div>
                </div>

                <!-- Description -->
                <div class="col-12 mb-3">
                    <label for="description" class="form-label">
                        <i class="fas fa-align-left"></i> Description <span class="text-danger">*</span>
                    </label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                        id="description" 
                        name="description" 
                        rows="4" 
                        placeholder="Enter expense description"
                        required>{{ old('description', $expense->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">
                        Maximum 1000 characters
                    </div>
                </div>
            </div>

            <hr>

            <!-- Warning Box -->
            <div class="alert alert-warning" role="alert">
                <h6 class="alert-heading">
                    <i class="fas fa-exclamation-triangle"></i> Important Notice
                </h6>
                <p class="mb-0 small">
                    When you update this expense, the related journal entry will also be updated automatically. 
                    This will affect your financial reports and account balances.
                </p>
            </div>

            <!-- Submit Buttons -->
            <div class="d-flex justify-content-between">
                <a href="{{ route('expenses.show', $expense->id) }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Expense
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Format amount as currency
document.getElementById('amount').addEventListener('input', function(e) {
    const value = parseFloat(e.target.value) || 0;
    const formatted = new Intl.NumberFormat('id-ID').format(value);
    document.getElementById('amountText').textContent = 'Rp ' + formatted;
});

// Trigger on page load
window.addEventListener('load', function() {
    const amount = parseFloat(document.getElementById('amount').value) || 0;
    const formatted = new Intl.NumberFormat('id-ID').format(amount);
    document.getElementById('amountText').textContent = 'Rp ' + formatted;
});
</script>
@endpush