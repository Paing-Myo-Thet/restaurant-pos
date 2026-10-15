<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class ReportController extends Controller
{
    public const PERIODS = ['daily', 'weekly', 'monthly', 'yearly'];

    public function index(Request $request): View
    {
        $period = in_array($request->input('period'), self::PERIODS, true)
            ? $request->input('period')
            : 'daily';

        $date = $this->parse($request->input('date'), 'Y-m-d') ?? CarbonImmutable::today();
        $month = $this->parse($request->input('month'), 'Y-m') ?? CarbonImmutable::today();
        $year = filter_var($request->input('year'), FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 2000, 'max_range' => 2100],
        ]) ?: now()->year;

        [$start, $end, $label] = match ($period) {
            'weekly' => [$date->startOfWeek(), $date->endOfWeek(),
                $date->startOfWeek()->format('M j').' – '.$date->endOfWeek()->format('M j, Y')],
            'monthly' => [$month->startOfMonth(), $month->endOfMonth(), $month->format('F Y')],
            'yearly' => [CarbonImmutable::create($year)->startOfYear(), CarbonImmutable::create($year)->endOfYear(), (string) $year],
            default => [$date->startOfDay(), $date->endOfDay(), $date->format('l, F j, Y')],
        };

        $sales = Sale::whereBetween('created_at', [$start, $end])->get();
        $completed = $sales->where('status', '!=', 'cancelled');
        $paid = $completed->where('payment_status', 'paid');

        $items = SaleItem::query()
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->leftJoin('products', 'products.id', '=', 'sale_items.product_id')
            ->whereBetween('sales.created_at', [$start, $end])
            ->where('sales.status', '!=', 'cancelled')
            ->get([
                'sale_items.product_id',
                'sale_items.product_name',
                'sale_items.quantity',
                'sale_items.subtotal',
                'sale_items.sale_id',
                // Older items predate unit_cost; fall back to the product's current cost.
                'sale_items.unit_cost',
                'products.cost as product_cost',
            ]);

        $costBySale = $items->groupBy('sale_id')->map(fn ($rows) => $rows->sum(
            fn ($row) => $row->quantity * (float) ($row->unit_cost ?? $row->product_cost ?? 0)
        ));

        $grossSales = (float) $completed->sum('total');
        $cost = (float) $costBySale->sum();

        return view('reports.index', [
            'period' => $period,
            'date' => $date->format('Y-m-d'),
            'month' => $month->format('Y-m'),
            'year' => $year,
            'label' => $label,
            'totalSales' => $grossSales,
            'totalOrders' => $completed->count(),
            'paidSales' => (float) $paid->sum('total'),
            'unpaidSales' => (float) $completed->where('payment_status', 'unpaid')->sum('total'),
            'cancelledOrders' => $sales->where('status', 'cancelled')->count(),
            // Cancelled after the kitchen started: the food was made but never sold.
            'writtenOff' => (float) $sales->filter(fn ($sale) => $sale->wasWrittenOff())->sum('sub_total'),
            'averageOrder' => $completed->count() ? $grossSales / $completed->count() : 0,
            'discounts' => (float) $completed->sum('descount'),
            'cost' => $cost,
            'profit' => $grossSales - $cost,
            'topProducts' => $this->topProducts($items),
            'paymentMethods' => $paid->groupBy(fn ($sale) => $sale->payment_method ?? 'unrecorded')
                ->map(fn ($group) => ['orders' => $group->count(), 'total' => (float) $group->sum('total')])
                ->sortByDesc('total'),
            'breakdown' => $this->breakdown($completed, $costBySale, $period),
        ]);
    }

    private function topProducts(Collection $items): Collection
    {
        return $items->groupBy('product_id')
            ->map(fn ($rows) => [
                'name' => $rows->first()->product_name,
                'quantity' => (int) $rows->sum('quantity'),
                'revenue' => (float) $rows->sum('subtotal'),
            ])
            ->sortByDesc('quantity')
            ->take(10)
            ->values();
    }

    private function breakdown(Collection $sales, Collection $costBySale, string $period): Collection
    {
        $groupFormat = $period === 'yearly' ? 'Y-m' : 'Y-m-d';
        $labelFormat = $period === 'yearly' ? 'F' : 'l, F j';

        return $sales->groupBy(fn ($sale) => $sale->created_at->format($groupFormat))
            ->sortKeys()
            ->map(function ($group, $key) use ($costBySale, $groupFormat, $labelFormat) {
                $total = (float) $group->sum('total');
                $cost = (float) $group->sum(fn ($sale) => $costBySale->get($sale->id, 0));

                return [
                    'label' => CarbonImmutable::createFromFormat('!'.$groupFormat, $key)->format($labelFormat),
                    'orders' => $group->count(),
                    'sales' => $total,
                    'paid' => (float) $group->where('payment_status', 'paid')->sum('total'),
                    'unpaid' => (float) $group->where('payment_status', 'unpaid')->sum('total'),
                    'profit' => $total - $cost,
                ];
            })
            ->values();
    }

    private function parse(mixed $value, string $format): ?CarbonImmutable
    {
        if (! $value || ! is_string($value)) {
            return null;
        }

        try {
            return CarbonImmutable::createFromFormat('!'.$format, $value) ?: null;
        } catch (\Throwable) {
            return null;
        }
    }
}
