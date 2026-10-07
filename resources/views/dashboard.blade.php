@extends('layouts.app')

@section('content')
    <div class="container-fluid p-0">
        <!-- Dashboard Heading[cite: 1, 3] -->
        <h2 class="fw-bold mb-3">Dashboard</h2>

        <!-- Welcome Message Alert[cite: 1, 3] -->
        <div class="alert alert-primary border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-info-circle-fill me-2"></i> Welcome to the Student Management System.
        </div>

        <!-- Summary Cards Grid[cite: 1, 3, 4] -->
        <div class="row g-3">
            <div class="col-12 col-sm-6 col-lg-3">
                <x-summary-card 
                    title="Total Students" 
                    :value="$totalStudents" 
                    icon="people-fill" 
                    color="text-primary" 
                />
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <x-summary-card 
                    title="Total Courses" 
                    :value="$totalCourses" 
                    icon="book-fill" 
                    color="text-info" 
                />
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <x-summary-card 
                    title="Fees Collected" 
                    :value="$feesCollected" 
                    icon="cash-coin" 
                    color="text-success" 
                />
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <x-summary-card 
                    title="Outstanding Fees" 
                    :value="$outstandingFees" 
                    icon="credit-card-fill" 
                    color="text-danger" 
                />
            </div>
        </div>
    </div>
@endsection