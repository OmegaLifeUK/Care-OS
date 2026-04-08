<!--notification start-->
<?php
$home_id = Auth::user()->home_id;

if(isset($manager_profile)){
    $notif_limit = 4; //is it is staff personal profile page
} else{
    $notif_limit = 10;
}

$notifications = App\Notification::getsuNotification('','','',$notif_limit, $home_id);  ?>
<section class="panel">
    <header class="panel-heading">
        Notification
    </header>
    <div class="panel-body">
        {!! $notifications !!}
        <!-- <div class="alert alert-info clearfix">
            <span class="alert-icon"><i class="fa fa-envelope-o"></i></span>
            <div class="notification-info">
                <ul class="clearfix notification-meta">
                    <li class="pull-left notification-sender"><span><a href="#">Steven Hall</a></span></li>
                    <li class="pull-right notification-time">1 min ago</li>
                </ul>
                <p>
                    Needs to be contacted by a member of staff
                </p>
            </div>
        </div>
        <div class="alert alert-danger">
            <span class="alert-icon"><i class="fa fa-facebook"></i></span>
            <div class="notification-info">
                <ul class="clearfix notification-meta">
                    <li class="pull-left notification-sender"><span><a href="#">Jeremy Fisher</a></span></li>
                    <li class="pull-right notification-time">7 Hours Ago</li>
                </ul>
                <p>
                    Placement Plan 6 days overdue!
                </p>
            </div>
        </div>
        <div class="alert alert-success ">
            <span class="alert-icon"><i class="fa fa-comments-o"></i></span>
            <div class="notification-info">
                <ul class="clearfix notification-meta">
                    <li class="pull-left notification-sender"><a href="#">Lewis Danes</a></li>
                    <li class="pull-right notification-time">1 Day ago</li>
                </ul>
                <p> Placement Plan is complete.</p>
            </div>
        </div>
        <div class="alert alert-warning ">
            <span class="alert-icon"><i class="fa fa-bell-o"></i></span>
            <div class="notification-info">
                <ul class="clearfix notification-meta">
                    <li class="pull-left notification-sender">Jeremy Fisher</li>
                    <li class="pull-right notification-time">1 Day Ago</li>
                </ul>
                <p>
                    Health record review date coming up
                </p>
            </div>
        </div>
        <div class="alert alert-success ">
            <span class="alert-icon"><i class="fa fa-comments-o"></i></span>
            <div class="notification-info">
                <ul class="clearfix notification-meta">
                    <li class="pull-left notification-sender"><a href="#">Steven Hall</a></li>
                    <li class="pull-right notification-time">1 Day ago</li>
                </ul>
                <p> Daily record all upto date</p>
            </div>
        </div>
        <div class="alert alert-danger">
            <span class="alert-icon"><i class="fa fa-facebook"></i></span>
            <div class="notification-info">
                <ul class="clearfix notification-meta">
                    <li class="pull-left notification-sender"><span><a href="#">Steven Hall</a></span></li>
                    <li class="pull-right notification-time">2 Days Ago</li>
                </ul>
                <p>
                    Earning Scheme review overdue
                </p>
            </div>
        </div>
        <div class="alert alert-success ">
            <span class="alert-icon"><i class="fa fa-comments-o"></i></span>
            <div class="notification-info">
                <ul class="clearfix notification-meta">
                    <li class="pull-left notification-sender"><a href="#">Lewis Danes</a></li>
                    <li class="pull-right notification-time">2 Days Ago</li>
                </ul>
                <p> Placement Plan is complete.</p>
            </div>
        </div>
        <div class="alert alert-warning ">
            <span class="alert-icon"><i class="fa fa-bell-o"></i></span>
            <div class="notification-info">
                <ul class="clearfix notification-meta">
                    <li class="pull-left notification-sender">Jeremy Fisher</li>
                    <li class="pull-right notification-time">3 Days Ago</li>
                </ul>
                <p>
                    Health record review date coming up
                </p>
            </div>
        </div>
        <div class="alert alert-success ">
            <span class="alert-icon"><i class="fa fa-comments-o"></i></span>
            <div class="notification-info">
                <ul class="clearfix notification-meta">
                    <li class="pull-left notification-sender"><a href="#">Steven Hall</a></li>
                    <li class="pull-right notification-time">4 Days Ago</li>
                </ul>
                <p> Daily record all upto date</p>
            </div>
        </div> -->
    </div>
</section>

@if($notifications != '<div class="text-center">No Notifications Found.</div>')
<div class="col-md-12 col-12 col-xs-12 view-more-noti-su-mng text-right">
    <a href="javascript:void(0)">View More</a>
</div>
@endif
<!--notification end-->



<!-- View more notification Modal -->
<div class="modal fade" id="SuMngViewMoreNotificationModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content notify">
            <div class="modal-header notify">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Notifications</h4>
            </div>
            <div class="modal-body notify m-b-10">
                <div class="notify-sec" >
                    <form class="form-horizontal" id="notif_filter_form_mng">
                        <div class="col-md-5 col-sm-5 col-xs-12">
                            <div class="form-group">
                                <label class="col-md-3 col-sm-3 control-label">From</label>
                                <div class="input-group">
                                <input type="text" name="start_date" readonly="" class="form-control startdate_picker1" placeholder="Start date" maxlength="10">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5 col-sm-5 col-xs-12">
                            <div class="form-group">
                                <label class="col-md-3 col-sm-3 control-label">To</label>
                                <div class="input-group">
                                <input type="text" name="end_date" readonly="" class="form-control enddate_picker1"  placeholder="End date" maxlength="10">
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="service_user_id" value="">
                        <div class="col-md-2 col-sm-2 col-xs-12">
                            <div class="form-group">
                                <button class="btn btn-primary filter_notif_btn"><i class="fa fa-search"></i></button>
                            </div>
                        </div>
                    </form>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="below-divider">
                        </div>
                    </div>
                    <div class="notify-panels" >
                        <div class="notifiScroller">
                            <!-- dynamic notifications will be shown here -->
                        </div>
                    </div>
                </div>
            </div>
            <!-- <div class="modal-footer notify">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div> -->
        </div>
    </div>
</div>
<!-- View more notification Modal end -->

<script>
    var today  = new Date;

    $('.startdate_picker1').datetimepicker({
        format: 'dd-mm-yyyy',
        endDate: today,
        minView : 2
    });

    $('.enddate_picker1').datetimepicker({
        format: 'dd-mm-yyyy',
        endDate: today,
        minView : 2
    });

    $('.startdate_picker1').on('click', function(){
        $('.startdate_picker1').datetimepicker('show');
    });

    $('.startdate_picker1').on('change', function(){
        $('.startdate_picker1').datetimepicker('hide');
    });

    $('.enddate_picker1').on('click', function(){
        $('.enddate_picker1').datetimepicker('show');
    });

    $('.enddate_picker1').on('change', function(){
        $('.enddate_picker1').datetimepicker('hide');
    });
</script>

<script>
    $('.view-more-noti-su-mng a').on('click',function(){

        var service_user_id = "";
        $('.loader').show();
        $('body').addClass('body-overflow');

        $.ajax({
            url : "{{ url('/service/notifications/') }}",
            type: "post",
            data: { 'service_user_id' : service_user_id },
            success:function(resp){

                $(".notifiScroller").html(resp);
                $('#notif_filter_form_mng')[0].reset();
                $('#SuMngViewMoreNotificationModal').modal('show');

                $('.loader').hide();
                $('body').removeClass('body-overflow');

            }
        });
    });

    $(document).on('click','.filter_notif_btn',function(){

        var error = 0;
        var start_date = $('input[name=\'start_date\']').val();
        var end_date   = $('input[name=\'end_date\']').val();
        if(start_date == '') {
            $('input[name=\'start_date\']').addClass('red_border');
            error = 1;
        } else {
            $('input[name=\'start_date\']').removeClass('red_border');
        }
        if(end_date == '') {
            $('input[name=\'end_date\']').addClass('red_border');
            error = 1;
        } else {
            $('input[name=\'end_date\']').removeClass('red_border');
        }
        if(error == 1) {
            return false;
        }


        var formdata = $('#notif_filter_form_mng').serialize();

        $('.loader').show();
        $('body').addClass('body-overflow');

        $.ajax({
            url : "{{ url('/service/notifications/') }}",
            type: "post",
            data: formdata,
            success:function(resp){

                $(".notifiScroller").html(resp);
                $('#SuMngViewMoreNotificationModal').modal('show');

                $('.loader').hide();
                $('body').removeClass('body-overflow');
            }
        });
        return false;
    });
</script>

<script>
    //notification scroller
    $(".notifiScroller").slimScroll({height:'348px'});
</script>
<!-- {height:'448px'}  -->
