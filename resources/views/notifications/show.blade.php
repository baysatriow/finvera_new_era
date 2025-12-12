@extends('layouts.dashboard')

@section('page_title', 'Detail Pesan')

@section('content')
<style>
    .detail-icon-box {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    .btn-back-outline {
        border: 1px solid #dee2e6;
        background: white;
        color: #6c757d;
        font-weight: 600;
        transition: all 0.2s;
    }
    .btn-back-outline:hover {
        background: #f8f9fa;
        color: #3A6D48;
        border-color: #3A6D48;
    }
</style>

<div class="row justify-content-center">
    <div class="col-lg-8">

        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-5">

                <div class="d-flex align-items-center mb-4 pb-4 border-bottom">
                    <div class="detail-icon-box text-white me-4 bg-{{ $notification->data['type'] ?? 'primary' }}">
                        <i class="fas fa-{{ $notification->data['icon'] ?? 'info' }}"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold text-dark mb-1">{{ $notification->data['title'] }}</h4>
                        <div class="text-muted small d-flex align-items-center gap-2">
                            <i class="far fa-clock"></i>
                            {{ $notification->created_at->locale('id')->isoFormat('dddd, D MMMM Y • HH:mm') }}
                        </div>
                    </div>
                </div>

                <div class="mb-5">
                    <p class="text-dark lh-lg" style="font-size: 1rem; white-space: pre-line;">
                        {{ $notification->data['message'] }}
                    </p>
                </div>

                <div class="d-flex flex-column flex-md-row gap-3">

                    <a href="{{ route('notifications.index') }}" class="btn btn-back-outline rounded-pill px-4 py-2 order-2 order-md-1">
                        <i class="fas fa-list-ul me-2"></i> Daftar Notifikasi
                    </a>

                    @php
                        $previousUrl = url()->previous();
                        $isFromList = str_contains($previousUrl, route('notifications.index'));
                        $hasTargetUrl = isset($notification->data['url']) && $notification->data['url'] !== '#';
                    @endphp

                    <div class="ms-md-auto order-1 order-md-2 d-flex gap-2">
                        @if(!$isFromList && $previousUrl !== url()->current())
                            <a href="{{ $previousUrl }}" class="btn btn-light border fw-bold rounded-pill px-4 py-2">
                                <i class="fas fa-arrow-left me-2"></i> Kembali
                            </a>
                        @endif

                        {{-- @if($hasTargetUrl)
                            <a href="{{ $notification->data['url'] }}" class="btn btn-success fw-bold rounded-pill px-4 py-2 shadow-sm">
                                Buka Tautan <i class="fas fa-external-link-alt ms-2"></i>
                            </a>
                        @endif --}}
                    </div>

                </div>

            </div>
        </div>

    </div>
</div>
@endsection
