<?php

namespace App\Http\Controllers\frontEnd\Roster;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use App\Services\ScheduledReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index()
    {
        return view('frontEnd.roster.report.report');
    }

    public function generate(Request $request, ReportService $service)
    {
        $request->validate([
            'report_type' => 'required|in:incidents,training,mar,shifts,feedback',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'status' => 'nullable|string|max:30',
            'code' => 'nullable|string|max:10',
            'shift_type' => 'nullable|string|max:30',
            'feedback_type' => 'nullable|string|max:30',
        ]);

        $homeId = (int) explode(',', Auth::user()->home_id)[0];

        $result = match ($request->report_type) {
            'incidents' => $service->generateIncidentReport(
                $homeId, $request->date_from, $request->date_to
            ),
            'training' => $service->generateTrainingReport(
                $homeId, $request->date_from, $request->date_to, $request->status
            ),
            'mar' => $service->generateMARReport(
                $homeId, $request->date_from, $request->date_to, $request->code
            ),
            'shifts' => $service->generateShiftReport(
                $homeId, $request->date_from, $request->date_to, $request->shift_type, $request->status
            ),
            'feedback' => $service->generateFeedbackReport(
                $homeId, $request->date_from, $request->date_to, $request->feedback_type, $request->status
            ),
        };

        return response()->json(['status' => true, 'report' => $result]);
    }

    public function scheduleList(ScheduledReportService $service)
    {
        $homeId = (int) explode(',', Auth::user()->home_id)[0];
        $schedules = $service->listForHome($homeId);

        return response()->json(['status' => true, 'schedules' => $schedules]);
    }

    public function scheduleStore(Request $request, ScheduledReportService $service)
    {
        $request->validate([
            'report_name' => 'required|string|max:255',
            'report_type' => 'required|in:incidents,training,mar,shifts,feedback',
            'schedule_frequency' => 'required|in:daily,weekly,fortnightly,monthly',
            'schedule_day' => 'nullable|integer|min:0|max:28',
            'schedule_time' => 'required|date_format:H:i',
            'recipients' => 'required|string|max:1000',
            'output_format' => 'required|in:csv,email_summary',
            'is_active' => 'nullable|boolean',
            'notes' => 'nullable|string|max:1000',
        ]);

        $homeId = (int) explode(',', Auth::user()->home_id)[0];
        $userId = Auth::user()->id;

        try {
            $schedule = $service->store($request->only([
                'report_name', 'report_type', 'schedule_frequency',
                'schedule_day', 'schedule_time', 'recipients',
                'output_format', 'is_active', 'notes',
            ]), $homeId, $userId);

            return response()->json(['status' => true, 'schedule' => $schedule]);
        } catch (\RuntimeException $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function scheduleUpdate(Request $request, ScheduledReportService $service)
    {
        $request->validate([
            'id' => 'required|integer',
            'report_name' => 'required|string|max:255',
            'report_type' => 'required|in:incidents,training,mar,shifts,feedback',
            'schedule_frequency' => 'required|in:daily,weekly,fortnightly,monthly',
            'schedule_day' => 'nullable|integer|min:0|max:28',
            'schedule_time' => 'required|date_format:H:i',
            'recipients' => 'required|string|max:1000',
            'output_format' => 'required|in:csv,email_summary',
            'is_active' => 'nullable|boolean',
            'notes' => 'nullable|string|max:1000',
        ]);

        $homeId = (int) explode(',', Auth::user()->home_id)[0];

        try {
            $schedule = $service->update($request->id, $request->only([
                'report_name', 'report_type', 'schedule_frequency',
                'schedule_day', 'schedule_time', 'recipients',
                'output_format', 'is_active', 'notes',
            ]), $homeId);

            return response()->json(['status' => true, 'schedule' => $schedule]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['status' => false, 'message' => 'Schedule not found.'], 404);
        } catch (\RuntimeException $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function scheduleToggle(Request $request, ScheduledReportService $service)
    {
        $request->validate([
            'id' => 'required|integer',
        ]);

        $homeId = (int) explode(',', Auth::user()->home_id)[0];

        try {
            $schedule = $service->toggleActive($request->id, $homeId);
            return response()->json(['status' => true, 'schedule' => $schedule]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['status' => false, 'message' => 'Schedule not found.'], 404);
        }
    }

    public function scheduleDelete(Request $request, ScheduledReportService $service)
    {
        $request->validate([
            'id' => 'required|integer',
        ]);

        $homeId = (int) explode(',', Auth::user()->home_id)[0];

        try {
            $service->delete($request->id, $homeId);
            return response()->json(['status' => true]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['status' => false, 'message' => 'Schedule not found.'], 404);
        }
    }
}
