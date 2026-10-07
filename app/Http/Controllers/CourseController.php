<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(): View
    {
        $courses = Course::latest()->paginate(10);

        return view('courses.index', compact('courses'));
    }

    public function create(): View
    {
        return view('courses.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateCourse($request);

        Course::create([
            'name' => $validated['course_name'],
            'course_code' => $validated['course_code'],
            'duration' => $validated['duration'] ?? null,
            'status' => $validated['status'],
        ]);

        return redirect()->route('courses.index')->with('status', 'Course added successfully.');
    }

    public function edit(Course $course): View
    {
        return view('courses.edit', compact('course'));
    }

    public function update(Request $request, Course $course): RedirectResponse
    {
        $validated = $this->validateCourse($request, $course);

        $course->update([
            'name' => $validated['course_name'],
            'course_code' => $validated['course_code'],
            'duration' => $validated['duration'] ?? null,
            'status' => $validated['status'],
        ]);

        return redirect()->route('courses.index')->with('status', 'Course updated successfully.');
    }

    public function destroy(Course $course): RedirectResponse
    {
        if ($course->students()->exists()) {
            return redirect()->route('courses.index')
                ->with('error', 'This course has student enrollments and cannot be deleted.');
        }

        $course->delete();

        return redirect()->route('courses.index')->with('status', 'Course deleted successfully.');
    }

    private function validateCourse(Request $request, ?Course $course = null): array
    {
        return $request->validate([
            'course_name' => [
                'required',
                'string',
                'max:255',
                'unique:courses,name'.($course ? ','.$course->id : ''),
            ],
            'course_code' => [
                'required',
                'string',
                'max:50',
                'unique:courses,course_code'.($course ? ','.$course->id : ''),
            ],
            'duration' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'in:active,inactive'],
        ]);
    }
}
