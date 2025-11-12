<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductCategoryController extends Controller
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

        $categories = ProductCategory::query()
            // Apply search filter if present
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->whereRaw('LOWER(name) like ?', "%" . strtolower($search) . "%");
            })
            // Sort by name for alphabetical display
            ->orderBy('name')
            // Paginate the results (suitable for a card grid)
            ->paginate(12)
            // Keep the search filter in the pagination links
            ->withQueryString();

        // Pass the paginated data and the current filters to the Inertia view
        return Inertia::render('ProductCategory/index', [
            'categories' => $categories,
            'filters' => $filters,
        ]);
    }

    public function apiIndex(Request $request)
    {
        $filters = $request->only(['search']);

        $categories = ProductCategory::query()
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->whereRaw('LOWER(name) like ?', "%" . strtolower($search) . "%");
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        // Return JSON data (not Inertia)
        return response()->json([
            'data' => $categories->items(),
            'next_page_url' => $categories->nextPageUrl(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('ProductCategory/create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:product_categories,name'],
        ]);

        ProductCategory::create($validatedData);

        return to_route('productCategory.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductCategory $category)
    {
        return response()->json([
            'id' => $category->id,
            'name' => $category->name,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductCategory $productCategory)
    {
        return Inertia::render('ProductCategory/create', [
            'productCategory' => $productCategory
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductCategory $productCategory)
    {
        $productCategory->update($request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:product_categories,name,' . $productCategory->id],
        ]));

        return to_route('productCategory.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductCategory $productCategory)
    {
        $productCategory->delete();

        return to_route('productCategory.index');
    }
}
