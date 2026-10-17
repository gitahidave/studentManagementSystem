<?php

namespace App\Http\Controllers;

use App\Models\Fee;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
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
        $validated = $this->validateFee($request);

        Fee::create($validated);

        return redirect()->route('fees.index')->with('status', 'Fee record added successfully.');
    }

    public function edit(Fee $fee): View
    {
        $students = Student::orderBy('first_name')
            ->orderBy('second_name')
            ->get();

        return view('fees.edit', compact('fee', 'students'));
    }

    public function update(Request $request, Fee $fee): RedirectResponse
    {
        $validated = $this->validateFee($request);
        $completedPaymentsTotal = (float) $fee->payments()
            ->where('status', 'Completed')
            ->sum('amount');
        $integrityErrors = [];

        if ((int) round((float) $validated['amount'] * 100) < (int) round($completedPaymentsTotal * 100)) {
            $integrityErrors['amount'] = 'The fee amount cannot be less than payments already completed.';
        }

        if ($fee->payments()->exists() && (int) $validated['student_id'] !== (int) $fee->student_id) {
            $integrityErrors['student_id'] = 'The student cannot be changed after payments have been recorded.';
        }

        if ($integrityErrors !== []) {
            throw ValidationException::withMessages($integrityErrors);
        }

        $fee->update($validated);

        return redirect()->route('fees.index')->with('status', 'Fee record updated successfully.');
    }

    public function destroy(Fee $fee): RedirectResponse
    {
        if ($fee->payments()->exists()) {
            return redirect()->route('fees.index')
                ->with('error', 'This fee has payment records and cannot be deleted.');
        }

        $fee->delete();

        return redirect()->route('fees.index')->with('status', 'Fee record deleted successfully.');
    }

    private function validateFee(Request $request): array
    {
        return $request->validate([
            'student_id' => ['required', 'integer', 'exists:students,id'],
            'amount' => ['required', 'numeric', 'gt:0', 'max:99999999.99', 'decimal:0,2'],
            'fee_type' => ['required', 'string', 'max:255'],
            'reference' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(['Paid', 'Pending', 'Partial'])],
        ]);
    }
}
