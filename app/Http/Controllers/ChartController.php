<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChartController extends Controller
{
    public function index()
    {
        // This will render the charts page view
        return view('charts.index');
    }

    public function yearlySales()
    {
        $rows = DB::table('orders')
            ->selectRaw('YEAR(created_at) as year, SUM(COALESCE(total, 0)) as total_sales')
            ->whereRaw("LOWER(COALESCE(status, '')) != ?", ['cancelled'])
            ->groupByRaw('YEAR(created_at)')
            ->orderByRaw('YEAR(created_at) ASC')
            ->get();

        return response()->json([
            'labels' => $rows->pluck('year')->map(fn ($y) => (string) $y)->values(),
            'values' => $rows->pluck('total_sales')->map(fn ($v) => round((float) $v, 2))->values(),
        ]);
    }

    public function salesByDateRange(Request $request)
    {
        [$startDate, $endDate] = $this->resolveDateRange($request);

        $salesRows = DB::table('orders')
            ->selectRaw('DATE(created_at) as sale_date, SUM(COALESCE(total, 0)) as total_sales')
            ->whereRaw("LOWER(COALESCE(status, '')) != ?", ['cancelled'])
            ->whereDate('created_at', '>=', $startDate->toDateString())
            ->whereDate('created_at', '<=', $endDate->toDateString())
            ->groupByRaw('DATE(created_at)')
            ->orderBy('sale_date')
            ->get()
            ->keyBy('sale_date');

        $labels = [];
        $values = [];

        foreach (CarbonPeriod::create($startDate, $endDate) as $date) {
            $dateKey = $date->toDateString();
            $labels[] = $date->format('M d');
            $values[] = round((float) ($salesRows[$dateKey]->total_sales ?? 0), 2);
        }

        return response()->json([
            'labels' => $labels,
            'values' => $values,
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
        ]);
    }

    public function productSalesShare(Request $request)
    {
        [$startDate, $endDate] = $this->resolveDateRange($request);

        $unitsByProduct = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereRaw("LOWER(COALESCE(orders.status, '')) != ?", ['cancelled'])
            ->whereDate('orders.created_at', '>=', $startDate->toDateString())
            ->whereDate('orders.created_at', '<=', $endDate->toDateString())
            ->selectRaw('order_items.product_id, SUM(order_items.quantity) as units_sold')
            ->groupBy('order_items.product_id')
            ->get();

        $unitsMap = $unitsByProduct->pluck('units_sold', 'product_id');

        $rows = Product::query()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(function ($product) use ($unitsMap) {
                return [
                    'label' => (string) $product->name,
                    'units' => (int) ($unitsMap[$product->id] ?? 0),
                ];
            });

        $grandTotalUnits = (int) $rows->sum('units');

        $percentages = $rows->map(function ($row) use ($grandTotalUnits) {
            $value = (int) $row['units'];
            $percentage = $grandTotalUnits > 0 ? round(($value / $grandTotalUnits) * 100, 2) : 0;

            return [
                'label' => (string) $row['label'],
                'units' => $value,
                'percentage' => $percentage,
            ];
        });

        return response()->json([
            'labels' => $percentages->pluck('label')->values(),
            'values' => $percentages->pluck('percentage')->values(),
            'units' => $percentages->pluck('units')->values(),
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
        ]);
    }

    private function resolveDateRange(Request $request): array
    {
        $validated = $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $endDate = isset($validated['end_date'])
            ? Carbon::parse($validated['end_date'])->endOfDay()
            : now()->endOfDay();

        $startDate = isset($validated['start_date'])
            ? Carbon::parse($validated['start_date'])->startOfDay()
            : $endDate->copy()->subDays(29)->startOfDay();

        return [$startDate, $endDate];
    }
}
