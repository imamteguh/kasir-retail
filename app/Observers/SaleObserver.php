<?php

namespace App\Observers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class SaleObserver
{
    public function saved(Sale $sale): void
    {
        DB::transaction(function () use ($sale) {
            // Pastikan item sudah ada
            if (!$sale->items()->exists()) {
                return;
            }

            // Hindari penyesuaian ganda untuk transaksi yang sama
            $alreadyMoved = StockMovement::where('reference_type', 'sale')
                ->where('reference_id', $sale->id)
                ->exists();

            if ($alreadyMoved) {
                return;
            }

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
