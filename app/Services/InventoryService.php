<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * Tambah pembelian (restock barang)
     */
    public function createPurchase(array $data)
    {
        return DB::transaction(function () use ($data) {
            $purchase = Purchase::create([
                'store_id'       => $data['store_id'],
                'supplier_id'    => $data['supplier_id'] ?? null,
                'invoice_number' => $data['invoice_number'] ?? $this->generateInvoice(),
                'date'           => now(),
                'total'          => 0,
            ]);

            $total = 0;
            foreach ($data['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $subtotal = $item['qty'] * $item['cost_price'];
                $total += $subtotal;

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id'  => $item['product_id'],
                    'qty'         => $item['qty'],
                    'cost_price'  => $item['cost_price'],
                    'subtotal'    => $subtotal,
                ]);
            }

            $purchase->update(['total' => $total]);

            return $purchase;
        });
    }

    /**
     * Update stok manual (penyesuaian stok)
     */
    public function adjustStock(Product $product, int $newStock)
    {
        $difference = $newStock - $product->stock;

        $product->update(['stock' => $newStock]);

        // Optional: Catat ke log pergerakan stok
        if ($difference !== 0) {
            $product->stockMovements()->create([
                'store_id'       => $product->store_id,
                'type'           => $difference > 0 ? 'in' : 'out',
                'reference_type' => 'adjustment',
                'reference_id'   => null,
                'qty'            => abs($difference),
            ]);
        }

        return $product;
    }

    /**
     * Buat nomor invoice otomatis
     */
    private function generateInvoice(): string
    {
        return 'PB-' . now()->format('YmdHis');
    }
}
