@extends('layouts.app')

@section('title', $title)

@section('content')
    <div class="container-fluid p-0">
        <h1 class="h2 fw-bold mb-3">{{ $title }}</h1>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <p class="mb-3">{{ $description }}</p>
                @isset($actionUrl)
                    <a class="btn btn-outline-primary me-2" href="{{ $actionUrl }}">{{ $actionLabel }}</a>
                @endisset
                <a class="btn btn-primary" href="{{ route('dashboard') }}">
                    <i class="bi bi-speedometer2 me-2" aria-hidden="true"></i>Back to dashboard
                </a>
            </div>
        </div>
    </div>
@endsection
