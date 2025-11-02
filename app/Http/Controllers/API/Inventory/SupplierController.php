<?php

namespace App\Http\Controllers\API\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => Supplier::where('store_id', tenant()->id)->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
        ]);

        $supplier = Supplier::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'store_id' => tenant()->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'supplier created',
            'data' => $supplier,
        ]);
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'name' => 'required|string|max:100',
        ]);

        $supplier->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'supplier updated',
            'data' => $supplier,
        ]);
    }

    public function destroy(Request $request, Supplier $supplier)
    {
        $supplier->delete();

        return response()->json([
            'success' => true,
            'message' => 'supplier deleted'
        ]);
    }
}
