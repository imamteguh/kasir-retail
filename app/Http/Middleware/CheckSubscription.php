<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\SubscriptionService;

class CheckSubscription
{
    protected $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    public function handle(Request $request, Closure $next): Response
    {
        $store = app('store');

        if (!$store) {
            return response()->json(['message' => 'Konteks toko tidak ditemukan.'], 403);
        }

        $isActive = $this->subscriptionService->isActive($store->id);

        if (!$isActive) {
            return response()->json([
                'message' => 'Langganan toko Anda sudah tidak aktif. Silakan perpanjang langganan.'
            ], 403);
        }

        return $next($request);
    }
}
