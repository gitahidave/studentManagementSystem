<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(): View
    {
        $students = Student::with('course')->latest()->get();

        return view('students.index', compact('students'));
    }

    public function create(): View
    {
        $courses = Course::orderBy('name')->get();

        return view('students.create', compact('courses'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'second_name' => ['required', 'string', 'max:255'],
            'course_id' => ['required', 'integer', 'exists:courses,id'],
        ]);

        Student::create($validated);

        return redirect()
            ->route('students.index')
            ->with('status', 'Student registered successfully.');
    }
}
