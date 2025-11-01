<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Product;
use App\Services\PdfInvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $invoices = Invoice::with('client')->where('status', 'not_paid')->get();
        return Inertia::render('Invoice/index', [
            'invoices' => $invoices
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function bills()
    {
        $invoices = Invoice::with('client')->where('status', 'paid')->get();
        return Inertia::render('Invoice/bills', [
            'invoices' => $invoices
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Invoice/create', [
            'clients' => Client::all(['id', 'name']),
            'availableProducts' => Product::all(['id', 'name', 'price']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'status' => ['required', 'string'],
            'line_items' => ['required', 'array', 'min:1'],
            'line_items.*.product_id' => ['required', 'exists:products,id'],
            'line_items.*.qty' => ['required', 'numeric', 'min:1'],
            'line_items.*.price' => ['required', 'numeric', 'min:0'],
        ]);

        // Calculate the total server-side for security and integrity
        $total = collect($validatedData['line_items'])->sum(fn($item) => $item['qty'] * $item['price']);

        $invoice = Invoice::create([
            'client_id' => $validatedData['client_id'],
            'status' => $validatedData['status'],
            'total' => $total,
            'user_id' => Auth::id(),
        ]);

        // Prepare data for the pivot table (InvoiceProduct)
        $productsToSync = collect($validatedData['line_items'])->mapWithKeys(function ($item) {
            return [
                $item['product_id'] => [
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                ]
            ];
        })->toArray();

        // Attach products with pivot data
        $invoice->products()->sync($productsToSync);

        return to_route('invoice.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Invoice $invoice)
    {
        // Load products along with pivot data (qty, price) for the edit form
        $invoice->load(['products' => fn($q) => $q->withPivot('qty', 'price')]);

        // Transform the loaded pivot data into the 'line_items' structure expected by the Vue component
        $invoice->line_items = $invoice->products->map(function ($product) {
            return [
                'product_id' => $product->id,
                'qty' => $product->pivot->qty,
                'price' => $product->pivot->price,
            ];
        })->toArray();

        // Remove the products collection before sending to Inertia to avoid confusion
        unset($invoice->products);

        return Inertia::render('Invoice/create', [
            'invoice' => $invoice,
            'clients' => Client::all(['id', 'name']),
            'availableProducts' => Product::all(['id', 'name', 'price']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Invoice $invoice)
    {
        $validatedData = $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'status' => ['required', 'string'],
            'line_items' => ['required', 'array', 'min:1'],
            'line_items.*.product_id' => ['required', 'exists:products,id'],
            'line_items.*.qty' => ['required', 'numeric', 'min:1'],
            'line_items.*.price' => ['required', 'numeric', 'min:0'],
        ]);

        // Calculate the total server-side
        $total = collect($validatedData['line_items'])->sum(fn($item) => $item['qty'] * $item['price']);

        // Update the main Invoice record
        $invoice->update([
            'client_id' => $validatedData['client_id'],
            'status' => $validatedData['status'],
            'total' => $total,
        ]);

        // Prepare data for the pivot table (InvoiceProduct)
        $productsToSync = collect($validatedData['line_items'])->mapWithKeys(function ($item) {
            return [
                $item['product_id'] => [
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                ]
            ];
        })->toArray();

        // Attach products with pivot data, deleting old ones and adding new ones
        $invoice->products()->sync($productsToSync);

        return to_route('invoice.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice)
    {
        $invoice->delete();

        return to_route('invoice.index');
    }

    /**
     * Download the specified invoice as a PDF.
     */
    public function downloadPdf(Invoice $invoice, PdfInvoiceService $pdfService)
    {
        $pdfContent = $pdfService->download($invoice);

        // Naming the file based on the design
        $filename = "Facture-{$invoice->id}-" . date('Y') . ".pdf";

        return response($pdfContent, 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }
}
