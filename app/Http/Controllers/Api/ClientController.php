<?php

namespace App\Http\Controllers\API;

use App\Models\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class ClientController extends Controller
{
    
    public function index()
    {
        $clients = Client::all();

        return response()->json($clients);
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:clients,email'],
        ]);

        $client = Client::create($validated);

        return response()->json($client, 201);
    }
}
