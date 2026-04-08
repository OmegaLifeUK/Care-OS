<?php

namespace App\Http\Controllers\Api\Staff;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Models\Staff\StaffSupervision;
use Illuminate\Support\Facades\Validator;
use App\Services\Staff\StaffSupervisionService;

class StaffSupervisionApi extends Controller
{
    protected $supervisions;
    public function __Construct(StaffSupervisionService $supervisions)
    {
        $this->supervisions = $supervisions;
    }

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:user,id',
            'search' => 'nullable',
            'filter' => 'nullable',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
            ], 422);
        } 
        $reqData['assinged_user_id'] = $request->user_id;
        $reqData['search'] = $request->search;
        $reqData['filter'] = $request->filter;
        $list = $this->supervisions->list($reqData);
        // if (!$list['status']) {
        //     return response()->json([
        //         'status'  => false,
        //         'message' => $validator->errors()->first(),
        //     ], 422);
        // }
        $data = $list['data'];
        $pagination = $list['pagination'];
        // return response()->json(['data'=>$staffTask->items()]);

        return response()->json([
            'success' => true,
            'message' => 'Staff Supervision List',
            'data' => $data,
            'pagination' => $pagination
        ]);
    }

    public function details(Request $req)
    {
        try {
            $validator = Validator::make($req->all(), [
                'id' => [
                    'required',
                    Rule::exists('staff_supervisions', 'id')
                        ->whereNull('deleted_at')
                ],
            ], [
                'id.exists' => 'Data Not Found !!',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status'  => false,
                    'message' => $validator->errors()->first(),
                ], 422);
            }
            $data = $this->supervisions->details($req->id);
            if (isset($data['success']) && !$data['success']) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Data Not Found !!',
                ], 422);
            }
            return response()->json([
                'status' => true,
                'message' => 'Supervisions Details',
                'data' => $data

            ]);
        } catch (\Exception $e) {
        }
    }
}
