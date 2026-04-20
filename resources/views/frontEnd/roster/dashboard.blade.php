@extends('frontEnd.layouts.master')
@section('title','Dashboard')
@section('content')


@include('frontEnd.roster.common.roster_header')
<section id="main-content">
    <div class="wrapper ps-0 pe-0 ">
        <div class="container-fluid">
            {{-- <div class="row">
                <div class="col-md-12">
                    <div class="wrappermenu">
                        <nav>
                            <input type="checkbox" id="show-search">
                            <input type="checkbox" id="show-menu">
                            <label for="show-menu" class="menu-icon"><i class="fas fa-bars"></i></label>
                            <div class="content">
                                <ul class="links">
                                    <li><a href="#"> <i class="fa fa-tachometer"></i> Dashboard</a></li>
                                    <li><a href="{{ url('/roster/manage-dashboard') }}"> <i class="fa fa-tachometer"></i> Manager Dashboard</a></li>
                                    <li><a href="{{ url('/roster/schedule-shift') }}"> <i class="fa fa-tachometer"></i> Schedule</a></li>
                                    <li><a href="#"> <i class="fa fa-tachometer"></i> Carer Availability</a></li>
                                    <li><a href="#"> <i class="fa fa-tachometer"></i> Messaging Center</a></li>
                                    <li><a href="#"> <i class="fa fa-tachometer"></i> Staff Tasks</a></li>
                                    <li><a href="#"> <i class="fa fa-tachometer"></i> Carers</a></li>
                                    <li><a href="#"> <i class="fa fa-tachometer"></i> Clients</a></li>
                                    <li><a href="#"> <i class="fa fa-tachometer"></i> Care Documents</a></li>
                                    <li><a href="#"> <i class="fa fa-tachometer"></i> Reports</a></li>
                                    <li><a href="#"> <i class="fa fa-tachometer"></i> Leave Requests</a></li>
                                    <li><a href="#"> <i class="fa fa-tachometer"></i> Daily Log</a></li>
                                </ul>
                            </div>
                        </nav>
                    </div>
                </div>
            </div> --}}
            {{-- SOS Alert Trigger --}}
            <div class="row m-t-30">
                <div class="col-md-12">
                    <div class="panel" style="background: #d9534f; border: none;">
                        <div class="panel-body text-center" style="padding: 12px;">
                            <button type="button" id="sos-trigger-btn" class="btn btn-lg" style="background: #fff; color: #d9534f; font-weight: bold; font-size: 18px; padding: 10px 40px; border-radius: 4px;">
                                <i class="fa fa-exclamation-triangle"></i> SOS ALERT
                            </button>
                            <p style="color: #fff; margin: 8px 0 0; font-size: 13px;">Press to alert all managers immediately</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SOS Alert History --}}
            <div class="row">
                <div class="col-md-12">
                    <div class="panel">
                        <header class="panel-heading">
                            <i class="fa fa-exclamation-triangle" style="color: #d9534f;"></i> SOS Alert History
                            <span id="sos-active-count" class="badge" style="background: #d9534f; color: #fff; margin-left: 5px;"></span>
                        </header>
                        <div class="panel-body">
                            <div id="sos-alerts-container">
                                <p class="text-muted text-center">Loading alerts...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-9">
                    <div class="m-t-30">
                        <div class="panel">
                            <header class="panel-heading"> Dashboard</header>
                            <div class="panel-body rosterBox">

                                <div class="col-md-3 col-sm-3 col-xs-6">
                                    <a href="{{ url('roster/dashboard') }}">
                                        <div class="sys-mngmnt-box">
                                            <div>
                                                <div class="sysMngmnticon">
                                                    <i class="fa fa-building-o"></i>
                                                </div>
                                            </div>
                                            <div class="rotsBoxRightCont">
                                                <h4>{{ $serviceUserCount }} </h4>
                                                <p> Active Clients </p>
                                            </div>
                                        </div>
                                    </a>
                                </div>

                                <div class="col-md-3 col-sm-3 col-xs-6">
                                    <a href="#!" data-toggle="modal">
                                        <div class="sys-mngmnt-box">
                                            <div>
                                                <div class="sysMngmnticon">
                                                    <i class="fa fa-medkit"></i>
                                                </div>
                                            </div>
                                            <div class="rotsBoxRightCont">
                                                <h4>{{ $userCount }} </h4>
                                                <p>Active Carers</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>

                                <div class="col-md-3 col-sm-3 col-xs-6">
                                    <a href="#!">
                                        <div class="sys-mngmnt-box">
                                            <div>
                                                <div class="sysMngmnticon">
                                                    <i class="fa fa-life-ring"></i>
                                                </div>
                                            </div>
                                            <div class="rotsBoxRightCont">
                                                <h4>44 </h4>
                                                <p> Today's Shifts </p>
                                            </div>
                                        </div>
                                    </a>
                                </div>

                                <div class="col-md-3 col-sm-3 col-xs-6">
                                    <a href="#!">
                                        <div class="sys-mngmnt-box">
                                            <div>
                                                <div class="sysMngmnticon">
                                                    <i class="fa fa-sun-o"></i>
                                                </div>
                                            </div>
                                            <div class="rotsBoxRightCont">
                                                <h4>22 </h4>
                                                <p>Unfilled Shifts</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        
                        <div class="col-md-12">
                            <div class="panel">
                                <header class="panel-heading headingCapitilize"> Today's Shifts</header>
                                <div class="panel-body">
                                    <div class="todayShiftsList">
                                        <div class="siftTime">
                                            <div class="siftTimeCont">
                                                <i class="fa fa-clock-o"></i>
                                                <span><strong>09:00 - 17:00</strong></span>
                                            </div>
                                            <div class="unfilledbtn">Unfilled</div>
                                        </div>
                                        <div class="siftTime">
                                            <div class="siftTimeCont">
                                                <i class="fa fa-user-o"></i>
                                                <span>Carer: <strong> Unassigned</strong></span>
                                            </div>
                                        </div>
                                        <div class="siftTime">
                                            <div class="siftTimeCont">
                                                <i class="fa  fa-map-marker"></i>
                                                <span>Client: <strong> Unknown Client</strong></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="todayShiftsList m-t-15">
                                        <div class="siftTime">
                                            <div class="siftTimeCont">
                                                <i class="fa fa-clock-o"></i>
                                                <span><strong>09:00 - 17:00</strong></span>
                                            </div>
                                            <div class="unfilledbtn">Unfilled</div>
                                        </div>
                                        <div class="siftTime">
                                            <div class="siftTimeCont">
                                                <i class="fa fa-user-o"></i>
                                                <span>Carer: <strong> Unassigned</strong></span>
                                            </div>
                                        </div>
                                        <div class="siftTime">
                                            <div class="siftTimeCont">
                                                <i class="fa  fa-map-marker"></i>
                                                <span>Client: <strong> Unknown Client</strong></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="todayShiftsList m-t-15">
                                        <div class="siftTime">
                                            <div class="siftTimeCont">
                                                <i class="fa fa-clock-o"></i>
                                                <span><strong>09:00 - 17:00</strong></span>
                                            </div>
                                            <div class="unfilledbtn">Unfilled</div>
                                        </div>
                                        <div class="siftTime">
                                            <div class="siftTimeCont">
                                                <i class="fa fa-user-o"></i>
                                                <span>Carer: <strong> Unassigned</strong></span>
                                            </div>
                                        </div>
                                        <div class="siftTime">
                                            <div class="siftTimeCont">
                                                <i class="fa  fa-map-marker"></i>
                                                <span>Client: <strong> Unknown Client</strong></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="todayShiftsList m-t-15">
                                        <div class="siftTime">
                                            <div class="siftTimeCont">
                                                <i class="fa fa-clock-o"></i>
                                                <span><strong>09:00 - 17:00</strong></span>
                                            </div>
                                            <div class="unfilledbtn">Unfilled</div>
                                        </div>
                                        <div class="siftTime">
                                            <div class="siftTimeCont">
                                                <i class="fa fa-user-o"></i>
                                                <span>Carer: <strong> Unassigned</strong></span>
                                            </div>
                                        </div>
                                        <div class="siftTime">
                                            <div class="siftTimeCont">
                                                <i class="fa  fa-map-marker"></i>
                                                <span>Client: <strong> Unknown Client</strong></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="panel">
                                <header class="panel-heading headingCapitilize"> Quick Actions</header>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <a href="#!">
                                                <div class="quickActions">   
                                                    <div class="activityCalendar"> <i class="fa fa-calendar-o"></i></div>
                                                    <div class="rotsBoxRightCont">
                                                        <h4>Create Shift </h4>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-md-6">
                                            <a href="#!">
                                                <div class="quickActions">   
                                                    <div class="activityCalendar"> <i class="fa fa-calendar-o"></i></div>
                                                    <div class="rotsBoxRightCont">
                                                        <h4>Add Carer </h4>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-md-6">
                                            <a href="#!">
                                                <div class="quickActions  m-t-15">   
                                                    <div class="activityCalendar"> <i class="fa fa-calendar-o"></i></div>
                                                    <div class="rotsBoxRightCont">
                                                        <h4>Add Client </h4>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-md-6">
                                            <a href="#!">
                                                <div class="quickActions m-t-15">   
                                                    <div class="activityCalendar"> <i class="fa fa-calendar-o"></i></div>
                                                    <div class="rotsBoxRightCont">
                                                        <h4>Leave Requests</h4>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>                            
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="panel">
                                <header class="panel-heading headingCapitilize"> Recent Activity</header>
                                <div class="panel-body">
                                    <div class="todayShiftsList recentActivity">
                                        <div class="activityCalendar"> <i class="fa fa-calendar-o"></i></div>
                                        <div class="recentCant">
                                            <div class="siftTime">
                                                <div class="siftTimeCont">
                                                    <span><strong>Shift unfilled</strong></span>
                                                </div>
                                                <div class="unfilledbtn">Unfilled</div>
                                            </div>
                                            <div class="siftTime">
                                                <div class="siftTimeCont">
                                                    <p class="m-b-5"> Unknown → Unknown</p>
                                                    <span>Nov 27, 2025 at 11:13 AM</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="todayShiftsList recentActivity m-t-15">
                                        <div class="activityCalendar"> <i class="fa fa-calendar-o"></i></div>
                                        <div class="recentCant">
                                            <div class="siftTime">
                                                <div class="siftTimeCont">
                                                    <span><strong>Shift unfilled</strong></span>
                                                </div>
                                                <div class="unfilledbtn">Unfilled</div>
                                            </div>
                                            <div class="siftTime">
                                                <div class="siftTimeCont">
                                                    <p class="m-b-5"> Unknown → Unknown</p>
                                                    <span>Nov 27, 2025 at 11:13 AM</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="todayShiftsList recentActivity m-t-15">
                                        <div class="activityCalendar"> <i class="fa fa-calendar-o"></i></div>
                                        <div class="recentCant">
                                            <div class="siftTime">
                                                <div class="siftTimeCont">
                                                    <span><strong>Shift unfilled</strong></span>
                                                </div>
                                                <div class="unfilledbtn">Unfilled</div>
                                            </div>
                                            <div class="siftTime">
                                                <div class="siftTimeCont">
                                                    <p class="m-b-5"> Unknown → Unknown</p>
                                                    <span>Nov 27, 2025 at 11:13 AM</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="todayShiftsList recentActivity m-t-15">
                                        <div class="activityCalendar"> <i class="fa fa-calendar-o"></i></div>
                                        <div class="recentCant">
                                            <div class="siftTime">
                                                <div class="siftTimeCont">
                                                    <span><strong>Shift unfilled</strong></span>
                                                </div>
                                                <div class="unfilledbtn">Unfilled</div>
                                            </div>
                                            <div class="siftTime">
                                                <div class="siftTimeCont">
                                                    <p class="m-b-5"> Unknown → Unknown</p>
                                                    <span>Nov 27, 2025 at 11:13 AM</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                   
                
                <div class="col-md-3">

                    <div class="rotawhitebgColor m-t-30">
                        <div class="panel">
                            @include('frontEnd.common.notification_bar')
                            {{-- <header class="panel-heading">Notifications</header> --}}
                            {{-- <div class="panel-body">
                                <div class="alert alert-placement clearfix">
                                    <span class="alert-icon"><i class="fa fa-map-marker"></i></span>
                                    <div class="notification-info">
                                        <ul class="clearfix notification-meta">
                                            <li class="pull-left notification-sender"><span><a href="http://localhost/socialcareitsolution/service/placement-plans/19"><b>Mick</b></a></span></li>
                                            <li class="pull-right notification-time">3 weeks ago</li>
                                        </ul>
                                        <p>A new Placement Plan 'task' is added</p>
                                    </div>
                                </div>
                                <div class="alert alert-placement clearfix">
                                    <span class="alert-icon"><i class="fa fa-map-marker"></i></span>
                                    <div class="notification-info">
                                        <ul class="clearfix notification-meta">
                                            <li class="pull-left notification-sender"><span><a href="http://localhost/socialcareitsolution/service/placement-plans/19"><b>Mick</b></a></span></li>
                                            <li class="pull-right notification-time">3 weeks ago</li>
                                        </ul>
                                        <p>A new Placement Plan 'task' is added</p>
                                    </div>
                                </div>
                                <div class="alert alert-placement clearfix">
                                    <span class="alert-icon"><i class="fa fa-map-marker"></i></span>
                                    <div class="notification-info">
                                        <ul class="clearfix notification-meta">
                                            <li class="pull-left notification-sender"><span><a href="http://localhost/socialcareitsolution/service/placement-plans/19"><b>Mick</b></a></span></li>
                                            <li class="pull-right notification-time">3 weeks ago</li>
                                        </ul>
                                        <p>A new Placement Plan 'task' is added</p>
                                    </div>
                                </div>
                                <div class="alert alert-placement clearfix">
                                    <span class="alert-icon"><i class="fa fa-map-marker"></i></span>
                                    <div class="notification-info">
                                        <ul class="clearfix notification-meta">
                                            <li class="pull-left notification-sender"><span><a href="http://localhost/socialcareitsolution/service/placement-plans/19"><b>Mick</b></a></span></li>
                                            <li class="pull-right notification-time">3 weeks ago</li>
                                        </ul>
                                        <p>A new Placement Plan 'task' is added</p>
                                    </div>
                                </div>
                                <div class="alert alert-placement clearfix">
                                    <span class="alert-icon"><i class="fa fa-map-marker"></i></span>
                                    <div class="notification-info">
                                        <ul class="clearfix notification-meta">
                                            <li class="pull-left notification-sender"><span><a href="http://localhost/socialcareitsolution/service/placement-plans/19"><b>Mick</b></a></span></li>
                                            <li class="pull-right notification-time">3 weeks ago</li>
                                        </ul>
                                        <p>A new Placement Plan 'task' is added</p>
                                    </div>
                                </div>
                                <div class="alert alert-placement clearfix">
                                    <span class="alert-icon"><i class="fa fa-map-marker"></i></span>
                                    <div class="notification-info">
                                        <ul class="clearfix notification-meta">
                                            <li class="pull-left notification-sender"><span><a href="http://localhost/socialcareitsolution/service/placement-plans/19"><b>Mick</b></a></span></li>
                                            <li class="pull-right notification-time">3 weeks ago</li>
                                        </ul>
                                        <p>A new Placement Plan 'task' is added</p>
                                    </div>
                                </div>
                                <div class="alert alert-placement clearfix">
                                    <span class="alert-icon"><i class="fa fa-map-marker"></i></span>
                                    <div class="notification-info">
                                        <ul class="clearfix notification-meta">
                                            <li class="pull-left notification-sender"><span><a href="http://localhost/socialcareitsolution/service/placement-plans/19"><b>Mick</b></a></span></li>
                                            <li class="pull-right notification-time">3 weeks ago</li>
                                        </ul>
                                        <p>A new Placement Plan 'task' is added</p>
                                    </div>
                                </div>

                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>



{{-- SOS Trigger Modal --}}
<div class="modal fade" id="sosModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: #d9534f; color: #fff;">
                <h4 class="modal-title"><i class="fa fa-exclamation-triangle"></i> Send SOS Alert</h4>
                <button type="button" class="close" data-dismiss="modal" style="color: #fff;"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <p><strong>This will alert all managers immediately.</strong></p>
                <div class="form-group">
                    <label for="sos-message">What's the emergency? (optional)</label>
                    <textarea id="sos-message" class="form-control" rows="3" maxlength="2000" placeholder="Describe the emergency..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" id="sos-confirm-btn" class="btn btn-danger"><i class="fa fa-exclamation-triangle"></i> SEND SOS</button>
            </div>
        </div>
    </div>
</div>

{{-- SOS Resolve Modal --}}
<div class="modal fade" id="sosResolveModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: #5cb85c; color: #fff;">
                <h4 class="modal-title"><i class="fa fa-check"></i> Resolve SOS Alert</h4>
                <button type="button" class="close" data-dismiss="modal" style="color: #fff;"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="resolve-notes">Resolution notes (optional)</label>
                    <textarea id="resolve-notes" class="form-control" rows="3" maxlength="2000" placeholder="How was this resolved?"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" id="resolve-alert-id" value="">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" id="resolve-confirm-btn" class="btn btn-success"><i class="fa fa-check"></i> Resolve</button>
            </div>
        </div>
    </div>
</div>

<input type="hidden" id="sos-user-type" value="{{ Auth::user()->user_type }}">
<script>
    var baseUrl = "{{ url('') }}";
</script>
<script src="{{ url('public/js/roster/sos_alerts.js') }}"></script>
@endsection
