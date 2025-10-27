<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckStoreAccess
{
    public function handle(Request $request, Closure $next)
    {
        $storeId = $request->route('store_id');
        $user = Auth::user();

        if (!$user->stores()->where('id', $storeId)->exists()) {
            return response()->json(['message' => 'Anda tidak memiliki akses ke toko ini.'], 403);
        }

        session(['store_id' => $storeId]);
        app()->instance('store', $user->stores()->find($storeId));

        return $next($request);
    }
}
