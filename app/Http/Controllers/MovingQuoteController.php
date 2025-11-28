<?php

namespace App\Http\Controllers;

use App\Models\MovingQuote;
use Illuminate\Http\Request;
use App\Services\SendGridService;

class MovingQuoteController extends Controller
{
    public function store(Request $request)
    {
        // 🔹 Validación completa (anidada)
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',

            // Direcciones anidadas
            'org_address.formatted' => 'required|string',
            'org_address.raw.location.lat' => 'required|numeric',
            'org_address.raw.location.lng' => 'required|numeric',

            'end_address.formatted' => 'required|string',
            'end_address.raw.location.lat' => 'required|numeric',
            'end_address.raw.location.lng' => 'required|numeric',

            // Otros campos
            'date' => 'required|date',
            'schedule' => 'nullable|string|max:100',

            'move_type' => 'required|string|in:residential,office,storage',
            'origin_floor' => 'nullable|string|max:10',
            'origin_elevator' => 'required|boolean',
            'destination_floor' => 'nullable|string|max:10',
            'destination_elevator' => 'required|boolean',
            'packing_service' => 'required|boolean',
            'comments' => 'nullable|string|max:500',
        ]);

        // 🔹 Extraemos datos formateados para guardar
        $quote = MovingQuote::create([
            'name' => $validated['name'],
            'email' => $validated['email'],

            'origin_address' => $request->input('org_address.formatted'),
            'origin_lat' => $request->input('org_address.raw.location.lat'),
            'origin_lng' => $request->input('org_address.raw.location.lng'),

            'destination_address' => $request->input('end_address.formatted'),
            'destination_lat' => $request->input('end_address.raw.location.lat'),
            'destination_lng' => $request->input('end_address.raw.location.lng'),

            'preferred_date' => $validated['date'],
            'schedule' => $validated['schedule'] ?? null,

            'move_type' => $validated['move_type'],
            'origin_floor' => $validated['origin_floor'] ?? null,
            'origin_elevator' => $validated['origin_elevator'],
            'destination_floor' => $validated['destination_floor'] ?? null,
            'destination_elevator' => $validated['destination_elevator'],
            'packing_service' => $validated['packing_service'],
            'comments' => $validated['comments'] ?? null,
            'status' => 'pending',
        ]);

        $sent = SendGridService::sendLead($quote);


        return response()->json([
            'message' => 'Quote request saved successfully.',
            'email_sent' => $sent,
            'quote' => $quote
        ], 201);
    }
    public function update(Request $request, MovingQuote $movingQuote)
    {
        // Validamos solo el status, porque es lo que viene del modal
        $data = $request->validate([
            'status' => 'required|string|in:pending,in_review,quoted,closed,cancelled',
        ]);

        $movingQuote->status = $data['status'];
        $movingQuote->save();

        return response()->json([
            'message' => 'Quote status updated successfully.',
            'quote'   => $movingQuote,
        ]);
    }
}
