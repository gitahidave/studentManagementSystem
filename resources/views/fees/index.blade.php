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
        @if (session('error'))
            <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
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
                                <th scope="col">Reference</th>
                                <th scope="col">Amount</th>
                                <th scope="col">Status</th>
                                <th scope="col">Recorded</th>
                                <th scope="col" class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($fees as $fee)
                                <tr>
                                    <td>{{ $fee->id }}</td>
                                    <td>{{ $fee->student->first_name }} {{ $fee->student->second_name }}</td>
                                    <td>{{ $fee->fee_type }}</td>
                                    <td>{{ $fee->reference ?? '—' }}</td>
                                    <td>{{ number_format((float) $fee->amount, 2) }}</td>
                                    <td>
                                        <span class="badge {{ $fee->status === 'Paid' ? 'text-bg-success' : ($fee->status === 'Pending' ? 'text-bg-warning' : 'text-bg-info') }}">
                                            {{ $fee->status }}
                                        </span>
                                    </td>
                                    <td>{{ $fee->created_at->format('M j, Y') }}</td>
                                    <td class="text-end text-nowrap">
                                        <a href="{{ route('fees.edit', $fee) }}"
                                           class="btn btn-sm btn-outline-primary"
                                           aria-label="Edit fee {{ $fee->id }}">
                                            <i class="bi bi-pencil" aria-hidden="true"></i>
                                        </a>
                                        <form action="{{ route('fees.destroy', $fee) }}"
                                              method="POST"
                                              class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-danger"
                                                    aria-label="Delete fee {{ $fee->id }}"
                                                    onclick="return confirm('Are you sure you want to delete this fee?')">
                                                <i class="bi bi-trash" aria-hidden="true"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-secondary">
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