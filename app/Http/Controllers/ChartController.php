<?php

namespace App\Http\Controllers;

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
            ->selectRaw('YEAR(created_at) as year, SUM(COALESCE(total, total_amount, 0)) as total_sales')
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
            ->selectRaw('DATE(created_at) as sale_date, SUM(COALESCE(total, total_amount, 0)) as total_sales')
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

        $rows = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->leftJoin('products', 'products.id', '=', 'order_items.product_id')
            ->selectRaw('COALESCE(products.name, ? ) as product_name, SUM(order_items.subtotal) as total_sales', ['Deleted Product'])
            ->whereRaw("LOWER(COALESCE(orders.status, '')) != ?", ['cancelled'])
            ->whereDate('orders.created_at', '>=', $startDate->toDateString())
            ->whereDate('orders.created_at', '<=', $endDate->toDateString())
            ->groupBy('product_name')
            ->orderByDesc('total_sales')
            ->get();

        $grandTotal = (float) $rows->sum('total_sales');

        $percentages = $rows->map(function ($row) use ($grandTotal) {
            $value = (float) $row->total_sales;
            $percentage = $grandTotal > 0 ? round(($value / $grandTotal) * 100, 2) : 0;

            return [
                'label' => (string) $row->product_name,
                'amount' => round($value, 2),
                'percentage' => $percentage,
            ];
        });

        return response()->json([
            'labels' => $percentages->pluck('label')->values(),
            'values' => $percentages->pluck('percentage')->values(),
            'amounts' => $percentages->pluck('amount')->values(),
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
