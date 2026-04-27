<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class ReportService
{
    public function generateIncidentReport(int $homeId, ?string $dateFrom, ?string $dateTo): array
    {
        $query = DB::table('su_incident_report')
            ->join('service_user', 'su_incident_report.service_user_id', '=', 'service_user.id')
            ->where('su_incident_report.home_id', $homeId)
            ->where('su_incident_report.is_deleted', 0);

        if ($dateFrom) {
            $query->where('su_incident_report.date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->where('su_incident_report.date', '<=', $dateTo);
        }

        $totalQuery = (clone $query)->count();

        $data = $query->select(
            'su_incident_report.id',
            'su_incident_report.date',
            'service_user.name as client_name',
            'su_incident_report.title',
            'su_incident_report.formdata',
            'su_incident_report.created_at'
        )
            ->orderBy('su_incident_report.date', 'desc')
            ->limit(500)
            ->get()
            ->map(function ($row) {
                $formdata = json_decode($row->formdata, true) ?: [];
                return [
                    'date' => $row->date,
                    'client' => $row->client_name,
                    'title' => $row->title,
                    'location' => $formdata['location'] ?? '',
                    'description' => $formdata['brief'] ?? '',
                    'time' => $formdata['time'] ?? '',
                ];
            })
            ->toArray();

        return [
            'summary' => [
                'total' => $totalQuery,
            ],
            'columns' => [
                ['key' => 'date', 'label' => 'Date'],
                ['key' => 'client', 'label' => 'Client'],
                ['key' => 'title', 'label' => 'Title'],
                ['key' => 'location', 'label' => 'Location'],
                ['key' => 'description', 'label' => 'Description'],
                ['key' => 'time', 'label' => 'Time'],
            ],
            'data' => $data,
        ];
    }

    public function generateTrainingReport(int $homeId, ?string $dateFrom, ?string $dateTo, ?string $status): array
    {
        $query = DB::table('staff_training')
            ->join('training', 'staff_training.training_id', '=', 'training.id')
            ->join('user', 'staff_training.user_id', '=', 'user.id')
            ->where('training.home_id', $homeId)
            ->where('training.is_deleted', 0);

        if ($dateFrom) {
            $query->where('staff_training.created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->where('staff_training.created_at', '<=', $dateTo . ' 23:59:59');
        }
        if ($status !== null && $status !== '') {
            $query->where('staff_training.status', (int) $status);
        }

        $total = (clone $query)->count();
        $completed = (clone $query)->where('staff_training.status', 2)->count();
        $inProgress = (clone $query)->where('staff_training.status', 1)->count();
        $pending = (clone $query)->where('staff_training.status', 0)->count();
        $complianceRate = $total > 0 ? round(($completed / $total) * 100, 1) : 0;

        $overdue = (clone $query)
            ->where('staff_training.status', '!=', 2)
            ->whereNotNull('staff_training.due_date')
            ->where('staff_training.due_date', '<', now()->toDateString())
            ->count();

        $data = $query->select(
            'user.name as staff_name',
            'training.training_name as course',
            'staff_training.status',
            'staff_training.due_date',
            'staff_training.completed_date',
            'staff_training.expiry_date'
        )
            ->orderBy('staff_training.created_at', 'desc')
            ->limit(500)
            ->get()
            ->map(function ($row) {
                $statusMap = [0 => 'Pending', 1 => 'In Progress', 2 => 'Completed'];
                return [
                    'staff_name' => $row->staff_name,
                    'course' => $row->course,
                    'status' => $statusMap[$row->status] ?? 'Unknown',
                    'due_date' => $row->due_date,
                    'completed_date' => $row->completed_date,
                    'expiry_date' => $row->expiry_date,
                ];
            })
            ->toArray();

        return [
            'summary' => [
                'total' => $total,
                'completed' => $completed,
                'in_progress' => $inProgress,
                'pending' => $pending,
                'overdue' => $overdue,
                'compliance_rate' => $complianceRate,
            ],
            'columns' => [
                ['key' => 'staff_name', 'label' => 'Staff Name'],
                ['key' => 'course', 'label' => 'Course'],
                ['key' => 'status', 'label' => 'Status'],
                ['key' => 'due_date', 'label' => 'Due Date'],
                ['key' => 'completed_date', 'label' => 'Completed'],
                ['key' => 'expiry_date', 'label' => 'Expiry Date'],
            ],
            'data' => $data,
        ];
    }

    public function generateMARReport(int $homeId, ?string $dateFrom, ?string $dateTo, ?string $code): array
    {
        $query = DB::table('mar_administrations')
            ->join('mar_sheets', 'mar_administrations.mar_sheet_id', '=', 'mar_sheets.id')
            ->join('service_user', 'mar_sheets.client_id', '=', 'service_user.id')
            ->leftJoin('user', 'mar_administrations.administered_by', '=', 'user.id')
            ->where('mar_administrations.home_id', $homeId);

        if ($dateFrom) {
            $query->where('mar_administrations.date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->where('mar_administrations.date', '<=', $dateTo);
        }
        if ($code !== null && $code !== '') {
            $query->where('mar_administrations.code', $code);
        }

        $total = (clone $query)->count();
        $administered = (clone $query)->where('mar_administrations.code', 'A')->count();
        $refused = (clone $query)->where('mar_administrations.code', 'R')->count();
        $spoilt = (clone $query)->where('mar_administrations.code', 'S')->count();
        $other = $total - $administered - $refused - $spoilt;
        $complianceRate = $total > 0 ? round(($administered / $total) * 100, 1) : 0;

        $data = $query->select(
            'mar_administrations.date',
            'service_user.name as client_name',
            'mar_sheets.medication_name',
            'mar_administrations.time_slot',
            'mar_administrations.code',
            'user.name as administered_by',
            'mar_administrations.notes'
        )
            ->orderBy('mar_administrations.date', 'desc')
            ->orderBy('mar_administrations.time_slot', 'asc')
            ->limit(500)
            ->get()
            ->map(function ($row) {
                $codeMap = ['A' => 'Administered', 'R' => 'Refused', 'S' => 'Spoilt'];
                return [
                    'date' => $row->date,
                    'client' => $row->client_name,
                    'medication' => $row->medication_name,
                    'time_slot' => $row->time_slot,
                    'code' => $codeMap[$row->code] ?? $row->code,
                    'administered_by' => $row->administered_by ?? 'N/A',
                    'notes' => $row->notes ?? '',
                ];
            })
            ->toArray();

        return [
            'summary' => [
                'total' => $total,
                'administered' => $administered,
                'refused' => $refused,
                'spoilt' => $spoilt,
                'other' => $other,
                'compliance_rate' => $complianceRate,
            ],
            'columns' => [
                ['key' => 'date', 'label' => 'Date'],
                ['key' => 'client', 'label' => 'Client'],
                ['key' => 'medication', 'label' => 'Medication'],
                ['key' => 'time_slot', 'label' => 'Time Slot'],
                ['key' => 'code', 'label' => 'Status'],
                ['key' => 'administered_by', 'label' => 'Administered By'],
                ['key' => 'notes', 'label' => 'Notes'],
            ],
            'data' => $data,
        ];
    }

    public function generateShiftReport(int $homeId, ?string $dateFrom, ?string $dateTo, ?string $shiftType, ?string $status): array
    {
        $query = DB::table('scheduled_shifts')
            ->leftJoin('service_user', 'scheduled_shifts.service_user_id', '=', 'service_user.id')
            ->leftJoin('user', 'scheduled_shifts.staff_id', '=', 'user.id')
            ->where('scheduled_shifts.home_id', (string) $homeId)
            ->whereNull('scheduled_shifts.deleted_at');

        if ($dateFrom) {
            $query->where('scheduled_shifts.start_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->where('scheduled_shifts.start_date', '<=', $dateTo);
        }
        if ($shiftType !== null && $shiftType !== '') {
            $query->where('scheduled_shifts.shift_type', $shiftType);
        }
        if ($status !== null && $status !== '') {
            $query->where('scheduled_shifts.status', $status);
        }

        $total = (clone $query)->count();
        $filled = (clone $query)->where('scheduled_shifts.status', '!=', 'unfilled')->count();
        $unfilled = (clone $query)->where('scheduled_shifts.status', 'unfilled')->count();
        $fillRate = $total > 0 ? round(($filled / $total) * 100, 1) : 0;

        $data = $query->select(
            'scheduled_shifts.start_date as date',
            'service_user.name as client_name',
            'scheduled_shifts.shift_type',
            'scheduled_shifts.start_time',
            'scheduled_shifts.end_time',
            'user.name as staff_name',
            'scheduled_shifts.status'
        )
            ->orderBy('scheduled_shifts.start_date', 'desc')
            ->orderBy('scheduled_shifts.start_time', 'asc')
            ->limit(500)
            ->get()
            ->map(function ($row) {
                return [
                    'date' => $row->date,
                    'client' => $row->client_name ?? 'N/A',
                    'shift_type' => ucfirst($row->shift_type ?? ''),
                    'start_time' => $row->start_time,
                    'end_time' => $row->end_time,
                    'staff' => $row->staff_name ?? 'Unfilled',
                    'status' => ucfirst(str_replace('_', ' ', $row->status)),
                ];
            })
            ->toArray();

        return [
            'summary' => [
                'total' => $total,
                'filled' => $filled,
                'unfilled' => $unfilled,
                'fill_rate' => $fillRate,
            ],
            'columns' => [
                ['key' => 'date', 'label' => 'Date'],
                ['key' => 'client', 'label' => 'Client'],
                ['key' => 'shift_type', 'label' => 'Shift Type'],
                ['key' => 'start_time', 'label' => 'Start'],
                ['key' => 'end_time', 'label' => 'End'],
                ['key' => 'staff', 'label' => 'Staff'],
                ['key' => 'status', 'label' => 'Status'],
            ],
            'data' => $data,
        ];
    }

    public function generateFeedbackReport(int $homeId, ?string $dateFrom, ?string $dateTo, ?string $feedbackType, ?string $status): array
    {
        $query = DB::table('client_portal_feedback')
            ->leftJoin('service_user', 'client_portal_feedback.client_id', '=', 'service_user.id')
            ->where('client_portal_feedback.home_id', $homeId)
            ->where('client_portal_feedback.is_deleted', 0);

        if ($dateFrom) {
            $query->where('client_portal_feedback.created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->where('client_portal_feedback.created_at', '<=', $dateTo . ' 23:59:59');
        }
        if ($feedbackType !== null && $feedbackType !== '') {
            $query->where('client_portal_feedback.feedback_type', $feedbackType);
        }
        if ($status !== null && $status !== '') {
            $query->where('client_portal_feedback.status', $status);
        }

        $total = (clone $query)->count();

        $byType = (clone $query)
            ->selectRaw('feedback_type, COUNT(*) as cnt')
            ->groupBy('feedback_type')
            ->pluck('cnt', 'feedback_type')
            ->toArray();

        $avgRating = (clone $query)
            ->where('client_portal_feedback.rating', '>', 0)
            ->avg('client_portal_feedback.rating');
        $avgRating = $avgRating ? round($avgRating, 1) : 0;

        $newCount = (clone $query)->where('client_portal_feedback.status', 'new')->count();
        $resolvedCount = (clone $query)->where('client_portal_feedback.status', 'resolved')->count();

        $data = $query->select(
            'client_portal_feedback.created_at as date',
            'client_portal_feedback.submitted_by',
            'client_portal_feedback.is_anonymous',
            'client_portal_feedback.feedback_type',
            'client_portal_feedback.category',
            'client_portal_feedback.rating',
            'client_portal_feedback.status',
            'client_portal_feedback.subject',
            'service_user.name as client_name'
        )
            ->orderBy('client_portal_feedback.created_at', 'desc')
            ->limit(500)
            ->get()
            ->map(function ($row) {
                return [
                    'date' => date('Y-m-d', strtotime($row->date)),
                    'submitted_by' => $row->is_anonymous ? 'Anonymous' : $row->submitted_by,
                    'client' => $row->client_name ?? 'N/A',
                    'type' => ucfirst($row->feedback_type),
                    'category' => ucfirst(str_replace('_', ' ', $row->category)),
                    'rating' => $row->rating,
                    'status' => ucfirst($row->status),
                    'subject' => $row->subject,
                ];
            })
            ->toArray();

        return [
            'summary' => [
                'total' => $total,
                'by_type' => $byType,
                'avg_rating' => $avgRating,
                'new' => $newCount,
                'resolved' => $resolvedCount,
            ],
            'columns' => [
                ['key' => 'date', 'label' => 'Date'],
                ['key' => 'submitted_by', 'label' => 'Submitted By'],
                ['key' => 'client', 'label' => 'Client'],
                ['key' => 'type', 'label' => 'Type'],
                ['key' => 'category', 'label' => 'Category'],
                ['key' => 'rating', 'label' => 'Rating'],
                ['key' => 'status', 'label' => 'Status'],
                ['key' => 'subject', 'label' => 'Subject'],
            ],
            'data' => $data,
        ];
    }
}
