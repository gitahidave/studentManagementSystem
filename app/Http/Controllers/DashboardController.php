<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Fee;
use App\Models\Payment;
use App\Models\Student;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    public function index()
    {
        $totalStudents = Student::count();
        $totalCourses = Course::count();
        $legacyPaidAmount = (float) Fee::query()
            ->where('status', 'Paid')
            ->whereDoesntHave('payments', fn ($query) => $query->where('status', 'Completed'))
            ->sum('amount');
        $totalAmountPaid = $legacyPaidAmount + (float) Payment::query()
            ->where('status', 'Completed')
            ->sum('amount');
        $totalAmountPending = (float) Fee::query()
            ->whereIn('status', ['Pending', 'Partial'])
            ->withSum(['payments as completed_payments_total' => fn ($query) => $query->where('status', 'Completed')], 'amount')
            ->get(['id', 'amount'])
            ->sum(fn (Fee $fee): float => max(
                0,
                (float) $fee->amount - (float) $fee->completed_payments_total
            ));

        $feeStatusStats = Fee::query()
            ->get(['status'])
            ->groupBy(fn (Fee $fee): string => ucfirst(strtolower($fee->status)))
            ->map(fn (Collection $fees, string $status): array => [
                'status' => $status,
                'count' => $fees->count(),
                'color' => match ($status) {
                    'Paid' => '#198754',
                    'Pending' => '#ffc107',
                    'Partial' => '#0dcaf0',
                    default => '#6c757d',
                },
            ])
            ->values();

        $totalFeeStatusRecords = (int) $feeStatusStats->sum('count');
        $pieStart = 0;
        $pieSegments = [];
        foreach ($feeStatusStats as $statusStat) {
            $pieEnd = $pieStart + ($statusStat['count'] / $totalFeeStatusRecords * 360);
            $pieSegments[] = sprintf(
                '%s %.2fdeg %.2fdeg',
                $statusStat['color'],
                $pieStart,
                $pieEnd
            );
            $pieStart = $pieEnd;
        }
        $feeStatusPieBackground = $pieSegments === []
            ? '#e9ecef'
            : 'conic-gradient('.implode(', ', $pieSegments).')';

        $monthStart = now()->startOfMonth()->subMonths(5);
        $monthlyFeeStats = collect();
        for ($monthOffset = 0; $monthOffset < 6; $monthOffset++) {
            $month = $monthStart->copy()->addMonths($monthOffset);
            $monthlyFeeStats->put($month->format('Y-m'), [
                'label' => $month->format('M Y'),
                'amount' => 0.0,
            ]);
        }

        Fee::query()
            ->where('created_at', '>=', $monthStart)
            ->where('status', 'Paid')
            ->whereDoesntHave('payments', fn ($query) => $query->where('status', 'Completed'))
            ->get(['amount', 'created_at'])
            ->each(function (Fee $fee) use ($monthlyFeeStats): void {
                $monthKey = $fee->created_at->format('Y-m');
                if ($monthlyFeeStats->has($monthKey)) {
                    $monthStat = $monthlyFeeStats->get($monthKey);
                    $monthStat['amount'] += (float) $fee->amount;
                    $monthlyFeeStats->put($monthKey, $monthStat);
                }
            });

        Payment::query()
            ->where('status', 'Completed')
            ->where('paid_at', '>=', $monthStart)
            ->get(['amount', 'paid_at'])
            ->each(function (Payment $payment) use ($monthlyFeeStats): void {
                $monthKey = $payment->paid_at->format('Y-m');
                if ($monthlyFeeStats->has($monthKey)) {
                    $monthStat = $monthlyFeeStats->get($monthKey);
                    $monthStat['amount'] += (float) $payment->amount;
                    $monthlyFeeStats->put($monthKey, $monthStat);
                }
            });

        $monthlyFeeStats = $monthlyFeeStats->values();
        $maxMonthlyFeeAmount = max(1, (float) $monthlyFeeStats->max('amount'));

        return view('dashboard', compact(
            'totalStudents',
            'totalCourses',
            'totalAmountPaid',
            'totalAmountPending',
            'feeStatusStats',
            'totalFeeStatusRecords',
            'feeStatusPieBackground',
            'monthlyFeeStats',
            'maxMonthlyFeeAmount'
        ));
    }
}
