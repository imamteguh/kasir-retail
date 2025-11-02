<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Product;
use Illuminate\Http\Request;

class POSController extends Controller
{
    public function index()
    {
        return view('pos.index');
    }

    public function receipt(Sale $sale)
    {
        // Optional: ensure sale belongs to current tenant store
        if ($sale->store_id !== tenant()->id) {
            abort(403);
        }

        $sale->load(['items.product', 'cashier']);
        return view('pos.receipt', compact('sale'));
    }
}
