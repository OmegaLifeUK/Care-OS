<?php

namespace App\Http\Controllers\Api\Staff;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\ServiceUserPlacementPlan;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\ServiceUserManagement\PlacementPlanComment;

class PlacementPlanTaskController extends Controller
{
    public function index($staff_member_id)
    {
        try {

            // $data = ServiceUserPlacementPlan::select(
            //     'id',
            //     'task',
            //     'description',
            //     'is_recurring',
            //     'date',
            //     'end_date',
            //     'status',
            //     'created_at'
            // )->whereRaw('service_user_id=?', $staff_member_id)
            //     ->get();

            $today = date('Y-m-d');

            $completed_targets = ServiceUserPlacementPlan::select(
                'id',
                'task',
                'description',
                'date',
                'status',
                'created_at'
            )
                ->where('service_user_id', $staff_member_id)
                ->where('status', '1')
                ->get();

            $active_targets = ServiceUserPlacementPlan::select(
                'id',
                'task',
                'description',
                'date',
                'status',
                'created_at'
            )->where('service_user_id', $staff_member_id)
                ->whereDate('date', '>=', $today)
                ->latest()
                ->where('status', '0')
                ->get();

            $pending_targets = ServiceUserPlacementPlan::select(
                'id',
                'task',
                'description',
                'date',
                'status',
                'created_at'
            )->where('service_user_id', $staff_member_id)
                ->whereDate('date', '<=', $today)
                ->where('status', '0')
                ->orderBy('created_at', 'desc')->get();

            // if (empty($active_targets)) {
            //     return response()->json([
            //         'status' => false,
            //         'message' => 'No Tasks Found'
            //     ]);
            // }
            $Ar = [];
            $completedTaskAr = [];
            $pendingTaskAr = [];
            // $active_targets = $this->replace_null($active_targets);
            // $pending_targets = $this->replace_null($pending_targets);
            // $completed_targets = $this->replace_null($completed_targets);
            $i = 0;
            $j = 0;
            $k = 0;
            if (count($active_targets) == 0) {
                $Ar = [];
            } else {
                $pre_date = date('Y-m-d', strtotime($active_targets[0]['created_at']));
                foreach ($active_targets as $key => $value) {
                    $arr = [
                        'id' => $value['id'],
                        'task' => ucfirst($value['task']),
                        'description' => Str::words(ucfirst($value['description']), 6),
                        'full_description' => ucfirst($value['description']),
                        'date' => $value['date'],
                        'status' => $value['status'],
                        'created_at' => Carbon::parse($value['created_at'])->format('d M,Y H:i:s'),
                    ];
                    $current_date = date('Y-m-d', strtotime($value['created_at']));
                    if ($pre_date == $current_date) {

                        $Ar[$i]['date']      = date('d F Y', strtotime($value['created_at']));
                        $Ar[$i]['records'][] = $arr;
                    } else {
                        // print_r($value);
                        $i++;
                        $Ar[$i]['date']      = date('d F Y', strtotime($value['created_at']));
                        $Ar[$i]['records'][] = $arr;
                        $pre_date = $current_date;
                    }
                }
            }
            if (count($pending_targets) == 0) {
                $pendingTaskAr = [];
            } else {
                $pre_date = date('Y-m-d', strtotime($pending_targets[0]['created_at']));
                foreach ($pending_targets as $key => $value) {
                    $arr = [
                        'id' => $value['id'],
                        'task' => ucfirst($value['task']),
                        'description' => Str::words(ucfirst($value['description']), 6),
                        'full_description' => ucfirst($value['description']),
                        'date' => $value['date'],
                        'status' => $value['status'],
                        'created_at' => Carbon::parse($value['created_at'])->format('d M,Y H:i:s'),
                    ];
                    $current_date = date('Y-m-d', strtotime($value['created_at']));
                    if ($pre_date == $current_date) {

                        $pendingTaskAr[$j]['date']      = date('d F Y', strtotime($value['created_at']));
                        $pendingTaskAr[$j]['records'][] = $arr;
                    } else {
                        // print_r($value);
                        $j++;
                        $pendingTaskAr[$j]['date']      = date('d F Y', strtotime($value['created_at']));
                        $pendingTaskAr[$j]['records'][] = $arr;
                        $pre_date = $current_date;
                    }
                }
            }
            if (count($completed_targets) == 0) {
                $completedTaskAr = [];
            } else {
                $pre_date = date('Y-m-d', strtotime($completed_targets[0]['created_at']));
                foreach ($completed_targets as $key => $value) {
                    $arr = [
                        'id' => $value['id'],
                        'task' => ucfirst($value['task']),
                        'description' => Str::words(ucfirst($value['description']), 6),
                        'date' => $value['date'],
                        'status' => $value['status'],
                        'created_at' => Carbon::parse($value['created_at'])->format('d M,Y H:i:s'),
                    ];
                    $current_date = date('Y-m-d', strtotime($value['created_at']));
                    if ($pre_date == $current_date) {

                        $completedTaskAr[$k]['date']      = date('d F Y', strtotime($value['created_at']));
                        $completedTaskAr[$k]['records'][] = $arr;
                    } else {
                        // print_r($value);
                        $k++;
                        $completedTaskAr[$k]['date']      = date('d F Y', strtotime($value['created_at']));
                        $completedTaskAr[$k]['records'][] = $arr;
                        $pre_date = $current_date;
                    }
                }
            }
            return response()->json([
                'status' => true,
                'message' => 'Task List',
                'activetask' => array_values($Ar),
                'completetask' => array_values($completedTaskAr),
                'pendingtask' => array_values($pendingTaskAr)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' =>  'Something went wrong!! :' . $e->getMessage()
            ]);
        }
    }
    public function update_task_status(Request $req)
    {
        try {
            $validator = Validator::make($req->all(), [
                'staff_member_id'   => 'required',
                'task_id' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => false,
                    'message' => $validator->errors()->first(),
                ], 422);
            }
            $staff_member_id = $req->staff_member_id;
            $daily_activity_id = $req->task_id;
            $task_record = ServiceUserPlacementPlan::where('id', $daily_activity_id)
                // ->where('service_user_id', $staff_member_id)
                ->where('status', 0)
                ->first();

            if (!isset($task_record)) {
                return response()->json([
                    'response' => false,
                    'message' => "Invalid task. Or already completed !!"
                ]);
            }
            $task_record->status = 1;
            $task_record->save();
            return response()->json([
                'status' => true,
                'message' => 'Task Marked As Completed'
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
                'task_id' => 'required',
                'comment' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => false,
                    'message' => $validator->errors()->first(),
                ], 422);
            }
            $staff_member_id = $req->staff_member_id;
            $task_id = $req->task_id;
            $comments = $req->comment;
            // $task_record = PlacementPlanComment::where('id', $task_id)
            //     ->where('staff_member_id', $staff_member_id)
            //     ->where('is_deleted', '0')
            //     ->first();

            // if (!isset($task_record)) {
            //     return response()->json([
            //         'response' => false,
            //         'message' => "Invalid Activity Id"
            //     ]);
            // }
            $task_record = new PlacementPlanComment;
            $task_record->user_id = $staff_member_id;
            $task_record->su_placement_plan_id = $task_id;
            $task_record->comments = $comments;
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
