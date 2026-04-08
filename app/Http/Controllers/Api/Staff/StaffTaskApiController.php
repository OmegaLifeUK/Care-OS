<?php

namespace App\Http\Controllers\Api\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\Staff\StaffTaskService;

class StaffTaskApiController extends Controller
{
    protected $taskService;
    public function __Construct(StaffTaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:user,id',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }
        $staffTask = $this->taskService->apiList($request->all());
        $allStaffTask = $staffTask['all'];
        $pendingStaffTask = $staffTask['pending'];
        $completedStaffTask = $staffTask['completed'];
        $pagination = $staffTask['pagination'];
        // return response()->json(['data'=>$staffTask->items()]);

        return response()->json([
            'success' => true,
            'message' => 'Staff Task List',
            'all_data' => $allStaffTask,
            'pending_data' => $pendingStaffTask,
            'completed_data' => $completedStaffTask,
            'pagination' => $pagination
        ]);
    }
    public function details(Request $req)
    {
        $validator = Validator::make($req->all(), ['id' =>  'required|exists:staff_tasks,id']);
        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }
        $val = $this->taskService->details($req->id);
        $priority_array = [
            '1' => 'Low',
            '2' => 'Medium',
            '3' => 'High',
            '4' => 'Urgent',
        ];
        $status_array = [
            '0' => 'Pending',
            '1' => 'Completed',
            '2' => 'In-Progress',
            '3' => 'Resolved',
            '4' => 'Closed',
        ];
        $allArr = [
            'id' => $val->id,
            'user_id' => $val->user_id,
            'task_type_id' => $val->task_type_id,
            'task_type_name' => "Assessment",
            'title' => $val->title,
            'assign_to' => $val->assign_to,
            'assign_to_user' => $val->assigns->name ?? "",
            'staff_member' => $val->staff_member,
            'staff_member_name' => $val->staffMembers->name ?? "",
            'form_template_id' => $val->form_template_id,
            'due_date' => date("d M Y", strtotime($val->due_date)),
            'scheduled_date' => $val->scheduled_date,
            'scheduled_time' => $val->scheduled_time,
            'priority' => $priority_array[$val->priority] ?? '',
            'description' => $val->description,
            'complete_notes' => $val->complete_notes ?? "",
            'status' => $status_array[$val->status] ?? '',
             'statusCode' => $val->status,
             'comments' => $val->comments ?? '',
        ];
        return response()->json([
            'status'  => true,
            'message' => 'Task Details',
            'data' => $allArr,
        ]);
    }
      public function add_comment(Request $req)
    {
        try {
            $validator = Validator::make(
                $req->all(),
                [
                    'id' => 'required',
                    'comment' => 'required'
                ]
            );
            if ($validator->fails()) {
                return response()->json([
                    'status'  => false,
                    'message' => $validator->errors()->first(),
                ], 422);
            }
            $data = $this->taskService->add_comment($req->all());
            if ($data['status']) {
                return response()->json([
                    'status'  => true,
                    'message' => $data['message']
                ]);
            }
            return response()->json([
                'status'  => false,
                'message' => $data['message']
            ], 422);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
    public function status_change(Request $req)
    {
        try {
            $validator = Validator::make(
                $req->all(),
                [
                    'id' => 'required',
                    'status' => 'required'
                ]
            );
            if ($validator->fails()) {
                return response()->json([
                    'status'  => false,
                    'message' => $validator->errors()->first(),
                ], 422);
            }
            $data = $this->taskService->status_change($req->all());
            if ($data['status']) {
                return response()->json([
                    'status'  => true,
                    'message' => $data['message']
                ]);
            }
            return response()->json([
                'status'  => false,
                'message' => $data['message']
            ], 422);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
