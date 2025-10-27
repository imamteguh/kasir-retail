<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Store;
use Symfony\Component\HttpFoundation\Response;

class SetStoreContext
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Jika belum login, lanjutkan saja
        if (!$user) {
            return $next($request);
        }

        // Tentukan toko aktif berdasarkan user
        $store = null;

        if ($user->role === 'owner') {
            // Owner biasanya punya 1 toko
            $store = $user->stores()->first();
        } elseif ($user->role === 'cashier') {
            // Jika kasir, ambil toko dari relasi user
            $store = Store::where('id', session('store_id'))->first();
        }

        if (!$store) {
            return response()->json([
                'message' => 'Toko tidak ditemukan atau belum dikonfigurasi.'
            ], 403);
        }

        // Simpan store_id ke session dan service container
        session(['store_id' => $store->id]);
        app()->instance('store', $store);

        return $next($request);
    }
}
