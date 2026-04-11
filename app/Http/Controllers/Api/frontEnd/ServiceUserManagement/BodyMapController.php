<?php

namespace App\Http\Controllers\Api\frontEnd\ServiceUserManagement;

use App\Http\Controllers\Controller;
use App\Services\BodyMapService;
use App\ServiceUserRisk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BodyMapController extends Controller
{
    protected BodyMapService $service;

    public function __construct()
    {
        $this->service = new BodyMapService();
    }

    public function index($su_risk_id = null)
    {
        $homeId = Auth::user()->home_id;

        $risk = ServiceUserRisk::where('id', $su_risk_id)
            ->where('home_id', $homeId)
            ->first();

        if (!$risk) {
            return response()->json(['success' => false, 'message' => 'Risk not found.'], 404);
        }

        $injuries = $this->service->listForRisk($homeId, $su_risk_id);

        return response()->json(['success' => true, 'data' => $injuries]);
    }

    public function addInjury(Request $request)
    {
        $data = $request->validate([
            'service_user_id' => 'required|integer|exists:service_user,id',
            'su_risk_id'      => 'required|integer|exists:su_risk,id',
            'sel_body_map_id' => 'required|string|max:20',
            'injury_type'     => 'nullable|string|in:bruise,wound,rash,burn,swelling,pressure_sore,other',
            'injury_description' => 'nullable|string|max:1000',
            'injury_date'     => 'nullable|date',
            'injury_size'     => 'nullable|string|max:100',
            'injury_colour'   => 'nullable|string|max:50',
        ]);

        $homeId = Auth::user()->home_id;

        $risk = ServiceUserRisk::where('id', $data['su_risk_id'])
            ->where('home_id', $homeId)
            ->first();

        if (!$risk) {
            return response()->json(['success' => false, 'message' => 'Not authorised.'], 403);
        }

        $injury = $this->service->addInjury($homeId, $data);

        return response()->json(['success' => true, 'id' => $injury->id]);
    }

    public function removeInjury(Request $request)
    {
        $data = $request->validate([
            'injury_id' => 'required|integer',
        ]);

        $homeId = Auth::user()->home_id;

        if (Auth::user()->user_type !== 'A') {
            return response()->json(['success' => false, 'message' => 'Only administrators can remove injuries.'], 403);
        }

        $removed = $this->service->removeInjury($homeId, $data['injury_id']);

        if (!$removed) {
            return response()->json(['success' => false, 'message' => 'Injury not found.'], 404);
        }

        return response()->json(['success' => true]);
    }
}
