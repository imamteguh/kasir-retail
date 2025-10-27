<?php

namespace App\Observers;

use App\Models\Product;

class ProductObserver
{
    public function creating(Product $product): void
    {
        // Pastikan stok default = 0
        if (is_null($product->stock)) {
            $product->stock = 0;
        }
    }
}
