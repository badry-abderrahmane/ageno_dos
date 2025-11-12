<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class SupplierController extends Controller
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

        $suppliers = Supplier::query()
            // Apply search filter if present
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->whereRaw('LOWER(name) like ?', "%" . strtolower($search) . "%");
            })
            // Sort by most recently created suppliers
            ->latest()
            // Paginate the results (12 items per page is suitable for the card grid view)
            ->paginate(12)
            // Keep the search filter in the pagination links
            ->withQueryString();

        // Pass the paginated data and the current filters to the Inertia view
        return Inertia::render('Supplier/index', [
            'suppliers' => $suppliers,
            'filters' => $filters,
        ]);
    }

    public function apiIndex(Request $request)
    {
        $filters = $request->only(['search']);

        $suppliers = Supplier::query()
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->whereRaw('LOWER(name) like ?', "%" . strtolower($search) . "%");
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        // Return JSON data (not Inertia)
        return response()->json([
            'data' => $suppliers->items(),
            'next_page_url' => $suppliers->nextPageUrl(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Supplier/create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'contact' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
        ]);

        Supplier::create($validatedData);

        return to_route('supplier.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier)
    {
        return response()->json([
            'id' => $supplier->id,
            'name' => $supplier->name,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier)
    {
        return Inertia::render('Supplier/create', [
            'supplier' => $supplier,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supplier $supplier)
    {
        $supplier->update($request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'contact' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
        ]));

        return to_route('supplier.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return to_route('supplier.index');
    }
}
