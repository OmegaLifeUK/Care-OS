<?php

namespace App\Http\Controllers\Api\Staff;

use Carbon\Carbon;
use App\ServiceUser;
use App\Models\IncidentType;
use Illuminate\Http\Request;
use App\Models\Staff\SafeguardingType;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Services\Staff\StaffReportIncidentService;

class IncidentManagementApi extends Controller
{
    protected $incidentService;

    public function __construct(StaffReportIncidentService $incidentService)
    {
        $this->incidentService = $incidentService;
    }
    public function index(Request $request)
    {
        $requestData = $request->all();
        $incidents = $this->incidentService->list($requestData);
        $data = [];
        $statusArr = [
            1 => 'Reported',
            2 => 'Under Investigation',
            3 => 'Resolved',
            4 => 'Closed',
            null => 'Pending'
        ];
        $severityArr = [
            1 => 'Low',
            2 => 'Medium',
            3 => 'High',
            4 => 'Critical',
        ];
        
        foreach ($incidents as $item) {
            $data[] = [
                'id' => $item->id,
                'home_id' => $item->home_id,
                'user_id' => $item->user_id,
                'ref' => $item->ref,
                'client_name' => $item->clients->name ?? "",
                'location' => $item->location ?? "",
                'location_detail' => $item->location_detail ?? "",
                'what_happened' => $item->what_happened ?? "",
                'incident_type_name' => $item->incidentType->type ?? "",
                'location_detail' => $item->location_detail ?? "",
                'is_safeguarding' => $item->is_safeguarding,
                'family_notify' => $item->family_notify,
                'cqcNotification' => $item->cqcNotification,
                'policeInvolved' => $item->policeInvolved,
                'date_time' => Carbon::parse($item->date_time)->format('l, F d, Y \a\t H:i'),
                'status' => $item->status,
                'statusText' => $statusArr[$item->status],
                'severity_id' => $item->severity_id,
                'severityText' => $severityArr[$item->severity_id],
            ];
        }
        return response()->json([
            'success' => true,
            'message' => 'Incident Management List',
            'data' => $data,
            'pagination' => [
                'total' => $incidents->total(),
                'per_page' => $incidents->perPage(),
                'current_page' => $incidents->currentPage(),
                'total_pages' => $incidents->lastPage(),
            ]
        ]);
    }
    public function details(Request $req)
    {
        try {
            $validator = Validator::make($req->all(), [
                'id' => 'required|exists:staff_report_incidents,id',
            ], ['id.exists' => 'Data Not Found !!']);
            if ($validator->fails()) {
                return response()->json([
                    'status'  => false,
                    'message' => $validator->errors()->first(),
                ], 422);
            }

            $item = $this->incidentService->report_details($req->id);
            if (!$item) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Data Not Found !!'
                ], 422);
            }
            $statusArr = [
                1 => 'Reported',
                2 => 'Under Investigation',
                3 => 'Resolved',
                4 => 'Closed',
                null => 'Pending'
            ];
            $severityArr = [
                1 => 'Low',
                2 => 'Medium',
                3 => 'High',
                4 => 'Critical',
            ];
             $safeguarddetailsArr = [];
            foreach ($item->safeguarddetails ?? [] as $items) {
                $safeguarddetailsArr[] = [
                    'id' => $items->id,
                    'home_id' => $items->home_id,
                    'type' => $items->type,
                ];
            }
            $report_details =  [
                'id' => $item->id,
                'home_id' => $item->home_id,
                'user_id' => $item->user_id,
                'client_id' => $item->client_id,
                'incident_type_id' => $item->incident_type_id,
                'ref' => $item->ref,
                'client_name' => $item->clients->name ?? "",
                'location' => $item->location ?? "",
                'location_detail' => $item->location_detail ?? "",
                'what_happened' => $item->what_happened ?? "",
                'immediate_action' => $item->immediate_action ?? "",
                'location_detail' => $item->location_detail ?? "",
                'investigation_findings' => $item->investigation_findings ?? "",
                'incident_type_name' => $item->incidentType->type ?? "",
                'lessons_learned' => $item->lessons_learned ?? "",
                'resolution_notes' => $item->resolution_notes ?? "",
                'is_safeguarding' => $item->is_safeguarding,
                'family_notify' => $item->family_notify,
                'cqcNotification' => $item->cqcNotification,
                'policeInvolved' => $item->policeInvolved,
                'date_time' => Carbon::parse($item->date_time)->format('l, F d, Y \a\t H:i'),
                'status' => $item->status,
                'statusText' => $statusArr[$item->status],
                'severity_id' => $item->severity_id,
                'severityText' => $severityArr[$item->severity_id],
                'safeguarddetails' => $safeguarddetailsArr
            ];
            return response()->json([
                'status' => true,
                'message' => 'Incident Management Details',
                'data' => $report_details
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Internal Server Error !!'
            ], 500);
        }
    }
    
    public function add_incident(Request $req)
    {
        try { // echo "<pre>";print_r($request->all());die;
            $validator = Validator::make($req->all(), [
                'home_id'  => 'required|integer',
                'user_id'  => 'required|integer',
                'is_safeguarding' => 'required|integer|in:0,1',
                'incident_type_id'  => 'required|integer|exists:incident_types,id',
                'severity_id'       => 'required|integer|in:1,2,3,4',
                'client_id'         => 'required|integer',
                'date_time'       => 'required|date',
                'location'          => 'required|string',
                'location_detail'          => 'nullable|string',
                'safeguarding_detail' => 'nullable|array',
                'what_happened'     => 'required|string',
                'immediate_action'  => 'required|string',
                'family_notify' => 'required|integer|in:0,1',
                'cqcNotification' => 'required|integer|in:0,1',
                'policeInvolved' => 'required|integer|in:0,1',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                ], 422);
            }
            try {
                $home_id = $req->home_id;
                $user_id = $req->user_id;
                $requestData = $req->all();
                $requestData['home_id'] = $home_id;
                $requestData['user_id'] = $user_id;
                $this->incidentService->store($requestData);
                return response()->json([
                    'success' => true,
                    'message' => 'Incident saved succcessfully !!',
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
            }
        } catch (\Exception $e) {
        }
    }

     public function incident_type_list(Request $req)
    {
        $incident_type_sub = IncidentType::select('id', 'home_id', 'type');
        if (isset($req->home_id)) {
            $incident_type_sub->where('home_id', $req->home_id);
        }
        $incident_type =  $incident_type_sub->where('status', 1)->get();
        return response()->json([
            'success' => true,
            'message' => 'Incident Type List',
            'data' => $incident_type,
        ]);
    }
    public function client_list(Request $req)
    {
        $client_list = ServiceUser::select(
            'id',
            'home_id',
            'name',
            'user_name',
        )
            ->where([
                'home_id' => $req->home_id,
                'is_deleted' => 0,
                'status' => 1
            ])->get();

        return response()->json([
            'success' => true,
            'message' => 'Client List',
            'data' => $client_list,
        ]);
    }
    public function saverity_list(Request $req)
    {
        $severityArr = [
            ['id' => 1, 'type' => 'Low'],
            ['id' => 2, 'type' => 'Medium'],
            ['id' => 3, 'type' => 'High'],
            ['id' => 4, 'type' => 'Critical'],
        ];

        return response()->json([
            'success' => true,
            'message' => 'Saverity List',
            'data' => $severityArr,
        ]);
    }
    public function safegaurding_list(Request $req)
    {

        $incident_type_sub = SafeguardingType::select('id', 'home_id', 'type');
        if (isset($req->home_id)) {
            $incident_type_sub->where('home_id', $req->home_id);
        }
        $severityArr =  $incident_type_sub->where('status', 1)->get();

        return response()->json([
            'success' => true,
            'message' => 'Safegaurding List',
            'data' => $severityArr,
        ]);
    }
}
