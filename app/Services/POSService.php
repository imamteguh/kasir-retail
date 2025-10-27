<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class POSService
{
    /**
     * Buat transaksi penjualan baru
     */
    public function createSale(array $data)
    {
        return DB::transaction(function () use ($data) {
            $invoice = $data['invoice_number'] ?? $this->generateInvoice();
            $sale = Sale::create([
                'store_id'        => $data['store_id'],
                'invoice_number'  => $invoice,
                'date'            => now(),
                'discount'        => $data['discount'] ?? 0,
                'payment_method'  => $data['payment_method'] ?? 'cash',
                'total'           => 0,
                'created_by'      => $data['created_by'],
            ]);

            $total = 0;
            foreach ($data['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $subtotal = $item['qty'] * $item['price'];
                $total += $subtotal;

                SaleItem::create([
                    'sale_id'    => $sale->id,
                    'product_id' => $item['product_id'],
                    'qty'        => $item['qty'],
                    'price'      => $item['price'],
                    'subtotal'   => $subtotal,
                ]);
            }

            $sale->update(['total' => $total - ($data['discount'] ?? 0)]);

            return $sale;
        });
    }

    /**
     * Generate invoice nomor otomatis
     */
    private function generateInvoice(): string
    {
        return 'SL-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(4));
    }

    /**
     * Hitung total keuntungan per transaksi
     */
    public function calculateProfit(Sale $sale): float
    {
        $profit = 0;

        foreach ($sale->items as $item) {
            $profit += ($item->price - $item->product->cost_price) * $item->qty;
        }

        return $profit;
    }
}
