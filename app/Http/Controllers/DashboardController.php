<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Fee;
use App\Models\Student;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    public function index()
    {
        $totalStudents = Student::count();
        $totalCourses = Course::count();
        $totalAmountPaid = (float) Fee::query()
            ->where('status', 'Paid')
            ->sum('amount');

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
            ->get(['amount', 'created_at'])
            ->each(function (Fee $fee) use ($monthlyFeeStats): void {
                $monthKey = $fee->created_at->format('Y-m');
                if ($monthlyFeeStats->has($monthKey)) {
                    $monthStat = $monthlyFeeStats->get($monthKey);
                    $monthStat['amount'] += (float) $fee->amount;
                    $monthlyFeeStats->put($monthKey, $monthStat);
                }
            });

        $monthlyFeeStats = $monthlyFeeStats->values();
        $maxMonthlyFeeAmount = max(1, (float) $monthlyFeeStats->max('amount'));

        return view('dashboard', compact(
            'totalStudents',
            'totalCourses',
            'totalAmountPaid',
            'feeStatusStats',
            'totalFeeStatusRecords',
            'feeStatusPieBackground',
            'monthlyFeeStats',
            'maxMonthlyFeeAmount'
        ));
    }
}
