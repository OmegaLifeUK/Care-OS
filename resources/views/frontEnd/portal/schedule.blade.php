@extends('frontEnd.portal.layouts.master')

@section('title', '— Schedule')

@section('styles')
<style>
    .schedule-header {
        margin-bottom: 25px;
    }
    .schedule-header h2 {
        margin: 0 0 5px;
        font-weight: 600;
        color: #2c3e50;
    }
    .schedule-header p {
        margin: 0;
        color: #777;
        font-size: 15px;
    }
    .week-nav {
        background: #fff;
        border-radius: 8px;
        padding: 15px 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .week-nav .nav-arrows {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .week-nav .nav-arrows .btn {
        width: 36px;
        height: 36px;
        padding: 0;
        line-height: 36px;
        text-align: center;
        border-radius: 6px;
        font-size: 16px;
    }
    .week-nav .week-label {
        font-size: 16px;
        font-weight: 600;
        color: #2c3e50;
        min-width: 220px;
        text-align: center;
    }
    .week-nav .btn-today {
        border-radius: 6px;
        font-size: 13px;
        padding: 6px 16px;
    }
    .calendar-grid {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        overflow: hidden;
        margin-bottom: 25px;
    }
    .calendar-grid .grid-header {
        display: flex;
        border-bottom: 1px solid #e0e0e0;
    }
    .calendar-grid .grid-header .day-header {
        flex: 1;
        text-align: center;
        padding: 12px 8px;
        border-right: 1px solid #e0e0e0;
    }
    .calendar-grid .grid-header .day-header:last-child {
        border-right: none;
    }
    .calendar-grid .grid-header .day-header.is-today {
        background: #eef5fb;
    }
    .day-header .day-abbr {
        text-transform: uppercase;
        font-size: 11px;
        color: #777;
        font-weight: 600;
        letter-spacing: 0.5px;
    }
    .day-header .day-num {
        font-size: 20px;
        font-weight: 700;
        color: #2c3e50;
        margin: 2px 0;
    }
    .day-header.is-today .day-num {
        color: #3498db;
    }
    .day-header .day-count {
        font-size: 11px;
        color: #999;
    }
    .calendar-grid .grid-body {
        display: flex;
        min-height: 200px;
    }
    .calendar-grid .grid-body .day-col {
        flex: 1;
        padding: 8px;
        border-right: 1px solid #e0e0e0;
        background: #fafafa;
    }
    .calendar-grid .grid-body .day-col:last-child {
        border-right: none;
    }
    .calendar-grid .grid-body .day-col.is-today {
        background: #f0f7fe;
    }
    .shift-card {
        background: #fff;
        border-radius: 6px;
        padding: 8px 10px;
        margin-bottom: 6px;
        border-left: 3px solid #3498db;
        font-size: 12px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.06);
    }
    .shift-card.status-completed {
        border-left-color: #2ecc71;
    }
    .shift-card.status-unfilled {
        border-left-color: #e67e22;
    }
    .shift-card.status-in_progress {
        border-left-color: #f1c40f;
    }
    .shift-card .shift-time {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 3px;
    }
    .shift-card .shift-type {
        display: inline-block;
        padding: 1px 6px;
        border-radius: 3px;
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 3px;
    }
    .shift-type-morning { background: #fef3cd; color: #856404; }
    .shift-type-afternoon { background: #d1ecf1; color: #0c5460; }
    .shift-type-evening { background: #d4edda; color: #155724; }
    .shift-type-night { background: #e2e3e5; color: #383d41; }
    .shift-card .shift-staff {
        color: #555;
        font-size: 11px;
    }
    .shift-card .shift-staff.unfilled {
        color: #e67e22;
        font-weight: 600;
    }
    .status-badge {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 10px;
        font-weight: 600;
    }
    .status-badge.badge-assigned { background: #d4e6f1; color: #2471a3; }
    .status-badge.badge-completed { background: #d5f5e3; color: #1e8449; }
    .status-badge.badge-in_progress { background: #fdebd0; color: #b7950b; }
    .status-badge.badge-unfilled { background: #fdebd0; color: #ca6f1e; }
    .list-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        overflow: hidden;
    }
    .list-card .list-header {
        padding: 15px 20px;
        border-bottom: 1px solid #e0e0e0;
        font-size: 16px;
        font-weight: 600;
        color: #2c3e50;
    }
    .list-card .list-body {
        padding: 15px 20px;
    }
    .list-item {
        display: flex;
        align-items: center;
        padding: 12px 15px;
        border: 1px solid #e8e8e8;
        border-radius: 8px;
        margin-bottom: 10px;
        transition: box-shadow 0.15s;
    }
    .list-item:hover {
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    }
    .list-item .list-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        background: #eef5fb;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        flex-shrink: 0;
    }
    .list-item .list-icon i {
        font-size: 18px;
        color: #3498db;
    }
    .list-item .list-details {
        flex: 1;
    }
    .list-item .list-details .list-title {
        font-weight: 600;
        font-size: 14px;
        color: #2c3e50;
        margin-bottom: 2px;
    }
    .list-item .list-details .list-sub {
        font-size: 12px;
        color: #777;
    }
    .list-item .list-meta {
        text-align: right;
        flex-shrink: 0;
    }
    .list-item .list-meta .list-time {
        font-weight: 600;
        font-size: 13px;
        color: #2c3e50;
    }
    .list-item .list-meta .list-staff {
        font-size: 12px;
        color: #777;
    }
    .badge-today {
        display: inline-block;
        background: #3498db;
        color: #fff;
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 10px;
        font-weight: 600;
        margin-left: 8px;
    }
    .empty-state {
        text-align: center;
        padding: 50px 20px;
        color: #999;
    }
    .empty-state i {
        font-size: 48px;
        color: #ddd;
        margin-bottom: 15px;
    }
    .empty-state p {
        font-size: 15px;
    }
    .access-denied {
        background: #fdf0f0;
        border: 1px solid #f5c6cb;
        border-radius: 8px;
        padding: 25px 30px;
    }
    .access-denied h4 {
        color: #721c24;
        font-weight: 600;
        margin: 0 0 8px;
    }
    .access-denied p {
        color: #856404;
        margin: 0 0 15px;
        font-size: 14px;
    }
</style>
@endsection

@section('content')
<div class="schedule-header">
    <h2>My Schedule</h2>
    <p>View your upcoming sessions and appointments</p>
</div>

@if($access_denied)
    <div class="access-denied">
        <h4><i class="fa fa-exclamation-circle"></i> Access Denied</h4>
        <p>You do not have permission to view the schedule. Please contact the care team.</p>
        <a href="{{ url('/portal') }}" class="btn btn-default">
            <i class="fa fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>
@else
    {{-- Week Navigation --}}
    <div class="week-nav">
        <div class="nav-arrows">
            <a href="{{ url('/portal/schedule?week=' . $week_start->copy()->subWeek()->toDateString()) }}" class="btn btn-default">
                <i class="fa fa-chevron-left"></i>
            </a>
            <span class="week-label">
                {{ $week_start->format('M d') }} &ndash; {{ $week_end->format('M d, Y') }}
            </span>
            <a href="{{ url('/portal/schedule?week=' . $week_start->copy()->addWeek()->toDateString()) }}" class="btn btn-default">
                <i class="fa fa-chevron-right"></i>
            </a>
        </div>
        <a href="{{ url('/portal/schedule') }}" class="btn btn-primary btn-today">
            <i class="fa fa-calendar"></i> Today
        </a>
    </div>

    {{-- Weekly Calendar Grid --}}
    <div class="calendar-grid">
        <div class="grid-header">
            @foreach($week_days as $day)
                @php
                    $isToday = $day->isToday();
                    $dayShifts = $shifts->filter(fn($s) => \Carbon\Carbon::parse($s->start_date)->isSameDay($day));
                @endphp
                <div class="day-header {{ $isToday ? 'is-today' : '' }}">
                    <div class="day-abbr">{{ $day->format('D') }}</div>
                    <div class="day-num">{{ $day->format('j') }}</div>
                    <div class="day-count">{{ $dayShifts->count() }} {{ $dayShifts->count() === 1 ? 'item' : 'items' }}</div>
                </div>
            @endforeach
        </div>
        <div class="grid-body">
            @foreach($week_days as $day)
                @php
                    $isToday = $day->isToday();
                    $dayShifts = $shifts->filter(fn($s) => \Carbon\Carbon::parse($s->start_date)->isSameDay($day));
                @endphp
                <div class="day-col {{ $isToday ? 'is-today' : '' }}">
                    @forelse($dayShifts as $shift)
                        <div class="shift-card status-{{ $shift->status }}">
                            <div class="shift-time">
                                {{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }} &ndash; {{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}
                            </div>
                            @if($shift->shift_type)
                                <span class="shift-type shift-type-{{ $shift->shift_type }}">{{ ucfirst($shift->shift_type) }}</span>
                            @endif
                            <div class="shift-staff {{ !$shift->staff ? 'unfilled' : '' }}">
                                <i class="fa fa-user"></i>
                                {{ $shift->staff ? $shift->staff->name : 'Unfilled' }}
                            </div>
                            @if($shift->status === 'completed')
                                <span class="status-badge badge-completed">Completed</span>
                            @elseif($shift->status === 'in_progress')
                                <span class="status-badge badge-in_progress">In Progress</span>
                            @endif
                        </div>
                    @empty
                        {{-- empty day --}}
                    @endforelse
                </div>
            @endforeach
        </div>
    </div>

    {{-- List View --}}
    <div class="list-card">
        <div class="list-header">
            <i class="fa fa-list"></i> This Week's Schedule
        </div>
        <div class="list-body">
            @if($shifts->isEmpty())
                <div class="empty-state">
                    <i class="fa fa-calendar-o"></i>
                    <p>No scheduled items this week</p>
                </div>
            @else
                @foreach($shifts as $shift)
                    @php
                        $shiftDate = \Carbon\Carbon::parse($shift->start_date);
                        $isShiftToday = $shiftDate->isToday();
                    @endphp
                    <div class="list-item">
                        <div class="list-icon">
                            <i class="fa fa-calendar-check-o"></i>
                        </div>
                        <div class="list-details">
                            <div class="list-title">
                                {{ ucfirst($shift->shift_type ?? 'Shift') }}
                                @if($isShiftToday)
                                    <span class="badge-today">Today</span>
                                @endif
                            </div>
                            <div class="list-sub">
                                {{ $shiftDate->format('l, M j, Y') }}
                                @if($shift->tasks)
                                    &mdash; {{ $shift->tasks }}
                                @endif
                            </div>
                        </div>
                        <div class="list-meta">
                            <div class="list-time">
                                {{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }} &ndash; {{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}
                            </div>
                            <div class="list-staff" style="{{ !$shift->staff ? 'color:#e67e22;font-weight:600' : '' }}">
                                <i class="fa fa-user"></i>
                                {{ $shift->staff ? $shift->staff->name : 'Unfilled' }}
                            </div>
                            <span class="status-badge badge-{{ $shift->status }}">{{ ucfirst(str_replace('_', ' ', $shift->status)) }}</span>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
@endif
@endsection

@section('scripts')
<script src="{{ url('public/js/portal/schedule.js') }}"></script>
@endsection
