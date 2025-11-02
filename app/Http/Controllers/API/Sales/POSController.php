<?php

namespace App\Http\Controllers\API\Sales;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class POSController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:cash,transfer,qris',
        ]);

        $store = tenant();

        return DB::transaction(function () use ($store, $request) {
            $invoice = 'INV-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(4));

            $sale = Sale::create([
                'store_id' => $store->id,
                'invoice_number' => $invoice,
                'date' => now(),
                'discount' => $request->discount ?? 0,
                'payment_method' => $request->payment_method,
                'created_by' => Auth::id(),
                'total' => 0,
            ]);

            $total = 0;

            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $subtotal = $item['qty'] * $item['price'];
                $total += $subtotal;

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'subtotal' => $subtotal,
                ]);
            }

            $sale->update(['total' => $total - ($request->discount ?? 0)]);

            return response()->json([
                'message' => 'Transaksi berhasil disimpan',
                'data' => $sale->load('items.product')
            ]);
        });
    }
}