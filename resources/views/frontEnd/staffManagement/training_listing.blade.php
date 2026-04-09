<?php  
    if(isset($_GET['year'])){ 
        $cur_year = $_GET['year']; 
    } else{ 
        $cur_year = date("Y"); 
    } 
 
    $prev_year = $cur_year-1; 
    $forw_year = $cur_year+1; 
?> 
 <style>
      .backIcon i {
        color: #1f88b5;
        font-size: 20px;
    }
    .backIcon {
        text-align: right;
        margin-bottom: 25px;
    }
 </style>
@extends('frontEnd.layouts.master') 
@section('title','Staff Training') 
@section('content') 
 
    <section id="main-content"> 
        <section class="wrapper"> 
        <div class="row">
            <div class="col-md-12">
                <div class="backIcon"> <a href="{{ url('/') }}"><i class="fa fa-arrow-right" aria-hidden="true"></i></a> </div>
            </div>
        </div>
       <!--  <section class="" style="border:1px red solid"> --> <!-- schedule-sec p-t-80 --> 
        <section class="panel cus-calendar" > 
            <header class="panel-heading"> 
                    Staff Training list 
                    <!-- <span class="tools pull-right"> 
                    <a href="javascript:;" class="fa fa-chevron-down"></a> 
                    <a href="javascript:;" class="fa fa-cog"></a> 
                    <a href="javascript:;" class="fa fa-times"></a> 
                    </span> --> 
                </header> 
                <div class="panel-body"> 
                    <!-- page start--> 
                    <!-- <div class="row"> --> 
            <!-- <div class="container"> 
                <div class="schedule-main"> --> 
                    <!-- <h2>Staff Rota</h2> 
                    <div class="divide"></div> --> 
                    <div class="row m-t-20"> 
                        <div class="col-md-6 col-sm-6 col-xs-12"> 
                            <div class="sche-dates"> 
                                <ul type="none"> 
                                    <li><a href="{{ url('/staff/trainings?year=').$prev_year }}"><span><i class="fa fa-angle-double-left"></i></span></a></li> 
                                    <li> {{ $cur_year }} </li> 
                                    <li><a href="{{ url('/staff/trainings?year=').$forw_year }}"><span><i class="fa fa-angle-double-right"></i></span></a></li> 
                                </ul> 
                            </div> 
                        </div> 
                        <div class="col-md-6 col-sm-6 col-xs-12"> 
                            <div class="add-schedule"> 
                                <a href="javascript:void(0)" class="btn sche-btn training_add_btn"> <i class="fa fa-plus"></i> Add More </a> 
                            </div> 
                        </div> 
                    </div> 
                    <div class="schedule-table"> 
                        <div class="table-responsive" id="calendar"> 
                            <table class="table table-bordered"> 
                                <tbody> 
                                    <tr class="rota-th"> 
                                        <td>Jan</td> 
                                        <td>Feb</td> 
                                        <td>Mar</td> 
                                        <td>Apr</td> 
                                        <td>May</td> 
                                        <td>June</td> 
                                    </tr> 
                                    <tr class="training-tr"> 
                                     	 
                                   		@for($i=1; $i<=6; $i++)
	                                        <td>
                                                <ul class="custom-ul">
	                                    		@if(isset($training[$i]))
	                                    			@foreach($training[$i] as $key => $traini)
                                                            <li class="tranig-name-inner">
                                                                <div class="tranig-name">
                                                                    <a href="{{ url('/staff/training/view/'.$traini['id']) }}">{{ $traini['name'] }}</a>
                                                                    @if(!empty($traini['is_mandatory']))
                                                                        <span class="badge badge-danger" style="background:#e74c3c;color:#fff;font-size:9px;padding:2px 5px;margin-left:4px;">Required</span>
                                                                    @endif
                                                                    <span class="edit-icon edit_staff_training" traini_id="{{ $traini['id'] }}">
                                                                        <i class="fa fa-edit"></i>
                                                                    </span>
                                                                </div>
                                                            </li>
	                                            	@endforeach
	                                        	@endif
                                                </ul>
	                                        </td>
                                     	@endfor 
                                    </tr> 
                                </tbody> 
                            </table> 
                                <table class="table table-bordered"> 
                                <tbody> 
                                    <tr class="rota-th"> 
                                        <td>July</td> 
                                        <td>Aug</td> 
                                        <td>Sep</td> 
                                        <td>Oct</td> 
                                        <td>Nov</td> 
                                        <td>Dec</td> 
                                    </tr> 
                                    <tr class="training-tr"> 
                                        @for($i=7; $i<=12; $i++)
	                                        <td>
	                                    		@if(isset($training[$i]))
	                                    			@foreach($training[$i] as $key => $traini)
	                                            		<div class="tranig-name">
                                                            <a href="{{ url('/staff/training/view/'.$traini['id']) }}">{{ $traini['name'] }}</a>
                                                            @if(!empty($traini['is_mandatory']))
                                                                <span class="badge badge-danger" style="background:#e74c3c;color:#fff;font-size:9px;padding:2px 5px;margin-left:4px;">Required</span>
                                                            @endif
                                                            <span class="edit-icon edit_staff_training" traini_id="{{ $traini['id'] }}">
                                                                <i class="fa fa-edit"></i>
                                                            </span>
                                                        </div>
	                                            	@endforeach
	                                        	@endif
	                                        </td>
                                     	@endfor 
                                    </tr> 
 
                                </tbody> 
                        </table> 
                    </div> 
                </div> 
           <!--  </div> --> 
       <!--  </div> 
        </div> --> 
        </div> 
    <!-- </section> --> 
    </section> 
    </section> 
 
<!-- Add Training --> 
<div class="modal fade" id="addshiftmodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"> 
    <div class="modal-dialog" role="document"> 
        <div class="modal-content"> 
            <div class="modal-header"> 
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> 
                <h4 class="modal-title" id="myModalLabel">Add Training</h4> 
            </div> 
            <div class="modal-body"> 
                <div class="add-shifts"> 
                    <form class="form-horizontal" id="add_training" method="post" action="{{url('/staff/training/add')}}"> 
                       	{{ csrf_field() }} 
                        <div class="form-group col-md-12 col-sm-12 col-xs-12"> 
                            <label class="col-md-2 col-sm-3 col-xs-12 p-0 control-label">Name : </label> 
                            <div class="col-md-10 col-sm-9 col-xs-12"> 
                                <input type="text" name="name" class="form-control"> 
                            </div> 
                        </div> 
                        <div class="form-group col-md-12 col-sm-12 col-xs-12"> 
                            <label class="col-md-2 col-sm-3 col-xs-12 p-0 control-label">Training Provider : </label> 
                            <div class="col-md-10 col-sm-9 col-xs-12 m-t-10"> 
                                <input type="text" name="training_provider" class="form-control"> 
                            </div> 
                        </div> 
                        <div class="form-group col-md-12 col-sm-12 col-xs-12"> 
                            <label class="col-md-2 col-sm-3 col-xs-12 p-0 control-label">Description: </label> 
                            <div class="col-md-10 col-sm-9 col-xs-12"> 
                                <textarea rows="3" class="form-control" name="desc"></textarea> 
                            </div> 
                        </div> 
                        <div class="form-group col-md-12 col-sm-12 col-xs-12"> 
                            <label class="col-md-2 col-sm-3 col-xs-12 p-0 control-label">Month: </label> 
                            <div class="col-md-10 col-sm-9 col-xs-12"> 
                                <div class="select-style"> 
                                    <select name="month"> 
                                        <option value=""> Select Month</option> 
                                        <option value="1"> Jan</option> 
                                        <option value="2"> Feb </option> 
                                        <option value="3"> Mar </option> 
                                        <option value="4"> Apr </option> 
                                        <option value="5"> May </option> 
                                        <option value="6"> Jun </option> 
                                        <option value="7"> Jul </option> 
                                        <option value="8"> Aug </option> 
                                        <option value="9"> Sep </option> 
                                        <option value="10"> Oct </option> 
                                        <option value="11"> Nov </option> 
                                        <option value="12"> Dec </option> 
                                    </select> 
                                </div> 
                            </div> 
                        </div> 
                        <div class="form-group col-md-12 col-sm-12 col-xs-12">
                            <label class="col-md-2 col-sm-3 col-xs-12 p-0 control-label">Year: </label>
                            <div class="col-md-10 col-sm-9 col-xs-12">
                                <div class="select-style">
                                    <select name="year">
                                        <option value=""> Select Year</option>
                                        <?php
                                        	$current_year = Date('Y');
                                        	$end_year = $current_year+10;
                                        ?>
                                        @for($i = $current_year; $i <= $end_year; $i++)
                                        	<option value="{{ $i }}"> {{ $i }} </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-12 col-sm-12 col-xs-12">
                            <label class="col-md-2 col-sm-3 col-xs-12 p-0 control-label">Mandatory: </label>
                            <div class="col-md-10 col-sm-9 col-xs-12">
                                <label class="m-t-5"><input type="checkbox" name="is_mandatory" value="1"> This training is mandatory</label>
                            </div>
                        </div>
                        <div class="form-group col-md-12 col-sm-12 col-xs-12">
                            <label class="col-md-2 col-sm-3 col-xs-12 p-0 control-label">Expiry (months): </label>
                            <div class="col-md-10 col-sm-9 col-xs-12">
                                <input type="number" name="expiry_months" class="form-control" min="1" max="120" placeholder="Leave blank if no expiry">
                            </div>
                        </div>
                		<div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
		            	<button type="submit" class="btn btn-warning">Submit</button>
		            	</div>
        			</form> 
        		</div> 
    		</div> 
        </div> 
    </div> 
</div> 
<!-- Add Training End --> 
 
<!-- Edit Training --> 
<div class="modal fade" id="editShiftModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"> 
    <div class="modal-dialog" role="document"> 
        <div class="modal-content" style="float:left; width:100%; background-color: #fff;"> 
            <div class="modal-header">   
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> 
                <a class="close delete_training_rec" style="font-size:18px; padding-right:8px;" id="del-training-record"> 
                    <i class="fa fa-trash" title="Delete"></i> 
                </a> 
                <a class="close edit_traing_record" href="#" style="font-size:18px; padding-right:8px;"> 
                    <i class="fa fa-pencil" title="Edit"></i> 
                </a> 
                <h4 class="modal-title" id="myModalLabel"> Training</h4> 
            </div> 
            <div class="modal-body"> 
                <div class="add-shifts"> 
                    <form class="form-horizontal" id="edit_training" method="post" action="{{url('/staff/training/edit_fields')}}"> 
                        {{ csrf_field() }} 
                         
                        <div class="form-group col-md-12 col-sm-12 col-xs-12"> 
                            <label class="col-md-2 col-sm-3 col-xs-12 p-0 control-label">Name : </label> 
                            <div class="col-md-10 col-sm-9 col-xs-12"> 
                                <input type="text" name="name" class="form-control"> 
                            </div> 
                        </div> 
 
                        <div class="form-group col-md-12 col-sm-12 col-xs-12"> 
                            <label class="col-md-2 col-sm-3 col-xs-12 p-0 control-label">Training Provider : </label> 
                            <div class="col-md-10 col-sm-9 col-xs-12 m-t-10"> 
                                <input type="text" name="training_provider" class="form-control"> 
                            </div> 
                        </div> 
                         
                        <div class="form-group col-md-12 col-sm-12 col-xs-12"> 
                            <label class="col-md-2 col-sm-3 col-xs-12 p-0 control-label">Description: </label> 
                            <div class="col-md-10 col-sm-9 col-xs-12"> 
                                <textarea rows="3" class="form-control" name="desc"></textarea> 
                            </div> 
                        </div> 
                        <div class="form-group col-md-12 col-sm-12 col-xs-12"> 
                            <label class="col-md-2 col-sm-3 col-xs-12 p-0 control-label">Month: </label> 
                            <div class="col-md-10 col-sm-9 col-xs-12"> 
                                <div class="select-style"> 
                                    <select name="month"> 
                                        <option value=""> Select Month</option> 
                                        <option value="1"> Jan</option> 
                                        <option value="2"> Feb </option> 
                                        <option value="3"> Mar </option> 
                                        <option value="4"> Apr </option> 
                                        <option value="5"> May </option> 
                                        <option value="6"> Jun </option> 
                                        <option value="7"> Jul </option> 
                                        <option value="8"> Aug </option> 
                                        <option value="9"> Sep </option> 
                                        <option value="10"> Oct </option> 
                                        <option value="11"> Nov </option> 
                                        <option value="12"> Dec </option> 
                                    </select> 
                                </div> 
                            </div> 
                        </div> 
                        <div class="form-group col-md-12 col-sm-12 col-xs-12">
                            <label class="col-md-2 col-sm-3 col-xs-12 p-0 control-label">Year: </label>
                            <div class="col-md-10 col-sm-9 col-xs-12">
                                <div class="select-style">
                                    <select name="year">
                                        <option value=""> Select Year</option>
                                        <?php
                                            $current_year = Date('Y');
                                            $end_year = $current_year+10;
                                        ?>
                                        @for($i = $current_year; $i <= $end_year; $i++)
                                            <option value="{{ $i }}"> {{ $i }} </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-12 col-sm-12 col-xs-12">
                            <label class="col-md-2 col-sm-3 col-xs-12 p-0 control-label">Mandatory: </label>
                            <div class="col-md-10 col-sm-9 col-xs-12">
                                <label class="m-t-5"><input type="checkbox" name="is_mandatory" value="1"> This training is mandatory</label>
                            </div>
                        </div>
                        <div class="form-group col-md-12 col-sm-12 col-xs-12">
                            <label class="col-md-2 col-sm-3 col-xs-12 p-0 control-label">Expiry (months): </label>
                            <div class="col-md-10 col-sm-9 col-xs-12">
                                <input type="number" name="expiry_months" class="form-control" min="1" max="120" placeholder="Leave blank if no expiry">
                            </div>
                        </div>

                        <div class="modal-footer" style="float:left; width:100%; background-color: #fff;">
                        <input type="hidden" name="training_id" value="">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning" id="sbt-edit-dis">Submit</button>
                        </div>
                    </form> 
                </div> 
            </div> 
        </div> 
    </div> 
</div> 
<!-- Edit Training End --> 
 
<script>  
 
    $(document).ready(function(){ 
         
        //Add training record modal open 
        $(document).on('click','.training_add_btn', function(){ 
            autosize.destroy($('textarea')); 
            autosize($("textarea")); 
            $('#addshiftmodal').modal('show'); 
            $('#add_training')[0].reset(); 
        }); 
 
        //view training record modal 
        $(document).on('click', '.edit_staff_training', function(){ 
            var training_id = $(this).attr('traini_id'); 
            staffTainingRecord(training_id); 
             
            $('#edit_training').find('input').attr('disabled', true); 
            $('#edit_training').find('textarea').attr('disabled', true); 
            $('#edit_training').find('select').attr('disabled', true); 
            $('#sbt-edit-dis').attr('disabled', true); 
        });  
 
        //edit training record 
        $(document).on('click','.edit_traing_record', function(){ 
            $('#edit_training').find('input').attr('disabled', false); 
            $('#edit_training').find('textarea').attr('disabled', false); 
            $('#edit_training').find('select').attr('disabled', false); 
            $('#sbt-edit-dis').attr('disabled', false); 
        }); 
 
        function staffTainingRecord(training_id) { 
 
            $('.loader').show(); 
            $('body').addClass('body-overflow'); 
 
            $.ajax({ 
                type: 'get', 
                url: "{{ url('/staff/training/view_fields') }}"+'/'+training_id, 
 
                success:function(resp) { 
                    if(isAuthenticated(resp) == false) { 
                        return false; 
                    } 
                    var response = resp['response']; 
                    if(response == true) { 
 
                        var training_id = resp['training_id']; 
         
                        $('input[name=\'training_id\']').val(training_id);
                        $('input[name=\'name\']').val(resp['training_name']);
                        $('input[name=\'training_provider\']').val(resp['training_provider']);
                        $('textarea[name=\'desc\']').val(resp['training_desc']);
                        $('select[name=\'month\']').val(resp['training_month']);
                        $('select[name=\'year\']').val(resp['training_year']);
                        // New fields
                        $('input[name=\'is_mandatory\']').prop('checked', resp['is_mandatory'] == 1);
                        $('input[name=\'expiry_months\']').val(resp['expiry_months']);
                        $('#editShiftModal').modal('show'); 
 
                        $('#del-training-record').attr("href","{{ url('/staff/training/delete/') }}"+'/'+training_id); 
 
                        autosize.destroy($('textarea')); 
                        setTimeout(function () { 
                            autosize($("textarea")); 
                        },200); 
 
                    } else { 
                         
                        $('.ajax-alert-err').find('.msg').text("{{ COMMON_ERROR }}"); 
                        $('.ajax-alert-err').show(); 
                        setTimeout(function(){$(".ajax-alert-err").fadeOut()}, 5000); 
                        // $('.popup_error').show(); 
                        // $('span.popup_error_txt').text('error', COMMON_ERROR); 
                        // setTimeout(function(){$(".popup_error_txt").fadeOut()}, 5000); 
                    }  
                } 
            }); 
            $('.loader').hide(); 
            $('body').removeClass('body-overflow'); 
        } 
 
    }); 
</script> 
 
<script>
    //delete training record via POST form
    $(document).ready(function(){
        $('.delete_training_rec').click(function(e){
            e.preventDefault();
            if(confirm('Do you want to delete this training record?')){
                var training_id = $('input[name="training_id"]').val();
                if(training_id) {
                    var form = $('<form>', {
                        'method': 'POST',
                        'action': "{{ url('/staff/training/delete') }}/" + training_id
                    });
                    form.append($('<input>', { 'type': 'hidden', 'name': '_token', 'value': '{{ csrf_token() }}' }));
                    form.appendTo('body').submit();
                }
            }
        });
    });
</script> 
 
<script type="text/javascript"> 
    $(function(){ 
        $('#add_training').validate({ 
            rules:{ 
                "name":{ 
                    required:true 
                }, 
                "training_provider":{ 
                    required:true   
                }, 
                "desc":{ 
                    required:true 
                }, 
                "month":{ 
                    required:true 
                }, 
                "year":{ 
                    required:true 
                } 
            } 
        }); 
    }); 
</script> 
 
<script type="text/javascript"> 
    $(function(){ 
        $('#edit_training').validate({ 
            rules:{ 
                "name":{ 
                    required:true 
                }, 
                "training_provider":{ 
                    required:true   
                }, 
                "desc":{ 
                    required:true 
                }, 
                "month":{ 
                    required:true 
                }, 
                "year":{ 
                    required:true 
                } 
            } 
        }); 
    }); 
</script> 
 
@endsection