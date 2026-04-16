<?php
    $homeId = (int) explode(',', Auth::user()->home_id)[0];
    $service_user_list = App\ServiceUser::where('is_deleted','0')
                                ->where('home_id', $homeId)
                                ->select('id', 'name', 'user_name')
                                ->get();
?>
<!-- Child list Modal -->
<div class="modal fade" id="ServiceUserlistModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <!-- <a class="close view-all-logs mdl-back-btn" href="" data-toggle="modal" data-dismiss="modal" data-target=""> -->
                    <!-- <i class="fa fa-arrow-left" title=""></i> -->
                <!-- </a> -->
                <h4 class="modal-title"> Child</h4>
            </div>
            <div class="modal-body" >
                <div class="row">
                    <div class="add-new-box risk-tabs custm-tabs">
                        <!-- <form method="post" action="{{ url('/handover/daily/log')}}" id="srvc-user-lst"> -->
                            <div class="form-group col-md-12 col-sm-12 col-xs-12 p-0 add-rcrd">
                                <label class="col-md-4 col-sm-2 col-xs-12 p-t-7 text-right"> Select Child: </label>
                                <div class="col-md-6 col-sm-9 col-xs-10">
                                    <div class="select-bi" style="width:100%;float:left;">
                                        
                                        <select class="js-example-placeholder-single1 form-control" required id="records_list" style="width:100%;" name="service_usr_id">
                                            <option value="">Select Child</option>';

                                            @foreach($service_user_list as $value){
                                                <option value="{{$value->id}}">{{ucfirst($value->user_name)}}</option>;
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- <div class="form-group col-md-12 col-sm-12 col-xs-12 p-0 add-rcrd">
                                <label class="col-md-4 col-sm-2 col-xs-12 p-t-7 text-right"> Select Category: </label>
                                    <div class="col-md-6 col-sm-10 col-xs-12">
                                        <div class="select-bi" style="width:100%;float:left;">
                                            <select class="select-field form-control" required id="records_list" style="width:100%;" name="category_id">
                                                <option value="">Select Category</option>
                                                <option value="SER_DEL_REC">Daily Record</option>
                                                <option value="SER_HEL_REC">Health Record</option>
                                                <option value="SER_EDU_REC">Education / Training</option>
                                                <option value="SER_LIV_SKIL">Living Skills</option>
                                            </select>
                                        </div>
                                    </div>
                            </div> -->

                            <!-- @include('frontEnd.common.popup_alert_messages') -->
            
                            <div class="form-group modal-footer m-t-0 modal-bttm">
                                <button class="btn btn-default" type="button" data-dismiss="modal" aria-hidden="true"> Cancel </button>
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <button class="btn btn-warning submt-srvc-user" type="submit"> Submit </button>
                                <!-- <button class="btn btn-warning" type="submit" data-target="#HandoverlogBookModal" data-toggle="modal" data-dismiss="modal"> Submit </button> -->
                            </div>
                        <!-- </form> -->

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="HandoverlogBookModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"> Hand Over Log Book </h4>
            </div>
            <div class="modal-body" >
                <div class="row">
                    <div class="form-group col-md-12 col-sm-12 col-xs-12 serch-btns text-right">
                        <!-- <button class="btn label-default add-new-btn active" type="button"> Add New </button> -->
                        <!-- <button class="btn label-default logged-btn active" type="button"> Logged </button>
                        <button class="btn label-default search-btn active" type="button"> Search </button> -->
                        <!-- <button class="btn label-default logged-btn active" type="button"> Logged </button>
                        <button class="btn label-default search-btn" type="button"> Search </button> -->
                    </div>
                    @include('frontEnd.common.popup_alert_messages')
                    <!-- logged plans -->
                    <div class="by-dflt-shw risk-tabs custm-tabs">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <h3 class="m-t-0 m-b-20 clr-blue fnt-20"> Logged </h3>
                        </div>
                        <!-- alert messages -->
                        <!-- @include('frontEnd.common.popup_alert_messages') -->
                        <!-- <form id="edit-hndovr-daily-logged-form" action="{{}}" method="post"> -->
                            <div class="modal-space modal-pading hndovr-log-book-list text-center">
                                <!-- logged book list be shown here using ajax -->
                            </div>
                        <!-- </form> -->
                        <div class="modal-footer m-t-0 recent-task-sec">
                            <button class="btn btn-default" type="button" data-dismiss="modal" aria-hidden="true"> Cancel </button>
                            <!-- <button class="btn btn-warning logged_daily_record_btn" type="button"> Confirm</button> -->
                        </div>
                    </div>

                    <div class="search-box risk-tabs custm-tabs">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <h3 class="m-t-0 m-b-20 clr-blue fnt-20">Search</h3>
                        </div>

                        <div class="col-md-12 col-sm-12 col-xs-12 p-0 type-field">
                            <label class="col-md-2 col-sm-2 col-xs-12 p-t-7 cus-lbl"> Type: </label>
                            <div class="col-md-9 col-sm-9 col-xs-12 p-0 m-b-15">
                                <div class="select-style">
                                    <select name="log_book_search_type">
                                        <option value='log_title' <?php echo 'selected';?>> Title </option>
                                        <option value='log_date'> Date </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 col-sm-12 col-xs-12 p-0 srch-field">
                            <label class="col-md-2 col-sm-2 col-xs-12 p-t-7 cus-lbl"> Title: </label>
                            <div class="col-md-9 col-sm-9 col-xs-12 p-0 m-b-15 title">
                                <input type="text" name="log_book_title_search" class="form-control"><!-- id="log-book-datepicker" -->
                            </div>
                        </div>

                        <div class="col-md-12 col-sm-12 col-xs-12 p-0 add-rcrd srch-field">
                            <label class="col-md-2 col-sm-2 col-xs-12 p-t-7 cus-lbl"> Date: </label>
                            <div class="col-md-9 col-sm-9 col-xs-12 p-0 m-b-15">
                                <div data-date-viewmode="" data-date-format="dd-mm-yyyy" data-date=""  class="input-group date dpYears">
                                    <input name="log_book_date_search" type="text"  value="" size="45" class="form-control" readonly="">
                                    <span class="input-group-btn add-on">
                                        <button class="btn clndr btn-primary" type="button"><i class="fa fa-calendar"></i></button>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <form id="srchd-log-books-form" method="post">
                            <div class="modal-space modal-pading log_book_searched_records text-center">
                            <!-- <div class="modal-space log_book_searched_records p-t-0" > -->
                            <!--searched Record List using ajax -->
                            </div>
                        </form>

                        <div class="modal-footer m-t-0 recent-task-sec">
                            <button class="btn btn-default" type="button" data-dismiss="modal" aria-hidden="true"> Cancel </button>
                            <button class="btn btn-warning search_log_record_btn" type="button"> Confirm</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $('.by-dflt-shw').show();
</script>
<script type="text/javascript">
    
    $(document).on('click','.hndovr-daily-rcd-head', function(){
        $(this).next('.hndovr-daily-rcd-content').slideToggle();
        $(this).find('i').toggleClass('fa-angle-down');
        $('.input-plusbox').hide();
    });
</script>
<script type="text/javascript">
    $(document).ready(function(){
        $('.hndovr-daily-rcd-content').slideUp();
    });
</script>
<script >
//select options of YPs
    $(".js-example-placeholder-single1").select2({
        dropdownParent: $('#ServiceUserlistModal'),
        // placeholder: "Select Option"
    });
</script>

<script type="text/javascript">
    $(document).on('click','.submt-srvc-user',function(){

        var service_usr_id  = $('select[name=service_usr_id]').val();
        var token           = $('input[name=_token]').val();
        error = 0;
        // if(service_usr_id =='') {

        //     $('select[name=\'service_usr_id\']').parent().addClass('red_border');
        //     error = 1;
        // } else {
        //     $('select[name=\'service_usr_id\']').parent().removeClass('red_border');
        // }
        // if(error == 1) {
        //     return false;
        // }
        $('.loader').show();
        $.ajax({
            type :  'post',
            url  :  "{{ url('/handover/daily/log') }}",
            // data :  {'service_usr_id':service_usr_id, '_token':token },
            //dataType : 'json',
            success: function(resp){
                if (isAuthenticated(resp) == false){
                    return false;
                }
                if(resp == 0 || resp == '') {
                    $('#ServiceUserlistModal').modal('hide');
                    $('#HandoverlogBookModal').modal('show');
                    $('.hndovr-log-book-list').html('No Logs Found');
                } else {
                    $('#ServiceUserlistModal').modal('hide');
                    $('#HandoverlogBookModal').modal('show');
                    $('.hndovr-log-book-list').html(resp);
                }

                $('.loader').hide();
                $('body').removeClass('body-overflow');
            }
        });
    }); 
</script>

<script type="text/javascript">
    $(document).on('click','.sbmt_btn',function(){
        var handover_log_book_id = $(this).attr('handover_log_book_id');
        var formdata = $('#edit-hndovr-daily-logged-form'+handover_log_book_id).serialize();
            $('.loader').show();
            $.ajax({
                type : 'post',
                data : formdata,
                url  : "{{ url('/handover/daily/log/edit') }}",
                success : function(resp){
                    if (isAuthenticated(resp) == false){
                        return false;
                    }
                    if(resp == '1'){
                        $('span.popup_success_txt').text('Record edited successfully');
                        $('.popup_success').show();
                        setTimeout(function(){$(".popup_success").fadeOut()}, 5000);
                    } else {
                        $('span.popup_error_txt').text('Failed to update record');
                        $('.popup_error').show();
                        setTimeout(function(){$(".popup_error").fadeOut()}, 5000);
                    }
                    $('.loader').hide();
                },
                error: function(){
                    $('.loader').hide();
                    $('span.popup_error_txt').text('An error occurred. Please try again.');
                    $('.popup_error').show();
                    setTimeout(function(){$(".popup_error").fadeOut()}, 5000);
                }
            });
            return false;
    });
</script>

<script type="text/javascript">
    // Search handler
    $(document).on('click','.search_log_record_btn',function(){
        var searchType = $('select[name=log_book_search_type]').val();
        var searchVal = '';
        var dateVal = '';

        if(searchType == 'log_title'){
            searchVal = $('input[name=log_book_title_search]').val();
            if(!searchVal) { return false; }
        } else {
            dateVal = $('input[name=log_book_date_search]').val();
            if(!dateVal) { return false; }
        }

        var token = $('input[name=_token]').val();
        $('.loader').show();
        $.ajax({
            type : 'post',
            url  : "{{ url('/handover/daily/log') }}?search=" + encodeURIComponent(searchVal) + "&log_book_search_type=" + encodeURIComponent(searchType) + "&log_book_date_search=" + encodeURIComponent(dateVal),
            data : {'_token': token},
            success: function(resp){
                if (isAuthenticated(resp) == false){
                    return false;
                }
                if(resp == 0 || resp == '') {
                    $('.log_book_searched_records').html('No records found');
                } else {
                    $('.log_book_searched_records').html(resp);
                }
                $('.loader').hide();
            },
            error: function(){
                $('.loader').hide();
                $('span.popup_error_txt').text('Search failed. Please try again.');
                $('.popup_error').show();
                setTimeout(function(){$(".popup_error").fadeOut()}, 5000);
            }
        });
    });

    // Toggle search fields based on type
    $(document).on('change','select[name=log_book_search_type]',function(){
        var val = $(this).val();
        if(val == 'log_title'){
            $('input[name=log_book_title_search]').closest('.srch-field').show();
            $('input[name=log_book_date_search]').closest('.srch-field').hide();
        } else {
            $('input[name=log_book_title_search]').closest('.srch-field').hide();
            $('input[name=log_book_date_search]').closest('.srch-field').show();
        }
    });
</script>

<script type="text/javascript">
    // Acknowledge handover handler
    $(document).on('click','.acknowledge-handover-btn',function(){
        var btn = $(this);
        var handoverId = btn.data('handover-id');
        var token = $('input[name=_token]').first().val();

        btn.prop('disabled', true).text('Acknowledging...');

        $.ajax({
            type : 'post',
            url  : "{{ url('/handover/acknowledge') }}",
            data : {'handover_log_book_id': handoverId, '_token': token},
            success: function(resp){
                if (isAuthenticated(resp) == false){
                    btn.prop('disabled', false).text('Acknowledge');
                    return false;
                }
                if(resp == '1'){
                    btn.closest('.pull-center').find('.label-warning').removeClass('label-warning').addClass('label-success').text('Acknowledged');
                    btn.remove();
                    $('span.popup_success_txt').text('Handover acknowledged');
                    $('.popup_success').show();
                    setTimeout(function(){$(".popup_success").fadeOut()}, 5000);
                } else {
                    btn.prop('disabled', false).text('Acknowledge');
                    $('span.popup_error_txt').text('Failed to acknowledge');
                    $('.popup_error').show();
                    setTimeout(function(){$(".popup_error").fadeOut()}, 5000);
                }
            },
            error: function(){
                btn.prop('disabled', false).text('Acknowledge');
                $('span.popup_error_txt').text('An error occurred. Please try again.');
                $('.popup_error').show();
                setTimeout(function(){$(".popup_error").fadeOut()}, 5000);
            }
        });
    });
</script>


