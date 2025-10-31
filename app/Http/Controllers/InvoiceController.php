<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
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
        $invoices = Invoice::where('status', 'not_paid')->get();
        return Inertia::render('Invoice/index', [
            'invoices' => $invoices
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function bills()
    {
        $invoices = Invoice::where('status', 'paid')->get();
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
            'clients' => Client::all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'total' => ['required'],
            'client_id' => ['required'],
            'status' => ['required'],
        ]);

        $data = array_merge($validatedData, ['user_id' => Auth::id()]);
        Invoice::create($data);

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
        return Inertia::render('Invoice/create', [
            'invoice' => $invoice,
            'clients' => Client::all()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Invoice $invoice)
    {
        $invoice->update($request->validate([
            'total' => ['required'],
            'client_id' => ['required'],
            'status' => ['required'],
        ]));

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
}
