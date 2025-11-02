<?php

namespace App\Observers;

use App\Models\Purchase;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class PurchaseObserver
{
    public function saved(Purchase $purchase): void
    {
        DB::transaction(function () use ($purchase) {
            // Pastikan item sudah ada
            if (!$purchase->items()->exists()) {
                return;
            }

            // Hindari penyesuaian ganda untuk transaksi yang sama
            $alreadyMoved = StockMovement::where('reference_type', 'purchase')
                ->where('reference_id', $purchase->id)
                ->exists();

            if ($alreadyMoved) {
                return;
            }

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
