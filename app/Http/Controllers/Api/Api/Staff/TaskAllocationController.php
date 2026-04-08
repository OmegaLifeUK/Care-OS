<?php

namespace App\Http\Controllers\Api\Staff;

use Carbon\Carbon;
use Illuminate\Support\Str;
use App\StaffTaskAllocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\StaffManagementController;

class TaskAllocationController extends StaffManagementController
{

    public function index($staff_member_id) //mk
    {
        try {
            $today = date('Y-m-d');

            $tasks = StaffTaskAllocation::select('id', 'title', 'details', 'is_completed', 'comment', 'created_at',  'updated_at')
                ->where('is_deleted', '0')
                ->where('is_completed', 0)
                ->where('staff_member_id', $staff_member_id)
                ->orderBy('staff_task_allocation.id', 'desc')
                ->orderBy('staff_task_allocation.created_at', 'desc')
                ->get();

            $completedtasks = StaffTaskAllocation::select('id', 'title', 'details', 'is_completed', 'comment',  'created_at', 'updated_at')
                ->where('is_deleted', '0')
                ->where('is_completed', '1')
                ->where('staff_member_id', $staff_member_id)
                ->orderBy('staff_task_allocation.id', 'desc')
                ->orderBy('staff_task_allocation.created_at', 'desc')
                ->get();
            $tasks = json_decode(json_encode($tasks), true);
            $completedtasks = json_decode(json_encode($completedtasks), true);
            // echo '<pre>'; print_r($tasks); die;

            $daily_tasks = array();
            $completed_tasks_arr = array();
            if (!empty($tasks)) {
                $tasks = $this->replace_null($tasks);
                $pre_date = date('Y-m-d', strtotime($tasks[0]['created_at']));
                $i = 0;
                foreach ($tasks as $key => $value) {
                    $current_date = date('Y-m-d', strtotime($value['created_at']));
                    $arr = [
                        'id' => $value['id'],
                        'title' => ucfirst($value['title']),
                        'details' => Str::words(ucfirst($value['details']), 6),
                        'full_details' => $value['details'],
                        'is_completed' => $value['is_completed'],
                        'comment' => $value['comment'],
                        'created_at' => Carbon::parse($value['created_at'])->format('d M,Y H:i:s'),
                        'updated_at' => Carbon::parse($value['updated_at'])->format('d M,Y H:i:s'),
                    ];
                    if ($pre_date == $current_date) {
                        $daily_tasks[$i]['date']      = date('d F Y', strtotime($value['created_at']));
                        $daily_tasks[$i]['records'][] = $arr;
                    } else {
                        // print_r($value);
                        $i++;
                        $daily_tasks[$i]['date']      = date('d F Y', strtotime($value['created_at']));
                        $daily_tasks[$i]['records'][] = $arr;
                        $pre_date                     = $current_date;
                    }
                }
            }
            if (!empty($completedtasks)) {
                $completedtasks = $this->replace_null($completedtasks);
                $pre_date = date('Y-m-d', strtotime($completedtasks[0]['created_at']));
                $i = 0;

                foreach ($completedtasks as $key => $value) {
                    $arr = [
                        'id' => $value['id'],
                        'title' => ucfirst($value['title']),
                        'details' => Str::words(ucfirst($value['details']), 6),
                        'full_details' => $value['details'],
                        'is_completed' => $value['is_completed'],
                        'comment' => $value['comment'],
                        'created_at' => Carbon::parse($value['created_at'])->format('d M,Y H:i:s'),
                        'updated_at' => Carbon::parse($value['updated_at'])->format('d M,Y H:i:s'),
                    ];
                    $current_date = date('Y-m-d', strtotime($value['created_at']));
                    if ($pre_date == $current_date) {
                        $completed_tasks_arr[$i]['date']      = date('d F Y', strtotime($value['created_at']));
                        $completed_tasks_arr[$i]['records'][] = $arr;
                    } else {
                        $i++;
                        $completed_tasks_arr[$i]['date']      = date('d F Y', strtotime($value['created_at']));
                        $completed_tasks_arr[$i]['records'][] = $arr;
                        $pre_date                     = $current_date;
                    }
                }
            }

            return json_encode(array(
                'status' => true,
                'message' => "Activity list.",
                'data' => $daily_tasks,
                'completedtasks' => $completed_tasks_arr

            ));
            if (!empty($daily_tasks)) {
                // return json_encode(array(
                //     'result' => array(
                //         'response' => true,
                //         'message' => "Activity list.",
                //         'data' => $daily_tasks,
                //         'completedtasks' => $completed_tasks_arr
                //     )
                // ));
            } else {
                // return json_encode(array(
                //     'result' => array(
                //         'response' => false,
                //         'message' => "No activity found.",
                //         //'data' => $daily_records
                //     )
                // ));
                return json_encode(array(
                    'status' => false,
                    'message' => "No activity found.",
                    //'data' => $daily_records

                ));
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong:' . $e->getMessage()
            ]);
        }
    }

    public function update_activity_status(Request $req)
    {
        try {
            $validator = Validator::make($req->all(), [
                'staff_member_id'   => 'required',
                'daily_activity_id' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => false,
                    'message' => $validator->errors()->first(),
                ], 422);
            }
            $staff_member_id = $req->staff_member_id;
            $daily_activity_id = $req->daily_activity_id;
            $task_record = StaffTaskAllocation::where('id', $daily_activity_id)
                ->where('staff_member_id', $staff_member_id)
                ->where('is_deleted', '0')
                ->where('is_completed', 0)
                ->first();

            if (!isset($task_record)) {
                return response()->json([
                    'response' => false,
                    'message' => "Invalid Activity Id"
                ]);
            }
            $task_record->is_completed = 1;
            $task_record->save();
            return response()->json([
                'status' => true,
                'message' => 'Activity Marked As Completed'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' =>  'Something went wrong!!'
            ]);
        }
    }

    public function add_comment(Request $req)
    {
        try {
            $validator = Validator::make($req->all(), [
                'staff_member_id'   => 'required',
                'daily_activity_id' => 'required',
                'comment' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => false,
                    'message' => $validator->errors()->first(),
                ], 422);
            }
            $staff_member_id = $req->staff_member_id;
            $daily_activity_id = $req->daily_activity_id;
            $comments = $req->comment;
            $task_record = StaffTaskAllocation::where('id', $daily_activity_id)
                ->where('staff_member_id', $staff_member_id)
                ->where('is_deleted', '0')
                ->first();

            if (!isset($task_record)) {
                return response()->json([
                    'response' => false,
                    'message' => "Invalid Activity Id"
                ]);
            }
            $task_record->comment = $comments;
            $task_record->save();
            return response()->json([
                'status' => true,
                'message' => 'Comment add successfully !!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' =>  'Something went wrong!! :' . $e->getMessage()
            ]);
        }
    }
}
