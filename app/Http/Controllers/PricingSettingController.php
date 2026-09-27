<?php

namespace App\Http\Controllers;

use App\Models\PricingSetting;
use App\Services\EstimatorService;
use Illuminate\Http\Request;

class PricingSettingController extends Controller
{
    public function index()
    {
        $settings = PricingSetting::orderBy('group')->orderBy('key')->get();

        return view('dashboard-pricing', compact('settings'));
    }

    /**
     * Bulk-updates the value/confirmed flag of one or more pricing settings.
     * This is the only way the estimator's numbers change: there is nothing
     * hardcoded in the calculation itself.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'settings' => 'required|array|min:1',
            'settings.*.key' => 'required|string|exists:pricing_settings,key',
            'settings.*.value' => 'required|numeric',
            'settings.*.confirmed' => 'required|boolean',
        ]);

        foreach ($validated['settings'] as $row) {
            PricingSetting::where('key', $row['key'])->update([
                'value' => $row['value'],
                'confirmed' => $row['confirmed'],
            ]);
        }

        $settings = PricingSetting::orderBy('group')->orderBy('key')->get();

        return response()->json(['settings' => $settings]);
    }

    /**
     * Lets the owner try the estimator with arbitrary inputs, without
     * touching the public form or creating a lead. Uses the exact same
     * EstimatorService the public booking flow uses, so the numbers always
     * match.
     */
    public function simulate(Request $request)
    {
        $validated = $request->validate([
            'move_type' => 'required|string|in:house,apartment,office,labor_only',
            'bedrooms' => 'required|string|in:studio,1,2,3,4+',
            'origin_floor' => 'required|string|in:ground,1,2,3+',
            'origin_elevator' => 'required|boolean',
            'destination_floor' => 'required|string|in:ground,1,2,3+',
            'destination_elevator' => 'required|boolean',
            'packing_service' => 'required|boolean',
            'special_items' => 'nullable|array',
            'special_items.*' => 'string|in:large_fridge,piano,pool_table,jacuzzi,safe',
            'miles' => 'nullable|numeric|min:0',
        ]);

        $result = EstimatorService::fromDatabase()->estimate($validated);

        return response()->json($result);
    }
}
