@extends('backEnd.layouts.master')
@section('title', 'Pay Rates')
@section('content')

    <?php
    if (isset($payrate)) {
        $action = url('admin/user/pay-rates/edit/' . $payrate->id);
        $task = 'Edit';
        $form_id = 'EditUserPayRates';
    } else {
        $action = url('admin/user/pay-rates/save');
        $task = 'Add';
        $form_id = 'AddUserPayRates';
    }
    ?>

    <style type="text/css">
        .form-actions {
            margin: 20px 0px 0px 0px;
        }

        .col-lg-offset-2 .btn.btn-primary {
            margin: 0px 10px 0px 0px;
        }
    </style>


    <section id="main-content" class="">
        <section class="wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <section class="panel">
                        <header class="panel-heading">
                            {{ $task }} Pay Rates
                        </header>
                        <div class="panel-body">
                            <div class="position-center">
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                @include('backEnd.common.alert_messages')
                                <form class="form-horizontal" role="form" method="post" action="{{ $action }}"
                                    id="{{ $form_id }}">
                                    <div class="form-group">
                                        <label class="col-lg-2 control-label">Access levels</label>
                                        <div class="col-lg-10">
                                            <select name="access_level_id" class="form-control" id="">
                                                @foreach ($accesslevel as $key => $value)
                                                    <option value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-lg-2 control-label">Rate Type</label>
                                        <div class="col-lg-10">
                                            <select name="rate_type_id" class="form-control" id="">
                                                @foreach ($rateType as $type)
                                                    <option value="{{ $type->id }}">{{ $type->type_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-lg-2 control-label">Pay Rate</label>
                                        <div class="col-lg-10">
                                            <input type="text" name="pay_rate" class="form-control"
                                                placeholder="Pay Rates"
                                                value="{{ isset($u_sick_leave->title) ? $u_sick_leave->title : '' }}"
                                                maxlength="255">
                                        </div>
                                    </div>
                                    <div class="form-actions">
                                        <div class="row">
                                            <div class="col-lg-offset-2 col-lg-10">
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                <input type="hidden" name="id" value="">
                                                <button type="submit" class="btn btn-primary">Save</button>
                                                <button type="button" class="btn btn-default"
                                                    name="cancel">Cancel</button>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </section>
    </section>

@endsection
