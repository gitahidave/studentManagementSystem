@extends('layouts.app')

@section('title', 'Fees')

@section('content')
    <div class="container-fluid p-0">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 fw-bold mb-1">Fees</h1>
                <p class="text-secondary mb-0">Record and review student fee charges.</p>
            </div>
            <a href="{{ route('fees.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1" aria-hidden="true"></i>
                Add Fee
            </a>
        </div>

        @if (session('status'))
            <div class="alert alert-success" role="status">{{ session('status') }}</div>
        @endif

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Student</th>
                                <th scope="col">Fee type</th>
                                <th scope="col">Amount</th>
                                <th scope="col">Status</th>
                                <th scope="col">Recorded</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($fees as $fee)
                                <tr>
                                    <td>{{ $fee->id }}</td>
                                    <td>{{ $fee->student->first_name }} {{ $fee->student->second_name }}</td>
                                    <td>{{ $fee->fee_type }}</td>
                                    <td>{{ number_format((float) $fee->amount, 2) }}</td>
                                    <td>
                                        <span class="badge {{ $fee->status === 'Paid' ? 'text-bg-success' : ($fee->status === 'Pending' ? 'text-bg-warning' : 'text-bg-info') }}">
                                            {{ $fee->status }}
                                        </span>
                                    </td>
                                    <td>{{ $fee->created_at->format('M j, Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-secondary">
                                        No fee records found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $fees->links() }}
            </div>
        </div>
    </div>
@endsection