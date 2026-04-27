<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ScheduledReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $reportName;
    public array $summary;
    public string $outputFormat;
    public string $csvContent;
    public string $csvFilename;

    public function __construct(string $reportName, array $summary, string $outputFormat, string $csvContent = '', string $csvFilename = '')
    {
        $this->reportName = $reportName;
        $this->summary = $summary;
        $this->outputFormat = $outputFormat;
        $this->csvContent = $csvContent;
        $this->csvFilename = $csvFilename;
    }

    public function build()
    {
        $date = now()->format('d M Y');
        $mail = $this->subject("[Care OS] {$this->reportName} — {$date}")
            ->view('emails.scheduled_report');

        if ($this->outputFormat === 'csv' && $this->csvContent !== '') {
            $mail->attachData($this->csvContent, $this->csvFilename, [
                'mime' => 'text/csv',
            ]);
        }

        return $mail;
    }
}
