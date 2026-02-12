@extends('layouts-main.app')
@section('title', __('Chart of Accounts'))
@section('page-title', __('Chart of Accounts'))
@section('content')
<div class="container-fluid">

    <!-- Success Alert -->
    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Error Alert -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> <strong>Error!</strong>
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="h3 mb-0 text-gray-800">{{ __('Chart of Accounts') }}</h1>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('chart-of-accounts.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> {{ __('Add Account') }}
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <!-- Total Accounts -->
        <div class="col-xl-2 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                {{ __('Total Accounts') }}
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['total'], 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Asset Accounts -->
        <div class="col-xl-2 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                {{ __('Asset') }} 
                            </div>
                            <div class="h3 mb-0 font-weight-bold text-gray-800">
                              {{ $stats['asset_count'] }}
                            </div>
                            
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-coins fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Revenue Accounts -->
        <div class="col-xl-2 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                {{ __('Revenue') }} 
                            </div>
                            <div class="h3 mb-0 font-weight-bold text-gray-800">
                              {{ $stats['revenue_count'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-up fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Expense Accounts -->
        <div class="col-xl-2 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                {{ __('Expense') }} 
                            </div>
                            <div class="h3 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['expense_count'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-down fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Liability Accounts -->
        <div class="col-xl-2 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                {{ __('Liability') }} 
                            </div>
                            <div class="h3 mb-0 font-weight-bold text-gray-800">
                              {{ $stats['liability_count'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-invoice-dollar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Equity Accounts -->
        <div class="col-xl-2 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                {{ __('Equity') }} 
                            </div>
                            <div class="h3 mb-0 font-weight-bold text-gray-800">
                              {{ $stats['equity_count'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-piggy-bank fa-2x text-gray-300"></i>
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
            <form action="{{ route('chart-of-accounts.index') }}" method="GET">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="search">{{ __('Search Code/Name') }}</label>
                            <input type="text" name="search" id="search" class="form-control" 
                                   placeholder="{{ __('Enter code or name') }}" value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="account_type">{{ __('Account Type') }}</label>
                            <select name="account_type" id="account_type" class="form-select">
                                <option value="">{{ __('All Types') }}</option>
                                <option value="asset" @selected(request('account_type') == 'asset')>{{ __('Asset') }}</option>
                                <option value="revenue" @selected(request('account_type') == 'revenue')>{{ __('Revenue') }}</option>
                                <option value="expense" @selected(request('account_type') == 'expense')>{{ __('Expense') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="is_cash">{{ __('Cash Account') }}</label>
                            <select name="is_cash" id="is_cash" class="form-select">
                                <option value="">{{ __('All') }}</option>
                                <option value="1" @selected(request('is_cash') == '1')>{{ __('Yes') }}</option>
                                <option value="0" @selected(request('is_cash') == '0')>{{ __('No') }}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> {{ __('Search') }}
                        </button>
                        <a href="{{ route('chart-of-accounts.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> {{ __('Reset') }}
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Accounts Table -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('Chart of Accounts') }}</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('Code') }}</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Type') }}</th>
                            <th class="text-center">{{ __('Cash') }}</th>
                            <th class="text-center">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($accounts as $account)
                            <tr>
                                <td>
                                    <strong>{{ $account->account_code }}</strong>
                                </td>
                                <td>{{ $account->account_name }}</td>
                                <td>
                                    @php
                                        $typeColors = [
                                            'asset' => 'info',
                                            'liability' => 'warning',
                                            'equity' => 'success',
                                            'revenue' => 'success',
                                            'expense' => 'danger',
                                        ];
                                        $typeLabels = [
                                            'asset' => 'Asset (Aset)',
                                            'liability' => 'Liability (Kewajiban)',
                                            'equity' => 'Equity (Modal)',
                                            'revenue' => 'Revenue (Pendapatan)',
                                            'expense' => 'Expense (Beban)',
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $typeColors[$account->account_type] ?? 'secondary' }}">
                                        {{ $typeLabels[$account->account_type] ?? $account->account_type }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($account->is_cash)
                                        <span class="badge bg-success">{{ __('Yes') }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ __('No') }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('chart-of-accounts.show', $account->id) }}" 
                                           class="btn btn-info" title="{{ __('View') }}">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('chart-of-accounts.edit', $account->id) }}" 
                                           class="btn btn-warning" title="{{ __('Edit') }}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('chart-of-accounts.destroy', $account->id) }}" 
                                              method="POST" style="display:inline;" 
                                              onsubmit="return confirm('{{ __('Are you sure?') }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="{{ __('Delete') }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-gray-300 mb-3 d-block"></i>
                                    {{ __('No accounts found') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <nav aria-label="Page navigation">
                {{ $accounts->links() }}
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
    .border-left-warning {
        border-left: 4px solid #f6c23e;
    }
    .border-left-success {
        border-left: 4px solid #1cc88a;
    }
    .border-left-danger {
        border-left: 4px solid #e74a3b;
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