<!-- Task allocation  model-->
<div class="modal fade" id="taskAllocationModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close rmp-modal-close" data-dismiss="modal"
                    aria-hidden="true">&times;</button>
                <h4 class="modal-title"> Task Allocation </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group col-md-12 col-sm-12 col-xs-12 serch-btns text-right">
                        <button class="btn label-default add-new-btn active" type="button"> Add New </button>
                        <button class="btn label-default logged-btn active task-allocation-logged-btn" type="button">
                            Logged Tasks </button>
                        <button class="btn label-default search-btn active" type="button"> Search </button>
                    </div>


                    <!-- Add new Details -->
                    <div class="add-new-box risk-tabs custm-tabs">
                        <form method="post" action="">
                            <div class="form-group col-md-12 col-sm-12 col-xs-12 p-0 add-rcrd">
                                <label class="col-md-1 col-sm-1 p-t-7"> Add: </label>
                                <div class="col-md-9 col-sm-9 col-xs-12 p-0 r-p-15">
                                    <input type="text" class="form-control" name="task_title">
                                    <p class="help-block"> Enter the task and click plus to add. </p>
                                </div>
                                <div class="col-md-1 col-sm-1 col-xs-12 p-0 r-p-15">
                                    <div class="col-md-1 col-sm-1 col-xs-1 p-0">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <button class="btn group-ico save-task-record-btn" type="submit">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <form method="post" action="" id="edit-today-task">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="below-divider"></div>
                                @include('frontEnd.common.popup_alert_messages')
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <h3 class="m-t-0 m-b-20 clr-blue fnt-20"> Recent Tasks </h3>
                                    <div class="task_allocation_today_rec">
                                        <!-- Today task list be shown here using ajax -->
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer m-t-0 m-b-15 modal-bttm">
                                <a class="bottm-btns" href="{{ url('/system/calendar') }}">
                                    <div class="pull-left"><i class="fa fa-calendar"></i></div>
                                </a>
                                <input type="hidden" name="staff_member_id" value="{{ $staff_id }}">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <button class="btn btn-default rmp-modal-close" type="button" data-dismiss="modal"
                                    aria-hidden="true"> Cancel </button>
                                <button class="btn btn-warning sbt_edit_today_task" type="submit"> Confirm </button>
                            </div>
                        </form>
                    </div>

                    <!-- logged task -->
                    <div class="logged-box risk-tabs custm-tabs">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <h3 class="m-t-0 m-b-20 clr-blue fnt-20"> Logged Task </h3>
                        </div>
                        @include('frontEnd.common.popup_alert_messages')
                        <form method="post" id="edit-log-detail-form">
                            <div class="modal-space modal-pading log-task-alloc-record">
                                <!--previous task logged list be shown here using ajax -->

                            </div>
                        </form>
                        <div class="modal-footer m-t-0 recent-task-sec">
                            <input type="hidden" name="staff_member_id" value="{{ $staff_id }}">
                            <button class="btn btn-default" type="button" data-dismiss="modal" aria-hidden="true">
                                Cancel </button>
                            <button class="btn btn-warning sbt_edit_log_task" type="submit"> Confirm</button>
                        </div>
                    </div>

                    <!-- Search  -->
                    <div class="search-box risk-tabs custm-tabs">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <h3 class="m-t-0 m-b-20 clr-blue fnt-20">Search</h3>
                        </div>
                        <!-- alert messages -->
                        @include('frontEnd.common.popup_alert_messages')
                        <div class="col-md-12 col-sm-12 col-xs-12 p-0 type-field">
                            <label class="col-md-2 col-sm-2 col-xs-12 p-t-7 cus-lbl"> Type: </label>
                            <div class="col-md-9 col-sm-9 col-xs-12 p-0 m-b-15">
                                <div class="select-style">
                                    <select name="sm_search_type">
                                        <option value='title' <?php echo 'selected'; ?>> Title </option>
                                        <option value='date'> Date </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 col-sm-12 col-xs-12 p-0 srch-field">
                            <label class="col-md-2 col-sm-2 col-xs-12 p-t-7 cus-lbl"> Title: </label>
                            <div class="col-md-9 col-sm-9 col-xs-12 p-0 m-b-15 title">
                                <input type="text" name="search_task_record" class="form-control" maxlength="255">
                            </div>
                        </div>
                        <div class="col-md-12 col-sm-12 col-xs-12 p-0 add-rcrd srch-field">
                            <label class="col-md-2 col-sm-2 col-xs-12 p-t-7 cus-lbl"> Date: </label>
                            <div class="col-md-9 col-sm-9 col-xs-12 p-0 m-b-15">
                                <div data-date-viewmode="" data-date-format="dd-mm-yyyy" data-date=""
                                    class="input-group date dpYears">
                                    <input name="sm_date" type="text" value="" size="45"
                                        class="form-control" readonly="" maxlength="10" id="task-date-staff">
                                    <span class="input-group-btn add-on">
                                        <button class="btn clndr btn-primary" type="button"><i
                                                class="fa fa-calendar"></i></button>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <form id="searched-task-alloc-records-form" method="post">
                            <div class="modal-space modal-pading searched-records text-center">
                            </div>
                        </form>
                        <div class="modal-footer m-t-0 recent-task-sec">
                            <button class="btn btn-default" type="button" data-dismiss="modal" aria-hidden="true">
                                Cancel </button>
                            <button class="btn btn-warning search-task-alloc-btn" type="submit"> Confirm</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--Task allocation model End -->

<script>
    //add new task while click on plus
    $(document).ready(function() {
        $(document).on('click', '.save-task-record-btn', function() {
            //alert(1); return false;
            var error = 0;
            var staff_id = $('.selected_staff_id').val();
            if (staff_id == undefined) {
                staff_id = "{{ $staff_id }}";
            }

            var task_title = $('input[name=\'task_title\']').val();
            task_title = jQuery.trim(task_title);
            //alert(task_title); return false;
            if (task_title == '' || task_title == null) {
                $('input[name=\'task_title\']').addClass('red_border');
                error = 1;
            } else {
                $('input[name=\'task_title\']').removeClass('red_border');
            }
            if (error == 1) {
                return false;
            }

            $('.loader').show();
            $('body').addClass('body-overflow');

            $.ajax({
                type: 'post',
                url: "{{ url('/staff/member/task-allocation/add') }}",
                data: {
                    'task_title': task_title,
                    'staff_member_id': staff_id
                },
                success: function(resp) {
                    if (isAuthenticated(resp) == false) {
                        return false;
                    }
                    //alert(resp); return false;
                    if (resp == '0') {
                        $('span.popup_error_txt').text('Error Occured');
                        $('.popup_error').show();
                    } else {

                        $('.task_allocation_today_rec').html(resp);
                        $('input[name=\'task_title\']').val('');
                        //show success message
                        $('span.popup_success_txt').text(' Task Added Successsfully');
                        $('.popup_success').show();
                        setTimeout(function() {
                            $(".popup_success").fadeOut()
                        }, 5000);
                    }
                    $('.loader').hide();
                    $('body').removeClass('body-overflow');
                }
            });
            return false;
        });
    });
</script>

<script>
    //logged btn click task show
    $(document).ready(function() {
        $(document).on('click', '.task-allocation-logged-btn', function() {
            var staff_id = $('.selected_staff_id').val();
            if (staff_id == undefined) {
                staff_id = "{{ $staff_id }}";
            }

            $('.loader').show();
            $('body').addClass('body-overflow');

            $.ajax({
                type: 'get',
                url: "{{ url('/staff/member/task-allocation/view/') }}" + '/' + staff_id +
                    '?logged',
                success: function(resp) {
                    if (isAuthenticated(resp) == false) {
                        return false;
                    }
                    if (resp == '') {
                        $('.log-task-alloc-record').html(
                            '<div class="text-center p-b-20" style="width:100%"> No Records found. </div>'
                            );
                    } else {
                        $('.log-task-alloc-record').html(resp);
                    }

                    $('.loader').hide();
                    $('body').removeClass('body-overflow');
                }
            });
            return false;
        });
    });
</script>

<script>
    //today task record show click on modal open
    $(document).ready(function() {
        $(document).on('click', '.task-allocation-list', function() {

            var staff_id = $('.selected_staff_id').val();
            if (staff_id == undefined) {
                staff_id = "{{ $staff_id }}";
            }

            $('.loader').show();
            $('body').addClass('body-overflow');

            $.ajax({
                type: 'get',
                url: "{{ url('/staff/member/task-allocation/view/') }}" + '/' + staff_id,
                success: function(resp2) {
                    if (isAuthenticated(resp2) == false) {
                        return false;
                    }
                    $('.task_allocation_today_rec').html(resp2);
                    $('#taskAllocationModal').modal('show');
                    $('.add-new-btn').click();

                    $('.loader').hide();
                    $('body').removeClass('body-overflow');
                }
            });
            return false;
        });
    });
</script>

<script>
    //delete a task
    $(document).ready(function() {
        $(document).on('click', '.remove-task-record', function() {
            if (!confirm('{{ DEL_CONFIRM }}')) {
                return false;
            }
            var task_id = $(this).attr('staff_m_task_id');

            $('.loader').show();
            $('body').addClass('body-overflow');

            var this_record = $(this);
            $.ajax({
                type: 'post',
                url: "{{ url('/staff/member/task-allocation/delete/') }}" + '/' + task_id,
                success: function(resp) {
                    if (isAuthenticated(resp) == false) {
                        return false;
                    }
                    if (resp == 1) {
                        this_record.closest('.delete-task-row').remove();

                        //show success delete message
                        $('span.popup_success_txt').text('Task Deleted Successfully');
                        $('.popup_success').show();
                        setTimeout(function() {
                            $(".popup_success").fadeOut()
                        }, 5000);
                    } else {
                        //show delete message error
                        $('span.popup_error_txt').text('Error Occured');
                        $('.popup_error').show();
                    }
                    $('.loader').hide();
                    $('body').removeClass('body-overflow');


                }
            });
            return false;
        });
    });
</script>

<script>
    $(document).ready(function() {

        //edit option => making editable
        $(document).on('click', '.edit-task-detail', function() {

            var edit_btn = $(this);
            var staff_m_task_id = $(this).attr('staff_m_task_id');

            $('.edit_task_detail_' + staff_m_task_id).removeAttr('disabled');
            $(this).closest('.cog-panel').find('.input-plusbox').toggle();
            $('.edit_task_id_' + staff_m_task_id).removeAttr('disabled');
            $(this).closest('.pop-notifbox').removeClass('active');
            autosize($("textarea"));
            return false;
        });


        //detail option
        $(document).on('click', '.task-detail', function() {


            $(this).closest('.cog-panel').find('.input-plusbox').toggle();
            $(this).closest('.pop-notifbox').removeClass('active');
            autosize($("textarea"));
            return false;
        });

    });
</script>


<script>
    $(document).ready(function() {

        // submit today edit details
        $(document).on('click', '.sbt_edit_today_task', function() {

            //validating the input fields
            var validate_res = validate_edit_task('.task_allocation_today_rec');

            //if any field is empty or if no field is editable then don't call ajax request
            if ((validate_res['err'] == 1) || (validate_res['enabled'] == 0)) {
                return false;
            }

            var formdata = $('#edit-today-task').serialize();

            $('.loader').show();
            $('body').addClass('body-overflow');

            $.ajax({
                type: 'post',
                url: "{{ url('/staff/member/task-allocation/edit') }}",
                data: formdata,
                success: function(resp) {

                    if (isAuthenticated(resp) == false) {
                        return false;
                    }
                    $('.task_allocation_today_rec').html(resp);

                    //loader
                    $('.loader').hide();
                    $('body').removeClass('body-overflow');

                    $('span.popup_success_txt').text('Task Detail Updated Successfully');
                    $('.popup_success').show();
                    setTimeout(function() {
                        $(".popup_success").fadeOut()
                    }, 5000);
                }
            });
            return false;

        });

        //submit logged edit details
        $(document).on('click', '.sbt_edit_log_task', function() {

            //validating input fields
            var validate_res = validate_edit_task('.log-task-alloc-record');

            //if any field is empty or if no field is editable  then don't call ajax request
            if ((validate_res['err'] == 1 || (validate_res['enabled'] == 0))) {
                return false;
            }
            var formdata = $('#edit-log-detail-form').serialize();

            $('.loader').show();
            $('body').addClass('body-overflow');

            $.ajax({
                type: 'post',
                url: "{{ url('/staff/member/task-allocation/edit?logged') }}",
                data: formdata,
                success: function(resp) {

                    if (isAuthenticated(resp) == false) {
                        return false;
                    }
                    $('.log-task-alloc-record').html(resp);

                    //loader
                    $('.loader').hide();
                    $('body').removeClass('body-overflow');

                    $('span.popup_success_txt').text('Task Detail Updated Successfully');
                    $('.popup_success').show();
                    setTimeout(function() {
                        $(".popup_success").fadeOut()
                    }, 5000);
                }
            });
            return false;

        });

        function validate_edit_task(parent_div_class) {

            var response = [];
            response['err'] = 0;
            response['enabled'] = 0;

            $(parent_div_class + ' .edit_task_record').each(function(index) {

                var disabled_attr = $(this).attr('disabled');
                // alert(disabled_attr); return false;
                if (disabled_attr == undefined) {
                    // alert('enterd'); return false;

                    var desc = $(this).val();
                    desc = jQuery.trim(desc);

                    if (desc == '' || desc == '0') {
                        $(this).addClass('red_border');
                        response['err'] = 1;
                    } else {
                        $(this).removeClass('red_border');
                    }
                    response['enabled'] = 1;
                }
            });
            return response;
        }
    });
</script>

<script>
    $('#taskAllocationModal').on('scroll', function() {
        $('.dpYears').datepicker('place')
    });
    //3rd tab search
    $('input[name=\'sm_date\']').closest('.srch-field').hide();
    $(document).ready(function() {

        $('input[name=\'search_task_record\']').keydown(function(event) {
            var keyCode = (event.keyCode ? event.keyCode : event.which);
            if (keyCode == 13) {
                $('.search-task-alloc-btn').click();
                return false;
            }
        });

        $('select[name=\'sm_search_type\']').change(function() {
            $('.searched-records').html('');
            var sm_src_title = $('input[name=\'search_task_record\']');
            var sm_src_date = $('input[name=\'sm_date\']');

            var type = $(this).val();
            if (type == 'date') {

                sm_src_date.closest('.srch-field').show();
                sm_src_date.removeClass('red_border');
                sm_src_title.closest('.srch-field').hide();
            } else {
                sm_src_title.closest('.srch-field').show();
                sm_src_title.removeClass('red_border');
                sm_src_date.closest('.srch-field').hide();
            }
        });

        $(document).on('click', '.search-task-alloc-btn', function() {

            var sm_search_type = $('select[name=\'sm_search_type\']');
            var search_input = $('input[name=\'search_task_record\']');
            var sm_search_date = $('input[name=\'sm_date\']');

            var search = search_input.val();
            //alert(search);
            var sm_date = sm_search_date.val();
            //alert(sm_date);
            var sm_search_type = sm_search_type.val();
            //alert(sm_search_type); return false;
            search = jQuery.trim(search);
            search = search.replace(/[&\/\\#,+()$~%.'":*?<>^@{}]/g, '');

            if (sm_search_type == 'title') {
                if (search == '') {

                    search_input.addClass('red_border');
                    return false;
                } else {
                    search_input.removeClass('red_border');
                }
            } else {
                if (sm_date == '') {

                    sm_search_date.addClass('red_border');
                    return false;
                } else {
                    sm_search_date.removeClass('red_border');
                }

            }
            var error = 0;
            $('.searched-records .edit_t_rcrd').each(function(index) {
                var is_disable = $(this).attr('disabled');
                if (is_disable == undefined) { //if it is not disabled
                    var title = $(this).val();
                    title = jQuery.trim(title);

                    if (title == '' || title == '0') {
                        $(this).addClass('red_border');
                        error = 1;
                    } else {
                        $(this).removeClass('red_border');
                    }
                    //enabled = 1;
                }
            });
            if (error == 1) {
                return false;
            }
            var formdata = $('#searched-task-alloc-records-form').serialize();

            var staff_id = $('.selected_staff_id').val();
            if (staff_id == undefined) {
                staff_id = "{{ $staff_id }}";
            }

            $('.loader').show();
            $('body').addClass('body-overflow');
            $.ajax({
                type: 'post',
                url: "{{ url('/staff/member/task-allocation/view/') }}" + '/' + staff_id +
                    '?search=' + search + '&sm_date=' + sm_date + '&sm_search_type=' +
                    sm_search_type,
                data: formdata,
                success: function(resp) {

                    if (isAuthenticated(resp) == false) {
                        return false;
                    }
                    // alert(resp); return false;
                    if (resp == '') {
                        $('.searched-records').html('No Records found.');
                    } else {
                        $('.searched-records').html(resp);
                    }
                    $('.loader').hide();
                    $('body').removeClass('body-overflow');
                }
            });
            return false;
        });
    });
</script>

<script>
    //status change btn
    $(document).ready(function() {
        $(document).on('click', '.task_status_change', function() {

            var task_id = $(this).attr('staff_m_task_id');
            $(this).addClass('active_record');
            var row = $(this);

            $('.loader').show();
            $('body').addClass('body-overflow');

            $.ajax({
                type: 'post',
                url: "{{ url('/staff/member/task-allocation/status-update/') }}" + '/' +
                    task_id,
                success: function(resp) {
                    if (isAuthenticated(resp) == false) {
                        return false;
                    }

                    if (resp == true) {

                        if ($('.active_record').closest('span.settings').hasClass(
                                'clr-blue')) {
                            $('.active_record').closest('span.settings').removeClass(
                                'clr-blue');
                            $('.active_record').closest('span.settings').addClass(
                                'clr-grey');

                        } else {
                            $('.active_record').closest('span.settings').removeClass(
                                'clr-grey');
                            $('.active_record').closest('span.settings').addClass(
                                'clr-blue');
                        }

                        $('span.popup_success_txt').text('Status Changed');
                        $('.popup_success').show();
                        setTimeout(function() {
                            $(".popup_success").fadeOut()
                        }, 5000);
                    } else {
                        $('span.popup_error_txt').text('Error Occured');
                        $('.popup_error').show();
                    }
                    $('.active_record').removeClass('active_record');
                    $('.pop-notifbox').removeClass('active');

                    $('.loader').hide();
                    $('body').removeClass('body-overflow');
                }
            });
            return false;

        });
        $(document).on('click', '.task_is_completed', function() {

            var task_id = $(this).attr('staff_m_task_id');
            $(this).addClass('active_record');
            var row = $(this);

            $('.loader').show();
            $('body').addClass('body-overflow');

            $.ajax({
                type: 'post',
                url: "{{ url('/staff/member/task-allocation/complete-status-update/') }}" +
                    '/' + task_id,
                success: function(resp) {
                    if (isAuthenticated(resp) == false) {
                        return false;
                    }

                    if (resp == true) {

                        if ($('.active_record').closest('span.settings').hasClass(
                                'clr-blue')) {
                            $('.active_record').closest('span.settings').removeClass(
                                'clr-blue');
                            $('.active_record').closest('span.settings').addClass(
                                'clr-grey');

                        } else {
                            $('.active_record').closest('span.settings').removeClass(
                                'clr-grey');
                            $('.active_record').closest('span.settings').addClass(
                                'clr-blue');
                        }

                        $('span.popup_success_txt').text('Activity Marked to Completed');
                        $('.popup_success').show();
                        setTimeout(function() {
                            $(".popup_success").fadeOut()
                        }, 5000);
                    } else {
                        $('span.popup_error_txt').text('Error Occured');
                        $('.popup_error').show();
                    }
                    $('.active_record').removeClass('active_record');
                    $('.pop-notifbox').removeClass('active');

                    $('.loader').hide();
                    $('body').removeClass('body-overflow');
                }
            });
            return false;

        });
    });
</script>


<script>
    //pagination
    $(document).on('click', '#taskAllocationModal .pagination li', function() {
        // var new_url = $(this).children('a').attr('href');
        // if(new_url == undefined){
        //     return false;
        // }
        var page_no = $(this).children('a').text();
        if (page_no == '') {
            return false;
        }
        if (isNaN(page_no)) {
            var new_url = $(this).children('a').attr('href');
            page_no = new_url[new_url.length - 1];
        }

        var staff_id = $('.selected_staff_id').val();
        if (staff_id == undefined) {
            staff_id = "{{ $staff_id }}";
        }

        $('.loader').show();
        $('body').addClass('body-overflow');

        $.ajax({
            type: 'get',
            // url  : new_url+'&logged',
            url: "{{ url('/staff/member/task-allocation/view/') }}" + '/' + staff_id + "?page=" +
                page_no + '&logged',
            success: function(resp) {
                if (isAuthenticated(resp) == false) {
                    return false;
                }

                $('.log-task-alloc-record').html(resp);
                $('.loader').hide();
                $('body').removeClass('body-overflow');
            }
        });
        return false;
    });
</script>


<!-- head script of show date  -->
<script>
    //same for all heads
    $(document).ready(function() {
        $(document).on('click', '.daily-rcd-head', function() {
            $(this).next('.daily-rcd-content').slideToggle();
            $(this).find('i').toggleClass('fa-angle-down');
            $('.input-plusbox').hide();
        });
    });
</script>
