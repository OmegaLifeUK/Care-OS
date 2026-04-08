<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth,DB,Session;
use Illuminate\Support\Facades\Validator;
use App\Services\Staff\ClientManagementService;

class medicalLogApiController extends Controller
{
    protected $clientService;

    public function __construct(ClientManagementService $clientService)
    {
        $this->clientService = $clientService;
    }

    public function index(Request $request){
        $validator = Validator::make($request->all(), [
            'child_id'=>'required',
        ]);
        
        if ($validator->fails()) {
            return [
                'success' => false,
                'errors' => $validator->errors()->first()
            ];
        }
        try{
            $medicationLogs = $this->clientService->list($request->all());
            $data = array();
            $status_array=[
                1=>'Administered',
                2=>'Refused',
                3=>'Missed',
                4=>'Not Required'
            ];
            // return $status_array[1];
            foreach($medicationLogs->items() as $val){
                $data[]=[
                    'id'=>$val->id,
                    'home_id'=>$val->home_id,
                    'user_id'=>$val->user_id,
                    'client_id'=>$val->client_id,
                    'medication_name'=>$val->medication_name,
                    'dosage'=>$val->dosage,
                    'frequesncy'=>$val->frequesncy ?? "",
                    'administrator_date'=>date('Y-m-d',strtotime($val->administrator_date)),
                    'administrator_time'=>date('H:i',strtotime($val->administrator_date)),
                    'witnessed_by'=>$val->witnessed_by ?? "",
                    'notes'=>$val->notes ?? "",
                    'side_effect'=>$val->side_effect ?? "",
                    'status'=>$status_array[$val->status],
                ];
            }
            return response()->json([
                'success'=>true,
                'message'=>'Medication Log List',
                'data'=>$data,
                'total'=>$medicationLogs->total(),
                'pagination' => [
                        'next_page_url' => $medicationLogs->nextPageUrl() ?? "",
                        'prev_page_url' => $medicationLogs->previousPageUrl() ?? "",
                    ]
            ]);
        }catch (Exception $e) {
            return response()->json(['success'=>false,'message'=>"Something went wrong",'data'=>$e->getMessage()]);
        }
    }
}
