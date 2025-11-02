<?php
namespace App\Http\Controllers\API\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\PurchaseItem;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $store = tenant();

        $purchases = Purchase::where('store_id', $store->id)
            ->with(['supplier', 'items.product'])
            ->latest('date')
            ->get();

        return response()->json($purchases);
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'nullable|exists:suppliers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.cost_price' => 'required|numeric|min:0',
        ]);

        $store = tenant();

        return DB::transaction(function () use ($store, $request) {
            $invoice = 'PB-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(4));

            $purchase = Purchase::create([
                'store_id' => $store->id,
                'supplier_id' => $request->supplier_id,
                'invoice_number' => $invoice,
                'date' => now(),
                'total' => 0,
                'created_by' => Auth::id(),
            ]);

            $total = 0;

            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $subtotal = $item['qty'] * $item['cost_price'];
                $total += $subtotal;

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $product->id,
                    'qty' => $item['qty'],
                    'cost_price' => $item['cost_price'],
                    'subtotal' => $subtotal,
                ]);
            }

            $purchase->update(['total' => $total]);

            return response()->json([
                'message' => 'Pembelian berhasil disimpan',
                'data' => $purchase->load('items.product')
            ]);
        });
    }
}
