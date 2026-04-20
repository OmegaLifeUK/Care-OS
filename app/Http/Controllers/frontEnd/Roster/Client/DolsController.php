<?php

namespace App\Http\Controllers\frontEnd\Roster\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Services\Client\ClientDolsService;

class DolsController extends Controller
{
    protected $ClientDolsService;

    public function __construct(ClientDolsService $ClientDolsService)
    {
        $this->ClientDolsService = $ClientDolsService;
    }

    public function index(Request $request)
    {
        $home_id = explode(',', Auth::user()->home_id)[0];

        $data = [];
        $data['home_id'] = $home_id;
        if ($request->filled('client_id')) {
            $data['client_id'] = $request->input('client_id');
        }

        $dols = $this->ClientDolsService->list($data);
        return response()->json(['success' => true, 'message' => 'DoLS list loaded', 'data' => $dols]);
    }

    public function save_dols(Request $request)
    {
        $rules = [
            'dols_status' => 'required|in:Not Applicable,Screening Required,Application Submitted,Standard Authorisation Granted,Urgent Authorisation Granted,Not Authorised,Under Review,Expired',
            'authorisation_type' => 'nullable|in:Standard,Urgent',
            'referral_date' => 'nullable|date',
            'authorisation_start_date' => 'nullable|date',
            'authorisation_end_date' => 'nullable|date',
            'review_date' => 'nullable|date',
            'supervisory_body' => 'nullable|string|max:255',
            'case_reference' => 'nullable|string|max:255',
            'best_interests_assessor' => 'nullable|string|max:255',
            'mental_health_assessor' => 'nullable|string|max:255',
            'reason_for_dols' => 'nullable|string|max:2000',
            'imca_appointed' => 'nullable|in:0,1',
            'mental_capacity_assessment' => 'nullable|in:0,1',
            'appeal_rights' => 'nullable|in:0,1',
            'care_plan_updated' => 'nullable|in:0,1',
            'family_notified' => 'nullable|in:0,1',
            'additional_notes' => 'nullable|string|max:2000',
            'client_id' => 'required|integer',
        ];

        if ($request->filled('dols_id')) {
            $rules['dols_id'] = 'required|integer|exists:dols,id';
        }

        $validator = \Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()->first()
            ]);
        }

        try {
            $home_id = explode(',', Auth::user()->home_id)[0];

            $requestData = $request->only([
                'dols_status', 'authorisation_type', 'referral_date',
                'authorisation_start_date', 'authorisation_end_date', 'review_date',
                'supervisory_body', 'case_reference', 'best_interests_assessor',
                'mental_health_assessor', 'reason_for_dols', 'imca_appointed',
                'mental_capacity_assessment', 'appeal_rights', 'care_plan_updated',
                'family_notified', 'additional_notes', 'client_id'
            ]);

            $requestData['user_id'] = Auth::user()->id;
            $requestData['id'] = $request->input('dols_id');

            $clientDols = $this->ClientDolsService->store($requestData, (int)$home_id);
            return response()->json(['success' => true, 'message' => 'DoLS record saved successfully', 'data' => $clientDols]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Something went wrong']);
        }
    }

    public function delete(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'id' => 'required|integer|exists:dols,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()->first()
            ]);
        }

        try {
            $home_id = explode(',', Auth::user()->home_id)[0];
            $this->ClientDolsService->delete($request->input('id'), (int)$home_id);
            return response()->json(['success' => true, 'message' => 'DoLS record deleted successfully']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Record not found or access denied']);
        }
    }
}
