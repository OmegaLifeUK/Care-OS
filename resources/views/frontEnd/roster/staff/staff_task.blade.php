@extends('frontEnd.layouts.master')
@section('title', 'Staff Task')
@section('content')
    @include('frontEnd.roster.common.roster_header')
    <main class="page-content">
        <div class="container-fluid">
            <div class="staffMainp">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="staffHeaderp">
                            <div>
                                <h1 class="mainTitlep">Staff Tasks</h1>

                                <p class="header-subtitle mb-0"> Supervisions, assessments, and other tasks </p>
                            </div>
                            <div>
                                <button class="bgBtn" data-toggle="modal" data-target="#newRecord"><i
                                        class='bx  bx-plus me-2'></i> Create Task</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-5">
                    <div class="col-lg-4 staffCardCol">
                        <div class="staffCardp p-4">
                            <div class="d-flex gap-3 align-items-center">
                                <div class="staffCicon">
                                    <i class='bx  bx-clock'></i>
                                </div>
                                <div>
                                    <h3 class="mt-0" id="pendingTotalCount">0</h3>
                                    <p>pending</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 staffCardCol">
                        <div class="staffCardp p-4">
                            <div class="d-flex gap-3 align-items-center">
                                <div class="staffCicon">
                                    <i class='bx  bx-caret-big-right'></i>
                                </div>
                                <div>
                                    <h3 class="mt-0" id="progressTotalCount">0</h3>
                                    <p>In Progress</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 staffCardCol">
                        <div class="staffCardp p-4">
                            <div class="d-flex gap-3 align-items-center">
                                <div class="staffCicon">
                                    <i class='bx  bx-alert-circle'></i>
                                </div>
                                <div>
                                    <h3 class="mt-0" id="overdueTotalCount">0</h3>
                                    <p>Over Due</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row  mt20">
                    <div class="col-lg-12">
                        <div class="calendarTabs staffTaskTab">
                            <div class="tabs p-1 ">
                                <button class="tab active" data-tab="generalTab" data-status="0">
                                    Pending

                                </button>
                                <button class="tab " data-tab="availabilityTab" data-status="2">
                                    In Progress

                                </button>

                                <button class="tab" data-tab="supervisionsTab" data-status="1">
                                    Completed

                                </button>
                                <button class="tab" data-tab="shiftsTab" data-status="all">
                                    All
                                </button>

                            </div>

                            <!-- TAB CONTENT -->
                            <div class="tab-content carertabcontent" id="stafftask-list-wrapper">
                                <div class="emergencyContent emergencyMain AllStaffTabC p-4 blueAllTabCard mt-4"">
                                    <div class="
                                leavebanktabCont p24">
                                        <i class="bx bx-file-detail"></i>
                                        <p class="mt-3">Please Wait...</p>
                                    </div>
                                </div>
                                {{-- <div class="content active" id="generalTab">
                                    <a href="{{ url('roster/staff-task-detail/1') }}">
                                        <div class="emergencyMain emergencyContent AllStaffTabC p-4 blueAllTabCard">

                                            <div class="d-flex justify-content-between  align-items-center">
                                                <div class="d-flex gap-4 align-items-center">
                                                    <div class="bgIconStaffT">
                                                        <i class='bxr  bx-file-detail'></i>
                                                    </div>
                                                    <div>
                                                        <div class="d-flex gap-3 align-items-center mb-2">
                                                            <h5 class="m-0">Task Start</h5>
                                                            <div class="userMum">
                                                                <span class="title mt-0">
                                                                    <i class='bxr  bx-file-detail me-1'></i> Form Required
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center gap-3">
                                                            <p class="para text-sm"><i class='bxr  bx-user'></i> Assigned
                                                                to:
                                                                <span>Emma Johnson</span>
                                                            </p>
                                                            <p class="para text-sm"><i class='bxr  bx-calendar-week'></i>
                                                                Due:
                                                                <span>27 Jan 2026</span>
                                                            </p>
                                                        </div>
                                                        <div class="d-flex align-items-center gap-3">

                                                            <div>
                                                                <span class="careBadg buleBadges">Training</span>
                                                            </div>
                                                            <div>
                                                                <span class="careBadg redbadges">High</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div>
                                                    <button class="bgBtn pgreenBtn"><i
                                                            class='bxr  bx-caret-big-right me-3'></i>
                                                        Start Task </button>

                                                </div>
                                            </div>

                                        </div>
                                    </a>
                                    <div class="emergencyMain emergencyContent AllStaffTabC p-4 purpleAllTabCard mt-4">
                                        <div class="d-flex justify-content-between  align-items-center">
                                            <div class="d-flex gap-4 align-items-center">
                                                <div class="bgIconStaffT">
                                                    <i class='bxr  bx-file-detail'></i>
                                                </div>
                                                <div>
                                                    <div class="d-flex gap-3 align-items-center mb-2">
                                                        <h5 class="m-0">Supervision - Michael Chen</h5>
                                                        <div class="userMum">
                                                            <span class="title mt-0">
                                                                <i class='bxr  bx-file-detail me-1'></i> Form Required
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-3">
                                                        <p class="para text-sm"><i class='bxr  bx-user'></i> Assigned to:
                                                            <span>Emma Johnson</span>
                                                        </p>
                                                        <p class="para text-sm"><i class='bxr  bx-user'></i>About:
                                                            <span>Sarah Mitchell</span>
                                                        </p>

                                                        <p class="para text-sm"><i class='bxr  bx-calendar-week'></i> Due:
                                                            <span>27 Jan 2026</span>
                                                        </p>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-3">

                                                        <div>
                                                            <span class="careBadg purpleBadges">Audit</span>
                                                        </div>
                                                        <div>
                                                            <span class="careBadg buleBadges">Medium</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <button class="bgBtn pgreenBtn"><i class='bxr  bx-caret-big-right me-3'></i>
                                                    Start Task</button>

                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="emergencyMain emergencyContent AllStaffTabC p-4 yellowAllTabCard allterStafftask mt-4">
                                        <div class="d-flex justify-content-between  align-items-center">
                                            <div class="d-flex gap-4 align-items-center">
                                                <div class="bgIconStaffT">
                                                    <i class='bx  bx-check-circle'></i>
                                                </div>
                                                <div>
                                                    <div class="d-flex gap-3 align-items-center mb-2">
                                                        <h5 class="m-0">Task Start</h5>
                                                        <div class="userMum">
                                                            <span class="title mt-0">
                                                                <i class='bxr  bx-file-detail me-1'></i> Form Required
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-3">
                                                        <p class="para text-sm"><i class='bxr  bx-user'></i> Assigned to:
                                                            <span>Emma Johnson</span>
                                                        </p>
                                                        <p class="para text-sm"><i class='bxr  bx-user'></i>About:
                                                            <span>Sarah Mitchell</span>
                                                        </p>

                                                        <p class="para text-sm allertStaffTaskDate"><i
                                                                class='bxr  bx-calendar-week'></i> Due: <span>27 Jan
                                                                2026</span></p>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-3">

                                                        <div>
                                                            <span class="careBadg yellowBadges">Spot Check</span>
                                                        </div>
                                                        <div>
                                                            <span class="careBadg muteBadges">Low</span>
                                                        </div>
                                                        <div>
                                                            <span class="careBadg redDarkBadges">Overdue</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <button class="bgBtn pgreenBtn"><i
                                                        class='bxr  bx-caret-big-right me-3'></i>
                                                    Start Task</button>

                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="emergencyMain emergencyContent AllStaffTabC p-4 yellowAllTabCard  allterStafftask  mt-4">
                                        <div class="d-flex justify-content-between  align-items-center">
                                            <div class="d-flex gap-4 align-items-center">
                                                <div class="bgIconStaffT">
                                                    <i class='bx  bx-check-circle'></i>
                                                </div>
                                                <div>
                                                    <div class="d-flex gap-3 align-items-center mb-2">
                                                        <h5 class="m-0">Task Start</h5>
                                                        <div class="userMum">
                                                            <span class="title mt-0">
                                                                <i class='bxr  bx-file-detail me-1'></i> Form Required
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-3">
                                                        <p class="para text-sm"><i class='bxr  bx-user'></i> Assigned to:
                                                            <span>Emma Johnson</span>
                                                        </p>
                                                        <p class="para text-sm"><i class='bxr  bx-calendar-week'></i>
                                                            About:
                                                            <span>Sarah Johnsan</span>
                                                        </p>
                                                        <p class="para text-sm allertStaffTaskDate"><i
                                                                class='bxr  bx-calendar-week'></i> Due: <span>27 Jan
                                                                2026</span></p>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-3">

                                                        <div>
                                                            <span class="careBadg yellowBadges">General</span>
                                                        </div>
                                                        <div>
                                                            <span class="careBadg highBadges">High</span>
                                                        </div>
                                                        <div>
                                                            <span class="careBadg redDarkBadges">Overdue</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <button class="bgBtn pgreenBtn"><i
                                                        class='bxr  bx-caret-big-right me-3'></i>
                                                    Start Task</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="emergencyMain emergencyContent AllStaffTabC p-4 purpleAllTabCard mt-4">
                                        <div class="d-flex justify-content-between  align-items-center">
                                            <div class="d-flex gap-4 align-items-center">
                                                <div class="bgIconStaffT">
                                                    <i class='bxr  bx-file-detail'></i>
                                                </div>
                                                <div>
                                                    <div class="d-flex gap-3 align-items-center mb-2">
                                                        <h5 class="m-0">Task Start</h5>
                                                        <div class="userMum">
                                                            <span class="title mt-0">
                                                                <i class='bxr  bx-file-detail me-1'></i> Form Required
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-3">
                                                        <p class="para text-sm"><i class='bxr  bx-user'></i> Assigned to:
                                                            <span>Emma Johnson</span>
                                                        </p>
                                                        <p class="para text-sm"><i class='bxr  bx-calendar-week'></i> Due:
                                                            <span>27 Jan 2026</span>
                                                        </p>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-3">

                                                        <div>
                                                            <span class="careBadg purpleBadges">General</span>
                                                        </div>
                                                        <div>
                                                            <span class="careBadg orangeBages">High</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <button class="bgBtn pgreenBtn"><i
                                                        class='bxr  bx-caret-big-right me-3'></i>
                                                    Start Task</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="content" id="availabilityTab">
                                    <div class="emergencyMain emergencyContent AllStaffTabC p-4 orangeAllTabCard">
                                        <div class="d-flex justify-content-between  align-items-center">
                                            <div class="d-flex gap-4 align-items-center">
                                                <div class="bgIconStaffT">
                                                    <i class='bxr  bx-file-detail'></i>
                                                </div>
                                                <div>
                                                    <div class="d-flex gap-3 align-items-center mb-2">
                                                        <h5 class="m-0">Task Start</h5>
                                                        <div class="userMum">
                                                            <span class="title mt-0">
                                                                <i class='bxr  bx-file-detail me-1'></i> Form Required
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-3">
                                                        <p class="para text-sm"><i class='bxr  bx-user'></i> Assigned to:
                                                            <span>Emma Johnson</span>
                                                        </p>
                                                        <p class="para text-sm"><i class='bxr  bx-calendar-week'></i> Due:
                                                            <span>27 Jan 2026</span>
                                                        </p>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-3">

                                                        <div>
                                                            <span class="careBadg orangeBages">Training</span>
                                                        </div>
                                                        <div>
                                                            <span class="careBadg orangeBages">High</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <button class="borderBtn"><i class='bxr  bx-caret-big-right me-3'></i>
                                                    Continue</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="emergencyMain emergencyContent AllStaffTabC p-4 blueAllTabCard mt-4">
                                        <div class="d-flex justify-content-between  align-items-center">
                                            <div class="d-flex gap-4 align-items-center">
                                                <div class="bgIconStaffT">
                                                    <i class='bxr  bx-group'></i>
                                                </div>
                                                <div>
                                                    <div class="d-flex gap-3 align-items-center mb-2">
                                                        <h5 class="m-0">Supervision - Michael Chen</h5>
                                                        <div class="userMum">
                                                            <span class="title mt-0">
                                                                <i class='bxr  bx-file-detail me-1'></i> Form Required
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-3">
                                                        <p class="para text-sm"><i class='bxr  bx-user'></i> Assigned to:
                                                            <span>Emma Johnson</span>
                                                        </p>
                                                        <p class="para text-sm"><i class='bxr  bx-user'></i>About:
                                                            <span>Sarah Mitchell</span>
                                                        </p>

                                                        <p class="para text-sm"><i class='bxr  bx-calendar-week'></i> Due:
                                                            <span>27 Jan 2026</span>
                                                        </p>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-3">

                                                        <div>
                                                            <span class="careBadg buleBadges">Supervision</span>
                                                        </div>
                                                        <div>
                                                            <span class="careBadg buleBadges">Medium</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <button class="borderBtn"><i class='bxr  bx-caret-big-right me-3'></i>
                                                    Continue</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="emergencyMain emergencyContent AllStaffTabC p-4 yellowAllTabCard mt-4">
                                        <div class="d-flex justify-content-between  align-items-center">
                                            <div class="d-flex gap-4 align-items-center">
                                                <div class="bgIconStaffT">
                                                    <i class='bx  bx-check-circle'></i>
                                                </div>
                                                <div>
                                                    <div class="d-flex gap-3 align-items-center mb-2">
                                                        <h5 class="m-0">Task Start</h5>
                                                        <div class="userMum">
                                                            <span class="title mt-0">
                                                                <i class='bxr  bx-file-detail me-1'></i> Form Required
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-3">
                                                        <p class="para text-sm"><i class='bxr  bx-user'></i> Assigned to:
                                                            <span>Emma Johnson</span>
                                                        </p>
                                                        <p class="para text-sm"><i class='bxr  bx-user'></i>About:
                                                            <span>Sarah Mitchell</span>
                                                        </p>

                                                        <p class="para text-sm"><i class='bxr  bx-calendar-week'></i> Due:
                                                            <span>27 Jan 2026</span>
                                                        </p>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-3">

                                                        <div>
                                                            <span class="careBadg yellowBadges">Spot Check</span>
                                                        </div>
                                                        <div>
                                                            <span class="careBadg buleBadges">Medium</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <button class="borderBtn"><i class='bxr  bx-caret-big-right me-3'></i>
                                                    Continue</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="emergencyMain emergencyContent AllStaffTabC p-4 muteAllTabCard mt-4">
                                        <div class="d-flex justify-content-between  align-items-center">
                                            <div class="d-flex gap-4 align-items-center">
                                                <div class="bgIconStaffT">
                                                    <i class='bx  bx-check-circle'></i>
                                                </div>
                                                <div>
                                                    <div class="d-flex gap-3 align-items-center mb-2">
                                                        <h5 class="m-0">Task Start</h5>
                                                        <div class="userMum">
                                                            <span class="title mt-0">
                                                                <i class='bxr  bx-file-detail me-1'></i> Form Required
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-3">
                                                        <p class="para text-sm"><i class='bxr  bx-user'></i> Assigned to:
                                                            <span>Emma Johnson</span>
                                                        </p>
                                                        <p class="para text-sm"><i class='bxr  bx-calendar-week'></i> Due:
                                                            <span>27 Jan 2026</span>
                                                        </p>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-3">

                                                        <div>
                                                            <span class="careBadg muteBadges">General</span>
                                                        </div>
                                                        <div>
                                                            <span class="careBadg buleBadges">Medium</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <button class="borderBtn"><i class='bxr  bx-caret-big-right me-3'></i>
                                                    Continue</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="emergencyMain emergencyContent AllStaffTabC p-4 purpleAllTabCard mt-4">
                                        <div class="d-flex justify-content-between  align-items-center">
                                            <div class="d-flex gap-4 align-items-center">
                                                <div class="bgIconStaffT">
                                                    <i class='bxr  bx-file-detail'></i>
                                                </div>
                                                <div>
                                                    <div class="d-flex gap-3 align-items-center mb-2">
                                                        <h5 class="m-0">Task Start</h5>
                                                        <div class="userMum">
                                                            <span class="title mt-0">
                                                                <i class='bxr  bx-file-detail me-1'></i> Form Required
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-3">
                                                        <p class="para text-sm"><i class='bxr  bx-user'></i> Assigned to:
                                                            <span>Emma Johnson</span>
                                                        </p>
                                                        <p class="para text-sm"><i class='bxr  bx-calendar-week'></i> Due:
                                                            <span>27 Jan 2026</span>
                                                        </p>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-3">

                                                        <div>
                                                            <span class="careBadg purpleBadges">General</span>
                                                        </div>
                                                        <div>
                                                            <span class="careBadg orangeBages">High</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <button class="borderBtn"><i class='bxr  bx-caret-big-right me-3'></i>
                                                    Continue</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="content" id="supervisionsTab">
                                    <div
                                        class="emergencyMain emergencyContent AllStaffTabC p-4 blueAllTabCard completedTaskStaff mt-4">
                                        <div class="d-flex justify-content-between  align-items-center">
                                            <div class="d-flex gap-4 align-items-center">
                                                <div class="bgIconStaffT">
                                                    <i class='bxr  bx-group'></i>
                                                </div>
                                                <div>
                                                    <div class="d-flex gap-3 align-items-center mb-2">
                                                        <h5 class="m-0">Supervision - Michael Chen</h5>
                                                        <div class="userMum">
                                                            <span class="title mt-0">
                                                                <i class='bxr  bx-file-detail me-1'></i> Form Required
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-3">
                                                        <p class="para text-sm"><i class='bxr  bx-user'></i> Assigned to:
                                                            <span>Emma Johnson</span>
                                                        </p>
                                                        <p class="para text-sm"><i class='bxr  bx-user'></i>About:
                                                            <span>Sarah Mitchell</span>
                                                        </p>
                                                        <p class="para text-sm"><i class='bxr  bx-calendar-week'></i> Due:
                                                            <span>27 Jan 2026</span>
                                                        </p>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-3">

                                                        <div>
                                                            <span class="careBadg buleBadges">Supervision</span>
                                                        </div>
                                                        <div>
                                                            <span class="careBadg buleBadges">Medium</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <span class="careBadg greenbadges"><i
                                                        class='bx  bx-check-circle greenText me-1'></i> Completed</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="content" id="shiftsTab">
                                    <div class="emergencyMain emergencyContent AllStaffTabC p-4 orangeAllTabCard">
                                        <div class="d-flex justify-content-between  align-items-center">
                                            <div class="d-flex gap-4 align-items-center">
                                                <div class="bgIconStaffT">
                                                    <i class='bxr  bx-file-detail'></i>
                                                </div>
                                                <div>
                                                    <div class="d-flex gap-3 align-items-center mb-2">
                                                        <h5 class="m-0">Task Start</h5>
                                                        <div class="userMum">
                                                            <span class="title mt-0">
                                                                <i class='bxr  bx-file-detail me-1'></i> Form Required
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-3">
                                                        <p class="para text-sm"><i class='bxr  bx-user'></i> Assigned to:
                                                            <span>Emma Johnson</span>
                                                        </p>
                                                        <p class="para text-sm"><i class='bxr  bx-calendar-week'></i> Due:
                                                            <span>27 Jan 2026</span>
                                                        </p>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-3">

                                                        <div>
                                                            <span class="careBadg orangeBages">Training</span>
                                                        </div>
                                                        <div>
                                                            <span class="careBadg orangeBages">High</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <button class="borderBtn"><i class='bxr  bx-caret-big-right me-3'></i>
                                                    Continue</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="emergencyMain emergencyContent AllStaffTabC p-4 blueAllTabCard mt-4">
                                        <div class="d-flex justify-content-between  align-items-center">
                                            <div class="d-flex gap-4 align-items-center">
                                                <div class="bgIconStaffT">
                                                    <i class='bxr  bx-group'></i>
                                                </div>
                                                <div>
                                                    <div class="d-flex gap-3 align-items-center mb-2">
                                                        <h5 class="m-0">Supervision - Michael Chen</h5>
                                                        <div class="userMum">
                                                            <span class="title mt-0">
                                                                <i class='bxr  bx-file-detail me-1'></i> Form Required
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-3">
                                                        <p class="para text-sm"><i class='bxr  bx-user'></i> Assigned to:
                                                            <span>Emma Johnson</span>
                                                        </p>
                                                        <p class="para text-sm"><i class='bxr  bx-user'></i>About:
                                                            <span>Sarah Mitchell</span>
                                                        </p>

                                                        <p class="para text-sm"><i class='bxr  bx-calendar-week'></i> Due:
                                                            <span>27 Jan 2026</span>
                                                        </p>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-3">

                                                        <div>
                                                            <span class="careBadg buleBadges">Supervision</span>
                                                        </div>
                                                        <div>
                                                            <span class="careBadg buleBadges">Medium</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <button class="bgBtn pgreenBtn"><i
                                                        class='bxr  bx-caret-big-right me-3'></i>
                                                    Start Task</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="emergencyMain emergencyContent AllStaffTabC p-4 yellowAllTabCard mt-4">
                                        <div class="d-flex justify-content-between  align-items-center">
                                            <div class="d-flex gap-4 align-items-center">
                                                <div class="bgIconStaffT">
                                                    <i class='bx  bx-check-circle'></i>
                                                </div>
                                                <div>
                                                    <div class="d-flex gap-3 align-items-center mb-2">
                                                        <h5 class="m-0">Task Start</h5>
                                                        <div class="userMum">
                                                            <span class="title mt-0">
                                                                <i class='bxr  bx-file-detail me-1'></i> Form Required
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-3">
                                                        <p class="para text-sm"><i class='bxr  bx-user'></i> Assigned to:
                                                            <span>Emma Johnson</span>
                                                        </p>
                                                        <p class="para text-sm"><i class='bxr  bx-user'></i>About:
                                                            <span>Sarah Mitchell</span>
                                                        </p>

                                                        <p class="para text-sm"><i class='bxr  bx-calendar-week'></i> Due:
                                                            <span>27 Jan 2026</span>
                                                        </p>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-3">

                                                        <div>
                                                            <span class="careBadg yellowBadges">Spot Check</span>
                                                        </div>
                                                        <div>
                                                            <span class="careBadg buleBadges">Medium</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <button class="borderBtn"><i class='bxr  bx-caret-big-right me-3'></i>
                                                    Continue</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="emergencyMain emergencyContent AllStaffTabC p-4 muteAllTabCard mt-4">
                                        <div class="d-flex justify-content-between  align-items-center">
                                            <div class="d-flex gap-4 align-items-center">
                                                <div class="bgIconStaffT">
                                                    <i class='bx  bx-check-circle'></i>
                                                </div>
                                                <div>
                                                    <div class="d-flex gap-3 align-items-center mb-2">
                                                        <h5 class="m-0">Task Start</h5>
                                                        <div class="userMum">
                                                            <span class="title mt-0">
                                                                <i class='bxr  bx-file-detail me-1'></i> Form Required
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-3">
                                                        <p class="para text-sm"><i class='bxr  bx-user'></i> Assigned to:
                                                            <span>Emma Johnson</span>
                                                        </p>
                                                        <p class="para text-sm"><i class='bxr  bx-calendar-week'></i> Due:
                                                            <span>27 Jan 2026</span>
                                                        </p>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-3">

                                                        <div>
                                                            <span class="careBadg muteBadges">General</span>
                                                        </div>
                                                        <div>
                                                            <span class="careBadg buleBadges">Medium</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <button class="borderBtn"><i class='bxr  bx-caret-big-right me-3'></i>
                                                    Continue</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="emergencyMain emergencyContent AllStaffTabC p-4 purpleAllTabCard mt-4">
                                        <div class="d-flex justify-content-between  align-items-center">
                                            <div class="d-flex gap-4 align-items-center">
                                                <div class="bgIconStaffT">
                                                    <i class='bxr  bx-file-detail'></i>
                                                </div>
                                                <div>
                                                    <div class="d-flex gap-3 align-items-center mb-2">
                                                        <h5 class="m-0">Task Start</h5>
                                                        <div class="userMum">
                                                            <span class="title mt-0">
                                                                <i class='bxr  bx-file-detail me-1'></i> Form Required
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-3">
                                                        <p class="para text-sm"><i class='bxr  bx-user'></i> Assigned to:
                                                            <span>Emma Johnson</span>
                                                        </p>
                                                        <p class="para text-sm"><i class='bxr  bx-calendar-week'></i> Due:
                                                            <span>27 Jan 2026</span>
                                                        </p>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-3">

                                                        <div>
                                                            <span class="careBadg purpleBadges">General</span>
                                                        </div>
                                                        <div>
                                                            <span class="careBadg orangeBages">High</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <button class="borderBtn"><i class='bxr  bx-caret-big-right me-3'></i>
                                                    Continue</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="emergencyMain emergencyContent AllStaffTabC p-4 mt-4">
                                        <div class="d-flex justify-content-between  align-items-center">
                                            <div class="d-flex gap-4 align-items-center">
                                                <div class="bgIconStaffT greenbadges">
                                                    <i class='bxr  bx-file-detail greenText'></i>
                                                </div>
                                                <div>
                                                    <div class="d-flex gap-3 align-items-center mb-2">
                                                        <h5 class="m-0">Task Start</h5>
                                                        <div class="userMum">
                                                            <span class="title mt-0">
                                                                <i class='bxr  bx-file-detail me-1'></i> Form Required
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-3">
                                                        <p class="para text-sm"><i class='bxr  bx-user'></i> Assigned to:
                                                            <span>Emma Johnson</span>
                                                        </p>
                                                        <p class="para text-sm"><i class='bxr  bx-calendar-week'></i> Due:
                                                            <span>27 Jan 2026</span>
                                                        </p>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-3">

                                                        <div>
                                                            <span class="careBadg greenbadges">General</span>
                                                        </div>
                                                        <div>
                                                            <span class="careBadg orangeBages">High</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <button class="borderBtn"><i class='bxr  bx-caret-big-right me-3'></i>
                                                    Continue</button>
                                            </div>
                                        </div>
                                    </div>
                                </div> --}}
                            </div>
                            <div id="stafftask-pagination" class="mt-3 text-center pagination"></div>
                            <!-- END TAB CONTENT -->
                        </div>
                    </div>
                </div>
            </div>
            <!-- pratima modal start -->
            <div class="modal fade leaveCommunStyle" id="newRecord" tabindex="1" role="dialog"
                aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog pModalScroll customModalWidthp">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title"> Create Staff Task
                            </h4>
                        </div>
                        <div class="modal-body heightScrollModal" style="height: unset;">
                            <form id="form-data">
                                <div class="carer-form">

                                    <div class="row mb-4">
                                        <div class="col-lg-12">
                                            <div class="alert alert-danger d-none errorMsgListWrapper" role="alert">
                                                <ul class="errorMsgList">
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <label>Task Type *</label>
                                            <select class="form-control" id="task_type_id" name="task_type_id">
                                                @foreach ($taskTypeList as $item)
                                                    <option value="{{ $item->id }}">{{ $item->type }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-12  m-t-10">
                                            <label>Title *</label>
                                            <input type="text" class="form-control" name="title" id="title"
                                                placeholder="e.g. Monthly Supervision - John Smith">
                                        </div>
                                        <div class="col-lg-12 m-t-10">
                                            <label> Assigned To (who will perform this task) *</label>
                                            <select class="form-control" id="assign_to" name="assign_to">
                                                @foreach ($userList as $item)
                                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-lg-12 m-t-10">
                                            <label>About (staff member being supervised/assessed)</label>
                                            <select class="form-control" id="staff_member" name="staff_member">
                                                @foreach ($userList as $item)
                                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-lg-12 m-t-10">
                                            <label>Form Template (optional)</label>
                                            <select class="form-control" name="form_template_id" id="form_template_id">
                                                <option selected disabled>Select Form</option>
                                                @foreach ($formTemplate as $item)
                                                    <option value="{{ $item->id }}">{{ $item->title }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6  m-t-10">
                                            <label>Due Date</label>
                                            <input type="date" name="due_date" id="due_date" class="form-control">
                                        </div>
                                        <div class="col-lg-6 m-t-10">
                                            <label>Priority</label>
                                            <select class="form-control" id="priority" name="priority">
                                                @foreach ($priority_array as $key => $item)
                                                    <option value="{{ $item['id'] }}">{{ $item['value'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6  m-t-10">
                                            <label>Scheduled Date</label>
                                            <input type="date" name="scheduled_date" id="scheduled_date"
                                                class="form-control">
                                        </div>
                                        <div class="col-md-6  m-t-10">
                                            <label>Time</label>
                                            <input type="time" id="scheduled_time" name="scheduled_time"
                                                class="form-control">
                                        </div>
                                        <div class="col-md-12 m-t-10">
                                            <label>Description</label>
                                            <textarea name="description" id="description" required="" class="form-control" rows="3" cols="20"
                                                placeholder="Additional details..."></textarea>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="mt20 d-flex gap-3 justify-content-end">
                                                <button type="button" data-dismiss="modal" aria-hidden="true"
                                                    class="borderBtn">Cancel</button>
                                                <button type="button" id="submitBtn" class="bgBtn darkBg submit ">Create
                                                    Task</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
            <!-- pratima modal end -->
        </div>


        <script>
            const tabs = document.querySelectorAll(".tab");
            const contents = document.querySelectorAll(".content");

            tabs.forEach(tab => {
                tab.addEventListener("click", () => {
                    document.querySelector(".tab.active")?.classList.remove("active");
                    tab.classList.add("active");

                    let tabName = tab.getAttribute("data-tab");

                    contents.forEach(content => {
                        content.classList.remove("active");
                    });

                    document.getElementById(tabName).classList.add("active");
                });
            });
            $("#submitBtn").click(function() {


                $(".errorMsgListWrapper").addClass('d-none');
                // alert('Record Supervision Saved Successfully!');
                let btn = $(this);
                let form = btn.closest('form'); // correct form
                let modal = btn.closest('.modal');
                $.ajax({
                    url: "{{ route('roster.stafftask.save') }}", // URL to send the request to
                    type: 'POST', // or 'POST'
                    data: new FormData(form[0]),
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        btn.html('Please Wait...').prop('disabled', true);
                    },
                    success: function(res) {
                        btn.html('Create Task').prop('disabled', false);
                        // Code to run on successful response
                        if (res.status) {
                            $(".errorMsgListWrapper").addClass('d-none');
                            alert(res.message);
                            fetch_supervisions(1);
                            modal.modal('hide');
                            form.trigger('reset');
                        }
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        btn.html('Create Task').prop('disabled',
                            false);
                        $(".errorMsgListWrapper").removeClass('d-none');
                        var errors = xhr.responseJSON.message;
                        // let target = $("#errorMsgListWrapper")[0].offsetTop;
                        $(".errorMsgList").html('');
                        if (xhr.status == 422) {

                            let htm = '';

                            $.each(errors, function(key, value) {
                                htm += `<li>${value[0]}</li>`;

                            });
                            $("#task_type_id").focus();
                            $(".errorMsgList").html(htm);

                            // scroll AFTER DOM updated
                            setTimeout(() => {
                                let target = $("#errorMsgListWrapper")[0].offsetTop;

                                $('#recordSuperModal .modal-body').animate({
                                    scrollTop: target
                                }, 400);
                            }, 50);

                            return false;
                        } else if (xhr.status == 404) {
                            $(".errorMsgList").html('<li>Data not found</li>');
                        } else {
                            $(".errorMsgList").html(
                                '<li>An error occurred while saving the supervision record.</li>'
                            );
                            // alert('An error occurred while saving the supervision record.');
                        }
                    }
                });

            });

            let searchTimer;
            $("#searchText").on('keyup', function() {
                clearTimeout(searchTimer);

                searchTimer = setTimeout(() => {
                    fetch_supervisions(1);
                }, 500);
            });

            $(document).on('click', '.page-btn', function() {
                let page = $(this).data('page');
                fetch_supervisions(page);
            });
            $(".staffTaskTab button").click(function() {
                console.log($(this).data('status'));

                fetch_supervisions(1);
            });

            fetch_supervisions(1);

            function fetch_supervisions(page = 1) {
                let statusVal = $(".staffTaskTab button.active").data('status') || "0";
                let searchText = $("#searchText").val();
                $("#overdue_text_wrapper").addClass('d-none');
                $.ajax({
                    url: "{{ route('roster.stafftask.fetch_list') }}?page=" +
                        page + "&status=" + statusVal + "&search=" +
                        searchText, // URL to send the request to
                    type: 'GET',
                    beforeSend: function() {
                        // $("#supervision-list-wrapper").html(`<div class="emergencyContent emergencyMain AllStaffTabC p-4 blueAllTabCard mt-4"">
                //     <div class="
                //     leavebanktabCont p24">
                //         <i class="bx bx-file-detail"></i>
                //         <p class="mt-3">Please Wait...</p>
                //     </div>
                // </div>`);
                        // $("#totalRecord").html(0);
                        // $("#totalOnTrack").html(0);
                        // $("#totalDueSoon").html(0);
                        // $("#totalOverdue").html(0);
                    },
                    success: function(res) {
                        // console.log(res);
                        // // return false;

                        if (typeof isAuthenticated === "function") {
                            if (isAuthenticated(res) == false) {
                                return false;
                            }
                        }
                        if (res.success) {

                            $("#stafftask-pagination").empty();
                            $("#stafftask-list-wrapper").empty();
                            $("#pendingTotalCount").html(res.counts.pending);
                            $("#progressTotalCount").html(res.counts.progress);
                            $("#overdueTotalCount").html(res.counts.overdue);
                            let htm = '';
                            if (res.data.length == 0) {
                                $("#stafftask-list-wrapper").html(`<div class="emergencyContent emergencyMain AllStaffTabC p-4 blueAllTabCard mt-4"">
                                        <div class="
                                        leavebanktabCont p24">
                                            <i class="bx bx-file-detail"></i>
                                            <p class="mt-3">No task records</p>
                                        </div>
                                    </div>`);
                                return;
                            }
                            $.each(res.data, function(index, item) {
                                let form_template_id = !item.form_template_id ? "d-none" : "";
                                // colorText = item.status == 'On Track' ? "greenbadges" : (item
                                //     .status == 'Due Soon' ? "yellowbadges" : "redbadges");
                                let statusText = 'Start Task';
                                let colorText = 'pgreenBtn';
                                let icons = 'bx-caret-big-right';
                                if (item.statusCode == 0) {
                                    statusText = 'Start Task';
                                    colorText = 'pgreenBtn';
                                    icons = 'bx-caret-big-right';
                                } else if (item.statusCode == 1) {
                                    colorText = 'pgreenBtn';
                                    statusText = 'Completed';
                                    icons = 'bx-check-circle';
                                } else if (item.statusCode == 2) {
                                    statusText = 'Continue';
                                    colorText = '';
                                    icons = 'bx-caret-big-right';
                                }
                                let routeUrl = "{{ route('roster.stafftask.details', ':id') }}";
                                routeUrl = routeUrl.replace(':id', item.id);

                                htm += `<div class="emergencyMain emergencyContent AllStaffTabC p-4 purpleAllTabCard mt-4" onclick="window.location='${routeUrl}'">
                                        <div class="d-flex justify-content-between  align-items-center">
                                            <div class="d-flex gap-4 align-items-center">
                                                <div class="bgIconStaffT">
                                                    <i class='bxr  bx-file-detail'></i>
                                                </div>
                                                <div>
                                                    <div class="d-flex gap-3 align-items-center mb-2">
                                                        <h5 class="m-0">${item.title}</h5>
                                                        <div class="userMum ${form_template_id}">
                                                            <span class="title mt-0">
                                                                <i class='bxr  bx-file-detail me-1'></i> Form Required
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-3">
                                                        <p class="para text-sm"><i class='bxr  bx-user'></i> Assigned to:
                                                            <span>${item.assign_to_name}</span>
                                                        </p>
                                                        <p class="para text-sm"><i class='bxr  bx-user'></i>About:
                                                            <span>${item.staffmembers_name}</span>
                                                        </p>

                                                        <p class="para text-sm"><i class='bxr  bx-calendar-week'></i> Due:
                                                            <span>${item.due_date}</span>
                                                        </p>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-3">
                                                        <div>
                                                            ${item.taskTypeName}
                                                        </div>
                                                        <div>
                                                            ${item.priorityText}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <button class="bgBtn ${colorText}"><i class='bxr ${icons}  me-3'></i>
                                                    ${statusText}
                                                    </button>

                                            </div>
                                        </div>
                                    </div>`;
                            });
                            // console.log(htm);
                            $("#stafftask-list-wrapper").html(htm);
                            let p = res.pagination;
                            let paginationHtml = '';

                            $("#current_page").val(p.current_page < p.total_pages ? p.current_page : 1);

                            if (p.total_pages > 1) {
                                paginationHtml += `
                                    <button class="page-btn btn ${p.current_page == 1 ? 'disabled' : ''}"
                                        data-page="${p.current_page - 1}">
                                        Prev
                                    </button>`;
                                for (let i = 1; i <= p.total_pages; i++) {

                                    paginationHtml += `
                                        <button class="page-btn btn ${i == p.current_page ? 'btn-primary disabled' : ''}"
                                            data-page="${i}">
                                            ${i}
                                        </button>`;
                                }

                                paginationHtml += `
                                    <button class="page-btn btn ${p.current_page == p.total_pages ? 'disabled' : ''}"
                                        data-page="${p.current_page + 1}">
                                        Next
                                    </button>
                                `;
                            }

                            $("#stafftask-pagination").html(paginationHtml);

                        } else {
                            $("#stafftask-list-wrapper").html(`<div class="emergencyContent emergencyMain AllStaffTabC p-4 blueAllTabCard mt-4"">
                                        <div class="
                                        leavebanktabCont p24">
                                            <i class="bx bx-file-detail"></i>
                                            <p class="mt-3">No task records</p>
                                        </div>
                                    </div>`);
                            return;
                        }

                    },
                    error: function(xhr, ajaxOptions, thrownError) {

                        if (xhr.status == 404) {
                            $("#stafftask-pagination").empty();
                            $("#stafftask-list-wrapper").html(`<div class="emergencyContent emergencyMain AllStaffTabC p-4 blueAllTabCard mt-4"">
                                    <div class="
                                    leavebanktabCont p24">
                                        <i class="bx bx-file-detail"></i>
                                        <p class="mt-3">No task records</p>
                                    </div>
                                </div>`);
                        }
                    }
                });

            }
        </script>
    @endsection
</main>
