@extends('layouts.app')

@section('title', 'Add course')

@section('content')
    <div class="container-fluid p-0">
        <h1 class="h2 fw-bold mb-3">Add course</h1>
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                @include('courses.form', ['course' => null, 'submitLabel' => 'Add course'])
            </div>
        </div>
    </div>
@endsection
