@extends('layouts.app')

@section('title', 'Add fee')

@section('content')
    <div class="container-fluid p-0">
        <h1 class="h2 fw-bold mb-3">Add fee</h1>
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                @include('fees.form', ['fee' => null, 'submitLabel' => 'Save fee'])
            </div>
        </div>
    </div>
@endsection