@extends('layouts-main.app')

@section('title', 'Account Detail')
@section('page-title', 'Account Detail')

@section('content')

<div class="container-fluid">

<div class="card shadow">
    <div class="card-header">
        <h5>Account Detail</h5>
    </div>

    <div class="card-body">

        <table class="table">
            <tr>
                <th>Account Code</th>
                <td>{{ $account->account_code }}</td>
            </tr>

            <tr>
                <th>Account Name</th>
                <td>{{ $account->account_name }}</td>
            </tr>

            <tr>
                <th>Type</th>
                <td>{{ ucfirst($account->account_type) }}</td>
            </tr>

            <tr>
                <th>Cash Account</th>
                <td>
                    @if($account->is_cash)
                        Yes
                    @else
                        No
                    @endif
                </td>
            </tr>

        </table>

        <a href="{{ route('chart-of-accounts.index') }}" class="btn btn-secondary">
            Back
        </a>

    </div>
</div>

</div>

@endsection