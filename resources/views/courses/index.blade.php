@extends('layouts.app')

@section('title', 'Courses')

@section('content')
    <div class="container-fluid p-0">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 fw-bold mb-1">Courses</h1>
                <p class="text-secondary mb-0">Manage courses offered by the institution.</p>
            </div>
            <a href="{{ route('courses.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1" aria-hidden="true"></i>
                Add Course
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
                                <th scope="col">Course Name</th>
                                <th scope="col" class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($courses as $course)
                                <tr>
                                    <td>{{ $course->id }}</td>
                                    <td>{{ $course->name }}</td>
                                    <td class="text-end text-nowrap">
                                        <a href="{{ route('courses.edit', $course) }}"
                                           class="btn btn-sm btn-outline-primary"
                                           aria-label="Edit {{ $course->name }}">
                                            <i class="bi bi-pencil" aria-hidden="true"></i>
                                        </a>
                                        <form action="{{ route('courses.destroy', $course) }}"
                                              method="POST"
                                              class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-danger"
                                                    aria-label="Delete {{ $course->name }}"
                                                    onclick="return confirm('Are you sure you want to delete this course?')">
                                                <i class="bi bi-trash" aria-hidden="true"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-secondary">
                                        No courses found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $courses->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
