<?php

namespace App\Observers;

use App\Models\Purchase;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class PurchaseObserver
{
    public function created(Purchase $purchase): void
    {
        DB::transaction(function () use ($purchase) {
            foreach ($purchase->items as $item) {
                $product = Product::find($item->product_id);

                if ($product) {
                    // Tambah stok barang
                    $product->increment('stock', $item->qty);

                    // Catat pergerakan stok
                    StockMovement::create([
                        'store_id'       => $purchase->store_id,
                        'product_id'     => $item->product_id,
                        'type'           => 'in',
                        'reference_type' => 'purchase',
                        'reference_id'   => $purchase->id,
                        'qty'            => $item->qty,
                    ]);
                }
            }
        });
    }
}
