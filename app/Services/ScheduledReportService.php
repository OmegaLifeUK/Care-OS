<?php

namespace App\Services;

use App\Models\ScheduledReport;
use App\Mail\ScheduledReportMail;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ScheduledReportService
{
    protected ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function listForHome(int $homeId): Collection
    {
        return ScheduledReport::forHome($homeId)
            ->active()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function store(array $data, int $homeId, int $userId): ScheduledReport
    {
        $count = ScheduledReport::forHome($homeId)->active()->count();
        if ($count >= 10) {
            throw new \RuntimeException('Maximum of 10 scheduled reports per home.');
        }

        $recipients = $this->parseRecipients($data['recipients'] ?? '');
        if (count($recipients) > 5) {
            throw new \RuntimeException('Maximum of 5 recipients per schedule.');
        }

        $nextRun = $this->calculateNextRunDate(
            $data['schedule_frequency'],
            isset($data['schedule_day']) ? (int) $data['schedule_day'] : null,
            $data['schedule_time']
        );

        $schedule = new ScheduledReport();
        $schedule->fill($data);
        $schedule->recipients = $recipients;
        $schedule->home_id = $homeId;
        $schedule->created_by = $userId;
        $schedule->next_run_date = $nextRun;
        $schedule->save();

        Log::info("Scheduled report created: #{$schedule->id} '{$schedule->report_name}' for home {$homeId}");

        return $schedule;
    }

    public function update(int $id, array $data, int $homeId): ScheduledReport
    {
        $schedule = ScheduledReport::forHome($homeId)->active()->where('id', $id)->firstOrFail();

        $recipients = $this->parseRecipients($data['recipients'] ?? '');
        if (count($recipients) > 5) {
            throw new \RuntimeException('Maximum of 5 recipients per schedule.');
        }

        $schedule->fill($data);
        $schedule->recipients = $recipients;

        $schedule->next_run_date = $this->calculateNextRunDate(
            $schedule->schedule_frequency,
            $schedule->schedule_day,
            $schedule->schedule_time
        );

        $schedule->save();

        Log::info("Scheduled report updated: #{$schedule->id} '{$schedule->report_name}'");

        return $schedule;
    }

    public function toggleActive(int $id, int $homeId): ScheduledReport
    {
        $schedule = ScheduledReport::forHome($homeId)->active()->where('id', $id)->firstOrFail();
        $schedule->is_active = !$schedule->is_active;

        if ($schedule->is_active) {
            $schedule->next_run_date = $this->calculateNextRunDate(
                $schedule->schedule_frequency,
                $schedule->schedule_day,
                $schedule->schedule_time
            );
        }

        $schedule->save();

        Log::info("Scheduled report toggled: #{$schedule->id} is_active={$schedule->is_active}");

        return $schedule;
    }

    public function delete(int $id, int $homeId): void
    {
        $schedule = ScheduledReport::forHome($homeId)->active()->where('id', $id)->firstOrFail();
        $schedule->is_deleted = 1;
        $schedule->save();

        Log::info("Scheduled report soft-deleted: #{$schedule->id} '{$schedule->report_name}'");
    }

    public function calculateNextRunDate(string $frequency, ?int $day, string $time): Carbon
    {
        $parts = explode(':', $time);
        $hour = (int) ($parts[0] ?? 8);
        $minute = (int) ($parts[1] ?? 0);
        $now = Carbon::now();

        switch ($frequency) {
            case 'daily':
                $next = $now->copy()->setTime($hour, $minute, 0);
                if ($next->lte($now)) {
                    $next->addDay();
                }
                return $next;

            case 'weekly':
                $dayOfWeek = $day ?? 1; // 0=Sunday, 1=Monday, etc.
                $next = $now->copy()->next($dayOfWeek)->setTime($hour, $minute, 0);
                // If today is the target day and time hasn't passed, use today
                if ($now->dayOfWeek === $dayOfWeek) {
                    $today = $now->copy()->setTime($hour, $minute, 0);
                    if ($today->gt($now)) {
                        $next = $today;
                    }
                }
                return $next;

            case 'fortnightly':
                $dayOfWeek = $day ?? 1;
                $next = $now->copy()->next($dayOfWeek)->setTime($hour, $minute, 0);
                if ($now->dayOfWeek === $dayOfWeek) {
                    $today = $now->copy()->setTime($hour, $minute, 0);
                    if ($today->gt($now)) {
                        $next = $today;
                    }
                }
                $next->addWeek();
                return $next;

            case 'monthly':
                $dayOfMonth = $day ?? 1;
                if ($dayOfMonth > 28) $dayOfMonth = 28;
                $next = $now->copy()->setDay($dayOfMonth)->setTime($hour, $minute, 0);
                if ($next->lte($now)) {
                    $next->addMonth();
                    $next->setDay(min($dayOfMonth, $next->daysInMonth));
                }
                return $next;

            default:
                return $now->copy()->addDay()->setTime($hour, $minute, 0);
        }
    }

    public function advanceNextRunDate(ScheduledReport $schedule): Carbon
    {
        $base = $schedule->next_run_date ?? Carbon::now();

        return match ($schedule->schedule_frequency) {
            'daily' => $base->copy()->addDay(),
            'weekly' => $base->copy()->addWeek(),
            'fortnightly' => $base->copy()->addWeeks(2),
            'monthly' => $base->copy()->addMonth(),
            default => $base->copy()->addDay(),
        };
    }

    public function dispatchDueReports(): array
    {
        $dueSchedules = ScheduledReport::due()->get();
        $results = [];

        foreach ($dueSchedules as $schedule) {
            try {
                $dateRange = $this->getDateRange($schedule->schedule_frequency);
                $reportResult = $this->generateReport(
                    $schedule->report_type,
                    $schedule->home_id,
                    $dateRange['from'],
                    $dateRange['to']
                );

                $csvContent = '';
                $csvFilename = '';
                if ($schedule->output_format === 'csv') {
                    $csvContent = $this->buildCSV($reportResult['columns'], $reportResult['data']);
                    $csvFilename = $schedule->report_type . '_report_' . now()->format('Y-m-d') . '.csv';
                }

                $mail = new ScheduledReportMail(
                    $schedule->report_name,
                    $reportResult['summary'],
                    $schedule->output_format,
                    $csvContent,
                    $csvFilename
                );

                $recipients = $schedule->recipients;
                if (!empty($recipients)) {
                    Mail::to($recipients[0])
                        ->cc(array_slice($recipients, 1))
                        ->send($mail);
                }

                $schedule->last_run_date = now();
                $schedule->last_run_status = 'success';
                $schedule->next_run_date = $this->advanceNextRunDate($schedule);
                $schedule->save();

                Log::info("Scheduled report dispatched: #{$schedule->id} '{$schedule->report_name}' — {$schedule->report_type} sent to " . implode(', ', $recipients));

                $results[] = ['schedule_id' => $schedule->id, 'status' => 'success'];
            } catch (\Throwable $e) {
                $schedule->last_run_date = now();
                $schedule->last_run_status = 'failed';
                $schedule->next_run_date = $this->advanceNextRunDate($schedule);
                $schedule->save();

                Log::error("Scheduled report failed: #{$schedule->id} — {$e->getMessage()}");

                $results[] = ['schedule_id' => $schedule->id, 'status' => 'failed', 'error' => $e->getMessage()];
            }
        }

        return $results;
    }

    protected function generateReport(string $type, int $homeId, string $dateFrom, string $dateTo): array
    {
        return match ($type) {
            'incidents' => $this->reportService->generateIncidentReport($homeId, $dateFrom, $dateTo),
            'training' => $this->reportService->generateTrainingReport($homeId, $dateFrom, $dateTo, null),
            'mar' => $this->reportService->generateMARReport($homeId, $dateFrom, $dateTo, null),
            'shifts' => $this->reportService->generateShiftReport($homeId, $dateFrom, $dateTo, null, null),
            'feedback' => $this->reportService->generateFeedbackReport($homeId, $dateFrom, $dateTo, null, null),
            default => throw new \RuntimeException("Unknown report type: {$type}"),
        };
    }

    protected function getDateRange(string $frequency): array
    {
        $yesterday = Carbon::yesterday();

        return match ($frequency) {
            'daily' => [
                'from' => $yesterday->toDateString(),
                'to' => $yesterday->toDateString(),
            ],
            'weekly' => [
                'from' => $yesterday->copy()->subDays(6)->toDateString(),
                'to' => $yesterday->toDateString(),
            ],
            'fortnightly' => [
                'from' => $yesterday->copy()->subDays(13)->toDateString(),
                'to' => $yesterday->toDateString(),
            ],
            'monthly' => [
                'from' => Carbon::now()->subMonth()->startOfMonth()->toDateString(),
                'to' => Carbon::now()->subMonth()->endOfMonth()->toDateString(),
            ],
            default => [
                'from' => $yesterday->copy()->subDays(6)->toDateString(),
                'to' => $yesterday->toDateString(),
            ],
        };
    }

    public function buildCSV(array $columns, array $data): string
    {
        $output = fopen('php://temp', 'r+');

        $headers = array_map(fn($col) => $col['label'], $columns);
        fputcsv($output, $headers);

        foreach ($data as $row) {
            $line = [];
            foreach ($columns as $col) {
                $val = $row[$col['key']] ?? '';
                $line[] = is_array($val) ? json_encode($val) : $val;
            }
            fputcsv($output, $line);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }

    protected function parseRecipients(string $recipientsStr): array
    {
        $raw = array_map('trim', explode(',', $recipientsStr));
        $valid = [];

        foreach ($raw as $email) {
            if ($email === '') continue;
            if (str_contains($email, "\r") || str_contains($email, "\n")) continue;
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) continue;
            $valid[] = $email;
        }

        if (empty($valid)) {
            throw new \RuntimeException('At least one valid email recipient is required.');
        }

        return $valid;
    }
}
