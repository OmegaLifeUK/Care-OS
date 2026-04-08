<?php

namespace App\Http\Controllers\Api\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\Staff\Documents;
use Illuminate\Support\Facades\Log;

class StaffDocumentController extends Controller
{
    public function document_list(Request $request)
    {
        $validator = Validator::make($request->all(), [
			'user_id'    => 'required|exists:user,id',
		]);
		if ($validator->fails()) {
			return response()->json([
				'success' => false,
				'message'  => $validator->errors()->first(),
                'data'    => array()
			]);
		}
        $user_documents = Documents::where('user_id', $request->user_id)->whereNull('deleted_at')->get();
        $data = $user_documents->map(function($item){
            return [
                    'id'=>$item->id,
                    'user_id'=>$item->user_id,
                    'title'=>$item->title,
                    'date'=>date("d M Y",strtotime($item->created_at)),
                    'file_path'=>asset('storage/app/public/').'/'.$item->file_path,
                ];
        });
        return response()->json(['success'=>true,'message'=>'Document List','data'=>$data]);
    }
    public function saveDouments(Request $request)
    {
         Log::info("Save documents", $request->all());
        // return response()->json(['success' => true,'message'=>'Document saved successfully']);
        // echo "<pre>";print_r($request->all());die;
        $validator = Validator::make($request->all(), [
			'user_id'    => 'required|exists:user,id',
            'document_title' => 'required|string',
            'document_file'  => 'required|file|max:2048',
		]);
		if ($validator->fails()) {
			return response()->json([
				'success' => false,
				'message'  => $validator->errors()->first(),
			]);
		}

        $filePath = $request->file('document_file')->store('staff-documents', 'public');

        Documents::create([
            'user_id'        => $request->user_id,
            'title'          => $request->document_title,
            'file_path'      => $filePath,
        ]);

        return response()->json(['success' => true,'message'=>'Document saved successfully']);
    }
    public function wishUser(){
        return "Welcome User";
    }
}
