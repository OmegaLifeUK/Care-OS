<?php

namespace App\Http\Controllers\Api\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth,DB,Session;
use Illuminate\Support\Facades\Validator;
use App\ServiceUser;
use App\Models\RosterDailyLog;
use App\Models\DailyLogCategory;
use App\Models\DailyLogSubCategory;

class DailyLogApiController extends Controller
{
    public function index(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:user,id',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }
        $date = $request->date ?? now()->toDateString();
        $baseQuery = RosterDailyLog::where('user_id', $request->user_id)
            ->whereDate('date', $date)
            ->when($request->filled('search_dailyLog'), function ($q) use ($request) {
                $q->where('visitor_name', 'like', '%' . $request->search_dailyLog . '%');
            });
        $total= (clone $baseQuery)->count();

        $visitorsCount = (clone $baseQuery)
                    ->whereHas('subCategorys', fn($q)=>$q->where('daily_cat_id',1))
                    ->count();

        $outingsCount= (clone $baseQuery)
                    ->whereHas('subCategorys', fn($q)=>$q->where('daily_cat_id',2))
                    ->count();

        $followUpCount= (clone $baseQuery)
                ->where('available_for_overtime',1)
                ->count();

        $allDataPaginated  = (clone $baseQuery)
                ->with(['subCategorys.dailyLogCategory'])
                ->orderByDesc('id')
                ->paginate(6, ['*'], 'all_pagination');
        $allDataArray = $allDataPaginated->items();
        $allData = $this->prepareData($allDataArray);

        $visitorsPaginated  = (clone $baseQuery)
            ->whereHas('subCategorys', function ($q) {
                $q->where('daily_cat_id', 1);
            })
        ->with(['subCategorys.dailyLogCategory'])
        ->orderByDesc('id')
        ->paginate(6, ['*'], 'visitors_pagination');
        $visitorsDataArray = $visitorsPaginated->items();
        $visitorsData = $this->prepareData($visitorsDataArray);
        
        $outingsPaginated = (clone $baseQuery)
            ->whereHas('subCategorys', function ($q) {
                $q->where('daily_cat_id', 2);
            })
        ->with(['subCategorys.dailyLogCategory'])
        ->orderByDesc('id')
        ->paginate(6, ['*'], 'outings_pagination');
        $outingsDataArray = $outingsPaginated->items();
        $outingsData = $this->prepareData($outingsDataArray);

         $medicalPaginated = (clone $baseQuery)
            ->whereHas('subCategorys', function ($q) {
                $q->where('id', 3);
            })
        ->with(['subCategorys.dailyLogCategory'])
        ->orderByDesc('id')
        ->paginate(6, ['*'], 'medical_pagination');
        $medicalDataArray = $medicalPaginated->items();
        $medicalData = $this->prepareData($medicalDataArray);

        $falmilyPaginated = (clone $baseQuery)
            ->whereHas('subCategorys', function ($q) {
                    $q->where('id', 2);
                })
        ->with(['subCategorys'])
        ->orderByDesc('id')
        ->paginate(6, ['*'], 'family_pagination');
        $falmilyDataArray = $falmilyPaginated->items();
        $falmilyData = $this->prepareData($falmilyDataArray);

       return response()->json([
            'success'=>true,
            'message'=>'Daily Log',
            'total'=>$total,
            'visitorsCount'=>$visitorsCount,
            'outingsCount'=>$outingsCount,
            'followUpCount'=>$followUpCount,
            'allData'=>$allData,
            'visitorsData'=>$visitorsData,
            'outingsData'=>$outingsData,
            'medicalData'=>$medicalData,
            'falmilyData'=>$falmilyData,
            'pagination' => [
                'all_pagination' => $this->simplePagination($allDataPaginated),
                'visitors_pagination' => $this->simplePagination($visitorsPaginated),
                'outings_pagination' => $this->simplePagination($outingsPaginated),
                'medical_pagination' => $this->simplePagination($medicalPaginated),
                'family_pagination' => $this->simplePagination($falmilyPaginated),
            ],
        ]);
    }
    public function prepareData($queryData){
        $data = array();
        foreach($queryData as $aval){
            $data[] = [
                'id'=>$aval->id,
                'home_id'=>$aval->home_id,
                'user_id'=>$aval->user_id,
                'date'=>date("d M Y",strtotime($aval->date)),
                'visitor_name'=>$aval->visitor_name,
                'purpose_visit'=>$aval->purpose_visit ?? "",
                'client_id'=>$aval->client_id,
                'arrival_time'=>date("H:i",strtotime($aval->arrival_time)) ?? "",
                'departure_time'=>date("H:i",strtotime($aval->departure_time)) ?? "",
                'notes'=>$aval->notes ?? "",
                'is_follow_up'=>$aval->available_for_overtime,
                'follow_up_message'=>$aval->follow_details ?? "",
                'risk_assessment'=>$aval->risk_assessment,
                'sub_category_id'=>$aval->subCategorys->id,
                'daily_cat_id'=>$aval->subCategorys->daily_cat_id,
                'sub_cat'=>$aval->subCategorys->sub_cat,
                'icon'=>$aval->subCategorys->icon,
                'color'=>$aval->subCategorys->color,
                'background_color'=>$aval->subCategorys->background_color,
                'daily_log_category_id'=>$aval->subCategorys->dailyLogCategory->id,
                'category'=>$aval->subCategorys->dailyLogCategory->category,
            ];
        }
        return $data;
    }
    public function simplePagination($paginator){
        return [
            'current_page' => $paginator->currentPage(),
            'next_page_url' => $paginator->nextPageUrl() ?? "",
            'prev_page_url' => $paginator->previousPageUrl() ?? "",
            'total' => $paginator->total(),
        ];
    }
}
