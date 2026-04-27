@extends('frontEnd.layouts.master')
@section('title','Reports')
@section('content')

@include('frontEnd.roster.common.roster_header')
<main class="page-content">

<style>
    .rpt-container { padding: 20px 30px; }
    .rpt-title { font-size: 22px; font-weight: 600; color: #2c3e50; margin-bottom: 5px; }
    .rpt-subtitle { color: #777; font-size: 14px; margin-bottom: 25px; }

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
</style>

<div class="rpt-container">
    <div class="rpt-title">Reports</div>
    <div class="rpt-subtitle">Generate reports from your care home data</div>

    <!-- Report Type Cards -->
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

    <!-- Filter Bar -->
    <div class="filter-section" id="filterSection">
        <div class="filter-row">
            <label>From:</label>
            <input type="date" id="dateFrom">
            <label>To:</label>
            <input type="date" id="dateTo">

            <!-- Training filters -->
            <div class="filter-extra" data-for="training">
                <label>Status:</label>
            </div>
            <select class="filter-extra" data-for="training" id="filterTrainingStatus">
                <option value="">All Statuses</option>
                <option value="0">Pending</option>
                <option value="1">In Progress</option>
                <option value="2">Completed</option>
            </select>

            <!-- MAR filters -->
            <div class="filter-extra" data-for="mar">
                <label>Code:</label>
            </div>
            <select class="filter-extra" data-for="mar" id="filterMARCode">
                <option value="">All Codes</option>
                <option value="A">Administered</option>
                <option value="R">Refused</option>
                <option value="S">Spoilt</option>
            </select>

            <!-- Shift filters -->
            <div class="filter-extra" data-for="shifts">
                <label>Type:</label>
            </div>
            <select class="filter-extra" data-for="shifts" id="filterShiftType">
                <option value="">All Types</option>
                <option value="morning">Morning</option>
                <option value="afternoon">Afternoon</option>
                <option value="night">Night</option>
                <option value="waking_night">Waking Night</option>
            </select>
            <div class="filter-extra" data-for="shifts">
                <label>Status:</label>
            </div>
            <select class="filter-extra" data-for="shifts" id="filterShiftStatus">
                <option value="">All Statuses</option>
                <option value="unfilled">Unfilled</option>
                <option value="assigned">Assigned</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>

            <!-- Feedback filters -->
            <div class="filter-extra" data-for="feedback">
                <label>Type:</label>
            </div>
            <select class="filter-extra" data-for="feedback" id="filterFeedbackType">
                <option value="">All Types</option>
                <option value="compliment">Compliment</option>
                <option value="complaint">Complaint</option>
                <option value="suggestion">Suggestion</option>
                <option value="concern">Concern</option>
                <option value="general">General</option>
            </select>
            <div class="filter-extra" data-for="feedback">
                <label>Status:</label>
            </div>
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

    <!-- Loading -->
    <div class="loading-overlay" id="loadingOverlay">
        <i class="fa fa-spinner fa-spin"></i>
        <p>Generating report...</p>
    </div>

    <!-- Summary -->
    <div class="report-summary" id="reportSummary">
        <div class="record-count" id="recordCount"></div>
        <div class="summary-badges" id="summaryBadges"></div>
    </div>

    <!-- Results Table -->
    <div class="report-table-wrap" id="reportTableWrap">
        <table class="report-table" id="reportTable">
            <thead id="reportThead"></thead>
            <tbody id="reportTbody"></tbody>
        </table>
        <div class="truncated-notice" id="truncatedNotice">
            Showing 500 of <span id="totalRecords"></span> records. Export CSV to see all displayed data.
        </div>
    </div>

    <!-- Empty State -->
    <div class="empty-state" id="emptyState">
        <i class="fa fa-bar-chart"></i>
        <p>No data found for the selected filters.</p>
        <p style="font-size:13px;">Try adjusting the date range or filter options.</p>
    </div>
</div>

</main>

<script>var baseUrl = "{{ url('') }}";</script>
<script src="{{ url('public/js/roster/reports.js') }}"></script>

@endsection
