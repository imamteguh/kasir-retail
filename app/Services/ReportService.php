<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\Purchase;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportService
{
    /**
     * Laporan penjualan per periode
     */
    public function salesReport(int $storeId, $startDate, $endDate)
    {
        $start = Carbon::parse($startDate)->startOfDay();
        $end   = Carbon::parse($endDate)->endOfDay();

        return Sale::where('store_id', $storeId)
            ->whereBetween('date', [$start, $end])
            ->with('items.product')
            ->get();
    }

    /**
     * Laporan pembelian per periode
     */
    public function purchaseReport(int $storeId, $startDate, $endDate)
    {
        $start = Carbon::parse($startDate)->startOfDay();
        $end   = Carbon::parse($endDate)->endOfDay();

        return Purchase::where('store_id', $storeId)
            ->whereBetween('date', [$start, $end])
            ->with('items.product')
            ->get();
    }

    /**
     * Laporan laba rugi per periode
     */
    public function profitLossReport(int $storeId, $startDate, $endDate)
    {
        $sales = Sale::where('store_id', $storeId)
            ->whereBetween('date', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()])
            ->with('items.product')
            ->get();

        $purchases = Purchase::where('store_id', $storeId)
            ->whereBetween('date', [$startDate, $endDate])
            ->with('items')
            ->get();

        $totalSales = $sales->sum('total');
        $totalPurchase = $purchases->sum('total');

        $totalCost = 0;
        foreach ($sales as $sale) {
            foreach ($sale->items as $item) {
                $totalCost += $item->product->cost_price * $item->qty;
            }
        }

        $profit = $totalSales - $totalCost;

        return [
            'total_sales'    => $totalSales,
            'total_purchase' => $totalPurchase,
            'total_cost'     => $totalCost,
            'profit'         => $profit,
        ];
    }
}
