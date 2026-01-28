<h2>Catat Pengeluaran</h2>
@if ($errors->any())
    <div style="color:red">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if(session('success'))
    <p style="color: green">{{ session('success') }}</p>
@endif

<form method="POST" action="{{ route('expenses.store') }}">
    @csrf

    <div>
        <label>Tanggal</label><br>
        <input type="date" name="expense_date" required>
    </div>

    <div>
        <label>Jenis Pengeluaran</label><br>
        <select name="expense_coa_id" required>
            <option value="">-- Pilih Beban --</option>
            @foreach ($expenseAccounts as $account)
                <option value="{{ $account->id }}">
{{ $account->account_code }} - {{ $account->account_name }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label>Ambil dari Kas / Bank</label><br>
        <select name="cash_coa_id" required>
            <option value="">-- Pilih Kas / Bank --</option>
            @foreach ($cashAccounts as $account)
                <option value="{{ $account->id }}">
                    {{ $account->account_code }} - {{ $account->account_name }}
                </option>
            @endforeach
        </select>
    </div>


    <div>
        <label>Nominal</label><br>
        <input type="number" name="amount" step="0.01" required>
    </div>

    <div>
        <label>Keterangan</label><br>
        <textarea name="description"></textarea>
    </div>

    <br>
    <button type="submit">Simpan Pengeluaran</button>
</form>
