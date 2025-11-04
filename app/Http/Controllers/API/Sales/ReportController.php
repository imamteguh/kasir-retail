<?php

namespace App\Http\Controllers\API\Sales;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function profitLoss(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        $store = tenant();
        $report = $this->reportService->profitLossReport(
            $store->id,
            $request->start_date,
            $request->end_date
        );

        return response()->json([
            'message' => 'Laporan laba rugi berhasil dihasilkan',
            'data' => $report
        ]);
    }
    
    public function daily()
    {
        $store = tenant();
        $today = now()->toDateString();

        $sales = Sale::where('store_id', $store->id)
            ->whereDate('date', $today)
            ->with('items.product')
            ->get();

        return response()->json([
            'date' => $today,
            'total_sales' => $sales->sum('total'),
            'transactions' => $sales->count(),
            'data' => $sales
        ]);
    }

    public function monthly()
    {
        $store = tenant();
        $month = now()->format('Y-m');

        $sales = Sale::where('store_id', $store->id)
            ->whereRaw("DATE_FORMAT(date, '%Y-%m') = ?", [$month])
            ->with('items.product')
            ->get();

        return response()->json([
            'month' => $month,
            'total_sales' => $sales->sum('total'),
            'transactions' => $sales->count(),
            'data' => $sales
        ]);
    }
}