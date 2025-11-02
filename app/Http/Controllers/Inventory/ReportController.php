<?php
namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ReportService;
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
            $purchases = $reportService->purchaseReport($store->id, $start, $end);

            // Group by date for monthly recap
            $grouped = $purchases->groupBy(function ($purchase) {
                return $purchase->date->toDateString();
            })->map(function ($dayPurchases) {
                return [
                    'total' => $dayPurchases->sum('total'),
                    'transactions' => $dayPurchases->count(),
                ];
            });

            return view('reports.purchases', [
                'type' => $type,
                'month' => $month,
                'start' => $start,
                'end' => $end,
                'purchases' => $purchases,
                'grouped' => $grouped,
                'total_purchases' => $purchases->sum('total'),
                'transactions' => $purchases->count(),
            ]);
        } else {
            $date = $request->input('date', now()->toDateString());
            $start = $date;
            $end = $date;
            $purchases = $reportService->purchaseReport($store->id, $start, $end);

            return view('reports.purchases', [
                'type' => $type,
                'date' => $date,
                'start' => $start,
                'end' => $end,
                'purchases' => $purchases,
                'total_purchases' => $purchases->sum('total'),
                'transactions' => $purchases->count(),
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
            $purchases = $reportService->purchaseReport($store->id, $start, $end);

            $grouped = $purchases->groupBy(function ($purchase) {
                return $purchase->date->toDateString();
            })->map(function ($dayPurchases) {
                return [
                    'total' => $dayPurchases->sum('total'),
                    'transactions' => $dayPurchases->count(),
                ];
            });

            $pdf = Pdf::loadView('reports.purchases_pdf', [
                'type' => $type,
                'month' => $month,
                'start' => $start,
                'end' => $end,
                'purchases' => $purchases,
                'grouped' => $grouped,
                'total_purchases' => $purchases->sum('total'),
                'transactions' => $purchases->count(),
                'store' => $store,
            ]);

            return $pdf->download("Rekap Pembelian Bulanan - {$month}.pdf");
        } else {
            $date = $request->input('date', now()->toDateString());
            $start = $date;
            $end = $date;
            $purchases = $reportService->purchaseReport($store->id, $start, $end);

            $pdf = Pdf::loadView('reports.purchases_pdf', [
                'type' => $type,
                'date' => $date,
                'start' => $start,
                'end' => $end,
                'purchases' => $purchases,
                'total_purchases' => $purchases->sum('total'),
                'transactions' => $purchases->count(),
                'store' => $store,
            ]);

            return $pdf->download("Rekap Pembelian Harian - {$date}.pdf");
        }
    }
    
}
