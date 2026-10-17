@extends('layouts.app')

@section('title', 'Student information')

@section('content')
    <div class="container-fluid p-0">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 fw-bold mb-1">Student information</h1>
                <p class="text-secondary mb-0">{{ $student->first_name }} {{ $student->second_name }}</p>
            </div>
            <div class="d-flex gap-2">
                <a class="btn btn-outline-secondary" href="{{ route('students.index') }}">Back to students</a>
                <a class="btn btn-primary" href="{{ route('students.edit', $student) }}">
                    <i class="bi bi-pencil me-1" aria-hidden="true"></i>Edit
                </a>
            </div>
        </div>

        @if (session('status'))
            <div class="alert alert-success" role="status">{{ session('status') }}</div>
        @endif

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-3">First name</dt>
                    <dd class="col-sm-9">{{ $student->first_name }}</dd>
                    <dt class="col-sm-3">Second name</dt>
                    <dd class="col-sm-9">{{ $student->second_name }}</dd>
                    <dt class="col-sm-3">Student No.</dt>
                    <dd class="col-sm-9">{{ $student->student_number }}</dd>
                    <dt class="col-sm-3">Gender</dt>
                    <dd class="col-sm-9">{{ $student->gender ?? '-' }}</dd>
                    <dt class="col-sm-3">Phone number</dt>
                    <dd class="col-sm-9">{{ $student->phone_number ?? '-' }}</dd>
                    <dt class="col-sm-3">Email</dt>
                    <dd class="col-sm-9">{{ $student->email ?? '-' }}</dd>
                    <dt class="col-sm-3">Course</dt>
                    <dd class="col-sm-9">{{ $student->course->name }}</dd>
                    <dt class="col-sm-3">Status</dt>
                    <dd class="col-sm-9">{{ $student->status }}</dd>
                </dl>
            </div>
        </div>

        <section aria-labelledby="student-fees-title">
            <h2 id="student-fees-title" class="h4 fw-bold mb-3">Fee records</h2>
            <div class="card border-0 shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Fee type</th>
                                <th scope="col">Amount</th>
                                <th scope="col">Status</th>
                                <th scope="col">Recorded</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($student->fees as $fee)
                                <tr>
                                    <td>{{ $fee->fee_type }}</td>
                                    <td>{{ number_format((float) $fee->amount, 2) }}</td>
                                    <td>{{ $fee->status }}</td>
                                    <td>{{ $fee->created_at->format('M j, Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-secondary py-4">
                                        No fee records for this student.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
@endsection
