@extends('frontEnd.layouts.master')
@section('title', 'Incident Management')
@section('content')

@include('frontEnd.roster.common.roster_header')

@php
    $severityMap = [1 => ['Low', 'careBadg'], 2 => ['Medium', 'careBadg yellowBadges'], 3 => ['High', 'careBadg highBadges'], 4 => ['Critical', 'careBadg redbadges']];
    $statusMap = [1 => ['Reported', 'careBadg yellowBadges'], 2 => ['Under Investigation', 'careBadg darkBlueBadg'], 3 => ['Resolved', 'careBadg darkGreenBadges'], 4 => ['Closed', 'careBadg muteBadges']];
    $severity = $severityMap[$incident->severity_id] ?? ['Unknown', 'careBadg'];
    $status = $statusMap[$incident->status] ?? ['Unknown', 'careBadg'];
@endphp

<main class="page-content">

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <a href="{{ url('roster/incident-management') }}" class="borderBtn"><i class=" f18 bx  bx-arrow-left-stroke"></i> Back to Incidents</a>
            </div>
        </div>
        <div class="row mt-5">
            <div class="col-lg-12">
                <div class="bBorderCard {{ $incident->is_safeguarding ? 'urReqSec' : '' }} emergencyHeader incidentDetailHead">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="mb-2">
                                <h5 class="h5Head mb-3"><i class="fs23 bx bx-shield me-3"></i>{{ $incident->incidentType->type ?? 'Incident' }}</h5>
                                <div class="d-flex align-items-center gap-3">
                                    <div>
                                        <span class="{{ $severity[1] }}">{{ $severity[0] }}</span>
                                    </div>
                                    <div>
                                        <span class="{{ $status[1] }}">{{ $status[0] }}</span>
                                    </div>
                                    @if($incident->is_safeguarding)
                                    <div>
                                        <span class="careBadg redDarkBadgesAni">SAFEGUARDING</span>
                                    </div>
                                    @endif
                                    @if($incident->cqcNotification)
                                    <div>
                                        <span class="careBadg purpleBadgesDark">CQC NOTIFIABLE</span>
                                    </div>
                                    @endif
                                    <div class="userMum">
                                        <span class="title mt-0">
                                            <span>Ref: </span> {{ $incident->ref }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Status Workflow --}}
        <div class="row mt20">
            <div class="col-lg-12">
                <div class="emergencyMain p24">
                    <h6 class="h6Head mb-3">Update Status</h6>
                    <div class="d-flex gap-3 align-items-center">
                        @php
                            $statusFlow = [
                                1 => ['next' => 2, 'label' => 'Start Investigation', 'icon' => 'bx-search-alt', 'class' => 'bgBtn'],
                                2 => ['next' => 3, 'label' => 'Mark Resolved', 'icon' => 'bx-check', 'class' => 'bgBtn bgGreenBtn'],
                                3 => ['next' => 4, 'label' => 'Close Incident', 'icon' => 'bx-lock', 'class' => 'borderBtn'],
                            ];
                        @endphp
                        @foreach([1 => 'Reported', 2 => 'Under Investigation', 3 => 'Resolved', 4 => 'Closed'] as $sId => $sLabel)
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:600;
                                    {{ $incident->status >= $sId ? 'background:#16a34a;color:#fff;' : 'background:#e5e7eb;color:#9ca3af;' }}">
                                    {{ $incident->status >= $sId ? '✓' : $sId }}
                                </div>
                                <span style="font-size:13px;{{ $incident->status == $sId ? 'font-weight:600;' : 'color:#6b7280;' }}">{{ $sLabel }}</span>
                                @if($sId < 4)
                                    <span style="color:#d1d5db;margin:0 4px;">→</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    @if(isset($statusFlow[$incident->status]))
                        <form action="{{ url('roster/incident-status-update/' . $incident->id) }}" method="POST" class="mt-3">
                            @csrf
                            <input type="hidden" name="status" value="{{ $statusFlow[$incident->status]['next'] }}">
                            <button type="submit" class="{{ $statusFlow[$incident->status]['class'] }}">
                                <i class="bx {{ $statusFlow[$incident->status]['icon'] }} f18 me-2"></i>
                                {{ $statusFlow[$incident->status]['label'] }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="row mt20">
            <!-- left part -->
            <div class="col-lg-8">
                <div class="emergencyMain">
                    <div class="cardHeaderp p24">
                        <h5 class="h5Head mb-0">Incident Details</h5>
                    </div>
                    <div class="incidentDeCon p24">
                        <p><strong>What Happened</strong></p>
                        <p>{{ $incident->what_happened }}</p>
                        <div class="bg-blue-50 p-4 mt-4">
                            <p><strong>Immediate Action Taken</strong></p>
                            <p>{{ $incident->immediate_action }}</p>
                        </div>
                    </div>
                    <div class="icndentDeFooter p24">
                        <div class="d-flex">
                            <div class="w50">
                                <p><strong>Date & Time</strong></p>
                                <p>{{ \Carbon\Carbon::parse($incident->date_time)->format('d/m/Y H:i') }}</p>
                            </div>
                            <div>
                                <p><strong>Location</strong></p>
                                <p class="mb-0">{{ $incident->location }}</p>
                                @if($incident->location_detail)
                                    <small class="muteText">({{ $incident->location_detail }})</small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Safeguarding section --}}
                @if($incident->is_safeguarding && $incident->safeguarddetails && $incident->safeguarddetails->count())
                <div class="emergencyMain bg-red-50 IncidentDetailsafe mt20">
                    <div class="cardHeaderp p24">
                        <h5 class="h5Head mb-0">
                            <i class="bx bx-alert-triangle fs23 me-2"></i> Safeguarding Concern
                        </h5>
                    </div>
                    <div class="incidentDeCon p24">
                        <p><strong>Types of Concern:</strong></p>
                        <div class="parentRedBad">
                            @foreach($incident->safeguarddetails as $sg)
                            <div>
                                <span class="careBadg redDarkBadges">{{ $sg->type }}</span>
                            </div>
                            @endforeach
                        </div>
                        <div class="incidentRrqAction p-4 mt-4">
                            <strong>Required Actions:</strong>
                            <ul>
                                <li>Notify Local Authority Safeguarding Team immediately</li>
                                <li>Complete safeguarding investigation</li>
                                <li>Notify CQC if serious harm or risk</li>
                                <li>Document all actions taken</li>
                                <li>Consider police involvement if criminal offense</li>
                            </ul>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Investigation & Resolution --}}
                <div class="emergencyMain mt20">
                    <div class="cardHeaderp p24">
                        <h5 class="h5Head mb-0">Investigation & Resolution</h5>
                    </div>
                    <div class="incidentDeCon p24">
                        @if($incident->investigation_findings || $incident->resolution_notes || $incident->lessons_learned)
                            @if($incident->investigation_findings)
                                <p><strong>Investigation Findings</strong></p>
                                <p>{{ $incident->investigation_findings }}</p>
                            @endif
                            @if($incident->resolution_notes)
                                <div class="bg-blue-50 p-4 mt-4">
                                    <p><strong>Resolution Notes</strong></p>
                                    <p>{{ $incident->resolution_notes }}</p>
                                </div>
                            @endif
                            @if($incident->lessons_learned)
                                <div class="mt-4">
                                    <p><strong>Lessons Learned</strong></p>
                                    <p>{{ $incident->lessons_learned }}</p>
                                </div>
                            @endif
                        @else
                            <p class="muteText text-center">No investigation details recorded yet</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- right part -->
            <div class="col-lg-4">
                <div class="inciDeRight">
                    <div class="emergencyMain">
                        <div class="cardHeaderp cyanGrad p24">
                            <h6 class="h6Head mb-0">Client Information</h6>
                        </div>
                        <div class="p24">
                            <p><strong>{{ $incident->clients->name ?? 'Unknown Client' }}</strong></p>
                            @if(isset($incident->clients->phone_no))
                            <p class="muteText">
                                <i class="bx bx-phone f18 me-1"></i> {{ $incident->clients->phone_no }}
                            </p>
                            @endif
                        </div>
                    </div>
                    <div class="emergencyMain mt20">
                        <div class="cardHeaderp p24">
                            <h6 class="h6Head mb-0">Notifications</h6>
                        </div>
                        <div class="p-4">
                            <div class="{{ $incident->family_notify ? 'bg-greenp-50' : 'muteBg' }} rounded8 p-3 mt-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <p class="mb-0"><strong>Family Notified</strong></p>
                                    <div>
                                        @if($incident->family_notify)
                                            <i class="bx bx-check-circle f18 me-2" style="color:#16a34a"></i>
                                        @else
                                            <i class="bx bx-x-circle f18 me-2" style="color:#9ca3af"></i>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="{{ $incident->cqcNotification ? 'bg-purple-50' : 'muteBg' }} rounded8 p-3 mt-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <p class="mb-0"><strong>CQC Notification</strong></p>
                                    <div>
                                        @if($incident->cqcNotification)
                                            <i class="bx bx-alert-triangle f18 me-2 redIColor"></i>
                                        @else
                                            <i class="bx bx-x-circle f18 me-2" style="color:#9ca3af"></i>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="{{ $incident->policeInvolved ? 'bg-red-50' : 'muteBg' }} rounded8 p-3 mt-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <p class="mb-0"><strong>Police Involved</strong></p>
                                    <div>
                                        @if($incident->policeInvolved)
                                            <i class="bx bx-check-circle f18 me-2" style="color:#dc2626"></i>
                                        @else
                                            <i class="bx bx-x-circle f18 me-2" style="color:#9ca3af"></i>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if($incident->cqcNotification)
                    <div class="emergencyMain maincqcInde bg-purple-50 mt20">
                        <div class="cardHeaderp p24">
                            <h6 class="h6Head mb-0">CQC Requirements</h6>
                        </div>
                        <div class="listcqcInDe p-3">
                            <p>This incident requires statutory notification to CQC</p>
                            <ul>
                                <li>Notify without delay</li>
                                <li>Use CQC notification portal</li>
                                <li>Provide full details</li>
                                <li>Keep reference number</li>
                            </ul>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
