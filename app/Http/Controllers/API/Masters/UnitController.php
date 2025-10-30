<?php
namespace App\Http\Controllers\API\Masters;

use App\Http\Controllers\Controller;
use App\Models\ProductUnit;
use Illuminate\Http\Request;

class UnitController extends Controller
{

    public function index(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => ProductUnit::where('store_id', tenant()->id)->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
        ]);

        $unit = ProductUnit::create([
            'name' => $request->name,
            'store_id' => tenant()->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'unit created',
            'data' => $unit,
        ]);
    }

    public function update(Request $request, ProductUnit $unit)
    {
        $request->validate([
            'name' => 'required|string|max:50',
        ]);

        $unit->update([
            'name' => $request->name,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'unit updated',
            'data' => $unit,
        ]);
    }

    public function destroy(Request $request, ProductUnit $unit)
    {
        $unit->delete();

        return response()->json([
            'success' => true,
            'message' => 'unit deleted'
        ]);
    }

}


