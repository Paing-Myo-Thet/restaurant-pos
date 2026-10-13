<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Sale;

class ReportController extends Controller
{
    public function index(Request $request): View
{
    $period = $request->input('period', 'daily');

    $date = $request->input('date', today()->format('Y-m-d'));
    $month = $request->input('month', now()->format('Y-m'));
    $year = $request->input('year', now()->year);

    $query = Sale::query()
        ->where('status', '!=', 'cancelled');

    switch ($period) {

        case 'weekly':

            $startOfWeek = now()->startOfWeek();
            $endOfWeek = now()->endOfWeek();

            $query->whereBetween('created_at', [
                $startOfWeek,
                $endOfWeek,
            ]);

            break;

        case 'monthly':

            $query->whereYear('created_at', substr($month, 0, 4))
                  ->whereMonth('created_at', substr($month, 5, 2));

            break;

        case 'yearly':

            $query->whereYear('created_at', $year);

            break;

        default:

            $query->whereDate('created_at', $date);

            $period = 'daily';

            break;
    }

    $sales = $query->get();

    $totalSales = $sales->sum('total');

    $totalOrders = $sales->count();

    $paidSales = $sales
        ->where('payment_status', 'paid')
        ->sum('total');

    $unpaidSales = $sales
        ->where('payment_status', 'unpaid')
        ->sum('total');

    // Cancelled orders
    $cancelledQuery = Sale::query()
        ->where('status', 'cancelled');

    switch ($period) {

        case 'weekly':

            $cancelledQuery->whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek(),
            ]);

            break;

        case 'monthly':

            $cancelledQuery->whereYear('created_at', substr($month, 0, 4))
                ->whereMonth('created_at', substr($month, 5, 2));

            break;

        case 'yearly':

            $cancelledQuery->whereYear('created_at', $year);

            break;

        default:

            $cancelledQuery->whereDate('created_at', $date);

            break;
    }

    $cancelledOrders = $cancelledQuery->count();
    $breakdown = collect();

if ($period === 'daily') {

    $breakdown = collect([
        [
            'label' => \Carbon\Carbon::parse($date)->format('l, F j, Y'),
            'orders' => $totalOrders,
            'sales' => $totalSales,
            'paid' => $paidSales,
            'unpaid' => $unpaidSales,
        ],
    ]);

} elseif ($period === 'weekly') {

    $start = now()->startOfWeek();
    $end = now()->endOfWeek();

    $breakdown = Sale::where('status', '!=', 'cancelled')
        ->whereBetween('created_at', [$start, $end])
        ->get()
        ->groupBy(function ($sale) {
            return $sale->created_at->format('Y-m-d');
        })
        ->map(function ($sales, $date) {

            return [
                'label' => \Carbon\Carbon::parse($date)->format('l, F j'),
                'orders' => $sales->count(),
                'sales' => $sales->sum('total'),
                'paid' => $sales
                    ->where('payment_status', 'paid')
                    ->sum('total'),
                'unpaid' => $sales
                    ->where('payment_status', 'unpaid')
                    ->sum('total'),
            ];
        })
        ->sortKeys();
} elseif ($period === 'monthly') {

    $startOfMonth = \Carbon\Carbon::createFromFormat('Y-m', $month)
        ->startOfMonth();

    $endOfMonth = \Carbon\Carbon::createFromFormat('Y-m', $month)
        ->endOfMonth();

    $breakdown = Sale::where('status', '!=', 'cancelled')
        ->whereBetween('created_at', [
            $startOfMonth,
            $endOfMonth,
        ])
        ->get()
        ->groupBy(function ($sale) {
            return $sale->created_at->format('Y-m-d');
        })
        ->map(function ($sales, $date) {

            return [
                'label' => \Carbon\Carbon::parse($date)
                    ->format('l, F j'),

                'orders' => $sales->count(),

                'sales' => $sales->sum('total'),

                'paid' => $sales
                    ->where('payment_status', 'paid')
                    ->sum('total'),

                'unpaid' => $sales
                    ->where('payment_status', 'unpaid')
                    ->sum('total'),
            ];
        })
        ->sortKeys();
}elseif ($period === 'yearly') {

    $startOfYear = \Carbon\Carbon::createFromDate($year, 1, 1)
        ->startOfYear();

    $endOfYear = \Carbon\Carbon::createFromDate($year, 12, 31)
        ->endOfYear();

    $breakdown = Sale::where('status', '!=', 'cancelled')
        ->whereBetween('created_at', [
            $startOfYear,
            $endOfYear,
        ])
        ->get()
        ->groupBy(function ($sale) {
            return $sale->created_at->format('Y-m');
        })
        ->map(function ($sales, $month) {

            return [
                'label' => \Carbon\Carbon::parse($month . '-01')
                    ->format('F'),

                'orders' => $sales->count(),

                'sales' => $sales->sum('total'),

                'paid' => $sales
                    ->where('payment_status', 'paid')
                    ->sum('total'),

                'unpaid' => $sales
                    ->where('payment_status', 'unpaid')
                    ->sum('total'),
            ];
        })
        ->sortKeys();
}

    return view('reports.index', compact(
        'totalSales',
        'totalOrders',
        'paidSales',
        'unpaidSales',
        'cancelledOrders',
        'period',
        'date',
        'month',
        'year',
        'breakdown'
    ));
}
}
