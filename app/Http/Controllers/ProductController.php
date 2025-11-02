<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        // Get current filters from the request
        $filters = $request->only(['search']);

        $products = Product::with(['productCategory', 'supplier'])
            // Apply search filter if present
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    // Search by product name 
                    $query->where('name', 'like', '%' . $search . '%');
                })
                    // Search in related Product Category name
                    ->orWhereHas('productCategory', function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%');
                    })
                    // Search in related Supplier name
                    ->orWhereHas('supplier', function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%');
                    });
            })
            // Sort by most recently created products
            ->latest()
            // Paginate the results (12 items per page is suitable for the card grid view)
            ->paginate(12)
            // Keep the search filter in the pagination links
            ->withQueryString();

        // Pass the paginated data and the current filters to the Inertia view
        return Inertia::render('Product/index', [
            'products' => $products,
            'filters' => $filters,
        ]);
    }

    public function apiIndex(Request $request)
    {
        $filters = $request->only(['search']);

        $products = Product::query()
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        // Return JSON data (not Inertia)
        return response()->json([
            'data' => $products->items(),
            'next_page_url' => $products->nextPageUrl(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Product/create', [
            'suppliers' => Supplier::all(),
            'categories' => ProductCategory::all(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'supplier_price' => ['nullable', 'numeric', 'min:0'],
            'delivery_time' => ['nullable', 'integer', 'min:0'],
            'min_qty' => ['nullable', 'integer', 'min:0'],
            'max_qty' => ['nullable', 'integer', 'min:0'],
            'note' => ['nullable', 'string'],
            'img' => ['nullable', 'string'],
            'product_category_id' => ['required', 'integer', 'exists:product_categories,id'],
            'supplier_id' => ['required', 'integer', 'exists:suppliers,id'],
        ]);

        Product::create($data);

        return to_route('product.index');
    }

    public function show(Product $product)
    {
        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
        ]);
    }

    public function edit(Product $product)
    {
        return Inertia::render('Product/create', [
            'product' => $product,
            'suppliers' => Supplier::all(),
            'categories' => ProductCategory::all(),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $product->update($request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'supplier_price' => ['nullable', 'numeric', 'min:0'],
            'delivery_time' => ['nullable', 'integer', 'min:0'],
            'min_qty' => ['nullable', 'integer', 'min:0'],
            'max_qty' => ['nullable', 'integer', 'min:0'],
            'note' => ['nullable', 'string'],
            'img' => ['nullable', 'string'],
            'product_category_id' => ['required', 'integer', 'exists:product_categories,id'],
            'supplier_id' => ['required', 'integer', 'exists:suppliers,id'],
        ]));

        return to_route('product.index');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return to_route('product.index');
    }
}
