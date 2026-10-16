<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Student;

class DashboardController extends Controller
{
    public function index()
    {
        $totalStudents = Student::count();
        $totalCourses = Course::count();
        $totalEnrollments = Enrollment::count();
        $activeCourses = Course::where('status', 'active')->count();

        return view('dashboard', compact(
            'totalStudents',
            'totalCourses',
            'totalEnrollments',
            'activeCourses'
        ));
    }
}