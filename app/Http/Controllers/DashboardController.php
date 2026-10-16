<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Fee;
use App\Models\Student;

class DashboardController extends Controller
{
    public function index()
    {
        $totalStudents = Student::count();
        $totalCourses = Course::count();
        $totalFees = Fee::count();

        return view('dashboard', compact(
            'totalStudents',
            'totalCourses',
            'totalFees'
        ));
    }
}
