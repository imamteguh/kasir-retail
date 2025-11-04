<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request, ReportService $reportService)
    {
        $store = tenant();
        $type = $request->input('type', 'daily'); // daily | monthly

        if ($type === 'monthly') {
            $month = $request->input('month', now()->format('Y-m'));
            $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth()->toDateString();
            $end   = Carbon::createFromFormat('Y-m', $month)->endOfMonth()->toDateString();
            $sales = $reportService->salesReport($store->id, $start, $end);

            // Group by date for monthly recap
            $grouped = $sales->groupBy(function ($sale) {
                return $sale->date->toDateString();
            })->map(function ($daySales) {
                return [
                    'total' => $daySales->sum('total'),
                    'transactions' => $daySales->count(),
                ];
            });

            return view('reports.sales', [
                'type' => $type,
                'month' => $month,
                'start' => $start,
                'end' => $end,
                'sales' => $sales,
                'grouped' => $grouped,
                'total_sales' => $sales->sum('total'),
                'transactions' => $sales->count(),
            ]);
        } else {
            $date = $request->input('date', now()->toDateString());
            $start = $date;
            $end = $date;
            $sales = $reportService->salesReport($store->id, $start, $end);

            return view('reports.sales', [
                'type' => $type,
                'date' => $date,
                'start' => $start,
                'end' => $end,
                'sales' => $sales,
                'total_sales' => $sales->sum('total'),
                'transactions' => $sales->count(),
            ]);
        }
    }

    public function pdf(Request $request, ReportService $reportService)
    {
        $store = tenant();
        $type = $request->input('type', 'daily');

        if ($type === 'monthly') {
            $month = $request->input('month', now()->format('Y-m'));
            $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth()->toDateString();
            $end   = Carbon::createFromFormat('Y-m', $month)->endOfMonth()->toDateString();
            $sales = $reportService->salesReport($store->id, $start, $end);

            $grouped = $sales->groupBy(function ($sale) {
                return $sale->date->toDateString();
            })->map(function ($daySales) {
                return [
                    'total' => $daySales->sum('total'),
                    'transactions' => $daySales->count(),
                ];
            });

            $pdf = Pdf::loadView('reports.sales_pdf', [
                'type' => $type,
                'month' => $month,
                'start' => $start,
                'end' => $end,
                'sales' => $sales,
                'grouped' => $grouped,
                'total_sales' => $sales->sum('total'),
                'transactions' => $sales->count(),
                'store' => $store,
            ]);

            return $pdf->download("Rekap Penjualan Bulanan - {$month}.pdf");
        } else {
            $date = $request->input('date', now()->toDateString());
            $start = $date;
            $end = $date;
            $sales = $reportService->salesReport($store->id, $start, $end);

            $pdf = Pdf::loadView('reports.sales_pdf', [
                'type' => $type,
                'date' => $date,
                'start' => $start,
                'end' => $end,
                'sales' => $sales,
                'total_sales' => $sales->sum('total'),
                'transactions' => $sales->count(),
                'store' => $store,
            ]);

            return $pdf->download("Rekap Penjualan Harian - {$date}.pdf");
        }
    }

    public function profitLoss()
    {
        return view('reports.profit_loss');
    }

    public function profitLossPdf(Request $request, ReportService $reportService)
    {
        $store = tenant();
        $type = $request->input('type', 'daily'); // daily | weekly | monthly | range

        // Determine period
        $start = null; $end = null; $label = '';
        if ($type === 'monthly') {
            $month = $request->input('month', now()->format('Y-m'));
            $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth()->toDateString();
            $end   = Carbon::createFromFormat('Y-m', $month)->endOfMonth()->toDateString();
            $label = "Bulanan {$month}";
        } elseif ($type === 'weekly') {
            $date = Carbon::parse($request->input('date', now()->toDateString()));
            $start = $date->copy()->startOfWeek()->toDateString();
            $end   = $date->copy()->endOfWeek()->toDateString();
            $label = "Mingguan {$start} s/d {$end}";
        } elseif ($type === 'range') {
            $start = Carbon::parse($request->input('start_date', now()->toDateString()))->toDateString();
            $end   = Carbon::parse($request->input('end_date', $start))->toDateString();
            $label = "Rentang {$start} s/d {$end}";
        } else { // daily
            $date = Carbon::parse($request->input('date', now()->toDateString()))->toDateString();
            $start = $date; $end = $date; $label = "Harian {$date}";
        }

        $report = $reportService->profitLossReport($store->id, $start, $end);

        $pdf = Pdf::loadView('reports.profit_loss_pdf', [
            'store' => $store,
            'type' => $type,
            'label' => $label,
            'start' => $start,
            'end' => $end,
            'report' => $report,
        ]);

        return $pdf->download("Laporan Laba Rugi - {$label}.pdf");
    }
}
