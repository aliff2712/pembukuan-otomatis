@extends('layouts-main.app')
@section('title', __('Income / Pendapatan Lain'))
@section('page-title', __('Pendapatan Lain'))
@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-plus-circle text-success me-2"></i>Income / Pendapatan Lain
            </h1>
            <span class="first-letter:">Kelola semua pendapatan lain yang tidak terkait dengan penjualan produk atau jasa utama.</span>
        </div>
        <a href="{{ route('other-incomes.create') }}" class="btn btn-success">
            <i class="fas fa-plus me-2"></i>Tambah Income
        </a>
    </div>

    <!-- Alerts -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Error!</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Table -->
    <div class="card shadow-sm">
        <div class="table-responsive">
            @if ($incomes->count() > 0)
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 12%">Tanggal</th>
                            <th style="width: 35%">Deskripsi</th>
                            <th style="width: 18%">Jumlah</th>
                            <th style="width: 15%">Status</th>
                            <th style="width: 12%">Dibuat Oleh</th>
                            <th style="width: 8%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($incomes as $income)
                            <tr>
                                <td class="fw-semibold">
                                    {{ $income->income_date->format('d M Y') }}
                                </td>
                                <td>
                                    {{ $income->description }}
                                    @if ($income->notes)
                                        <br>
                                        <small class="text-muted">{{ $income->notes }}</small>
                                    @endif
                                </td>
                                <td class="text-end fw-semibold">
                                    Rp{{ number_format($income->amount, 0, ',', '.') }}
                                </td>
                                <td>
                                    @if ($income->isPosted())
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle me-1"></i>Posted
                                        </span>
                                    @else
                                        <span class="badge bg-warning text-dark">
                                            <i class="fas fa-pencil-alt me-1"></i>Recorded
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ $income->createdBy->name ?? 'Unknown' }}</small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('other-incomes.show', $income) }}" 
                                           class="btn btn-outline-primary" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if (!$income->isPosted())
                                            <a href="{{ route('other-incomes.edit', $income) }}" 
                                               class="btn btn-outline-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-danger" 
                                                    onclick="confirmDelete('{{ route('other-incomes.destroy', $income) }}')"
                                                    title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="d-flex justify-content-center p-3">
                    {{ $incomes->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox" style="font-size: 3rem;"></i>
                    <p class="mt-3">Belum ada data income</p>
                    <a href="{{ route('other-incomes.create') }}" class="btn btn-success">
                        <i class="fas fa-plus me-2"></i>Buat Income Pertama
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
function confirmDelete(deleteUrl) {
    if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
        document.getElementById('deleteForm').action = deleteUrl;
        document.getElementById('deleteForm').submit();
    }
}
</script>
@endsection
