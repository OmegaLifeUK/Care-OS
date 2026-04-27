@extends('frontEnd.layouts.master')
@section('title','Reports')
@section('content')

@include('frontEnd.roster.common.roster_header')
<main class="page-content">

<style>
    .rpt-container { padding: 20px 30px; }
    .rpt-title { font-size: 22px; font-weight: 600; color: #2c3e50; margin-bottom: 5px; }
    .rpt-subtitle { color: #777; font-size: 14px; margin-bottom: 20px; }

    /* Tab bar */
    .rpt-tabs { display: flex; gap: 0; margin-bottom: 25px; border-bottom: 2px solid #e9ecef; }
    .rpt-tab {
        padding: 10px 24px; font-size: 14px; font-weight: 600; color: #777;
        cursor: pointer; border-bottom: 3px solid transparent; margin-bottom: -2px;
        transition: color 0.2s, border-color 0.2s;
    }
    .rpt-tab:hover { color: #2c3e50; }
    .rpt-tab.active { color: #3498db; border-bottom-color: #3498db; }
    .rpt-tab-content { display: none; }
    .rpt-tab-content.active { display: block; }

    /* Report cards */
    .report-cards { display: flex; gap: 15px; margin-bottom: 25px; flex-wrap: wrap; }
    .report-card {
        flex: 0 0 auto; width: 170px; background: #fff; border-radius: 10px;
        padding: 20px 15px; text-align: center; cursor: pointer;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 2px solid transparent;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .report-card:hover { box-shadow: 0 3px 10px rgba(0,0,0,0.15); }
    .report-card.active { border-color: #3498db; box-shadow: 0 3px 10px rgba(52,152,219,0.3); }
    .report-card .rc-icon {
        width: 50px; height: 50px; border-radius: 50%; margin: 0 auto 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 22px; color: #fff;
    }
    .report-card .rc-label { font-size: 13px; font-weight: 600; color: #2c3e50; }
    .rc-icon-incidents { background: #e74c3c; }
    .rc-icon-training { background: #6c5ce7; }
    .rc-icon-mar { background: #e84393; }
    .rc-icon-shifts { background: #3498db; }
    .rc-icon-feedback { background: #2ecc71; }

    .filter-section {
        background: #fff; border-radius: 10px; padding: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 20px;
        display: none;
    }
    .filter-section.visible { display: block; }
    .filter-row { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
    .filter-row label { font-size: 13px; font-weight: 600; color: #555; margin-bottom: 0; }
    .filter-row input[type="date"],
    .filter-row select {
        border: 1px solid #ddd; border-radius: 6px; padding: 6px 10px;
        font-size: 13px; height: 34px;
    }
    .filter-row .btn-generate {
        background: #3498db; color: #fff; border: none; border-radius: 6px;
        padding: 7px 20px; font-size: 13px; font-weight: 600; cursor: pointer;
    }
    .filter-row .btn-generate:hover { background: #2980b9; }
    .filter-row .btn-csv {
        background: #27ae60; color: #fff; border: none; border-radius: 6px;
        padding: 7px 16px; font-size: 13px; font-weight: 600; cursor: pointer;
        display: none;
    }
    .filter-row .btn-csv:hover { background: #219a52; }
    .filter-extra { display: none; }
    .filter-extra.visible { display: contents; }

    .report-summary {
        display: none; background: #fff; border-radius: 10px; padding: 15px 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 20px;
    }
    .report-summary.visible { display: block; }
    .summary-badges { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }
    .summary-badge {
        background: #f0f4f8; border-radius: 20px; padding: 6px 14px;
        font-size: 13px; font-weight: 600; color: #2c3e50;
    }
    .summary-badge .sb-val { color: #3498db; }
    .record-count {
        font-size: 14px; font-weight: 600; color: #2c3e50; margin-bottom: 10px;
    }
    .record-count span { color: #3498db; }

    .report-table-wrap {
        display: none; background: #fff; border-radius: 10px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden;
    }
    .report-table-wrap.visible { display: block; }
    .report-table {
        width: 100%; border-collapse: collapse; font-size: 13px;
    }
    .report-table thead th {
        background: #f8f9fa; padding: 10px 12px; text-align: left;
        font-weight: 600; color: #555; border-bottom: 2px solid #e9ecef;
        cursor: pointer; white-space: nowrap; user-select: none;
    }
    .report-table thead th:hover { background: #e9ecef; }
    .report-table thead th .sort-icon { margin-left: 4px; color: #aaa; font-size: 11px; }
    .report-table tbody td {
        padding: 9px 12px; border-bottom: 1px solid #f0f0f0; color: #333;
        max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
    }
    .report-table tbody tr:hover { background: #f8f9fa; }

    .empty-state {
        display: none; text-align: center; padding: 50px 20px; color: #999;
    }
    .empty-state.visible { display: block; }
    .empty-state .fa { font-size: 48px; margin-bottom: 15px; color: #ddd; }
    .empty-state p { font-size: 15px; }
    .truncated-notice {
        display: none; padding: 10px 20px; font-size: 13px; color: #777;
        background: #fff9e6; border-radius: 0 0 10px 10px;
    }
    .truncated-notice.visible { display: block; }

    .loading-overlay {
        display: none; text-align: center; padding: 40px;
    }
    .loading-overlay.visible { display: block; }
    .loading-overlay .fa-spinner { font-size: 32px; color: #3498db; }

    /* ── Scheduled Reports Tab ── */
    .sched-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; }
    .sched-stats { display: flex; gap: 12px; }
    .sched-stat {
        background: #fff; border-radius: 10px; padding: 12px 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1); font-size: 13px; font-weight: 600; color: #2c3e50;
    }
    .sched-stat .ss-val { color: #3498db; font-size: 18px; margin-right: 4px; }
    .btn-new-schedule {
        background: #3498db; color: #fff; border: none; border-radius: 8px;
        padding: 10px 20px; font-size: 13px; font-weight: 600; cursor: pointer;
    }
    .btn-new-schedule:hover { background: #2980b9; }

    .sched-list { display: flex; flex-direction: column; gap: 12px; }
    .sched-card {
        background: #fff; border-radius: 10px; padding: 16px 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1); display: flex;
        align-items: center; justify-content: space-between;
        transition: opacity 0.2s;
    }
    .sched-card.inactive { opacity: 0.5; }
    .sched-card-info { flex: 1; }
    .sched-card-name { font-size: 15px; font-weight: 600; color: #2c3e50; margin-bottom: 6px; }
    .sched-card-meta { display: flex; gap: 12px; flex-wrap: wrap; align-items: center; font-size: 12px; color: #777; }
    .sched-badge {
        display: inline-block; padding: 2px 10px; border-radius: 12px;
        font-size: 11px; font-weight: 600; color: #fff;
    }
    .sched-badge-type { background: #6c5ce7; }
    .sched-badge-freq { background: #3498db; }
    .sched-badge-paused { background: #e74c3c; }
    .sched-card-actions { display: flex; gap: 8px; }
    .sched-card-actions button {
        background: none; border: 1px solid #ddd; border-radius: 6px;
        width: 32px; height: 32px; cursor: pointer; font-size: 13px; color: #555;
        display: flex; align-items: center; justify-content: center;
        transition: background 0.2s;
    }
    .sched-card-actions button:hover { background: #f0f0f0; }
    .sched-card-actions button.btn-delete:hover { background: #ffeaea; color: #e74c3c; border-color: #e74c3c; }

    .sched-empty { text-align: center; padding: 50px 20px; color: #999; }
    .sched-empty .fa { font-size: 48px; margin-bottom: 15px; color: #ddd; }

    /* Schedule modal */
    .sched-modal-body label { font-size: 13px; font-weight: 600; color: #555; display: block; margin-bottom: 4px; margin-top: 12px; }
    .sched-modal-body input[type="text"],
    .sched-modal-body input[type="time"],
    .sched-modal-body select,
    .sched-modal-body textarea {
        width: 100%; border: 1px solid #ddd; border-radius: 6px; padding: 8px 10px;
        font-size: 13px;
    }
    .sched-modal-body textarea { height: 60px; resize: vertical; }
    .sched-modal-body .form-row { display: flex; gap: 15px; }
    .sched-modal-body .form-row > div { flex: 1; }
    .sched-next-run {
        margin-top: 15px; padding: 10px 15px; background: #f0f8ff; border-radius: 8px;
        font-size: 13px; color: #2c3e50;
    }
    .sched-next-run i { margin-right: 6px; color: #3498db; }
</style>

<div class="rpt-container">
    <div class="rpt-title">Reports</div>
    <div class="rpt-subtitle">Generate and schedule reports from your care home data</div>

    <!-- Tab Bar -->
    <div class="rpt-tabs">
        <div class="rpt-tab active" data-tab="generate"><i class="fa fa-cog"></i> Generate Report</div>
        <div class="rpt-tab" data-tab="scheduled"><i class="fa fa-clock-o"></i> Scheduled Reports</div>
    </div>

    <!-- ════ Tab 1: Generate Report ════ -->
    <div class="rpt-tab-content active" id="tabGenerate">
        <div class="report-cards">
            <div class="report-card" data-type="incidents">
                <div class="rc-icon rc-icon-incidents"><i class="fa fa-exclamation-triangle"></i></div>
                <div class="rc-label">Incident Summary</div>
            </div>
            <div class="report-card" data-type="training">
                <div class="rc-icon rc-icon-training"><i class="fa fa-graduation-cap"></i></div>
                <div class="rc-label">Training Compliance</div>
            </div>
            <div class="report-card" data-type="mar">
                <div class="rc-icon rc-icon-mar"><i class="fa fa-medkit"></i></div>
                <div class="rc-label">MAR Compliance</div>
            </div>
            <div class="report-card" data-type="shifts">
                <div class="rc-icon rc-icon-shifts"><i class="fa fa-calendar"></i></div>
                <div class="rc-label">Shift Coverage</div>
            </div>
            <div class="report-card" data-type="feedback">
                <div class="rc-icon rc-icon-feedback"><i class="fa fa-comments"></i></div>
                <div class="rc-label">Client Feedback</div>
            </div>
        </div>

        <div class="filter-section" id="filterSection">
            <div class="filter-row">
                <label>From:</label>
                <input type="date" id="dateFrom">
                <label>To:</label>
                <input type="date" id="dateTo">

                <div class="filter-extra" data-for="training"><label>Status:</label></div>
                <select class="filter-extra" data-for="training" id="filterTrainingStatus">
                    <option value="">All Statuses</option>
                    <option value="0">Pending</option>
                    <option value="1">In Progress</option>
                    <option value="2">Completed</option>
                </select>

                <div class="filter-extra" data-for="mar"><label>Code:</label></div>
                <select class="filter-extra" data-for="mar" id="filterMARCode">
                    <option value="">All Codes</option>
                    <option value="A">Administered</option>
                    <option value="R">Refused</option>
                    <option value="S">Spoilt</option>
                </select>

                <div class="filter-extra" data-for="shifts"><label>Type:</label></div>
                <select class="filter-extra" data-for="shifts" id="filterShiftType">
                    <option value="">All Types</option>
                    <option value="morning">Morning</option>
                    <option value="afternoon">Afternoon</option>
                    <option value="night">Night</option>
                    <option value="waking_night">Waking Night</option>
                </select>
                <div class="filter-extra" data-for="shifts"><label>Status:</label></div>
                <select class="filter-extra" data-for="shifts" id="filterShiftStatus">
                    <option value="">All Statuses</option>
                    <option value="unfilled">Unfilled</option>
                    <option value="assigned">Assigned</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>

                <div class="filter-extra" data-for="feedback"><label>Type:</label></div>
                <select class="filter-extra" data-for="feedback" id="filterFeedbackType">
                    <option value="">All Types</option>
                    <option value="compliment">Compliment</option>
                    <option value="complaint">Complaint</option>
                    <option value="suggestion">Suggestion</option>
                    <option value="concern">Concern</option>
                    <option value="general">General</option>
                </select>
                <div class="filter-extra" data-for="feedback"><label>Status:</label></div>
                <select class="filter-extra" data-for="feedback" id="filterFeedbackStatus">
                    <option value="">All Statuses</option>
                    <option value="new">New</option>
                    <option value="acknowledged">Acknowledged</option>
                    <option value="in_progress">In Progress</option>
                    <option value="resolved">Resolved</option>
                    <option value="closed">Closed</option>
                </select>

                <button class="btn-generate" id="btnGenerate"><i class="fa fa-cog"></i> Generate</button>
                <button class="btn-csv" id="btnExportCSV"><i class="fa fa-download"></i> Export CSV</button>
            </div>
        </div>

        <div class="loading-overlay" id="loadingOverlay">
            <i class="fa fa-spinner fa-spin"></i>
            <p>Generating report...</p>
        </div>

        <div class="report-summary" id="reportSummary">
            <div class="record-count" id="recordCount"></div>
            <div class="summary-badges" id="summaryBadges"></div>
        </div>

        <div class="report-table-wrap" id="reportTableWrap">
            <table class="report-table" id="reportTable">
                <thead id="reportThead"></thead>
                <tbody id="reportTbody"></tbody>
            </table>
            <div class="truncated-notice" id="truncatedNotice">
                Showing 500 of <span id="totalRecords"></span> records. Export CSV to see all displayed data.
            </div>
        </div>

        <div class="empty-state" id="emptyState">
            <i class="fa fa-bar-chart"></i>
            <p>No data found for the selected filters.</p>
            <p style="font-size:13px;">Try adjusting the date range or filter options.</p>
        </div>
    </div>

    <!-- ════ Tab 2: Scheduled Reports ════ -->
    <div class="rpt-tab-content" id="tabScheduled">
        <div class="sched-header">
            <div class="sched-stats">
                <div class="sched-stat"><span class="ss-val" id="schedActiveCount">0</span> Active</div>
                <div class="sched-stat"><span class="ss-val" id="schedSentCount">0</span> Sent This Month</div>
            </div>
            <button class="btn-new-schedule" id="btnNewSchedule"><i class="fa fa-plus"></i> New Schedule</button>
        </div>

        <div class="sched-list" id="schedList"></div>

        <div class="sched-empty" id="schedEmpty" style="display:none;">
            <i class="fa fa-clock-o"></i>
            <p>No scheduled reports yet.</p>
            <p style="font-size:13px;">Create one to automate report generation and delivery.</p>
        </div>
    </div>
</div>

<!-- Schedule Create/Edit Modal -->
<div class="modal fade" id="scheduleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="schedModalTitle">New Schedule</h4>
            </div>
            <div class="modal-body sched-modal-body">
                <input type="hidden" id="schedEditId">

                <label>Report Name <span style="color:#e74c3c">*</span></label>
                <input type="text" id="schedName" maxlength="255" placeholder="e.g. Weekly Training Report">

                <div class="form-row">
                    <div>
                        <label>Report Type <span style="color:#e74c3c">*</span></label>
                        <select id="schedType">
                            <option value="incidents">Incident Summary</option>
                            <option value="training">Training Compliance</option>
                            <option value="mar">MAR Compliance</option>
                            <option value="shifts">Shift Coverage</option>
                            <option value="feedback">Client Feedback</option>
                        </select>
                    </div>
                    <div>
                        <label>Frequency <span style="color:#e74c3c">*</span></label>
                        <select id="schedFrequency">
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="fortnightly">Fortnightly</option>
                            <option value="monthly">Monthly</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div id="schedDayOfWeekWrap">
                        <label>Day of Week</label>
                        <select id="schedDayOfWeek">
                            <option value="0">Sunday</option>
                            <option value="1" selected>Monday</option>
                            <option value="2">Tuesday</option>
                            <option value="3">Wednesday</option>
                            <option value="4">Thursday</option>
                            <option value="5">Friday</option>
                            <option value="6">Saturday</option>
                        </select>
                    </div>
                    <div id="schedDayOfMonthWrap" style="display:none;">
                        <label>Day of Month</label>
                        <select id="schedDayOfMonth">
                            @for($i = 1; $i <= 28; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label>Time <span style="color:#e74c3c">*</span></label>
                        <input type="time" id="schedTime" value="08:00">
                    </div>
                </div>

                <label>Recipients <span style="color:#e74c3c">*</span></label>
                <input type="text" id="schedRecipients" maxlength="1000" placeholder="email1@example.com, email2@example.com">
                <span style="font-size:11px; color:#999;">Comma-separated emails (max 5)</span>

                <div class="form-row">
                    <div>
                        <label>Output Format</label>
                        <select id="schedFormat">
                            <option value="csv">CSV Attachment</option>
                            <option value="email_summary">Email Summary</option>
                        </select>
                    </div>
                    <div style="display:flex; align-items:flex-end; padding-bottom:4px;">
                        <label style="display:inline; margin-right:8px;">
                            <input type="checkbox" id="schedActive" checked> Active
                        </label>
                    </div>
                </div>

                <label>Notes</label>
                <textarea id="schedNotes" maxlength="1000" placeholder="Optional notes about this schedule"></textarea>

                <div class="sched-next-run" id="schedNextRunPreview">
                    <i class="fa fa-calendar"></i> Next run: <strong id="schedNextRunText">—</strong>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btnSaveSchedule">Save Schedule</button>
            </div>
        </div>
    </div>
</div>

</main>

<script>var baseUrl = "{{ url('') }}";</script>
<script src="{{ url('public/js/roster/reports.js') }}"></script>

@endsection
