@extends('layouts.app')

@section('title', 'Students')

@section('content')
    <div class="container-fluid p-0">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 fw-bold mb-1">Students</h1>
                <p class="text-secondary mb-0">Students and their course enrollments.</p>
            </div>
            <a class="btn btn-primary" href="{{ route('students.create') }}">
                <i class="bi bi-person-plus me-2" aria-hidden="true"></i>Register student
            </a>
        </div>

        @if (session('status'))
            <div class="alert alert-success" role="status">{{ session('status') }}</div>
        @endif

        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Student</th>
                            <th scope="col">Email</th>
                            <th scope="col">Phone</th>
                            <th scope="col">Course</th>
                            <th scope="col">Semester</th>
                            <th scope="col">Academic year</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($students as $student)
                            @forelse ($student->enrollments as $enrollment)
                                <tr>
                                    <td>{{ $student->firstname }} {{ $student->secondname }}</td>
                                    <td>{{ $student->email }}</td>
                                    <td>{{ $student->phoneno }}</td>
                                    <td>{{ $enrollment->course->name }}</td>
                                    <td>{{ $enrollment->semester->name }}</td>
                                    <td>{{ $enrollment->academicYear->name }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td>{{ $student->firstname }} {{ $student->secondname }}</td>
                                    <td>{{ $student->email }}</td>
                                    <td>{{ $student->phoneno }}</td>
                                    <td>{{ $student->course }}</td>
                                    <td colspan="2" class="text-secondary">No enrollment details</td>
                                </tr>
                            @endforelse
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-secondary py-4">
                                    No students registered yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
