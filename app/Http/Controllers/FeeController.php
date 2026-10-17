<?php

namespace App\Http\Controllers;

use App\Models\Fee;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FeeController extends Controller
{
    public function index(): View
    {
        $fees = Fee::with('student')->latest()->paginate(10);

        return view('fees.index', compact('fees'));
    }

    public function create(): View
    {
        $students = Student::orderBy('first_name')
            ->orderBy('second_name')
            ->get();

        return view('fees.create', compact('students'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'integer', 'exists:students,id'],
            'amount' => ['required', 'numeric', 'gt:0', 'max:99999999.99', 'decimal:0,2'],
            'fee_type' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(['Paid', 'Pending', 'Partial'])],
        ]);

        Fee::create($validated);

        return redirect()->route('fees.index')->with('status', 'Fee record added successfully.');
    }
}
