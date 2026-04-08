<?php

namespace App\Http\Controllers\frontEnd\Roster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth,DB,Session;
use Illuminate\Support\Facades\Validator;
use App\Services\Staff\StaffReportIncidentService;
use App\ServiceUser;
use App\Models\IncidentType;
use App\Models\Staff\SafeguardingType;
class IncidentManagementController extends Controller
{
    protected $incidentService;

    public function __construct(StaffReportIncidentService $incidentService)
    {
        $this->incidentService = $incidentService;
    }
    public function index(Request $request)
    {
        $home_ids = Auth::user()->home_id;
		$ex_home_ids = explode(',', $home_ids);
		$home_id = $ex_home_ids[0];

        $data['client'] = ServiceUser::select('id','home_id','earning_scheme_label_id','name','user_name','phone_no','date_of_birth','child_type','room_type','current_location','street','care_needs','status','is_deleted')
        ->where(['home_id'=>$home_id,'is_deleted'=>0,'status'=>1])->get();
        $data['incident_type'] = IncidentType::where('status',1)->where('home_id', $home_id)->get();
        $data['safeguard_type'] = SafeguardingType::where('status', 1)->where('home_id', $home_id)->get();
        // echo "<pre>";print_r($data['incidents']);die;
        return view('frontEnd.roster.incident_management.incident',$data);
    }
    public function ai_prevention()
    {
        return view('frontEnd.roster.incident_management.ai_prevention');
    }
    public function incident_report_details($id)
    {
        return view('frontEnd.roster.incident_management.incident_report_details');
    }
    public function incident_report_save(Request $request){
        // echo "<pre>";print_r($request->all());die;
        $validator = Validator::make($request->all(), [
            'incident_type_id'  => 'required|integer',
            'severity_id'       => 'required|integer',
            'client_id'         => 'required|integer',
            'date_time'       => 'required|date',
            'location'          => 'required|string',
            'what_happened'     => 'required|string',
            'immediate_action'  => 'required|string',
        ]);

        if ($validator->fails()) {
            Session::flash('error',$validator->errors()->first());
            return redirect()->back();
        }
        try {
            $home_ids = Auth::user()->home_id;
            $ex_home_ids = explode(',', $home_ids);
            $home_id = $ex_home_ids[0];
            $requestData = $request->all();
            $requestData['home_id'] = $home_id;
             $requestData['user_id'] = Auth::user()->id;
           
            $incident = $this->incidentService->store($requestData);
            Session::flash('success','Incident Report saved successfully');
            return redirect()->back();

        } catch (Exception $e) {
            Session::flash('error',$e->getMessage());
            return redirect()->back();
        }
    }
    public function incidentReportLoadData(Request $request){
        // echo "<pre>";print_r($request->all());die;
        // $requestData = $request->all();
        // $requestData['user_id'] = Auth::user()->id;
         $home_ids = Auth::user()->home_id;
         $user_id = Auth::user()->user_type != 'M' ? Auth::user()->id : "";
        $ex_home_ids = explode(',', $home_ids);
        $home_id = $ex_home_ids[0];
        $requestData = $request->all();
        $requestData['home_id'] = $home_id;
        //$requestData['user_id'] = $user_id;
        // return $requestData;
        $incidents = $this->incidentService->list($requestData);
        return response()->json([
            'success'=>true,
            'message'=>'Incident Report List',
            'data'=>$incidents->items(),
            'pagination' => [
                    'next_page_url' => $incidents->nextPageUrl(),
                    'prev_page_url' => $incidents->previousPageUrl(),
                ]
        ]);
    }
}