@extends('backEnd.layouts.master')

@section('title',':Mood Form')

@section('content')

<?php
    if(isset($mood_info)) {
        $action = url('admin/mood/edit/'.$mood_info->id);
        $task   = "Edit";
        $form_id = 'edit_mood_form';
    } else {
        $action = url('admin/mood/add');
        $task   = "Add";
        $form_id = 'add_mood_form';
    }
?>

<style type="text/css">
    .form-horizontal .has-feedback .istyle i.form-control-feedback {
        top: -6px;
        right: 5px;
    }
    .sele select {
        -moz-appearance:none;
        -webkit-appearance:none;
    }
    .sele {
        position: relative;
    }
    .sele::after {
        right: 45px;
        top: 41%;
        border-top: 7px solid #9d9d9d;
        border-left: 4px solid transparent;
        border-right: 4px solid transparent;
        border-bottom: 1px solid transparent;
        position: absolute;
        content: "";
    }
</style>
<section id="main-content" class="">
    <section class="wrapper">
        <div class="row">
			<div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                    {{ $task }} Mood 
                    </header>
                    <div class="panel-body">
                        <div class="position-center">
                            <form class="form-horizontal" role="form" method="post" action=" {{ $action }}" id="{{ $form_id }}" enctype="multipart/form-data">

                                <div class="form-group">
                                    <label class="col-lg-2 control-label">Name</label>
                                    <div class="col-lg-10">
                                        <input type="text" name="name" class="form-control" placeholder="mood" value="{{ (isset($mood_info->name)) ? $mood_info->name : '' }}">
                                    </div>
                                </div>
          
                                <?php
                                    $image = MoodImgPath.'/dummy.jpg';
                                    // echo "<pre>"; print_r($image); die;
                                    if(isset($mood_info->image)) {
                                        $image = MoodImgPath.'/'.$mood_info->image;
                                    }
                                ?>
                                <div class="form-group">
                                    <label class="col-lg-2 control-label"></label>
                                    <div class="col-md-8">
                                        <img src="{{ $image }}" id="old_image"  alt="No image" style="max-width: 200px; max-height: 150px; min-width: 150px; min-height: 100px; line-height: 100px;">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-2 control-label">Image</label>
                                    <div class="col-lg-4 istyle">
                                        <input type="file" id="img_upload" name="image" val="">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-lg-2 control-label">Status</label>
                                    <div class="col-lg-10 sele">
                                        <select name="status" class="form-control">
                                                <option value="">Select Status</option>
                                                <option value="1" <?php if(isset($mood_info->status)) { if($mood_info->status == '1'){ echo 'selected'; } }   ?>>Active
                                                
                                                </option>
                                                <option value="0" <?php if(isset($mood_info->status)) { if($mood_info->status == '0'){ echo 'selected'; } }   ?>>Inactive

                                                </option>           
                                        </select>
                                    </div>
                                </div>

    							<div class="form-actions">
    								<div class="row">
    									<div class="col-lg-offset-2 col-lg-10">
    										<input type="hidden" name="_token" value="{{ csrf_token() }}">
    										<input type="hidden" name="mood_id" value="{{ (isset($mood_info->id)) ? $mood_info->id : '' }}">
    										<button type="submit" class="btn btn-primary" name="submit1">Save</button>
    										<a href="{{ url('admin/moods') }}">
    											<button type="button" class="btn btn-default" name="cancel">Cancel</button>
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




<script>
$(document).ready(function() {

  function readURL(input) 
    {
      	if (input.files && input.files[0])
        {
            var reader = new FileReader();
            reader.onload = function (e) 
                {
                    //$('#old_image').attr('src', e.target.result).width(150).height(170);
                    $('#old_image').attr('src', e.target.result);
                };
            reader.readAsDataURL(input.files[0]);
        }
    }

  	$("#img_upload").change(function(){	
        
      	var img_name = $(this).val();
      	if(img_name != "" && img_name!=null)
      	{
        	var img_arr=img_name.split('.');
        	var ext = img_arr.pop();
        	ext     = ext.toLowerCase();
        	if(ext =="jpg" || ext =="jpeg" || ext =="gif" || ext =="png")
			{
	            input=document.getElementById('img_upload');
	            if(input.files[0].size > 2097152 || input.files[0].size <  10240)
	            {
	              $(this).val('');
	              $("#img_upload").removeAttr("src");
	              alert("image size should be at least 10KB and upto 2MB");
	              return false;
	            }
	            else
	            {
	              readURL(this);
	            }   
	        }
           else
	        {
	           	$(this).val('');
	           	alert('Please select an image .jpg, .png, .gif file format type.');
	        }
	    }
	    return true;
	}); 
});
</script>

@endsection