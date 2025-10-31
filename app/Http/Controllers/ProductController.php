<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['supplier', 'productCategory'])->get();
        return Inertia::render('Product/index', [
            'products' => $products
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
            'ref' => ['required', 'string', 'max:50', 'unique:products,ref'],
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
        //
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
            'ref' => ['required', 'string', 'max:50', 'unique:products,ref,' . $product->id],
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
