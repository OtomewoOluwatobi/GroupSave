<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    /**
     * Store a new lead from the waitlist/interest form.
     *
     * POST /leads
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'firstName'  => 'required|string|max:100',
            'lastName'   => 'nullable|string|max:100',
            'email'      => 'required|email|unique:leads,email',
            'useCase'    => 'nullable|string|max:255',
            'pain'       => 'nullable|string|max:255',
            'trust'      => 'nullable|string|max:2000',
            'likelihood' => 'nullable|string|max:100',
        ]);

        $lead = Lead::create([
            'first_name' => $validated['firstName'],
            'last_name'  => $validated['lastName'] ?? null,
            'email'      => $validated['email'],
            'use_case'   => $validated['useCase'] ?? null,
            'pain'       => $validated['pain'] ?? null,
            'trust'      => $validated['trust'] ?? null,
            'likelihood' => $validated['likelihood'] ?? null,
            'ip_address' => $request->ip(),
        ]);

        return response()->json(['message' => 'Success', 'id' => $lead->id], 201);
    }
}
