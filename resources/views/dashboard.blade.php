@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="container-fluid p-0">
        <h1 class="h2 fw-bold mb-3">Dashboard</h1>

        <div class="alert alert-primary border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-info-circle-fill me-2"></i> Welcome to the Student Management System.
        </div>

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
                    :value="number_format($totalAmountPaid, 2)"
                    icon="cash-stack"
                    color="text-success"
                />
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <x-summary-card
                    title="Pending Fees"
                    :value="number_format($totalAmountPending, 2)"
                    icon="credit-card"
                    color="text-danger"
                />
            </div>
        </div>

        <div class="row g-3 mt-1">
            <div class="col-12 col-lg-5">
                <section class="card border-0 shadow-sm h-100" aria-labelledby="fee-status-chart-title">
                    <div class="card-body">
                        <h2 id="fee-status-chart-title" class="h5 fw-bold">Fee status</h2>
                        <p class="text-secondary small">Number of fee records by payment status.</p>
                        <div class="d-flex flex-wrap align-items-center gap-4">
                            <div class="rounded-circle flex-shrink-0"
                                 role="img"
                                 aria-label="Fee status pie chart for {{ $totalFeeStatusRecords }} records"
                                 style="width: 180px; height: 180px; background: {{ $feeStatusPieBackground }}"></div>
                            <div class="d-flex flex-column gap-2">
                                @forelse ($feeStatusStats as $statusStat)
                                    <div class="d-flex align-items-center gap-2 small">
                                        <span class="rounded-circle flex-shrink-0"
                                              aria-hidden="true"
                                              style="width: 12px; height: 12px; background-color: {{ $statusStat['color'] }}"></span>
                                        <span>{{ $statusStat['status'] }}: {{ $statusStat['count'] }} records</span>
                                    </div>
                                @empty
                                    <p class="text-secondary mb-0">No fee records to chart yet.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <div class="col-12 col-lg-7">
                <section class="card border-0 shadow-sm h-100" aria-labelledby="monthly-fees-chart-title">
                    <div class="card-body">
                        <h2 id="monthly-fees-chart-title" class="h5 fw-bold">Fees recorded by month</h2>
                        <p class="text-secondary small">Paid fee amounts for the last six months.</p>
                        <div class="d-flex align-items-end justify-content-between gap-2"
                             role="img" aria-label="Monthly fee amounts for the last six months"
                             style="height: 220px">
                            @foreach ($monthlyFeeStats as $monthStat)
                                <div class="d-flex flex-column align-items-center justify-content-end h-100 flex-fill"
                                     title="{{ $monthStat['label'] }}: {{ number_format($monthStat['amount'], 2) }}">
                                    <span class="small text-secondary text-truncate w-100 text-center">
                                        {{ number_format($monthStat['amount'], 0) }}
                                    </span>
                                    <div class="bg-primary rounded-top w-75 mt-1"
                                         style="height: {{ $monthStat['amount'] > 0 ? max(4, ($monthStat['amount'] / $maxMonthlyFeeAmount) * 150) : 2 }}px"></div>
                                    <span class="small text-secondary mt-2 text-center">{{ $monthStat['label'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection