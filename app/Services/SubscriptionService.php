<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SubscriptionService
{
    /**
     * Aktifkan atau perpanjang langganan toko
     */
    public function activateOrRenew(int $storeId, int $planId)
    {
        $plan = Plan::findOrFail($planId);

        return DB::transaction(function () use ($storeId, $plan) {
            $active = Subscription::where('store_id', $storeId)
                ->where('status', 'active')
                ->latest('end_date')
                ->first();

            $startDate = $active ? Carbon::parse($active->end_date)->addDay() : now();
            $endDate = Carbon::parse($startDate)->addDays($plan->duration_days);

            // Nonaktifkan langganan lama jika ada
            if ($active) {
                $active->update(['status' => 'expired']);
            }

            // Buat langganan baru
            return Subscription::create([
                'store_id'  => $storeId,
                'plan_id'   => $plan->id,
                'start_date'=> $startDate,
                'end_date'  => $endDate,
                'status'    => 'active',
            ]);
        });
    }

    /**
     * Cek apakah langganan masih aktif
     */
    public function isActive(int $storeId): bool
    {
        $subscription = Subscription::where('store_id', $storeId)
            ->where('status', 'active')
            ->whereDate('end_date', '>=', now())
            ->first();

        return $subscription !== null;
    }
}
