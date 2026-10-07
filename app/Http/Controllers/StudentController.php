<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Course;
use App\Models\Semester;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(): View
    {
        $students = Student::with([
            'enrollments.course',
            'enrollments.semester',
            'enrollments.academicYear',
        ])->latest()->get();

        return view('students.index', compact('students'));
    }

    public function create(): View
    {
        return view('students.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'firstname' => ['required', 'string', 'max:255'],
            'secondname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:students,email'],
            'phoneno' => ['required', 'string', 'max:50'],
            'course' => ['required', 'string', 'max:255'],
            'semester' => ['required', 'string', 'max:100'],
            'academic_year' => ['required', 'string', 'max:20'],
        ]);

        DB::transaction(function () use ($validated): void {
            $course = Course::firstOrCreate(['name' => $validated['course']]);
            $semester = Semester::firstOrCreate(['name' => $validated['semester']]);
            $academicYear = AcademicYear::firstOrCreate(['name' => $validated['academic_year']]);

            $student = Student::create([
                'firstname' => $validated['firstname'],
                'secondname' => $validated['secondname'],
                'email' => $validated['email'],
                'phoneno' => $validated['phoneno'],
                // Retain the existing legacy column while enrollment data lives in the relational tables.
                'course' => $course->name,
            ]);

            $student->enrollments()->create([
                'course_id' => $course->id,
                'semester_id' => $semester->id,
                'academic_year_id' => $academicYear->id,
            ]);
        });

        return redirect()
            ->route('students.index')
            ->with('status', 'Student and enrollment registered successfully.');
    }
}
