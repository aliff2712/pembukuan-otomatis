@extends('layouts-main.app')

@section('title', 'Add Chart of Account')
@section('page-title', 'Add Chart of Account')

@section('content')
<div class="container-fluid">

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> <strong>Error!</strong>
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('Add Account') }}</h6>
            <a href="{{ route('chart-of-accounts.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('chart-of-accounts.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="account_code" class="form-label">{{ __('Account Code') }}</label>
                    <input type="text" name="account_code" id="account_code" 
                           class="form-control @error('account_code') is-invalid @enderror" 
                           value="{{ old('account_code') }}" maxlength="20" required>
                    @error('account_code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="account_name" class="form-label">{{ __('Account Name') }}</label>
                    <input type="text" name="account_name" id="account_name" 
                           class="form-control @error('account_name') is-invalid @enderror" 
                           value="{{ old('account_name') }}" maxlength="255" required>
                    @error('account_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="account_type" class="form-label">{{ __('Account Type') }}</label>
                    <select name="account_type" id="account_type" 
                            class="form-select @error('account_type') is-invalid @enderror" required>
                        <option value="">-- Select Type --</option>
                        @foreach($accountTypes as $key => $label)
                            <option value="{{ $key }}" @selected(old('account_type') == $key)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('account_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" value="1" id="is_cash" name="is_cash" 
                           @checked(old('is_cash'))>
                    <label class="form-check-label" for="is_cash">{{ __('Is Cash / Bank Account') }}</label>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> {{ __('Save') }}
                    </button>
                    <a href="{{ route('chart-of-accounts.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
