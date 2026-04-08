@extends('frontEnd.layouts.master')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('title','Staff Timesheet')
<link rel="stylesheet" type="text/css" href="{{ url('public/frontEnd/jobs/css/custom.css')}}" />
@section('content')

<style>
    table.tablechange tbody td {
        font-size: 12px;
        white-space: nowrap;
    }

    .image_delete {
        cursor: pointer;
    }

    .textbox {
        box-sizing: border-box;
        perspective: 500px;
        position: relative;
        text-align: left;
    }

    .textbox input {
        padding: 10px 14px;
        width: 100%;
    }

    .textbox input::placeholder {
        color: #ccc;
    }

    .textbox .autoCompleteJob {
        left: 0;
        position: absolute;
        top: calc(100% + 5px);
        width: 100%;
    }

    .textbox .autoCompleteJob .item {
        animation: showItem .3s ease forwards;
        background-color: #fff;
        box-shadow: 0 8px 8px -10px rgba(0, 0, 0, .4);
        box-sizing: border-box;
        color: #7C8487;
        cursor: pointer;
        display: block;
        font-size: .8rem;
        opacity: 0;
        outline: none;
        padding: 10px;
        text-decoration: none;
        transform-origin: top;
        /* transform: rotateX(-90deg); */
        transform: translateX(10px);
    }

    .textbox .autoCompleteJob .item:hover,
    .textbox .autoCompleteJob .item:focus {
        background-color: #fafafa;
        color: #D1822B;
    }

    @keyframes showItem {
        0% {
            opacity: 0;
            /* transform: rotateX(-90deg); */
            transform: translateX(10px);
        }

        100% {
            opacity: 1;
            /* transform: rotateX(0); */
            transform: translateX(0);
        }
    }

    .select2-container--default .select2-selection--single {
        height: 38px;
        padding: 5px;
        border-radius: 4px;
        border: 1px solid #ced4da;
        font-size: 14px;
    }

    .select2-container .select2-selection--single .select2-selection__arrow {
        height: 100%;
    }

    .select2-container .select2-selection--single .select2-selection__rendered {
        padding: 4px;
    }

    .select2-container .select2-selection--multiple {
        min-height: 32px !important;
    }

    .parent-container {
        position: absolute;
        background: #fff;
        width: 190px;
    }

    #productList li:hover {
        cursor: pointer;
    }

    .dropdown-item {
        padding: 6px 15px;
        font-size: 13px;
        color: #212529;
        text-align: inherit;
        text-decoration: none;
        display: block;
        width: 100%;
        background-color: transparent;
        border: 0;
        border-radius: 0;
        transition: all 0.2s ease-in-out;
    }

    .dropdown-item:hover {
        background-color: #f8f9fa;
        color: #212529;
    }
</style>

<!--main content start-->
<section class="wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 p-0">
                <div class="panel">
                    <header class="panel-heading px-5">
                        <h4>Staff Timesheet</h4>
                    </header>
                    <div class="panel-body">
                        <div class="col-lg-12">
                            <div class="jobsection justify-content-between align-items-center">
                                <div class="d-flex justify-content-end gap-4 align-items-center">
                                    <a href="javascript:void(0)" class="btn btn-warning modal_open" data-action="add"><i class="fa fa-plus"></i> Add</a>
                                    <label for="fromDate" class="mb-0">Date:</label>
                                    
                                    <input type="date" name="" id="date_timesheet">
                                    <button type="button" class="btn btn-warning" onclick="location.reload()">Reset</button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-12">
                            @if(session('message'))
                            <div class="alert alert-success text-center success_message mt-3 m-auto" style="height:50px; width:50%">
                                <p>{{ session('message') }}</p>
                            </div>
                            @endif
                            @if(session('staff_error'))
                            <div class="form-group col-md-12 col-sm-12 col-xs-12 popup_alrt_msg">
                                <div class="popup_notification-box">
                                    <div class="alert alert-danger alert-dismissible m-0" role="alert">
                                        <button type="button" class="close close-msg-btn"><span aria-hidden="true">&times;</span></button>
                                        <strong>Success!</strong> <span class="popup_error_txt">{{ session('staff_error') }}</span>.
                                    </div>
                                </div>
                            </div>
                            @endif
                            <div class="form-group col-md-12 col-sm-12 col-xs-12 popup_alrt_msgblade" style="display:none">
                                <div class="popup_notification-box">
                                    <div class="alert alert-success alert-dismissible m-0" role="alert">
                                        <button type="button" class="close close-msg-btn"><span aria-hidden="true">&times;</span></button>
                                        <strong>Success!</strong> <span class="popup_success_txt"></span>.
                                    </div>
                                </div>
                            </div>

                            <div class="maimtable productDetailTable mb-4  table-responsive">
                                <!-- <div class="delete_table_row">
                                    <a href="javascript:void(0)" id="deleteSelectedRows" class="btn btn-danger">Delete</a>
                                </div> -->
                                <table class="table border-top border-bottom tablechange" id="satffTimesheetTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{date('F Y')}}</th>
                                            <th>Total Shitf Hours</th>
                                            <th>Category Type</th>
                                            <th>Extra Hours</th>
                                            <th>Comments</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody id="user_data">
                                        
                                       <tr>
                                            <td>1</td>
                                            <td>{{date('Y-m-d')}}</td>
                                            <td>1 Hour</td>
                                            <td>On Call</td>
                                            <td>1h 15min</td>
                                            <td>Test</td>
                                            <td>
                                                <div class="pageTitleBtn p-0">
                                                    <div class="dropdown">
                                                        <a href="#" class="btn btn-primary btn-sm" data-toggle="dropdown" aria-expanded="false">
                                                            Action <i class="fa fa-caret-down"></i>
                                                        </a>
                                                        <div class="dropdown-menu dropdown-menu-right fade-up m-0">
                                                            <a href="javascript:void(0)" class="dropdown-item col-form-label modal_open">Edit</a>
                                                            <!-- <a href="javascript:void(0)" class="dropdown-item col-form-label">View Details</a> -->
                                                            <a href="javascript:void(0)" class="dropdown-item col-form-label">Delete</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                       </tr>
                                    </tbody>
                                </table>
                            </div> <!-- End off main Table -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js" defer></script>
<script>
    $(document).ready(function(){
        setTimeout(function() {
            $(".alert").fadeOut();
        }, 3000);
    });
    new DataTable('#satffTimesheetTable');
</script>
@endsection