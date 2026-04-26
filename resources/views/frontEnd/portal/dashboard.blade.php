@extends('frontEnd.portal.layouts.master')

@section('title', '— Dashboard')

@section('styles')
<style>
    .welcome-banner {
        background: linear-gradient(135deg, #3498db, #2c3e50);
        color: #fff;
        border-radius: 8px;
        padding: 25px 30px;
        margin-bottom: 25px;
    }
    .welcome-banner h2 {
        margin: 0 0 5px;
        font-weight: 600;
    }
    .welcome-banner p {
        margin: 0;
        opacity: 0.85;
        font-size: 15px;
    }
    .resident-card {
        background: #fff;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }
    .resident-card .avatar {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        background: #ecf0f1;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        color: #95a5a6;
        float: left;
        margin-right: 15px;
        overflow: hidden;
    }
    .resident-card .avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .resident-card h4 {
        margin: 0 0 5px;
        font-weight: 600;
    }
    .resident-card .detail {
        color: #777;
        font-size: 13px;
        margin-bottom: 2px;
    }
    .stat-card {
        background: #fff;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        text-align: center;
        margin-bottom: 20px;
    }
    .stat-card .stat-icon {
        font-size: 32px;
        margin-bottom: 10px;
    }
    .stat-card .stat-value {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 5px;
    }
    .stat-card .stat-label {
        font-size: 13px;
        color: #777;
    }
    .stat-card .coming-soon {
        font-size: 11px;
        color: #bdc3c7;
        font-style: italic;
        margin-top: 5px;
    }
    .stat-schedule { border-top: 3px solid #3498db; }
    .stat-schedule .stat-icon { color: #3498db; }
    .stat-messages { border-top: 3px solid #2ecc71; }
    .stat-messages .stat-icon { color: #2ecc71; }
    .stat-requests { border-top: 3px solid #e67e22; }
    .stat-requests .stat-icon { color: #e67e22; }
    .stat-notifications { border-top: 3px solid #9b59b6; }
    .stat-notifications .stat-icon { color: #9b59b6; }
    .quick-actions {
        margin-top: 10px;
    }
    .quick-actions .btn {
        margin: 5px;
        padding: 12px 25px;
        font-size: 14px;
        border-radius: 6px;
    }
</style>
@endsection

@section('content')
<div class="welcome-banner">
    <h2>Welcome, {{ $portal_access->full_name }}</h2>
    <p>
        {{ ucfirst($portal_access->relationship) }} of {{ $client->name ?? 'Resident' }}
    </p>
</div>

<div class="resident-card clearfix">
    <div class="avatar">
        @if(!empty($client->image))
            <img src="{{ url('public/uploads/service_users/' . $client->image) }}" alt="{{ $client->name ?? 'Resident' }}">
        @else
            <i class="fa fa-user"></i>
        @endif
    </div>
    <div>
        <h4>{{ $client->name ?? 'Resident' }}</h4>
        @if(!empty($client->date_of_birth))
            <div class="detail"><i class="fa fa-birthday-cake"></i> {{ $client->date_of_birth }}</div>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-md-3 col-sm-6">
        <div class="stat-card stat-schedule">
            <div class="stat-icon"><i class="fa fa-calendar"></i></div>
            <div class="stat-value">{{ $stats['upcoming_schedule'] }}</div>
            <div class="stat-label">Upcoming Schedule</div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-card stat-messages">
            <div class="stat-icon"><i class="fa fa-envelope"></i></div>
            <div class="stat-value">{{ $stats['unread_messages'] }}</div>
            <div class="stat-label">Unread Messages</div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-card stat-requests">
            <div class="stat-icon"><i class="fa fa-clock-o"></i></div>
            <div class="stat-value">{{ $stats['pending_requests'] }}</div>
            <div class="stat-label">Pending Requests</div>
            <div class="coming-soon">Coming soon</div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-card stat-notifications">
            <div class="stat-icon"><i class="fa fa-bell"></i></div>
            <div class="stat-value">{{ $stats['notifications'] }}</div>
            <div class="stat-label">Notifications</div>
            <div class="coming-soon">Coming soon</div>
        </div>
    </div>
</div>

<div class="quick-actions text-center">
    <h4 style="color:#555; margin-bottom:15px">Quick Actions</h4>
    <a href="{{ url('/portal/schedule') }}" class="btn btn-primary">
        <i class="fa fa-calendar"></i> View Schedule
    </a>
    <a href="{{ url('/portal/messages') }}" class="btn btn-success">
        <i class="fa fa-envelope"></i> Send Message
    </a>
    <a href="{{ url('/portal/feedback') }}" class="btn btn-warning">
        <i class="fa fa-comment"></i> Submit Feedback
    </a>
</div>
@endsection

@section('scripts')
<script src="{{ url('public/js/portal/dashboard.js') }}"></script>
@endsection
