@extends('layouts-main.app')
@section('title', __('Income / Pendapatan Lain'))
@section('page-title', __('Pendapatan Lain'))

@section('content')

<style>

/* ===============================
   PAGE HEADER
=================================*/

.page-header{
    background: linear-gradient(135deg,#9a3412,#c2410c);
    color:white;
    padding:22px;
    border-radius:14px;
    margin-bottom:20px;
}

.page-header h1{
    font-size:20px;
    font-weight:600;
}

.page-header p{
    opacity:.9;
    font-size:13px;
    margin-bottom:0;
}

/* ===============================
   CARD
=================================*/

.income-card{
    border:none;
    border-radius:14px;
    overflow:hidden;
}

/* ===============================
   TABLE
=================================*/

.table-modern thead{
    background:#f8fafc;
}

.table-modern thead th{
    font-size:11px;
    text-transform:uppercase;
    letter-spacing:.5px;
    border:none;
}

.table-modern tbody tr{
    transition:all .15s ease;
}

.table-modern tbody tr:hover{
    background:#f9fafb;
}

.table-modern td{
    vertical-align:middle;
    font-size:14px;
}

/* ===============================
   AMOUNT
=================================*/

.amount{
    font-weight:600;
    color:#c2410c;
}

/* ===============================
   BADGES
=================================*/

.badge-posted{
    background:#dcfce7;
    color:#166534;
}

.badge-recorded{
    background:#fff7ed;
    color:#9a3412;
}

/* ===============================
   BUTTONS
=================================*/

.btn-modern{
    border-radius:8px;
}

/* ===============================
   EMPTY STATE
=================================*/

.empty-state{
    padding:60px 20px;
    text-align:center;
}

.empty-state i{
    font-size:50px;
    color:#9ca3af;
}

.empty-state h5{
    margin-top:16px;
    font-weight:600;
}

/* ===============================
   MOBILE
=================================*/

@media (max-width:768px){

.page-header{
    flex-direction:column;
    align-items:flex-start !important;
    gap:12px;
}

.page-header h1{
    font-size:18px;
}

.page-header p{
    font-size:12px;
}

.table-modern td{
    font-size:13px;
}

.table-modern thead{
    font-size:10px;
}

.btn-group{
    flex-direction:column;
    gap:4px;
}

}

</style>


<div class="container-fluid py-4">

<!-- HEADER -->
<div class="page-header d-flex justify-content-between align-items-center">

<div>

<h1>
<i class="fas fa-coins me-2"></i>
Income / Pendapatan Lain
</h1>

<p>
Kelola semua pendapatan lain yang tidak berasal dari penjualan utama.
</p>

</div>

<div class="d-flex gap-2">

<a href="{{ route('dashboard') }}" class="btn btn-light btn-modern">
<i class="fas fa-arrow-left me-2"></i>
Back
</a>

<a href="{{ route('other-incomes.create') }}" class="btn btn-light btn-modern">
<i class="fas fa-plus me-2"></i>
Tambah Income
</a>

</div>

</div>


@if ($errors->any())
<div class="alert alert-danger">
<strong>Error!</strong>
<ul class="mb-0">
@foreach ($errors->all() as $error)
<li>{{ $error }}</li>
@endforeach
</ul>
</div>
@endif


@if (session('success'))
<div class="alert alert-success">
{{ session('success') }}
</div>
@endif


<div class="card income-card shadow-sm">

<div class="table-responsive">

@if ($incomes->count() > 0)

<table class="table table-modern mb-0">

<thead>
<tr>
<th width="12%">Tanggal</th>
<th width="35%">Deskripsi</th>
<th width="18%">Jumlah</th>
<th width="15%">Status</th>
<th width="12%">Dibuat Oleh</th>
<th width="8%">Aksi</th>
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

<small class="text-muted">
{{ $income->notes }}
</small>

@endif

</td>

<td class="text-end amount">
Rp {{ number_format($income->amount,0,',','.') }}
</td>

<td>

@if ($income->isPosted())

<span class="badge badge-posted">
<i class="fas fa-check-circle me-1"></i>
Posted
</span>

@else

<span class="badge badge-recorded">
<i class="fas fa-pencil-alt me-1"></i>
Recorded
</span>

@endif

</td>

<td>
<small>{{ $income->createdBy->name ?? 'Unknown' }}</small>
</td>

<td>

<div class="btn-group btn-group-sm">

<a href="{{ route('other-incomes.show',$income) }}"
class="btn btn-outline-primary"
title="Detail">

<i class="fas fa-eye"></i>

</a>

@if (!$income->isPosted())

<a href="{{ route('other-incomes.edit',$income) }}"
class="btn btn-outline-warning"
title="Edit">

<i class="fas fa-edit"></i>

</a>

<button type="button"
class="btn btn-outline-danger"
onclick="confirmDelete('{{ route('other-incomes.destroy',$income) }}')">

<i class="fas fa-trash"></i>

</button>

@endif

</div>

</td>

</tr>

@endforeach

</tbody>

</table>

<div class="p-3">
{{ $incomes->links() }}
</div>

@else

<div class="empty-state">

<i class="fas fa-inbox"></i>

<h5>Belum Ada Data Income</h5>

<p class="text-muted">
Tambahkan pendapatan lain untuk mulai mencatat transaksi.
</p>

</div>

@endif

</div>

</div>



</div>


<form id="deleteForm" method="POST" style="display:none">
@csrf
@method('DELETE')
</form>

<script>

function confirmDelete(url){

if(confirm('Apakah Anda yakin ingin menghapus data ini?')){

document.getElementById('deleteForm').action = url
document.getElementById('deleteForm').submit()

}

}

</script>

@endsection