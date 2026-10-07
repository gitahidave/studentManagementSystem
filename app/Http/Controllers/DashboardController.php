<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Dynamic dashboard metrics passed to Blade view
        $totalStudents = 250;
        $totalCourses = 12;
        $feesCollected = 'KES 500,000';
        $outstandingFees = 'KES 120,000';

        return view('dashboard', compact(
            'totalStudents',
            'totalCourses',
            'feesCollected',
            'outstandingFees'
        ));
    }
}