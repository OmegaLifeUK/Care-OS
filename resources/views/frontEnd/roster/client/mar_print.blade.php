<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MAR Sheet &mdash; {{ $client->name }} &mdash; {{ date('F Y', mktime(0,0,0,$month,1,$year)) }}</title>
    <style>
        @page { size: A4 landscape; margin: 8mm; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 8pt; color: #000; -webkit-print-color-adjust: exact; print-color-adjust: exact; }

        .title-bar { background: #1e3a5f; color: #fff; font-size: 11pt; font-weight: bold; padding: 5px 10px; margin-bottom: 6px; }

        .header-grid { display: grid; grid-template-columns: 1fr auto; gap: 8px; border: 1px solid #000; padding: 6px; margin-bottom: 6px; }
        .resident-fields { display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 2px 12px; }
        .field { display: flex; flex-direction: column; }
        .field-label { font-size: 6.5pt; color: #555; font-weight: bold; text-transform: uppercase; }
        .field-value { border-bottom: 1px solid #000; min-height: 14px; font-size: 8pt; padding: 1px 0; }

        .key-box { font-size: 7pt; border: 1px solid #999; padding: 4px; }

        .mar-table { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
        .mar-table th, .mar-table td { border: 0.5pt solid #888; padding: 1px 2px; text-align: center; font-size: 6.5pt; }
        .mar-table thead th { background: #1e3a5f; color: #fff; font-size: 6.5pt; font-weight: 600; }
        .mar-table .col-med { text-align: left; width: 140px; white-space: normal; word-break: break-word; font-size: 7pt; }
        .mar-table .col-time { width: 32px; font-weight: bold; font-size: 6.5pt; background: #f0f4ff; }
        .day-cell { width: 16px; height: 16px; }
        .week-sep { border-left: 2pt solid #1e3a5f !important; }

        .stock-row td { background: #f0f0f0; font-size: 6pt; }
        .stock-inner { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
        .stock-field { display: inline-flex; gap: 3px; align-items: center; }
        .stock-line { border-bottom: 1pt solid #000; min-width: 30px; display: inline-block; text-align: center; }

        .code-A, .code-S { color: #166534; font-weight: bold; }
        .code-R { color: #991b1b; }
        .code-W { color: #92400e; }
        .code-N { color: #475569; }
        .code-O { color: #6b21a8; }

        .footer { margin-top: 8px; font-size: 6pt; color: #666; }

        @media screen {
            body { max-width: 1200px; margin: 10px auto; padding: 10px; }
            .no-print { display: block; }
        }
        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

<div class="no-print" style="margin-bottom:10px;text-align:right;">
    <button onclick="window.print()" style="padding:8px 20px;font-size:14px;cursor:pointer;background:#1e3a5f;color:#fff;border:none;border-radius:4px;">Print MAR Sheet</button>
</div>

<div class="title-bar">Medication Administration Record</div>

<div class="header-grid">
    <div class="resident-fields">
        <div class="field"><span class="field-label">Name</span><span class="field-value">{{ $client->name }}</span></div>
        <div class="field"><span class="field-label">Date of Birth</span><span class="field-value">{{ $client->date_of_birth ? date('d/m/Y', strtotime($client->date_of_birth)) : '—' }}</span></div>
        <div class="field"><span class="field-label">Room</span><span class="field-value">{{ $client->room_type ?? '—' }}</span></div>
        <div class="field"><span class="field-label">Month / Year</span><span class="field-value">{{ date('F Y', mktime(0,0,0,$month,1,$year)) }}</span></div>
        <div class="field" style="grid-column:span 2"><span class="field-label">Address</span><span class="field-value">{{ $client->street ?? '' }} {{ $client->city ?? '' }} {{ $client->postcode ?? '' }}</span></div>
        <div class="field"><span class="field-label">Phone</span><span class="field-value">{{ $client->phone_no ?? '—' }}</span></div>
        <div class="field"><span class="field-label">Gender</span><span class="field-value">{{ $client->gender ?? '—' }}</span></div>
        <div class="field" style="grid-column:span 4">
            <span class="field-label">Allergies / Warnings</span>
            <span class="field-value" style="font-weight:bold;color:#b91c1c;">{{ $client->allergies ?? 'None recorded' }}</span>
        </div>
    </div>

    <div>
        <div class="key-box">
            <strong>Key:</strong><br>
            A = Administered &nbsp; S = Self-admin<br>
            R = Refused &nbsp; W = Withheld<br>
            N = Not Available &nbsp; O = Other
        </div>
    </div>
</div>

@php
    $daysInMonth = $gridData['days_in_month'];
    $sheets = $gridData['sheets'];
    $monthStart = \Carbon\Carbon::create($year, $month, 1);
    $codeSymbols = ['A' => 'A', 'S' => 'S', 'R' => 'R', 'W' => 'W', 'N' => 'N', 'O' => 'O'];
@endphp

<table class="mar-table">
    <thead>
        <tr>
            <th class="col-med">Medication Details</th>
            <th class="col-time">Time</th>
            @for($d = 1; $d <= $daysInMonth; $d++)
                @php
                    $dayDate = \Carbon\Carbon::create($year, $month, $d);
                    $isWeekSep = $d > 1 && $dayDate->dayOfWeek === 1;
                @endphp
                <th class="day-cell {{ $isWeekSep ? 'week-sep' : '' }}" style="font-size:5.5pt;">
                    {{ substr($dayDate->format('D'), 0, 1) }}<br>{{ $d }}
                </th>
            @endfor
        </tr>
    </thead>
    <tbody>
        @forelse($sheets as $sheet)
            @php
                $timeSlots = $sheet->time_slots ?? [];
                if (empty($timeSlots)) $timeSlots = ['—'];
                $adminsByDateSlot = [];
                foreach ($sheet->administrations as $admin) {
                    $aDate = $admin->date instanceof \Carbon\Carbon ? $admin->date->format('Y-m-d') : (is_string($admin->date) ? substr($admin->date, 0, 10) : '');
                    $adminsByDateSlot[$aDate][$admin->time_slot] = $admin;
                }
                $givenCount = $sheet->administrations->whereIn('code', ['A', 'S'])->count();
                $startBal = ($sheet->quantity_received ?? 0) + ($sheet->quantity_carried_forward ?? 0);
                $currentBal = $startBal - $givenCount - ($sheet->quantity_returned ?? 0);
            @endphp

            @foreach($timeSlots as $slotIdx => $slot)
            <tr class="{{ $slotIdx === 0 ? 'border-top: 2px solid #333;' : '' }}">
                @if($slotIdx === 0)
                <td class="col-med" rowspan="{{ count($timeSlots) }}" style="border-top:2px solid #333;">
                    <strong>{{ $sheet->medication_name }}</strong>
                    @if($sheet->dosage) {{ $sheet->dosage }} @endif
                    @if($sheet->dose) &middot; {{ $sheet->dose }} @endif
                    @if($sheet->route) ({{ $sheet->route }}) @endif
                    @if($sheet->frequency)<br><em style="color:#666;">{{ $sheet->frequency }}</em>@endif
                    @if($sheet->as_required) <strong style="color:#0369a1;">[PRN]</strong>@endif
                </td>
                @endif
                <td class="col-time" @if($slotIdx === 0) style="border-top:2px solid #333;" @endif>{{ $slot }}</td>
                @for($d = 1; $d <= $daysInMonth; $d++)
                    @php
                        $dateStr = sprintf('%04d-%02d-%02d', $year, $month, $d);
                        $dayDate = \Carbon\Carbon::create($year, $month, $d);
                        $isWeekSep = $d > 1 && $dayDate->dayOfWeek === 1;
                        $admin = $adminsByDateSlot[$dateStr][$slot] ?? null;
                        $code = $admin ? ($admin->code ?? '') : '';
                    @endphp
                    <td class="day-cell {{ $isWeekSep ? 'week-sep' : '' }} {{ $code ? 'code-'.$code : '' }}" @if($slotIdx === 0) style="border-top:2px solid #333;" @endif>{{ $code }}</td>
                @endfor
            </tr>
            @endforeach

            <tr class="stock-row">
                <td colspan="{{ $daysInMonth + 2 }}" style="border-top:1px solid #666;">
                    <div class="stock-inner">
                        <span>Qty received: <span class="stock-line">{{ $sheet->quantity_received ?? '' }}</span></span>
                        <span>Carried forward: <span class="stock-line">{{ $sheet->quantity_carried_forward ?? '' }}</span></span>
                        <span>Returned: <span class="stock-line">{{ $sheet->quantity_returned ?? '' }}</span></span>
                        <span>Balance: <span class="stock-line" style="font-weight:bold;">{{ $startBal > 0 ? $currentBal : '' }}</span></span>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="{{ $daysInMonth + 2 }}" style="padding:20px;text-align:center;color:#888;">No active prescriptions for this resident.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="footer">
    Printed: {{ now()->format('d/m/Y H:i') }} | Care One OS
</div>

</body>
</html>
