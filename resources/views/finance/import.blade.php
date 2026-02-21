@extends('layouts-main.app')

@section('title', 'Import Transaksi')

@section('page-title', 'Import Data Transaksi DHS Dipanet Hotspot Solution')

@section('content')

<div class="container-fluid">

    <!-- Page Heading -->
    <div class="mb-4">
        <h1 class="h3 text-gray-800 font-weight-bold">
            Import Transaksi
        </h1>
        <p class="text-muted">
            Upload data transaksi dari file Excel (xlsx) untuk mempercepat proses input data.
        </p>
    </div>

    <!-- Success Alert -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Berhasil!</strong> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    <!-- Error Alert -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Terjadi Kesalahan:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Card -->
    <div class="card shadow-lg mb-4">

        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Upload File Excel
            </h6>
        </div>

        <div class="card-body">

            <form action="{{ route('finance.transaksi.import') }}"
                  method="POST"
                  enctype="multipart/form-data">

                @csrf

                <!-- File Input -->
                <div class="form-group">
                    <label><strong>Pilih File Excel</strong></label>

                    <div class="custom-file">
                        <input type="file"
                               name="file"
                               class="custom-file-input"
                               id="fileInput"
                               accept=".xlsx,.xls,.csv"
                               required>

                        <label class="custom-file-label" for="fileInput">
                            Pilih file...
                        </label>
                    </div>

                    <small class="form-text text-muted">
                        Format yang didukung: .xlsx, .xls, .csv
                    </small>
                </div>

                <!-- Action Buttons -->
                <div class="mt-4">

                    <button type="submit"
                            class="btn btn-success">
                        <i class="fas fa-file-import"></i>
                        Import Transaksi
                    </button>

                    <a href="{{ route('finance.transaksi.index') }}"
                       class="btn btn-secondary">
                        Batal
                    </a>

                </div>

            </form>

        </div>

    </div>

</div>

{{-- Script untuk menampilkan nama file --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('fileInput');

    fileInput.addEventListener('change', function () {
        let fileName = this.files[0].name;
        this.nextElementSibling.innerText = fileName;
    });
});
</script>

@endsection
