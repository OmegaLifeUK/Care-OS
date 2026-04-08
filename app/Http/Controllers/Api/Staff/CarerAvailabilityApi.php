<?php

namespace App\Http\Controllers\Api\Staff;

use App\Http\Controllers\Controller;
use App\Services\Staff\CarerWorkingHourService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CarerAvailabilityApi extends Controller
{
    protected $carerWorkingHourService;

    public function __construct(CarerWorkingHourService $carerWorkingHourService)
    {
        $this->carerWorkingHourService = $carerWorkingHourService;
    }

    function index(Request $req)
    {
        try {
            $validator = Validator::make($req->all(), [
                'carer_id' => 'required',
                'date' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message'   => $validator->errors()->first(),
                ], 422);
            }
            $reqData = $req->all();
            $date = $req->date; // 03-2026

            [$month, $year] = explode('-', $date);
            $startOfMonth = Carbon::createFromDate($year, $month, 1);
            $endOfMonth = $startOfMonth->copy()->endOfMonth();
            $data = $this->carerWorkingHourService->load_overview_data($reqData);
            $type = 'standard';
            if ($data->count() == 0) {
                // return $reqData;
                $type = 'specific';
                $data = $this->carerWorkingHourService->load_specific_working_data($reqData);
            }

            // return $data;
            $ar = [];

            foreach ($data as $item) {
                if ($type == 'specific') {
                    $start_date = date('Y-m-d', strtotime($item->start_date));
                    $end_date = date('Y-m-d', strtotime($item->end_date));
                    $start_time = date('H:i', strtotime($item->start_date));
                    $end_time = date('H:i', strtotime($item->end_date));
                    $week_number = '';
                    $daysName = '';
                    $type = 'specific';
                } else {
                    $week_number = $item->week_number;
                    $start_date = '';
                    $end_date = '';
                    $start_time = date('H:i', strtotime($item->start_time));
                    $end_time = date('H:i', strtotime($item->end_time));
                    $daysName = $item->day;
                    $type = $item->type;
                }


                // $now = Carbon::now();
                // $start = Carbon::parse($item->start_date);
                // $end = Carbon::parse($item->end_date);

                $ar[] = [
                    'id' => $item->id,
                    'type' => $type,
                    'start_time' => $start_time,
                    'end_time' => $end_time,
                    'start_date' => $start_date ?? "",
                    'end_date' => $end_date ?? "",
                    'day' => $daysName,
                    'is_working' => $item->is_working,
                    'week_number' => $week_number
                ];
            }
            $get_unavailability_data = $this->carerWorkingHourService->get_unavailability_data($reqData);
            $get_staff_leaves = $this->carerWorkingHourService->load_staff_leaves($reqData)->select(
                    'id',
                    'home_id',
                    'user_id',
                    'leave_type',
                    'start_date',
                    'end_date',
                    'end_time',
                    'start_time'
                )
                ->with('leave_types:id,leave_name')
                ->latest()->get();
            $unavailability_ar = [];
            $leave_arr = [];
            foreach ($get_unavailability_data as $item) {

                $start = Carbon::parse($item->start_date);
                $end = Carbon::parse($item->end_date);

                if ($item->type == 'range') {

                    $period = CarbonPeriod::create($start, $end);

                    foreach ($period as $date) {
                        $unavailability_ar[$date->format('Y-m-d')] = true;
                    }
                } else {
                    $unavailability_ar[$start->format('Y-m-d')] = true;
                }
            }
            foreach ($get_staff_leaves as $item) {

                $start = Carbon::parse($item->start_date);
                $end = Carbon::parse($item->end_date);

                $period = CarbonPeriod::create($start, $end);

                foreach ($period as $date) {
                    $leave_arr[$date->format('Y-m-d')] = true;
                }
            }
            $finalCalendar = [];

            $period = CarbonPeriod::create($startOfMonth, $endOfMonth);

            foreach ($period as $dateObj) {

                $currentDate = $dateObj->format('Y-m-d');
                $dayName = strtolower($dateObj->format('l'));
                $daysNumber = strtolower($dateObj->format('d'));

                $status = 'day-off';
                $start_time = '';
                $end_time  = '';
                $timeText = '';
                // ✅ 1. Check unavailability first
                if (isset($leave_arr[$currentDate])) {
                    $status = 'onleave';
                } elseif (isset($unavailability_ar[$currentDate])) {

                    $status = 'unavailable';
                } else {

                    // ✅ 2. Check working hours
                    foreach ($ar as $item) {

                        if ($item['type'] == 'specific') {

                            $start = Carbon::parse($item['start_date']);
                            $end = Carbon::parse($item['end_date']);

                            if ($dateObj->between($start, $end)) {

                                $status = 'available';
                                $start_time = $start->format('H:i');
                                $end_time = $end->format('H:i');
                                $timeText = "$start_time $end_time";
                                break;
                            }
                        } elseif ($item['type'] == 'alternate') {

                            if ($item['day'] == $dayName) {

                                $weekNumber = $dateObj->weekOfMonth;

                                $isValidWeek =
                                    ($item['week_number'] == 1 && $weekNumber % 2 != 0) || // odd
                                    ($item['week_number'] == 2 && $weekNumber % 2 == 0);   // even

                                if ($isValidWeek) {

                                    $status = 'available';
                                    $start_time = $item['start_time'];
                                    $end_time = $item['end_time'];
                                    $timeText = "$start_time $end_time";
                                    break; // ✅ only break when valid match
                                }

                                // ❌ yaha break mat karo
                                // ❌ status bhi yaha set mat karo
                            }
                        } else {

                            if ($item['day'] == $dayName) {

                                $status = 'available';
                                $start_time = date('H:i', strtotime($item['start_time']));
                                $end_time = date('H:i', strtotime($item['end_time']));
                                $timeText = "$start_time $end_time";
                                break;
                            }
                        }
                    }
                }

                $finalCalendar[] = [
                    'dayName' => $dayName,
                    'day' => $daysNumber,
                    'date' => $currentDate,
                    'time' => $timeText,
                    'status' => $status, // available | unavailable | day-off | onleave
                    // 'start_time' => $start_time,
                    // 'end_time' => $end_time,
                ];
            }

            return response()->json([
                'status' => true,
                'message' => 'My Availability',
                'data' => $finalCalendar,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message'   => $e->getMessage(),
            ], 500);
        }
    }
}
