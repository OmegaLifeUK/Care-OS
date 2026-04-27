<?php

namespace App\Http\Controllers\frontEnd\Roster;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
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
}
