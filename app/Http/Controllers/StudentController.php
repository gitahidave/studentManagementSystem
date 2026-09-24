<?php

namespace App\Http\Controllers;

use App\Models\Student;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::all();

        return view('students.hello', compact('students'));
    }

    public function show(Student $student)
    {
        return view('students.hello', compact('student'));
    }
}