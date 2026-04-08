<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
@extends('frontEnd.layouts.master')
@section('title', 'Client Onboadrding')
@section('content')
    @include('frontEnd.roster.common.roster_header')
    <main class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="staffHeaderp">
                        <div>
                            <h1 class="mainTitlep"> Onboarding Configuration</h1>
                            <p class="header-subtitle mb-0">Configure onboarding workflows for your organisation</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt20">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="calendarTabs onBoardConTabHor">
                            <div class="tabs p-1 ">
                                <div class="dFlexGap onBoardTabBtn">
                                    <button class="tab active" data-tab="generalTab"> <i class="bx bx-group f18"></i> Staff
                                        Workflows </button>
                                    <button class="tab " data-tab="availabilityTab"> <i class="bx bx-user-circle f18"></i>
                                        Client Workflows </button>
                                </div>
                            </div>
                            <div class="mt20">
                                <label for="" class="formLabel">Care Setting</label>
                                <select name="" id="" class="form-control" style="background: transparent;">
                                    <option value="">All Care Setting</option>
                                    <option value="">Residential Care</option>
                                    <option value="">Domiciliary Care</option>
                                    <option value="">Supported Living</option>
                                    <option value="">Day Care</option>
                                </select>
                            </div>
                            <div class="mt20">
                                <div class="flexBw align-items-center">
                                    <p class="mb-0 fs13 textGray500">2 workflow(s) for domiciliary</p>
                                    <div>
                                        <button class="bgBtn blackBtn"><i class="bx bx-plus me-2"></i>Create
                                            Workflow</button>
                                    </div>
                                </div>
                            </div>
                            <!-- TAB CONTENT -->
                            <div>
                                <div class="row mt20">
                                    <div class="col-lg-4">
                                        <div class="tab-content carertabcontent">
                                            <div class="content active" id="generalTab">
                                                <!-- staff list  -->
                                                <div class="workflowList">
                                                    <div class="workflowItem active" data-id="1" data-target="wf">
                                                        <div class="emergencyMain p-4">
                                                            <div class="flexBw mb-2">
                                                                <h6 class="h6Head mb-0">Domiciliary Staff Onboarding</h6>
                                                                <div>
                                                                    <span class="careBadg darkGreenBadges">Active</span>
                                                                </div>
                                                            </div>
                                                            <p class="mb-0 muchsmallText">5 stage . domiciliary</p>

                                                        </div>
                                                    </div>
                                                    <div class="workflowItem" data-id="2" data-target="wf">
                                                        <div class="emergencyMain p-4">
                                                            <div class="flexBw mb-2">
                                                                <h6 class="h6Head mb-0">Supported Staff Onboarding</h6>
                                                                <div>
                                                                    <span class="careBadg darkGreenBadges">Active</span>
                                                                </div>
                                                            </div>
                                                            <p class="mb-0 muchsmallText">5 stage . domiciliary</p>

                                                        </div>
                                                    </div>
                                                    <div class="workflowItem" data-id="3" data-target="wf">
                                                        <div class="emergencyMain p-4">
                                                            <div class="flexBw mb-2">
                                                                <h6 class="h6Head mb-0">Resident Staff Onboarding</h6>
                                                                <div>
                                                                    <span class="careBadg darkGreenBadges">Active</span>
                                                                </div>
                                                            </div>
                                                            <p class="mb-0 muchsmallText">5 stage . domiciliary</p>

                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- end  staff list -->
                                            </div>
                                            <div class="content" id="availabilityTab">
                                                <!-- client list  -->
                                                <div class="workflowList">
                                                    <div class="workflowItem active" data-id="1" data-target="wfTab2_">
                                                        <div class="emergencyMain p-4">
                                                            <div class="flexBw mb-2">
                                                                <h6 class="h6Head mb-0">Domiciliary Client Onboarding</h6>
                                                                <div>
                                                                    <span class="careBadg darkGreenBadges">Active</span>
                                                                </div>
                                                            </div>
                                                            <p class="mb-0 muchsmallText">5 stage . domiciliary</p>

                                                        </div>
                                                    </div>
                                                    <div class="workflowItem" data-id="2" data-target="wfTab2_">
                                                        <div class="emergencyMain p-4">
                                                            <div class="flexBw mb-2">
                                                                <h6 class="h6Head mb-0">Supported Client Onboarding</h6>
                                                                <div>
                                                                    <span class="careBadg darkGreenBadges">Active</span>
                                                                </div>
                                                            </div>
                                                            <p class="mb-0 muchsmallText">5 stage . domiciliary</p>

                                                        </div>
                                                    </div>
                                                    <div class="workflowItem" data-id="3" data-target="wfTab2_">
                                                        <div class="emergencyMain p-4">
                                                            <div class="flexBw mb-2">
                                                                <h6 class="h6Head mb-0">Resident Client Onboarding</h6>
                                                                <div>
                                                                    <span class="careBadg darkGreenBadges">Active</span>
                                                                </div>
                                                            </div>
                                                            <p class="mb-0 muchsmallText">5 stage . domiciliary</p>

                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- end client list -->
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="workflowDetails">
                                            <!-- staff details -->
                                            <div class="wfContent active" id="wf1">
                                                <div class="emergencyMain p24">
                                                    <div class="flexBw">
                                                        <h6 class="h6Head mb-0"> Domiciliary Staff Onboarding </h6>
                                                        <div class="dFlexGap">
                                                            <div>
                                                                <button class="borderBtn">Deactivate</button>
                                                            </div>
                                                            <div>
                                                                <button class="bgBtn redBtn"><i class="bx bx-trash"
                                                                        style="font-size: 17px;"></i></button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mt20">
                                                        <div class="flexBw align-items-center">
                                                            <p class="mb-0 h6Head">Workflow Stages</p>
                                                            <div>
                                                                <button class="bgBtn blackBtn" data-toggle="modal"
                                                                    data-target="#addStage" type="button"><i
                                                                        class="bx bx-plus me-2"></i>Add Stage</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- workflow list detail -->
                                                    <div class="mt20">
                                                        <div class="emergencyMain p-4 bottomSpace">
                                                            <div class="flexBw">
                                                                <h6 class="h6Head mb-0">1. Pre-Employment Checks <span
                                                                        class="borderBadg ms-2">Required </span></h6>
                                                                <div class="dFlexGap onBoardIcons mb-3">
                                                                    <button class="hoverBtn"><i
                                                                            class="bx bx-arrow-left f20"
                                                                            style="transform: rotate(90deg);"></i></button>
                                                                    <button class="hoverBtn"><i
                                                                            class="bx bx-arrow-left f20"
                                                                            style="transform: rotate(-90deg);"></i></button>
                                                                    <button class="hoverBtn"><i
                                                                            class="fa fa-pencil-square-o f20"> </i>
                                                                    </button>
                                                                    <button class="hoverBtn"><i class="fa fa-trash-o f20">
                                                                        </i> </button>
                                                                </div>

                                                            </div>
                                                            <p class="muteText mb-2">ID verification, right to work,
                                                                application form</p>
                                                            <p class="muchsmallText mb-0">Entity: PreEmploymentCompliance
                                                            </p>
                                                        </div>
                                                        <div class="emergencyMain p-4 bottomSpace">
                                                            <div class="flexBw">
                                                                <h6 class="h6Head mb-0">2. Pre-Employment Checks <span
                                                                        class="borderBadg ms-2">Required </span></h6>
                                                                <div class="dFlexGap onBoardIcons mb-3">
                                                                    <button class="hoverBtn"><i
                                                                            class="bx bx-arrow-left f20"
                                                                            style="transform: rotate(90deg);"></i></button>
                                                                    <button class="hoverBtn"><i
                                                                            class="bx bx-arrow-left f20"
                                                                            style="transform: rotate(-90deg);"></i></button>
                                                                    <button class="hoverBtn"><i
                                                                            class="fa fa-pencil-square-o f20"> </i>
                                                                    </button>
                                                                    <button class="hoverBtn"><i class="fa fa-trash-o f20">
                                                                        </i> </button>
                                                                </div>

                                                            </div>
                                                            <p class="muteText mb-2">ID verification, right to work,
                                                                application form</p>
                                                            <p class="muchsmallText mb-0">Entity: PreEmploymentCompliance
                                                            </p>
                                                        </div>
                                                        <div class="emergencyMain p-4 bottomSpace">
                                                            <div class="flexBw">
                                                                <h6 class="h6Head mb-0">3. Pre-Employment Checks <span
                                                                        class="borderBadg ms-2">Required </span></h6>
                                                                <div class="dFlexGap onBoardIcons mb-3">
                                                                    <button class="hoverBtn"><i
                                                                            class="bx bx-arrow-left f20"
                                                                            style="transform: rotate(90deg);"></i></button>
                                                                    <button class="hoverBtn"><i
                                                                            class="bx bx-arrow-left f20"
                                                                            style="transform: rotate(-90deg);"></i></button>
                                                                    <button class="hoverBtn"><i
                                                                            class="fa fa-pencil-square-o f20"> </i>
                                                                    </button>
                                                                    <button class="hoverBtn"><i class="fa fa-trash-o f20">
                                                                        </i> </button>
                                                                </div>

                                                            </div>
                                                            <p class="muteText mb-2">ID verification, right to work,
                                                                application form</p>
                                                            <p class="muchsmallText mb-0">Entity: PreEmploymentCompliance
                                                            </p>
                                                        </div>
                                                        <div class="worFlowFooter">
                                                            <div class="flexBw">
                                                                <p class="mb-0 font700 fs13">Auto-activate on completion
                                                                </p>
                                                                <div>
                                                                    <label class="mySwitch">
                                                                        <input type="checkbox" checked>
                                                                        <span class="slider round"></span>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- workflow details end -->
                                                </div>

                                            </div>
                                            <!-- sec -->
                                            <div class="wfContent" id="wf2">
                                                <div class="emergencyMain p24">
                                                    <div class="flexBw">
                                                        <h6 class="h6Head mb-0"> Domiciliary Client Onboarding </h6>
                                                        <div class="dFlexGap">
                                                            <div>
                                                                <button class="borderBtn">Deactivate</button>
                                                            </div>
                                                            <div>
                                                                <button class="bgBtn redBtn"><i class="bx bx-trash"
                                                                        style="font-size: 17px;"></i></button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mt20">
                                                        <div class="flexBw align-items-center">
                                                            <p class="mb-0 h6Head">Workflow Stages</p>
                                                            <div>
                                                                <button class="bgBtn blackBtn"><i
                                                                        class="bx bx-plus me-2"></i>Add Stage</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- workflow list detail -->
                                                    <div class="mt20">
                                                        <div class="emergencyMain p-4 bottomSpace">
                                                            <div class="flexBw">
                                                                <h6 class="h6Head mb-0">1.Mandatory Training <span
                                                                        class="borderBadg ms-2">Required </span></h6>
                                                                <div class="dFlexGap onBoardIcons mb-3">
                                                                    <button class="hoverBtn"><i
                                                                            class="bx bx-arrow-left f20"
                                                                            style="transform: rotate(90deg);"></i></button>
                                                                    <button class="hoverBtn"><i
                                                                            class="bx bx-arrow-left f20"
                                                                            style="transform: rotate(-90deg);"></i></button>
                                                                    <button class="hoverBtn"><i
                                                                            class="fa fa-pencil-square-o f20"> </i>
                                                                    </button>
                                                                    <button class="hoverBtn"><i class="fa fa-trash-o f20">
                                                                        </i> </button>
                                                                </div>

                                                            </div>
                                                            <p class="muteText mb-2">ID verification, right to work,
                                                                application form</p>
                                                            <p class="muchsmallText mb-0">Entity: PreEmploymentCompliance
                                                            </p>
                                                        </div>
                                                        <div class="emergencyMain p-4 bottomSpace">
                                                            <div class="flexBw">
                                                                <h6 class="h6Head mb-0">2. Induction <span
                                                                        class="borderBadg ms-2">Required </span></h6>
                                                                <div class="dFlexGap onBoardIcons mb-3">
                                                                    <button class="hoverBtn"><i
                                                                            class="bx bx-arrow-left f20"
                                                                            style="transform: rotate(90deg);"></i></button>
                                                                    <button class="hoverBtn"><i
                                                                            class="bx bx-arrow-left f20"
                                                                            style="transform: rotate(-90deg);"></i></button>
                                                                    <button class="hoverBtn"><i
                                                                            class="fa fa-pencil-square-o f20"> </i>
                                                                    </button>
                                                                    <button class="hoverBtn"><i class="fa fa-trash-o f20">
                                                                        </i> </button>
                                                                </div>

                                                            </div>
                                                            <p class="muteText mb-2">ID verification, right to work,
                                                                application form</p>
                                                            <p class="muchsmallText mb-0">Entity: PreEmploymentCompliance
                                                            </p>
                                                        </div>
                                                        <div class="emergencyMain p-4 bottomSpace">
                                                            <div class="flexBw">
                                                                <h6 class="h6Head mb-0">3.Probation Review<span
                                                                        class="borderBadg ms-2">Required </span></h6>
                                                                <div class="dFlexGap onBoardIcons mb-3">
                                                                    <button class="hoverBtn"><i
                                                                            class="bx bx-arrow-left f20"
                                                                            style="transform: rotate(90deg);"></i></button>
                                                                    <button class="hoverBtn"><i
                                                                            class="bx bx-arrow-left f20"
                                                                            style="transform: rotate(-90deg);"></i></button>
                                                                    <button class="hoverBtn"><i
                                                                            class="fa fa-pencil-square-o f20"> </i>
                                                                    </button>
                                                                    <button class="hoverBtn"><i class="fa fa-trash-o f20">
                                                                        </i> </button>
                                                                </div>

                                                            </div>
                                                            <p class="muteText mb-2">ID verification, right to work,
                                                                application form</p>
                                                            <p class="muchsmallText mb-0">Entity: PreEmploymentCompliance
                                                            </p>
                                                        </div>
                                                        <div class="worFlowFooter">
                                                            <div class="flexBw">
                                                                <p class="mb-0 font700 fs13">Auto-activate on completion
                                                                </p>
                                                                <div>
                                                                    <label class="mySwitch">
                                                                        <input type="checkbox" checked>
                                                                        <span class="slider round"></span>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- workflow details end -->
                                                </div>
                                            </div>
                                            <!-- third -->
                                            <div class="wfContent" id="wf3">
                                                <div class="emergencyMain p24">
                                                    <div class="flexBw">
                                                        <h6 class="h6Head mb-0"> Domiciliary Client Onboarding </h6>
                                                        <div class="dFlexGap">
                                                            <div>
                                                                <button class="borderBtn">Deactivate</button>
                                                            </div>
                                                            <div>
                                                                <button class="bgBtn redBtn"><i class="bx bx-trash"
                                                                        style="font-size: 17px;"></i></button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mt20">
                                                        <div class="flexBw align-items-center">
                                                            <p class="mb-0 h6Head">Workflow Stages</p>
                                                            <div>
                                                                <button class="bgBtn blackBtn"><i
                                                                        class="bx bx-plus me-2"></i>Add Stage</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- workflow list detail -->
                                                    <div class="mt20">
                                                        <div class="emergencyMain p-4 bottomSpace">
                                                            <div class="flexBw">
                                                                <h6 class="h6Head mb-0">1. Probation Review <span
                                                                        class="borderBadg ms-2">Required </span></h6>
                                                                <div class="dFlexGap onBoardIcons mb-3">
                                                                    <button class="hoverBtn"><i
                                                                            class="bx bx-arrow-left f20"
                                                                            style="transform: rotate(90deg);"></i></button>
                                                                    <button class="hoverBtn"><i
                                                                            class="bx bx-arrow-left f20"
                                                                            style="transform: rotate(-90deg);"></i></button>
                                                                    <button class="hoverBtn"><i
                                                                            class="fa fa-pencil-square-o f20"> </i>
                                                                    </button>
                                                                    <button class="hoverBtn"><i class="fa fa-trash-o f20">
                                                                        </i> </button>
                                                                </div>

                                                            </div>
                                                            <p class="muteText mb-2">ID verification, right to work,
                                                                application form</p>
                                                            <p class="muchsmallText mb-0">Entity: PreEmploymentCompliance
                                                            </p>
                                                        </div>
                                                        <div class="emergencyMain p-4 bottomSpace">
                                                            <div class="flexBw">
                                                                <h6 class="h6Head mb-0">2. Pre-Employment Checks <span
                                                                        class="borderBadg ms-2">Required </span></h6>
                                                                <div class="dFlexGap onBoardIcons mb-3">
                                                                    <button class="hoverBtn"><i
                                                                            class="bx bx-arrow-left f20"
                                                                            style="transform: rotate(90deg);"></i></button>
                                                                    <button class="hoverBtn"><i
                                                                            class="bx bx-arrow-left f20"
                                                                            style="transform: rotate(-90deg);"></i></button>
                                                                    <button class="hoverBtn"><i
                                                                            class="fa fa-pencil-square-o f20"> </i>
                                                                    </button>
                                                                    <button class="hoverBtn"><i class="fa fa-trash-o f20">
                                                                        </i> </button>
                                                                </div>

                                                            </div>
                                                            <p class="muteText mb-2">ID verification, right to work,
                                                                application form</p>
                                                            <p class="muchsmallText mb-0">Entity: PreEmploymentCompliance
                                                            </p>
                                                        </div>
                                                        <div class="emergencyMain p-4 bottomSpace">
                                                            <div class="flexBw">
                                                                <h6 class="h6Head mb-0">3. stagen <span
                                                                        class="borderBadg ms-2">Required </span></h6>
                                                                <div class="dFlexGap onBoardIcons mb-3">
                                                                    <button class="hoverBtn"><i
                                                                            class="bx bx-arrow-left f20"
                                                                            style="transform: rotate(90deg);"></i></button>
                                                                    <button class="hoverBtn"><i
                                                                            class="bx bx-arrow-left f20"
                                                                            style="transform: rotate(-90deg);"></i></button>
                                                                    <button class="hoverBtn"><i
                                                                            class="fa fa-pencil-square-o f20"> </i>
                                                                    </button>
                                                                    <button class="hoverBtn"><i class="fa fa-trash-o f20">
                                                                        </i> </button>
                                                                </div>

                                                            </div>
                                                            <p class="muteText mb-2">ID verification, right to work,
                                                                application form</p>
                                                            <p class="muchsmallText mb-0">Entity: PreEmploymentCompliance
                                                            </p>
                                                        </div>
                                                        <div class="worFlowFooter">
                                                            <div class="flexBw">
                                                                <p class="mb-0 font700 fs13">Auto-activate on completion
                                                                </p>
                                                                <div>
                                                                    <label class="mySwitch">
                                                                        <input type="checkbox" checked>
                                                                        <span class="slider round"></span>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- workflow details end -->
                                                </div>
                                            </div>
                                            <!-- client details  -->
                                            <div class="wfContent2 active" id="wfTab2_1">
                                                <div class="emergencyMain p24">
                                                    <div class="flexBw">
                                                        <h6 class="h6Head mb-0"> Domiciliary Client Onboarding </h6>
                                                        <div class="dFlexGap">
                                                            <div>
                                                                <button class="borderBtn">Deactivate</button>
                                                            </div>
                                                            <div>
                                                                <button class="bgBtn redBtn"><i class="bx bx-trash"
                                                                        style="font-size: 17px;"></i></button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mt20">
                                                        <div class="flexBw align-items-center">
                                                            <p class="mb-0 h6Head">Workflow Stages</p>
                                                            <div>
                                                                <button class="bgBtn blackBtn"><i
                                                                        class="bx bx-plus me-2"></i>Add Stage</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- workflow list detail -->
                                                    <div class="mt20">
                                                        <div class="emergencyMain p-4 bottomSpace">
                                                            <div class="flexBw">
                                                                <h6 class="h6Head mb-0">1. Probation Review <span
                                                                        class="borderBadg ms-2">Required </span></h6>
                                                                <div class="dFlexGap onBoardIcons mb-3">
                                                                    <button class="hoverBtn"><i
                                                                            class="bx bx-arrow-left f20"
                                                                            style="transform: rotate(90deg);"></i></button>
                                                                    <button class="hoverBtn"><i
                                                                            class="bx bx-arrow-left f20"
                                                                            style="transform: rotate(-90deg);"></i></button>
                                                                    <button class="hoverBtn"><i
                                                                            class="fa fa-pencil-square-o f20"> </i>
                                                                    </button>
                                                                    <button class="hoverBtn"><i class="fa fa-trash-o f20">
                                                                        </i> </button>
                                                                </div>

                                                            </div>
                                                            <p class="muteText mb-2">ID verification, right to work,
                                                                application form</p>
                                                            <p class="muchsmallText mb-0">Entity: PreEmploymentCompliance
                                                            </p>
                                                        </div>
                                                        <div class="emergencyMain p-4 bottomSpace">
                                                            <div class="flexBw">
                                                                <h6 class="h6Head mb-0">2. Pre-Employment Checks <span
                                                                        class="borderBadg ms-2">Required </span></h6>
                                                                <div class="dFlexGap onBoardIcons mb-3">
                                                                    <button class="hoverBtn"><i
                                                                            class="bx bx-arrow-left f20"
                                                                            style="transform: rotate(90deg);"></i></button>
                                                                    <button class="hoverBtn"><i
                                                                            class="bx bx-arrow-left f20"
                                                                            style="transform: rotate(-90deg);"></i></button>
                                                                    <button class="hoverBtn"><i
                                                                            class="fa fa-pencil-square-o f20"> </i>
                                                                    </button>
                                                                    <button class="hoverBtn"><i class="fa fa-trash-o f20">
                                                                        </i> </button>
                                                                </div>

                                                            </div>
                                                            <p class="muteText mb-2">ID verification, right to work,
                                                                application form</p>
                                                            <p class="muchsmallText mb-0">Entity: PreEmploymentCompliance
                                                            </p>
                                                        </div>
                                                        <div class="emergencyMain p-4 bottomSpace">
                                                            <div class="flexBw">
                                                                <h6 class="h6Head mb-0">3. stagen <span
                                                                        class="borderBadg ms-2">Required </span></h6>
                                                                <div class="dFlexGap onBoardIcons mb-3">
                                                                    <button class="hoverBtn"><i
                                                                            class="bx bx-arrow-left f20"
                                                                            style="transform: rotate(90deg);"></i></button>
                                                                    <button class="hoverBtn"><i
                                                                            class="bx bx-arrow-left f20"
                                                                            style="transform: rotate(-90deg);"></i></button>
                                                                    <button class="hoverBtn"><i
                                                                            class="fa fa-pencil-square-o f20"> </i>
                                                                    </button>
                                                                    <button class="hoverBtn"><i class="fa fa-trash-o f20">
                                                                        </i> </button>
                                                                </div>

                                                            </div>
                                                            <p class="muteText mb-2">ID verification, right to work,
                                                                application form</p>
                                                            <p class="muchsmallText mb-0">Entity: PreEmploymentCompliance
                                                            </p>
                                                        </div>
                                                        <div class="worFlowFooter">
                                                            <div class="flexBw">
                                                                <p class="mb-0 font700 fs13">Auto-activate on completion
                                                                </p>
                                                                <div>
                                                                    <label class="mySwitch">
                                                                        <input type="checkbox" checked>
                                                                        <span class="slider round"></span>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- workflow details end -->
                                                </div>
                                            </div>

                                            <div class="wfContent2" id="wfTab2_2">
                                                <div class="emergencyMain p24">
                                                    <div class="flexBw">
                                                        <h6 class="h6Head mb-0"> Domiciliary Client Onboarding </h6>
                                                        <div class="dFlexGap">
                                                            <div>
                                                                <button class="borderBtn">Deactivate</button>
                                                            </div>
                                                            <div>
                                                                <button class="bgBtn redBtn"><i class="bx bx-trash"
                                                                        style="font-size: 17px;"></i></button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mt20">
                                                        <div class="flexBw align-items-center">
                                                            <p class="mb-0 h6Head">Workflow Stages</p>
                                                            <div>
                                                                <button class="bgBtn blackBtn"><i
                                                                        class="bx bx-plus me-2"></i>Add Stage</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- workflow list detail -->
                                                    <div class="mt20">
                                                        <div class="emergencyMain p-4 bottomSpace">
                                                            <div class="flexBw">
                                                                <h6 class="h6Head mb-0">1. Probation Review <span
                                                                        class="borderBadg ms-2">Required </span></h6>
                                                                <div class="dFlexGap onBoardIcons mb-3">
                                                                    <button class="hoverBtn"><i
                                                                            class="bx bx-arrow-left f20"
                                                                            style="transform: rotate(90deg);"></i></button>
                                                                    <button class="hoverBtn"><i
                                                                            class="bx bx-arrow-left f20"
                                                                            style="transform: rotate(-90deg);"></i></button>
                                                                    <button class="hoverBtn"><i
                                                                            class="fa fa-pencil-square-o f20"> </i>
                                                                    </button>
                                                                    <button class="hoverBtn"><i class="fa fa-trash-o f20">
                                                                        </i> </button>
                                                                </div>

                                                            </div>
                                                            <p class="muteText mb-2">ID verification, right to work,
                                                                application form</p>
                                                            <p class="muchsmallText mb-0">Entity: PreEmploymentCompliance
                                                            </p>
                                                        </div>
                                                        <div class="emergencyMain p-4 bottomSpace">
                                                            <div class="flexBw">
                                                                <h6 class="h6Head mb-0">2. Pre-Employment Checks <span
                                                                        class="borderBadg ms-2">Required </span></h6>
                                                                <div class="dFlexGap onBoardIcons mb-3">
                                                                    <button class="hoverBtn"><i
                                                                            class="bx bx-arrow-left f20"
                                                                            style="transform: rotate(90deg);"></i></button>
                                                                    <button class="hoverBtn"><i
                                                                            class="bx bx-arrow-left f20"
                                                                            style="transform: rotate(-90deg);"></i></button>
                                                                    <button class="hoverBtn"><i
                                                                            class="fa fa-pencil-square-o f20"> </i>
                                                                    </button>
                                                                    <button class="hoverBtn"><i class="fa fa-trash-o f20">
                                                                        </i> </button>
                                                                </div>

                                                            </div>
                                                            <p class="muteText mb-2">ID verification, right to work,
                                                                application form</p>
                                                            <p class="muchsmallText mb-0">Entity: PreEmploymentCompliance
                                                            </p>
                                                        </div>
                                                        <div class="emergencyMain p-4 bottomSpace">
                                                            <div class="flexBw">
                                                                <h6 class="h6Head mb-0">3. stagen <span
                                                                        class="borderBadg ms-2">Required </span></h6>
                                                                <div class="dFlexGap onBoardIcons mb-3">
                                                                    <button class="hoverBtn"><i
                                                                            class="bx bx-arrow-left f20"
                                                                            style="transform: rotate(90deg);"></i></button>
                                                                    <button class="hoverBtn"><i
                                                                            class="bx bx-arrow-left f20"
                                                                            style="transform: rotate(-90deg);"></i></button>
                                                                    <button class="hoverBtn"><i
                                                                            class="fa fa-pencil-square-o f20"> </i>
                                                                    </button>
                                                                    <button class="hoverBtn"><i class="fa fa-trash-o f20">
                                                                        </i> </button>
                                                                </div>

                                                            </div>
                                                            <p class="muteText mb-2">ID verification, right to work,
                                                                application form</p>
                                                            <p class="muchsmallText mb-0">Entity: PreEmploymentCompliance
                                                            </p>
                                                        </div>
                                                        <div class="worFlowFooter">
                                                            <div class="flexBw">
                                                                <p class="mb-0 font700 fs13">Auto-activate on completion
                                                                </p>
                                                                <div>
                                                                    <label class="mySwitch">
                                                                        <input type="checkbox" checked>
                                                                        <span class="slider round"></span>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- workflow details end -->
                                                </div>
                                            </div>

                                            <div class="wfContent2" id="wfTab2_3">
                                                <div class="emergencyMain p24">
                                                    <div class="flexBw">
                                                        <h6 class="h6Head mb-0"> Domiciliary Client Onboarding </h6>
                                                        <div class="dFlexGap">
                                                            <div>
                                                                <button class="borderBtn">Deactivate</button>
                                                            </div>
                                                            <div>
                                                                <button class="bgBtn redBtn"><i class="bx bx-trash"
                                                                        style="font-size: 17px;"></i></button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mt20">
                                                        <div class="flexBw align-items-center">
                                                            <p class="mb-0 h6Head">Workflow Stages</p>
                                                            <div>
                                                                <button class="bgBtn blackBtn"><i
                                                                        class="bx bx-plus me-2"></i>Add Stage</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- workflow list detail -->
                                                    <div class="mt20">
                                                        <div class="emergencyMain p-4 bottomSpace">
                                                            <div class="flexBw">
                                                                <h6 class="h6Head mb-0">1.Mandatory Training <span
                                                                        class="borderBadg ms-2">Required </span></h6>
                                                                <div class="dFlexGap onBoardIcons mb-3">
                                                                    <button class="hoverBtn"><i
                                                                            class="bx bx-arrow-left f20"
                                                                            style="transform: rotate(90deg);"></i></button>
                                                                    <button class="hoverBtn"><i
                                                                            class="bx bx-arrow-left f20"
                                                                            style="transform: rotate(-90deg);"></i></button>
                                                                    <button class="hoverBtn"><i
                                                                            class="fa fa-pencil-square-o f20"> </i>
                                                                    </button>
                                                                    <button class="hoverBtn"><i class="fa fa-trash-o f20">
                                                                        </i> </button>
                                                                </div>

                                                            </div>
                                                            <p class="muteText mb-2">ID verification, right to work,
                                                                application form</p>
                                                            <p class="muchsmallText mb-0">Entity: PreEmploymentCompliance
                                                            </p>
                                                        </div>
                                                        <div class="emergencyMain p-4 bottomSpace">
                                                            <div class="flexBw">
                                                                <h6 class="h6Head mb-0">2. Mandatory Training <span
                                                                        class="borderBadg ms-2">Required </span></h6>
                                                                <div class="dFlexGap onBoardIcons mb-3">
                                                                    <button class="hoverBtn"><i
                                                                            class="bx bx-arrow-left f20"
                                                                            style="transform: rotate(90deg);"></i></button>
                                                                    <button class="hoverBtn"><i
                                                                            class="bx bx-arrow-left f20"
                                                                            style="transform: rotate(-90deg);"></i></button>
                                                                    <button class="hoverBtn"><i
                                                                            class="fa fa-pencil-square-o f20"> </i>
                                                                    </button>
                                                                    <button class="hoverBtn"><i class="fa fa-trash-o f20">
                                                                        </i> </button>
                                                                </div>

                                                            </div>
                                                            <p class="muteText mb-2">ID verification, right to work,
                                                                application form</p>
                                                            <p class="muchsmallText mb-0">Entity: PreEmploymentCompliance
                                                            </p>
                                                        </div>
                                                        <div class="emergencyMain p-4 bottomSpace">
                                                            <div class="flexBw">
                                                                <h6 class="h6Head mb-0">3.Induction<span
                                                                        class="borderBadg ms-2">Required </span></h6>
                                                                <div class="dFlexGap onBoardIcons mb-3">
                                                                    <button class="hoverBtn"><i
                                                                            class="bx bx-arrow-left f20"
                                                                            style="transform: rotate(90deg);"></i></button>
                                                                    <button class="hoverBtn"><i
                                                                            class="bx bx-arrow-left f20"
                                                                            style="transform: rotate(-90deg);"></i></button>
                                                                    <button class="hoverBtn"><i
                                                                            class="fa fa-pencil-square-o f20"> </i>
                                                                    </button>
                                                                    <button class="hoverBtn"><i class="fa fa-trash-o f20">
                                                                        </i> </button>
                                                                </div>

                                                            </div>
                                                            <p class="muteText mb-2">ID verification, right to work,
                                                                application form</p>
                                                            <p class="muchsmallText mb-0">Entity: PreEmploymentCompliance
                                                            </p>
                                                        </div>
                                                        <div class="worFlowFooter">
                                                            <div class="flexBw">
                                                                <p class="mb-0 font700 fs13">Auto-activate on completion
                                                                </p>
                                                                <div>
                                                                    <label class="mySwitch">
                                                                        <input type="checkbox" checked>
                                                                        <span class="slider round"></span>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- workflow details end -->
                                                </div>

                                                <!-- workflow details end -->
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <!-- END TAB CONTENT -->
                        </div>
                    </div>
                </div>
            </div>
            <!-- add stage -->
            <div class="modal fade" id="addStage" tabindex="1" role="dialog" aria-labelledby="myModalLabel"
                aria-hidden="true">
                <div class="modal-dialog pModalScroll">
                    <div class="modal-content">
                        <form action="">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal"
                                    aria-hidden="true">&times;</button>
                                <h4 class="modal-title"> Add New Stage</h4>
                            </div>
                            <div class="modal-body heightScrollModal">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label for="">Stage Name</label>
                                        <input type="text" class="form-control"
                                            placeholder="e.g., Pre-Employment Checks">
                                    </div>
                                    <div class="col-lg-12 m-t-10">
                                        <label for="">Description</label>
                                        <textarea name="morning" class="form-control" rows="3" cols="20"
                                            placeholder="Describe what needs to be completed"></textarea>
                                    </div>
                                    <div class="col-lg-6 m-t-10">
                                        <label for="">Entity Type </label>
                                        <select name="" id="" class="form-control">
                                            <option value="">Pre-Employment</option>
                                            <option value="">Induction</option>
                                            <option value="">Training</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-6 m-t-10">
                                        <label for="">Stage Name</label>
                                        <input type="text" class="form-control"
                                            placeholder="e.g., Pre-Employment Checks">
                                    </div>
                                    <div class="col-lg-12 mt-4">
                                        <div class="flexBw mb-3">
                                            <p class="fs13 font700 mb-0">Required Stage</p>
                                            <label class="mySwitch">
                                                <input type="checkbox" checked>
                                                <span class="slider round"></span>
                                            </label>
                                        </div>
                                        <div class="flexBw mb-3">
                                            <p class="fs13 font700 mb-0">Auto-create Task</p>
                                            <label class="mySwitch">
                                                <input type="checkbox" checked>
                                                <span class="slider round"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer dFlexGap justify-content-end">
                                <div>
                                    <button class="borderBtn" data-dismiss="modal" type="button">Cancel</button>
                                </div>
                                <div><button class="bgBtn blackBtn"><i class="bx bx-save f18"></i> Save Stage</button>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
            <!-- end add stage -->
        </div>

        <script>
            document.querySelectorAll(".tab").forEach(tab => {
                tab.addEventListener("click", () => {
                    // Activate clicked tab button
                    document.querySelectorAll(".tab").forEach(t => t.classList.remove("active"));
                    tab.classList.add("active");

                    document.querySelectorAll(".content").forEach(c => c.classList.remove("active"));
                    const tabId = tab.dataset.tab;
                    document.getElementById(tabId).classList.add("active");

                    document.querySelectorAll(".wfContent, .wfContent2").forEach(d => d.classList.remove(
                        "active"));

                    const activeLeftPanel = document.getElementById(tabId);
                    const firstItem = activeLeftPanel.querySelector(".workflowItem");

                    if (firstItem) {

                        activeLeftPanel.querySelectorAll(".workflowItem").forEach(i => i.classList.remove(
                            "active"));
                        firstItem.classList.add("active");
                        const prefix = firstItem.dataset.target || "wf";
                        const id = firstItem.dataset.id;
                        const detailId = prefix + id;

                        const detailElement = document.getElementById(detailId);
                        if (detailElement) {
                            detailElement.classList.add("active");
                        }
                    }
                });
            });
        </script>
        <script>
            document.querySelectorAll(".workflowItem").forEach(item => {
                item.addEventListener("click", () => {
                    const parentContent = item.closest(".content");
                    parentContent.querySelectorAll(".workflowItem").forEach(i => i.classList.remove("active"));
                    item.classList.add("active");
                    const targetPrefix = item.dataset.target || "wf";
                    document.querySelectorAll(`[id^="${targetPrefix}"]`).forEach(d => d.classList.remove(
                        "active"));
                    const id = item.dataset.id;
                    const targetElement = document.getElementById(targetPrefix + id);
                    if (targetElement) {
                        targetElement.classList.add("active");
                    }
                });
                const activeTabContent = document.querySelector(".content.active");
                if (!activeTabContent) return;
                let activeItem = activeTabContent.querySelector(".workflowItem.active");
                if (!activeItem) {
                    activeItem = activeTabContent.querySelector(".workflowItem"); // fallback to first one
                }

                if (activeItem) {
                    activeItem.click();
                }
            });
        </script>
    </main>
@endsection
