<?php
namespace App\Http\Controllers\API\Masters;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => ProductCategory::where('store_id', tenant()->id)->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
        ]);

        $category = ProductCategory::create([
            'name' => $request->name,
            'store_id' => tenant()->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'category created',
            'data' => $category,
        ]);
    }

    public function update(Request $request, ProductCategory $category)
    {
        $request->validate([
            'name' => 'required|string|max:100',
        ]);

        $category->update([
            'name' => $request->name,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'category updated',
            'data' => $category,
        ]);
    }

    public function destroy(Request $request, ProductCategory $category)
    {
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'category deleted'
        ]);
    }
}

