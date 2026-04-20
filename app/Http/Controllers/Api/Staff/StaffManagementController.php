<?php

namespace App\Http\Controllers\Api\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\DB;
use App\Models\staffManagement\sosAlert;
use App\User;
use App\Notification;

class StaffManagementController extends Controller
{
    public function sos_alert(Request $request){
        $validator = Validator::make($request->all(), [
            'staff_id'   => 'required|integer|exists:user,id',
            'location'   => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
            ]);
        }
        try{
            $staffDetails=User::where(['id'=>$request->staff_id,'user_type'=>'N'])->first();
            if(empty($staffDetails)){
                return response()->json([
                    'status'  => false,
                    'message' => "Staff not found!",
                ]);
            }
            $managers = User::where('user_type', 'M')
                ->where('status',1)
                ->where('is_deleted',0)
                ->whereRaw('FIND_IN_SET(?, home_id)', [$staffDetails->home_id])
                ->get();
            if(count($managers) > 0){
                DB::beginTransaction();
                $sosAlertRecord = New sosAlert;
                $sosAlertRecord->staff_id = $request->staff_id;
                $sosAlertRecord->location = $request->location;
                if($sosAlertRecord->save()){
                    foreach($managers as $val){
                        $notification = new Notification;
                        $notification->home_id = $staffDetails->home_id;
                        $notification->user_id = $val->id;
                        $notification->event_id = $sosAlertRecord->id;
                        $notification->notification_event_type_id = 24;
                        $notification->event_action = 'SOS_ALERT';
                        $notification->message = $staffDetails->name.' need help!';
                        $notification->is_sticky = 1;
                        $notification->save();
                    }
                    DB::commit();
                    return response()->json([
                        'status'  => true,
                        'message' => "Your request has been sent to manager",
                        'data'=>json_decode('{}')
                    ]);
                }else{
                    return response()->json([
                        'status'  => false,
                        'message' => "Something went wrong!",
                    ]);
                }
            }else{
                return response()->json([
                    'status'  => false,
                    'message' => "No managers allot for this home!",
                ]);
            }
        }catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again.',
            ], 500);
        }
    }
}
