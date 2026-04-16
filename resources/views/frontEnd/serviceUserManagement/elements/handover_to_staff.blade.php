<!-- Log Book Modal -->
<div class="modal fade" id="StaffUserlogBookModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <a class="close view-all-logs mdl-back-btn" href="" data-toggle="modal" data-dismiss="modal" data-target="">
                    <i class="fa fa-arrow-left" title=""></i>
                </a>
                <h4 class="modal-title"> Handover to Staff User</h4>
            </div>
            <div class="modal-body" >
                <div class="row">
                    <div class="add-new-box risk-tabs custm-tabs">
                        <!-- <form method="post" action="" id="staff-user-add-log"> -->
                            <div class="form-group col-md-12 col-sm-12 col-xs-12 p-0 add-rcrd staff-user-list">
                                
                                
                                
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
                                <input type="hidden" name="log_id" value="">
                                <input type="hidden" name="servc_use_id" value="">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <button class="btn btn-warning submt-staff-user" type="submit"> Submit </button>
                            </div>
                        <!-- </form> -->

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $('.log_book').click(function(){ //alert(1);
        $('#logBookModal').modal('show');
    });

    $('.submt-staff-user').click(function(){

        var log_id          = $('input[name=\'log_id\']').val();
        var staff_user_id   = $('select[name=\'staff_id\']').val();
        var servc_use_id    = $('input[name=\'servc_use_id\']').val();
        // console.log(servc_use_id);
        // var category_id = $('select[name=\'category_id\']').val();
        var token  =  $('input[name=\'_token\']').val();

        error = 0;
        if(staff_user_id =='') {

            $('select[name=\'staff_id\']').parent().addClass('red_border');
            error = 1;
        } else {
            $('select[name=\'staff_id\']').parent().removeClass('red_border');
        }

        if(servc_use_id =='') {
            $('select[name=\'servc_use_id\']').parent().addClass('red_border');
            error = 1;
        }   else {
            $('select[name=\'servc_use_id\']').parent().removeClass('red_border');
        }

        if(error == 1) {
            return false;
        }

        // $('.loader').show();
        // $('body').addClass('body-overflow');

        $.ajax({
            type :  'post',
            url  :  "{{ url('/handover/service/log') }}",
            data :  {'log_id':log_id, 'staff_user_id':staff_user_id, 'servc_use_id':servc_use_id, '_token':token },
            //dataType : 'json',
            success: function(resp){

                if (isAuthenticated(resp) == false){
                    return false;
                }
                if (resp == '0') {
                    $('span.popup_error_txt').text('Error Occured');
                    $('.popup_error').show();

                } else if(resp == '1') {
                    $('span.popup_success_txt').text('Log has been handovered to staff member successfully');
                    $('.popup_success').show();
                    setTimeout(function(){$('.popup_success').fadeOut()},5000);
                    $('.logged-btn').click();

                }   else {
                    $('span.popup_error_txt').text('Log is already handovered to this staff member');
                    $('.popup_error').show();
                    setTimeout(function(){$('.popup_error').fadeOut()},5000);
                }
                $('#StaffUserlogBookModal').modal('hide');
                $('#logBookModal').modal('show');

                $('.loader').hide();
                $('body').addClass('body-overflow');
            },
            error: function(){
                $('.loader').hide();
                $('span.popup_error_txt').text('An error occurred. Please try again.');
                $('.popup_error').show();
                setTimeout(function(){$('.popup_error').fadeOut()},5000);
            }
        });
    });

// Modal Back Button
    $('.view-all-logs').click(function(){
        $('#logBookModal').modal('show');
        $('.logged-btn').click();
    });
</script>

<script >
//select options of YPs
    $(".select-field").select2({
        dropdownParent: $('#StaffUserlogBookModal'),
        placeholder: "Select Option"
    });
</script>