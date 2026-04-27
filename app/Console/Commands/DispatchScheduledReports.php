<?php

namespace App\Console\Commands;

use App\Services\ScheduledReportService;
use Illuminate\Console\Command;

class DispatchScheduledReports extends Command
{
    protected $signature = 'reports:dispatch';
    protected $description = 'Dispatch all scheduled reports that are due';

    public function handle(ScheduledReportService $service): int
    {
        $this->info('Checking for due scheduled reports...');

        $results = $service->dispatchDueReports();

        if (empty($results)) {
            $this->info('No scheduled reports are due.');
            return 0;
        }

        $this->info('Dispatched ' . count($results) . ' report(s):');
        foreach ($results as $r) {
            $status = $r['status'] === 'success' ? '<info>success</info>' : '<error>failed</error>';
            $msg = "  Schedule #{$r['schedule_id']}: {$status}";
            if (isset($r['error'])) {
                $msg .= " — {$r['error']}";
            }
            $this->line($msg);
        }

        return 0;
    }
}
