<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Observers\ProductObserver;
use App\Observers\PurchaseObserver;
use App\Observers\SaleObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Product::observe(ProductObserver::class);
        Purchase::observe(PurchaseObserver::class);
        Sale::observe(SaleObserver::class);
    }
}
