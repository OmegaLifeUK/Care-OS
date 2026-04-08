<style>
    .font_color {
        color: #1f88b5 !important;
    }
</style>
<!-- Task allocation  model-->
<div class="modal fade" id="myTaskAllocationModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
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
                        <!--  <button class="btn label-default add-new-btn active" type="button"> Add New </button> -->
                        <button class="btn label-default add-new-btn active task-allocation-logged-btn" type="button">
                            Logged Tasks </button> <!-- logged-btn -->
                        <button class="btn label-default search-btn active" type="button"> Search </button>
                    </div>


                    <!-- Add new Details -->
                    <!--  <div class="add-new-box risk-tabs custm-tabs">
                        <form method="post" action="" >
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

                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer m-t-0 m-b-15 modal-bttm">
                                <a class="bottm-btns" href="{{ url('/system/calendar') }}" ><div class="pull-left"><i class="fa fa-calendar"></i></div></a>
                                <input type="hidden" name="staff_member_id" value="">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <button class="btn btn-default rmp-modal-close" type="button" data-dismiss="modal" aria-hidden="true"> Cancel </button>
                                <button class="btn btn-warning sbt_edit_today_task" type="submit"> Confirm </button>
                            </div>
                        </form>
                    </div> -->

                    <!-- logged task -->
                    <div class="add-new-box risk-tabs custm-tabs"> <!-- logged-box -->
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <h3 class="m-t-0 m-b-20 clr-blue fnt-20"> Logged Task </h3>
                        </div>
                        @include('frontEnd.common.popup_alert_messages')
                        <form method="post" id="edit-log-detail-form">
                            <div class="modal-space modal-pading log-my-task-alloc-record">
                                <!--previous task logged list be shown here using ajax -->

                            </div>
                        </form>
                        <div class="modal-footer m-t-0 recent-task-sec" style="visibility: hidden;">
                            <input type="hidden" name="staff_member_id" value="">
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
                            <div class="modal-space modal-pading my-searched-records text-center">
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
    //detail option
    $(document).on('click', '.my-task-detail', function() {


        $(this).closest('.cog-panel').find('.input-plusbox').toggle();
        $(this).closest('.pop-notifbox').removeClass('active');
        autosize($("textarea"));
        return false;
    });
</script>

<script>
    //my task show
    $(document).ready(function() {
        $(document).on('click', '#markedCompletedBtn', function() {

            var manager_id = $(this).data('id');
            $('.loader').show();
            $('body').addClass('body-overflow');

            $.ajax({
                type: 'post ',
                url: "{{ url('staff/member/task-allocation/complete-status-update') }}" + '/' +
                    manager_id,
                success: function(resp) {
                    if (isAuthenticated(resp) == false) {
                        return false;
                    }
                    if (resp) {
                        alert('Activity marked as completed')
                    } else {
                        alert('Something went wrong')

                    }
                    $('.loader').hide();
                    $('body').removeClass('body-overflow');
                }
            });
            return false;
        });
        $(document).on('click', '.my_task_allocation_list', function() {

            var manager_id = $(this).attr('manager_id');

            $('.loader').show();
            $('body').addClass('body-overflow');

            $.ajax({
                type: 'get',
                url: "{{ url('/my-profile/task-allocation/view/') }}" + '/' + manager_id +
                    '?logged',
                success: function(resp) {
                    if (isAuthenticated(resp) == false) {
                        return false;
                    }
                    if (resp == '') {
                        $('.log-my-task-alloc-record').html(
                            '<div class="text-center p-b-20"style="width:100%"> No Records found. </div>'
                        );
                    } else {
                        $('.log-my-task-alloc-record').html(resp);
                    }
                    $('#myTaskAllocationModal').modal('show');
                    $('.loader').hide();
                    $('body').removeClass('body-overflow');
                }
            });
            return false;
        });
    });
</script>

<script>
    $('#myTaskAllocationModal').on('scroll', function() {
        $('.dpYears').datepicker('place')
    });

    //3rd tab search
    $('input[name=\'sm_date\']').closest('.srch-field').hide();
    $(document).ready(function() {

        $('input[name=\'search_task_record\']').keydown(function(event) {
            var keyCode = (event.keyCode ? event.keyCode : event.which);
            if (keyCode == 13) {
                return false;
            }
        });

        $('select[name=\'sm_search_type\']').change(function() {
            $('.my-searched-records').html('');
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
            var sm_date = sm_search_date.val();
            var sm_search_type = sm_search_type.val();

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
            var formdata = $('#searched-task-alloc-records-form').serialize();

            var manager_id = "{{ $manager_id }}";

            $('.loader').show();
            $('body').addClass('body-overflow');
            $.ajax({
                type: 'post',
                url: "{{ url('/my-profile/task-allocation/view/') }}" + '/' + manager_id +
                    '?search=' + search + '&sm_date=' + sm_date + '&sm_search_type=' +
                    sm_search_type,
                data: formdata,
                success: function(resp) {

                    if (isAuthenticated(resp) == false) {
                        return false;
                    }
                    if (resp == '') {
                        $('.my-searched-records').html('No Records found.');
                    } else {
                        $('.my-searched-records').html(resp);
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
    //pagination
    $(document).on('click', '#myTaskAllocationModal .pagination li', function() {

        var page_no = $(this).children('a').text();
        if (page_no == '') {
            return false;
        }
        if (isNaN(page_no)) {
            var new_url = $(this).children('a').attr('href');
            page_no = new_url[new_url.length - 1];
        }

        var manager_id = "{{ $manager_id }}";

        $('.loader').show();
        $('body').addClass('body-overflow');

        $.ajax({
            type: 'get',
            url: "{{ url('/my-profile/task-allocation/view/') }}" + '/' + manager_id + "?page=" +
                page_no + '&logged',
            success: function(resp) {
                if (isAuthenticated(resp) == false) {
                    return false;
                }

                $('.log-my-task-alloc-record').html(resp);
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
