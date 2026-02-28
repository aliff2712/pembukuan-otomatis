@extends('layouts-main.app')

@section('title', 'Edit Account')
@section('page-title', 'Edit Chart of Account')

@section('content')
<!-- Back Button -->
<div class="mb-3">
    <a href="{{ route('chart-of-accounts.show', $account->id) }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left"></i> Back to Detail
    </a>
</div>

<!-- Form Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-edit"></i> Edit Account Information
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

        <form method="POST" action="{{ route('chart-of-accounts.update', $account->id) }}">
            @csrf
            @method('PUT')

            <div class="row">
                <!-- Account Code -->
                <div class="col-md-6 mb-3">
                    <label for="account_code" class="form-label">
                        <i class="fas fa-hashtag"></i> Account Code <span class="text-danger">*</span>
                    </label>
                    <input type="text"
                        class="form-control @error('account_code') is-invalid @enderror"
                        id="account_code"
                        name="account_code"
                        value="{{ old('account_code', $account->account_code) }}"
                        placeholder="e.g. 1-1000"
                        maxlength="20"
                        {{ $hasTransactions ? 'readonly' : '' }}
                        required>
                    @error('account_code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    @if($hasTransactions)
                        <div class="form-text text-warning">
                            <i class="fas fa-lock"></i> Kode akun tidak dapat diubah karena sudah memiliki transaksi.
                        </div>
                    @else
                        <div class="form-text">
                            Maksimal 20 karakter. Contoh: 1-1000, 2-1000, dsb.
                        </div>
                    @endif
                </div>

                <!-- Account Name -->
                <div class="col-md-6 mb-3">
                    <label for="account_name" class="form-label">
                        <i class="fas fa-signature"></i> Account Name <span class="text-danger">*</span>
                    </label>
                    <input type="text"
                        class="form-control @error('account_name') is-invalid @enderror"
                        id="account_name"
                        name="account_name"
                        value="{{ old('account_name', $account->account_name) }}"
                        placeholder="e.g. Kas, Bank BCA, Hutang Dagang"
                        maxlength="255"
                        required>
                    @error('account_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">
                        Nama akun yang deskriptif dan mudah dipahami.
                    </div>
                </div>

                <!-- Account Type -->
                <div class="col-md-6 mb-3">
                    <label for="account_type" class="form-label">
                        <i class="fas fa-layer-group"></i> Account Type <span class="text-danger">*</span>
                    </label>
                    <select class="form-control @error('account_type') is-invalid @enderror"
                        id="account_type"
                        name="account_type"
                        {{ $hasTransactions ? 'disabled' : '' }}
                        required>
                        <option value="">-- Select Account Type --</option>
                        @foreach($accountTypes as $value => $label)
                            <option value="{{ $value }}"
                                {{ old('account_type', $account->account_type) == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    {{-- Re-submit value if disabled --}}
                    @if($hasTransactions)
                        <input type="hidden" name="account_type" value="{{ $account->account_type }}">
                    @endif
                    @error('account_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text" id="accountTypeHint">
                        Tipe akun menentukan posisi normal debit/kredit.
                    </div>
                </div>

                <!-- Is Cash -->
                <div class="col-md-6 mb-3">
                    <label class="form-label">
                        <i class="fas fa-coins"></i> Cash / Bank Account
                    </label>
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input"
                            type="checkbox"
                            id="is_cash"
                            name="is_cash"
                            value="1"
                            {{ old('is_cash', $account->is_cash) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_cash">
                            Tandai sebagai akun kas / bank
                        </label>
                    </div>
                    @error('is_cash')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                    <div class="form-text">
                        Aktifkan jika akun ini digunakan sebagai sumber pembayaran (kas atau bank).
                    </div>
                </div>
            </div>

            <hr>

            <!-- Account Type Info Box -->
            <div id="accountTypeInfo" class="alert alert-info d-none" role="alert">
                <h6 class="alert-heading mb-1">
                    <i class="fas fa-info-circle"></i> <span id="accountTypeTitle"></span>
                </h6>
                <p class="mb-0 small" id="accountTypeDesc"></p>
            </div>

            <!-- Warning Box -->
            @if($hasTransactions)
                <div class="alert alert-warning" role="alert">
                    <h6 class="alert-heading">
                        <i class="fas fa-exclamation-triangle"></i> Restricted Fields
                    </h6>
                    <p class="mb-0 small">
                        Akun ini sudah memiliki transaksi, sehingga <strong>Kode Akun</strong> dan <strong>Tipe Akun</strong>
                        tidak dapat diubah untuk menjaga integritas data keuangan.
                    </p>
                </div>
            @else
                <div class="alert alert-warning" role="alert">
                    <h6 class="alert-heading">
                        <i class="fas fa-exclamation-triangle"></i> Important Notice
                    </h6>
                    <p class="mb-0 small">
                        Perubahan pada kode atau tipe akun dapat mempengaruhi laporan keuangan dan saldo akun.
                        Pastikan perubahan sudah sesuai sebelum menyimpan.
                    </p>
                </div>
            @endif

            <!-- Submit Buttons -->
            <div class="d-flex justify-content-between">
                <a href="{{ route('chart-of-accounts.show', $account->id) }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Account
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
const accountTypeInfo = {
    asset: {
        title: 'Asset (Aset)',
        desc: 'Akun aset memiliki saldo normal di sisi Debit. Contoh: Kas, Piutang, Persediaan, Peralatan.'
    },
    liability: {
        title: 'Liability (Kewajiban)',
        desc: 'Akun kewajiban memiliki saldo normal di sisi Kredit. Contoh: Hutang Dagang, Hutang Bank, Hutang Gaji.'
    },
    equity: {
        title: 'Equity (Modal)',
        desc: 'Akun modal memiliki saldo normal di sisi Kredit. Contoh: Modal Pemilik, Laba Ditahan.'
    },
    revenue: {
        title: 'Revenue (Pendapatan)',
        desc: 'Akun pendapatan memiliki saldo normal di sisi Kredit. Contoh: Pendapatan Jasa, Penjualan.'
    },
    expense: {
        title: 'Expense (Beban)',
        desc: 'Akun beban memiliki saldo normal di sisi Debit. Contoh: Beban Gaji, Beban Sewa, Beban Listrik.'
    }
};

function updateAccountTypeHint(value) {
    const infoBox = document.getElementById('accountTypeInfo');
    const titleEl = document.getElementById('accountTypeTitle');
    const descEl = document.getElementById('accountTypeDesc');

    if (value && accountTypeInfo[value]) {
        titleEl.textContent = accountTypeInfo[value].title;
        descEl.textContent = accountTypeInfo[value].desc;
        infoBox.classList.remove('d-none');
    } else {
        infoBox.classList.add('d-none');
    }
}

const accountTypeSelect = document.getElementById('account_type');
if (accountTypeSelect) {
    accountTypeSelect.addEventListener('change', function () {
        updateAccountTypeHint(this.value);
    });

    // Trigger on page load
    updateAccountTypeHint(accountTypeSelect.value);
}

// Toggle is_cash visibility based on account type
const isCashRow = document.getElementById('is_cash')?.closest('.col-md-6');
if (accountTypeSelect && isCashRow) {
    function toggleCashVisibility(type) {
        if (type === 'asset') {
            isCashRow.style.opacity = '1';
            isCashRow.style.pointerEvents = 'auto';
        } else {
            isCashRow.style.opacity = '0.5';
            isCashRow.style.pointerEvents = 'none';
            document.getElementById('is_cash').checked = false;
        }
    }

    accountTypeSelect.addEventListener('change', function () {
        toggleCashVisibility(this.value);
    });

    toggleCashVisibility(accountTypeSelect.value);
}
</script>
@endpush