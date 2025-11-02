<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class POSController extends Controller
{
    public function index()
    {
        return view('pos.index', [
            'products' => Product::where('store_id', tenant()->id)
                ->where('is_active', true)
                ->orderBy('name')
                ->get()
        ]);
    }
}
