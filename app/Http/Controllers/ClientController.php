<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get the search term from the request, default to null
        $search = $request->input('search');

        // Build the query
        $clients = Client::query()
            ->when($search, function ($query, $search) {
                // Apply the search filter if a term is present
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('ice', 'like', "%{$search}%"); // Add more columns as needed
            })
            // Paginate the results (e.g., 10 per page)
            // ->withQueryString() is crucial to preserve the search filter when paginating
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Client/index', [
            'clients' => $clients, // Pass the paginated object
            'filters' => [
                'search' => $search, // Pass the current search term back to the component
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Client/create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'max:250'],
            'ice' => ['required', 'min:15,max:15', 'digits:15'],
        ]);
        $data = array_merge($validatedData, ['user_id' => Auth::id()]);
        Client::create($data);

        return to_route('client.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
    {
        return Inertia::render('Client/create', [
            'client' => $client
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client)
    {
        $client->update($request->validate([
            'name' => ['required', 'max:250'],
            'ice' => ['required', 'min:15,max:15', 'digits:15'],
        ]));

        return to_route('client.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        $client->delete();

        return to_route('client.index');
    }
}
