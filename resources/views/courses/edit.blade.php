@extends('layouts.app')

@section('title', 'Edit course')

@section('content')
    <div class="container-fluid p-0">
        <h1 class="h2 fw-bold mb-3">Edit course</h1>
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                @include('courses.form', ['course' => $course, 'submitLabel' => 'Save changes'])
            </div>
        </div>
    </div>
@endsection
