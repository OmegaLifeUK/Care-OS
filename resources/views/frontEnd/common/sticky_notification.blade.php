<link href="{{ url('public/frontEnd/js/gritter/jquery.gritter.css') }}" rel="stylesheet">
<script src="{{ url('public/frontEnd/js/gritter/gritter.js') }}"></script>
<script src="{{ url('public/frontEnd/js/gritter/jquery.gritter.min.js') }}"></script>

<!-- danger notifications -->
<?php
$notifications = App\Notification::getStickyNotifications();
// echo '<pre>'; print_r($notifications); die;
?>


<!-- Showing alert dynamic form notification -->
<script>
    <?php
        $alert_dynamic_form = App\DynamicForm::alertDynamicForm();
        // echo "<pre>"; print_r($alert_dynamic_form); die;
        foreach($alert_dynamic_form as $value) {

            if( (empty($value->id))){   ?>

    var d_title = '{{ $value['title'] }}';
    var su_name = '{{ ucfirst($value['name']) }}';

    var unique_id = $.gritter.add({
        title: 'Dynamic form reminder',
        text: su_name + "'s Dynamic form '" + d_title + "' reminder.",
        sticky: true,
        // (int | optional) the time you want it to be alive for before fading out
        //time: '',
        // (string | optional) the class name you want to apply to that specific message
        class_name: 'my-sticky-class'
    });
    <?php  } } ?>
</script>
<!-- Showing alert dynamic form notification end -->

<script type="text/javascript">
    <?php
    foreach ($notifications as $key => $value) {

        $notification_id = $value['id'];
        $event_type_id   = $value['notification_event_type_id'];
        $event_action    = $value['event_action'];
        $event_id        = $value['event_id'];
        $service_user_id = $value['service_user_id'];

        //don't show that notifications who are already approved
        $individual_ack  = $value['sticky_individual_ack'];
        //converting object into array
        $individual_ack  = json_decode($individual_ack,true);
        $user_id         = Auth::user()->id;
        if(is_array($individual_ack)){
            if(array_key_exists($user_id,$individual_ack)){
                continue;
            }
        }
        $type = '';

        $su_name         = App\ServiceUser::where('id',$value['service_user_id'])->value('name');
        $created_at_date = date('Y-m-d',strtotime($value['created_at']));
        $today_date      = date('Y-m-d');

        if($created_at_date == $today_date) {
            $created_at = ' at '.date('H:i a',strtotime($value['created_at']));
        } else{
            $created_at = ' on '.date('d M Y',strtotime($value['created_at']));
            $created_at .= ' at '.date('H:i a',strtotime($value['created_at']));
        }

        $title = '';
        $message = '';
        if($event_type_id == '14'){ //In danger

            if($event_action == 'ADD'){
                $title   = "In danger";
                $message = " was in danger ".$created_at." and needs help.";
                $type = 'IN_DANGER';
            }
        } else if($event_type_id == '15'){ //REQ_CALLBACK

            if($event_action == 'ADD'){
                $title   = "Callback request";
                $message = " has requested callback ".$created_at.".";
                $type = 'REQ_CALLBACK';
            }
        } else if($event_type_id == '16'){ //NEED_ASSITANCE

            if($event_action == 'ADD'){

                //get to_inform_user_id
                //if($value['to_inform_user_id'] == Auth::user()->id ){
                    $title   = "Need assistance";
                    $message = " needs assistance ".$created_at.".";
                //} else{ //do not show notification if user is not that whom yp want to inform
                    //continue;
                //}
                $type = 'NEED_ASSIT';
            }
        }  else if($event_type_id == '17'){ //location alert

            if($event_action == 'ADD'){

                $su_loc_notif = App\ServiceUserLocationNotification::select('id','location_name','location_type','old_location_type')
                                    ->where('id',$event_id)
                                    ->first();

                if(!empty($su_loc_notif)){
                    $old_loc = $su_loc_notif->old_location_type;
                    $new_loc = $su_loc_notif->location_type;

                    if($old_loc == 'A'){
                        $old_loc_txt = 'allowed';
                    } else if($old_loc == 'R'){
                        $old_loc_txt = 'restricted';
                    } else{
                        $old_loc_txt = 'grey';
                    }

                    if($new_loc == 'A') {
                        $new_loc_txt = 'allowed';
                    } else if($new_loc == 'R'){
                        $new_loc_txt = 'restricted';
                    } else{
                        $new_loc_txt = 'grey';
                    }
                    $title   = "Location alert";
                    $message = " has entered into ".$new_loc_txt." area from the ".$old_loc_txt." area ".$created_at.".";
                    $type = 'LOC_ALERT';
                }
            }
        } else if($event_type_id == '18'){ //su money request

            if($event_action == 'ADD'){

                $amount = App\ServiceUserMoneyRequest::where('id',$event_id)->value('amount');
                $title   = "Money request";
                $message = " has made money request for  € ".$amount." ".$created_at.".";
                $type = 'MONEY_REQ';
            }
        }else if($event_type_id == '21'){ //su mood
            $su_mood_info  = DB::table('su_mood')
                ->select('su_mood.description', 'm.name')
                ->join('mood as m', 'm.id', 'su_mood.mood_id')
                ->where('su_mood.id', $event_id)
                ->first();
                    if(empty($su_mood_info)){
                        continue;
                    }
                    $title   = "Child's Mood";
                    $type = 'SU_MOOD';
            if($event_action == 'ADD'){

                $mood_title =  $su_mood_info->name;
                $message = "A " . $mood_title . " is added."; //" has made money request for  € ".$amount." ".$created_at.".";
            }else{
                  $mood_title =  $su_mood_info->name;
                $message = "A " . $mood_title . " is edited.";
            }
        }else if($event_type_id == '11'){ //su risk
             $risk_change = DB::table('su_risk')->select('su_risk.status', 'r.description', 'r.icon')
                            ->join('risk as r', 'r.id', 'su_risk.risk_id')
                            ->where('su_risk.id', $event_id)
                            ->first();
                        // echo "<pre>"; print_r($risk_title); die;
                            $title = 'Risk';  $type = 'SU_RISK';
                        if (!empty($risk_change)) {
                            if ($risk_change->status == 2) {
                                $risk_type = "Live Risk";
                            } elseif ($risk_change->status == 1) {
                                $risk_type = "Historic Risk";
                            } else {
                                $risk_type = "No Risk";
                            }
                            $risk_description = $risk_change->description;
                            $icon = $risk_change->icon;
                        } else {
                            continue;
                        }
                        $message = "Risk " . $risk_description . " has changed to " . $risk_type;
        }else if($event_type_id == '4'){ //su task
            $task       = App\ServiceUserPlacementPlan::where('id', $event_id)->value('task');
                    if (empty($task)) {
                        continue;
                    }  $title = 'Task';  $type = 'Su Placement Plan';
                    if ($event_action == "ADD") {

                        $message = "A new task '" . $task . "' is added";
                    } else if ($event_action == "MARK_COMPLETE") {

                        $message = "Task '" . $task . "' is completed";
                    } else if ($event_action == "MARK_ACTIVE") {

                        $message = "Task '" . $task . "' is made active";
                    } else {
                        $message = "Task all upto date";
                    }
        }else{
            continue;
        }
    ?>

    var title = "{{ $title }}";
    var unique_id = $.gritter.add({
        title: '<a href="" class="sticky_noti_title" id="{{ $event_id }}" su_id="{{ $service_user_id }}" type="{{ $type }}" >' +
            title + '</a>',
        //text: '{{ $su_name . ' ' . $message }} <div> <button class="btn btn-sm btn-griter-ok danger-conf-btn m-t-15" id={{ $notification_id }}> Ok </button> </div>',
        text: '{{ "'$su_name'" . ' ' . $message }} <div> <button class="btn btn-sm btn-griter-ok indv-conf-btn m-t-5" id="{{ $notification_id }}" > Ok </button> <button class="btn btn-sm btn-griter-ok master-conf-btn m-t-5 m-l-5" id="{{ $notification_id }}" > Remove for all </button> </div>',
        sticky: true,
        class_name: "my-sticky-class"
    });
    <?php } ?>
</script>

<script type="text/javascript">
    $(".indv-conf-btn").on('click', function() {
        var id = $(this).attr('id');
        var click = $(this);
        $.ajax({
            type: 'GET',
            //url: "{{ url('/service/location-history/notif/acnowldg/personal') }}" +"/" + id,
            url: "{{ url('/notification/ack/indiv') }}" + "/" + id,

            success: function(resp) {

                if (isAuthenticated(resp) == false) {
                    return false;
                }

                if (resp == 'true') {
                    click.closest('.gritter-item-wrapper').fadeOut('slow');
                } else {
                    alert('{{ COMMON_ERROR }}');
                }

            }
        })
    });

    $(".master-conf-btn").on('click', function() {
        var id = $(this).attr('id');
        var click = $(this);
        $.ajax({
            type: 'GET',
            //url: "{{ url('/service/location-history/notif/acnowldg/master/') }}" +"/" + id,
            url: "{{ url('/notification/ack/master') }}" + "/" + id,

            success: function(resp) {

                if (isAuthenticated(resp) == false) {
                    return false;
                }

                if (resp == 'true') {
                    click.closest('.gritter-item-wrapper').fadeOut('slow');
                } else {
                    alert('{{ COMMON_ERROR }}');
                }

            }
        })
    });
</script>

<!-- Showing pending risks notification -->

<!-- Showing pending risks notification end -->

<form class="temp_noti_data">
    <input type="hidden" name="event_id" value="">
    <input type="hidden" name="event_type" value="">
    <input type="hidden" name="su_id" value="">
    <input type="hidden" name="back_path" value="{{ Request::fullUrl() }}">
    {{ csrf_field() }}
    <input type="button" name="submit" class="submit" style="display: none">
</form>

<script type="text/javascript">
    //  $(document).ready(function(){
    $('.sticky_noti_title').on('click', function() {
        var noti = $(this);
        var noti_id = noti.attr('id');
        var su_id = noti.attr('su_id');
        var type = noti.attr('type');

        if (type != '') {
            $('.temp_noti_data input[name="event_id"]').val(noti_id);
            $('.temp_noti_data input[name="event_type"]').val(type);
            $('.temp_noti_data input[name="su_id"]').val(su_id);
            // $('.temp_noti_data .submit').click();
            return false;
        } else {
            return false;
        }
    });
    // })
</script>
<script>
    $('.sticky_noti_title').on('click', function(e) {
        e.preventDefault();

        var noti = $(this);
        var noti_id = noti.attr('id');
        var su_id = noti.attr('su_id');
        var type = noti.attr('type');

        if (type != '') {
            var form = $('.temp_noti_data');

            // Hidden fields me value set karo
            form.find('input[name="event_id"]').val(noti_id);
            form.find('input[name="event_type"]').val(type);
            form.find('input[name="su_id"]').val(su_id);

            // AJAX Submit
            $.ajax({
                url: "{{ url('notif/response') }}",
                type: "POST",
                data: form.serialize(),
                success: function(response) {
                    console.log("Success:", response);

                    // Laravel se redirect handle karna ho to:
                    if (response.redirect_url) {
                        window.location.href = response.redirect_url;
                    }
                },
                error: function(xhr) {
                    console.log("Error:", xhr.responseText);
                }
            });
        } else {
            alert("type nhi hai");
        }
    });
</script>
