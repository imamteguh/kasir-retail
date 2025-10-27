<?php

namespace App\Observers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class SaleObserver
{
    public function created(Sale $sale): void
    {
        DB::transaction(function () use ($sale) {
            foreach ($sale->items as $item) {
                $product = Product::find($item->product_id);

                if ($product) {
                    // Kurangi stok barang
                    $product->decrement('stock', $item->qty);

                    // Catat pergerakan stok
                    StockMovement::create([
                        'store_id'       => $sale->store_id,
                        'product_id'     => $item->product_id,
                        'type'           => 'out',
                        'reference_type' => 'sale',
                        'reference_id'   => $sale->id,
                        'qty'            => $item->qty,
                    ]);
                }
            }
        });
    }
}
