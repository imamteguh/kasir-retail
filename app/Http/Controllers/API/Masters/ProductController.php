<?php
namespace App\Http\Controllers\API\Masters;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::where('store_id', tenant()->id)
            ->with(['category', 'unit'])
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => $products,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'category_id' => 'nullable|exists:product_categories,id',
            'unit_id' => 'nullable|exists:product_units,id',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'min_stock' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data['store_id'] = tenant()->id;
        $data['code'] = 'PRD-' . Str::upper(Str::random(6));

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($data);

        return response()->json([
            'success' => true,
            'message' => 'product created',
            'data' => $product,
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'category_id' => 'nullable|exists:product_categories,id',
            'unit_id' => 'nullable|exists:product_units,id',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'min_stock' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return response()->json([
            'success' => true,
            'message' => 'product updated',
            'data' => $product,
        ]);
    }

    public function destroy(Request $request, Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'product deleted',
        ]);
    }

    public function lowStock()
    {
        $products = Product::where('store_id', tenant()->id)
            ->whereColumn('stock', '<=', 'min_stock')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => $products,
        ]);
    }
}
