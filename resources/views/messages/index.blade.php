@extends('layouts-main.app')

@section('title', 'Messages')
@section('page-title', 'Pesan Admin')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Kontak Admin</h6>
    </div>
    <div class="card-body">
        <div class="list-group">
            @forelse($users as $user)
                <a href="{{ route('messages.show', $user->id) }}" 
                   class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="icon-circle bg-primary mr-3">
                            <i class="fas fa-user text-white"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">{{ $user->name }}</h6>
                            <small class="text-muted">{{ $user->email }}</small>
                        </div>
                    </div>
                    @if($user->unread_count > 0)
                        <span class="badge badge-danger badge-pill">{{ $user->unread_count }}</span>
                    @endif
                </a>
            @empty
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-inbox fa-3x mb-3"></i>
                    <p>Tidak ada admin lain</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection