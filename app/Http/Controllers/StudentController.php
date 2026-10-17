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

    public function show(Student $student): View
    {
        $student->load(['course', 'fees' => fn ($query) => $query->latest()]);

        return view('students.show', compact('student'));
    }

    public function edit(Student $student): View
    {
        $courses = Course::orderBy('name')->get();

        return view('students.edit', compact('student', 'courses'));
    }

    public function store(Request $request): RedirectResponse
    {
        Student::create($this->validateStudent($request));

        return redirect()
            ->route('students.index')
            ->with('status', 'Student registered successfully.');
    }

    public function update(Request $request, Student $student): RedirectResponse
    {
        $student->update($this->validateStudent($request));

        return redirect()
            ->route('students.show', $student)
            ->with('status', 'Student information updated successfully.');
    }

    public function destroy(Student $student): RedirectResponse
    {
        if ($student->fees()->exists()) {
            return redirect()
                ->route('students.index')
                ->with('error', 'This student has fee records and cannot be deleted.');
        }

        $student->delete();

        return redirect()
            ->route('students.index')
            ->with('status', 'Student deleted successfully.');
    }

    private function validateStudent(Request $request): array
    {
        return $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'second_name' => ['required', 'string', 'max:255'],
            'course_id' => ['required', 'integer', 'exists:courses,id'],
        ]);
    }
}
