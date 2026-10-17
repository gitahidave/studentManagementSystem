@extends('layouts.app')

@section('title', 'Payments')

@section('content')
    <div class="payments-page">
        <div class="page-header">
            <div>
                <h1 class="h2 fw-bold mb-1">Payments</h1>
                <p class="text-secondary mb-0">Manage student fee payments and transactions.</p>
            </div>

            <a href="{{ route('payments.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2" aria-hidden="true"></i>
                Record Payment
            </a>
        </div>

        @if (session('status'))
            <div class="alert alert-success" role="status">{{ session('status') }}</div>
        @endif

        <div class="summary-grid">
            <div class="summary-card">
                <div class="summary-label">Total Collected</div>
                <div class="summary-value">KSh {{ number_format((float) $totalCollected, 2) }}</div>
            </div>

            <div class="summary-card active">
                <div class="summary-label">This Month</div>
                <div class="summary-value">KSh {{ number_format((float) $thisMonthCollected, 2) }}</div>
            </div>

            <div class="summary-card pending">
                <div class="summary-label">Pending M-Pesa</div>
                <div class="summary-value">KSh {{ number_format((float) $pendingMpesaTotal, 2) }}</div>
            </div>
        </div>

        <form class="payment-toolbar" method="GET" action="{{ route('payments.index') }}">
            <div class="toolbar-field search-field">
                <input type="search" name="search" value="{{ $search }}" placeholder="Student name or student number" aria-label="Search payments">
            </div>
            <div class="toolbar-field">
                <select name="status" aria-label="Filter by status">
                    <option value="">All Statuses</option>
                    @foreach (['Completed', 'Pending', 'Failed'] as $paymentStatus)
                        <option value="{{ $paymentStatus }}" @selected($status === $paymentStatus)>{{ $paymentStatus }}</option>
                    @endforeach
                </select>
            </div>
            <div class="toolbar-field">
                <select name="method" aria-label="Filter by method">
                    <option value="">All Methods</option>
                    @foreach (['M-Pesa', 'Cash', 'Bank', 'Cheque'] as $paymentMethod)
                        <option value="{{ $paymentMethod }}" @selected($method === $paymentMethod)>{{ $paymentMethod }}</option>
                    @endforeach
                </select>
            </div>
            <button class="search-action" type="submit" aria-label="Search">
                <i class="bi bi-search"></i>
            </button>
        </form>

        <section class="transactions-panel">
            <div class="panel-heading">
                <h2>Payment Transactions</h2>
                <p>Recent student fee payments</p>
            </div>

            <div class="table-responsive payment-table-wrap">
                <table class="payments-table">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Receipt</th>
                            <th>Fee Structure</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Reference</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($payments as $payment)
                            @php($student = $payment->fee->student)
                            @php($studentName = trim(($student->first_name ?? '') . ' ' . ($student->second_name ?? '')))
                            <tr>
                                <td>
                                    <div class="student-name">{{ $studentName ?: 'Unknown Student' }}</div>
                                    <div class="student-number">{{ $student->student_number ?? $student->id }}</div>
                                </td>
                                <td>RCP{{ str_pad((string) $payment->id, 6, '0', STR_PAD_LEFT) }}</td>
                                <td>
                                    {{ $payment->fee->fee_type }}
                                </td>
                                <td>
                                    <span class="amount-cell">KSh {{ number_format((float) $payment->amount, 2) }}</span>
                                </td>
                                <td>
                                    <span class="method-badge {{ strtolower(str_replace(['-', ' '], '', $payment->method)) }}">
                                        {{ $payment->method }}
                                    </span>
                                </td>
                                <td>{{ $payment->transaction_reference ?? $payment->provider_transaction_id ?? '—' }}</td>
                                <td>{{ $payment->paid_at?->format('d M Y') ?? '—' }}</td>
                                <td>
                                    <span class="status-badge {{ strtolower($payment->status) }}">
                                        {{ $payment->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="empty-state">No payments recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($payments->hasPages())
                <div class="pagination-wrap">
                    {{ $payments->links() }}
                </div>
            @endif
        </section>
    </div>
@endsection
