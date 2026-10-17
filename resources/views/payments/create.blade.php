@extends('layouts.app')

@section('title', 'Record payment')

@section('content')
    <div class="container-fluid p-0">
        <h1 class="h2 fw-bold mb-3">Record payment</h1>
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                @if ($fees->isEmpty())
                    <div class="alert alert-info mb-0" role="status">
                        There are no outstanding fees to pay.
                        <a href="{{ route('fees.create') }}" class="alert-link">Add a fee</a>
                    </div>
                @else
                    <form method="POST" action="{{ route('payments.store') }}">
                        @csrf
                        @if ($errors->any())
                            <div class="alert alert-danger" role="alert">Please correct the errors below.</div>
                        @endif

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="fee_id" class="form-label">Outstanding fee</label>
                                <select id="fee_id" name="fee_id" required
                                        class="form-select @error('fee_id') is-invalid @enderror">
                                    <option value="">Choose a fee</option>
                                    @foreach ($fees as $fee)
                                        @php($balance = (float) $fee->amount - (float) $fee->completed_payments_total)
                                        <option value="{{ $fee->id }}" @selected(old('fee_id') == $fee->id)>
                                            {{ $fee->student->first_name }} {{ $fee->student->second_name }}
                                            — {{ $fee->fee_type }} (Balance: {{ number_format($balance, 2) }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('fee_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="amount" class="form-label">Amount</label>
                                <input id="amount" name="amount" type="number" min="0.01" step="0.01"
                                       value="{{ old('amount') }}" required
                                       class="form-control @error('amount') is-invalid @enderror">
                                @error('amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="method" class="form-label">Payment method</label>
                                <select id="method" name="method" required
                                        class="form-select @error('method') is-invalid @enderror">
                                    @foreach (['Cash', 'Bank', 'Cheque', 'M-Pesa'] as $method)
                                        <option value="{{ $method }}" @selected(old('method', 'Cash') === $method)>
                                            {{ $method }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('method') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="transaction_reference" class="form-label">Transaction reference</label>
                                <input id="transaction_reference" name="transaction_reference"
                                       value="{{ old('transaction_reference') }}" maxlength="255"
                                       class="form-control @error('transaction_reference') is-invalid @enderror">
                                <div class="form-text">Required for bank, cheque, and M-Pesa payments.</div>
                                @error('transaction_reference') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="phone_number" class="form-label">Payer phone number</label>
                                <input id="phone_number" name="phone_number" type="tel"
                                       value="{{ old('phone_number') }}" maxlength="30"
                                       class="form-control @error('phone_number') is-invalid @enderror">
                                <div class="form-text">Required for M-Pesa; retained for future provider integration.</div>
                                @error('phone_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mt-4 d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Save payment</button>
                            <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
@endsection
