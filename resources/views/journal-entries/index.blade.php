@extends('layouts-main.app')
@section('title', __('Journal Entries'))
@section('page-title', __('Journal Entries'))
@section('content')
<div class="container-fluid">

    <!-- Success Alert -->
    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="h3 mb-0 text-gray-800">{{ __('Journal Entries') }}</h1>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('journal-entries.export') }}" class="btn btn-secondary">
                <i class="fas fa-download"></i> {{ __('Export') }}
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <!-- Total Entries -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                {{ __('Total Entries') }}
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['total_entries'] ?? 0) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- This Month -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                {{ __('This Month') }}
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['this_month'] ?? 0) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Debit -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                {{ __('Total Debit') }}
                            </div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($stats['total_debit'] ?? 0, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-up fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Credit -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                {{ __('Total Credit') }}
                            </div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($stats['total_credit'] ?? 0, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-down fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('Filter & Search') }}</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('journal-entries.index') }}" method="GET">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="search">{{ __('Search Description/Reference') }}</label>
                            <input type="text" name="search" id="search" class="form-control" 
                                   placeholder="{{ __('Enter description or reference') }}" value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="source_type">{{ __('Source Type') }}</label>
                            <select name="source_type" id="source_type" class="form-select">
                                <option value="">{{ __('All Sources') }}</option>
                                @foreach($sourceTypes as $type)
                                    <option value="{{ $type }}" @selected(request('source_type') == $type)>{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="date_from">{{ __('Date From') }}</label>
                            <input type="date" name="date_from" id="date_from" class="form-control" 
                                   value="{{ request('date_from') }}">
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="date_to">{{ __('Date To') }}</label>
                            <input type="date" name="date_to" id="date_to" class="form-control" 
                                   value="{{ request('date_to') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="month">{{ __('Month') }}</label>
                            <select name="month" id="month" class="form-select">
                                <option value="">{{ __('All') }}</option>
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" @selected(request('month') == $m)>
                                        {{ \Carbon\Carbon::createFromFormat('n', $m)->format('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="year">{{ __('Year') }}</label>
                            <select name="year" id="year" class="form-select">
                                <option value="">{{ __('All') }}</option>
                                @for($y = now()->year; $y >= 2020; $y--)
                                    <option value="{{ $y }}" @selected(request('year') == $y)>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search"></i> {{ __('Search') }}
                            </button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <a href="{{ route('journal-entries.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> {{ __('Reset') }}
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Journal Entries Table -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('Journal Entries') }}</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Description') }}</th>
                            <th>{{ __('Reference') }}</th>
                            <th>{{ __('Source') }}</th>
                            <th class="text-center">{{ __('Debit') }}</th>
                            <th class="text-center">{{ __('Credit') }}</th>
                            <th class="text-center">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($entries as $entry)
                            <tr>
                                <td><strong>{{ $entry->journal_date->format('Y-m-d') }}</strong></td>
                                <td>{{ $entry->description }}</td>
                                <td>{{ $entry->reference_no ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $entry->source_type }}</span>
                                </td>
                                <td class="text-end">
                                    Rp {{ number_format($entry->total_debit ?? 0, 0, ',', '.') }}
                                </td>
                                <td class="text-end">
                                    Rp {{ number_format($entry->total_credit ?? 0, 0, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('journal-entries.show', $entry->id) }}" 
                                       class="btn btn-sm btn-info" title="{{ __('View Details') }}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-gray-300 mb-3 d-block"></i>
                                    {{ __('No journal entries found') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Info -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted small">
                    {{ __('Showing') }} <strong>{{ $entries->firstItem() ?? 0 }}</strong> {{ __('to') }} <strong>{{ $entries->lastItem() ?? 0 }}</strong> {{ __('of') }} <strong>{{ $entries->total() }}</strong> {{ __('entries') }}
                </div>
            </div>

            <!-- Pagination -->
            <nav class="mt-4">
                {{ $entries->links('pagination::bootstrap-4') }}
            </nav>
        </div>
    </div>

</div>

<style>
    .border-left-primary {
        border-left: 4px solid #4e73df;
    }
    .border-left-info {
        border-left: 4px solid #36b9cc;
    }
    .border-left-success {
        border-left: 4px solid #1cc88a;
    }
    .border-left-warning {
        border-left: 4px solid #f6c23e;
    }
    .text-gray-800 {
        color: #2e59d9;
    }
    .text-gray-300 {
        color: #e3e6f0;
    }
    .font-weight-bold {
        font-weight: 700;
    }
    .text-xs {
        font-size: 0.8rem;
    }
    .text-uppercase {
        text-transform: uppercase;
    }
</style>
@endsection