<?php

namespace App\Http\Controllers;

use App\Models\Fee;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', Rule::in(['Completed', 'Pending', 'Failed'])],
            'method' => ['nullable', Rule::in(['M-Pesa', 'Cash', 'Bank', 'Cheque'])],
        ]);
        $search = trim($filters['search'] ?? '');
        $status = $filters['status'] ?? '';
        $method = $filters['method'] ?? '';

        $paymentsQuery = Payment::with('fee.student')
            ->when($search !== '', function ($query) use ($search): void {
                $query->whereHas('fee.student', function ($studentQuery) use ($search): void {
                    $studentQuery->where(function ($nameQuery) use ($search): void {
                        $nameQuery
                            ->where('first_name', 'like', "%{$search}%")
                            ->orWhere('second_name', 'like', "%{$search}%")
                            ->orWhere('student_number', 'like', "%{$search}%");
                    });
                });
            })
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->when($method !== '', fn ($query) => $query->where('method', $method))
            ->latest();

        $payments = $paymentsQuery->paginate(10)->withQueryString();
        $legacyPaidAmount = (float) Fee::query()
            ->where('status', 'Paid')
            ->whereDoesntHave('payments', fn ($query) => $query->where('status', 'Completed'))
            ->sum('amount');
        $totalCollected = $legacyPaidAmount + (float) Payment::where('status', 'Completed')->sum('amount');
        $thisMonthCollected = Payment::where('status', 'Completed')
            ->whereBetween('paid_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('amount');
        $pendingMpesaTotal = Payment::where('method', 'M-Pesa')
            ->where('status', 'Pending')
            ->sum('amount');

        return view('payments.index', compact(
            'payments',
            'totalCollected',
            'thisMonthCollected',
            'pendingMpesaTotal',
            'search',
            'status',
            'method',
        ));
    }

    public function create(): View
    {
        $fees = Fee::with('student')
            ->withSum(['payments as completed_payments_total' => fn ($query) => $query->where('status', 'Completed')], 'amount')
            ->where('status', '!=', 'Paid')
            ->orderBy('student_id')
            ->get()
            ->filter(fn (Fee $fee): bool => (int) round(((float) $fee->amount - (float) $fee->completed_payments_total) * 100) > 0);

        return view('payments.create', compact('fees'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'fee_id' => ['required', 'integer', 'exists:fees,id'],
            'amount' => ['required', 'numeric', 'gt:0', 'max:99999999.99', 'decimal:0,2'],
            'method' => ['required', Rule::in(['Cash', 'Bank', 'Cheque', 'M-Pesa'])],
            'transaction_reference' => [
                Rule::requiredIf(in_array($request->input('method'), ['Bank', 'Cheque', 'M-Pesa'], true)),
                'nullable',
                'string',
                'max:255',
            ],
            'phone_number' => [
                Rule::requiredIf($request->input('method') === 'M-Pesa'),
                'nullable',
                'string',
                'max:30',
            ],
        ]);

        DB::transaction(function () use ($validated): void {
            $fee = Fee::query()->lockForUpdate()->findOrFail($validated['fee_id']);
            $amountCents = (int) round((float) $validated['amount'] * 100);
            $completedCents = (int) round(
                (float) $fee->payments()->where('status', 'Completed')->sum('amount') * 100
            );
            $feeAmountCents = (int) round((float) $fee->amount * 100);
            $balanceCents = $feeAmountCents - $completedCents;

            if ($fee->status === 'Paid' || $amountCents > $balanceCents) {
                throw ValidationException::withMessages([
                    'amount' => 'The payment amount cannot exceed the outstanding fee balance.',
                ]);
            }

            Payment::create([
                'fee_id' => $fee->id,
                'amount' => $validated['amount'],
                'method' => $validated['method'],
                'transaction_reference' => $validated['transaction_reference'] ?? null,
                'provider' => $validated['method'] === 'M-Pesa' ? 'Safaricom' : null,
                'phone_number' => $validated['phone_number'] ?? null,
                'status' => 'Completed',
                'paid_at' => now(),
            ]);

            $fee->update([
                'status' => $amountCents === $balanceCents ? 'Paid' : 'Partial',
            ]);
        });

        return redirect()->route('payments.index')->with('status', 'Payment recorded successfully.');
    }
}
