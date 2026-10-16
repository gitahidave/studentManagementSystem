@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="container-fluid p-0">
        <h1 class="h2 fw-bold mb-3">Dashboard</h1>

        <div class="alert alert-primary border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-info-circle-fill me-2"></i> Welcome to the Student Management System.
        </div>

        <div class="row g-3">
            <div class="col-12 col-sm-6 col-lg-4">
                <x-summary-card
                    title="Total Students"
                    :value="$totalStudents"
                    icon="people-fill"
                    color="text-primary"
                />
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <x-summary-card
                    title="Total Courses"
                    :value="$totalCourses"
                    icon="book-fill"
                    color="text-info"
                />
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <x-summary-card
                    title="Fee Records"
                    :value="$totalFees"
                    icon="cash-stack"
                    color="text-success"
                />
            </div>
        </div>
    </div>
@endsection