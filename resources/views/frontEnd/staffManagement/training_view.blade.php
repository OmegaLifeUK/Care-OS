@extends('frontEnd.layouts.master')
@section('title','Staff Training')
@section('content')
<style type="text/css">
    .pop-notifbox {
      left: 50px;
      min-width: 172px;
      opacity: 0;
      position: absolute;
      text-align: left;
      top: -40px;
      visibility: hidden;
      z-index: 9;
    }
    .fll-wdth.select2-width .select2 {
      width: 85% !important;
    }
</style>
<section id="main-content">
    <section class="wrapper">

        <div class="col-md-12 col-sm-12 col-xs-12 p-0">
            <!--notification start-->
            <section class="panel">
                <header class="panel-heading">
                    Staff Training view <!-- <span class="tools pull-right">
                    <a href="javascript:;" class="fa fa-chevron-down"></a>
                    <a href="javascript:;" class="fa fa-cog"></a>
                    <a href="javascript:;" class="fa fa-times"></a>
                    </span> -->
                </header>
                <div class="panel-body">
                    <div class="row">

                        <!-- left part -->
                        <div class="col-md-6 col-sm-12 col-xs-12">
                            <div class="col-md-12 col-sm-12 col-xs-12 p-0">
                                <h3 class="m-t-0 m-b-20 clr-blue fnt-20">Training Name : {{ $training_name }}
                                    @if(isset($training) && $training->is_mandatory)
                                        <span class="badge" style="background:#e74c3c;color:#fff;font-size:11px;padding:3px 8px;">Mandatory</span>
                                    @endif
                                </h3>
                                @if(isset($training) && $training->expiry_months)
                                    <p style="color:#888;margin-top:-10px;margin-bottom:15px;">
                                        <i class="fa fa-clock-o"></i> Expires {{ $training->expiry_months }} month(s) after completion
                                    </p>
                                @endif
                            </div>
                            <input type="hidden" name="training_id" value="{{ $training_id }}">
                            <!-- <div class="form-group col-md-12 col-sm-12 col-xs-12 p-0 cus-label">
                                <label class="col-md-2 col-sm-2 p-t-7 p-0"> Add Staff: </label>
                                <div class="col-md-5 col-sm-7 col-xs-12 p-0">
                                    <input type="text" class="form-control">
                                   
                                </div>

                            </div> -->
                            @if(isset($remaining_capacity))
                                <p style="color:#e67e22;margin-bottom:10px;">
                                    <i class="fa fa-users"></i> <strong>{{ $remaining_capacity }}</strong> slot(s) remaining out of {{ $training->max_employees }}
                                </p>
                            @endif
                            @if($is_admin)
                            <form id="training_user_add" method="post" action="{{ url('/staff/training/staff/add') }}">
                                {{ csrf_field() }}
                                <div class="form-group col-md-12 col-sm-12 col-xs-12 p-0" id="user-add">
                                    <label class="col-md-3 col-sm-1 p-t-7"> Select Staff: </label>
                                    <div class="col-md-9 col-sm-11 col-xs-12 p-0">
                                        <div class="input-group popovr fll-wdth select2-width">
                                            <select class="js-example-placeholder-single form-control" multiple="multiple" style="width:100%;" name="user_ids[]">
                                                <option value=""></option>
                                                @foreach($home_users as $users)
                                                    <option value="{{ $users['id'] }}">{{ $users['name'] }}</option>
                                                @endforeach    
                                            </select>
                                            <!-- <span class="input-group-addon placmt-ico cus-inpt-grp-addon">
                                                <i class="fa fa-plus"></i>
                                            </span> -->
                                            <button class="input-group-addon placmt-ico cus-inpt-grp-addon"><i class="fa fa-plus"></i></button>
                                        </div>
                                        <input type="hidden" name="training_id" value="{{ $training_id }}">

                                        
                                        <p class="help-block">Select staff member to add this training.</p>
                                    </div>
                                   
                                </div>
                            </form>
                            @endif
                            <!-- Divider -->
                            <div class="col-md-12 col-sm-12 col-xs-12 p-0">
                                <div class="below-divider"></div>
                            </div>

                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <h3 class="color-green"> Active Staff </h3>
                            </div>
                            <span class="active-append">
                                <div class="form-group col-md-12 col-sm-12 col-xs-12 cog-panel p-0">
                                     @if($active_training->isEmpty())
                                        <p class="p-l-20">No Staff has active this training.</p>
                                    @else
                                        @foreach($active_training as $active)
                                            <div class="col-md-12 col-sm-12 col-xs-12 cog-panel p-0">
                                                <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                                    <a href="">{{ $active->name }}</a>
                                                    <span class="m-l-15 clr-blue settings setting-sze">
                                                        <i class="fa fa-cog"></i>
                                                        <div class="pop-notifbox">
                                                            <ul class="pop-notification" type="none">
                                                                
                                                                <li> <a href="{{url('/staff/training/status/update').'/'.$active->id.'?status=complete'}}"><span class="color-green"> <i class="fa fa-check"></i> </span> Mark complete </a></li>
                                                                
                                                                <li> <a href="{{url('/staff/training/status/update').'/'.$active->id.'?status=notcompleted'}}"> <span class="color-red"> <i class="fa fa-exclamation-circle"></i> </span> Mark uncomplete </a> </li>
                                                            </ul>
                                                        </div>
                                                    </span>
                                                </div>
                                            </div>
                                        @endforeach   
                                    @endif 
                                </div>
                                <div class="col-md-12 col-sm-12 col-xs-12 clearfix active_training">
                                    {{ $active_training->links() }}
                                </div>
                            </span>
                        </div>
                        <!-- Right part -->

                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <h3 class="m-t-0 m-b-20 clr-blue fnt-20"> Completed </h3>
                            </div>
                            <span class="complete-append">
                                @if($completed_training->isEmpty())
                                    <p class="p-l-20">No Staff has completed this training.</p>
                                @else
                                    @foreach($completed_training as $complete)
                                        <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                            <a href="">{{ $complete->name }}</a> <span class="color-green m-l-15"><i class="fa fa-check"></i></span>
                                        </div>
                                    @endforeach
                                @endif
                                <div class="col-md-12 col-sm-12 col-xs-12 clearfix completed">
                                    {{ $completed_training->links() }}
                                </div> 
                            </span>  

                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="below-divider"></div>
                            </div>
                            
                            <div class="col-md-12 col-sm-12 col-xs-12 cog-panel p-0">
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <h3 class="m-t-0 m-b-20 color-red fnt-20"> Not Completed </h3>
                                </div>

                            </div>
                            <span class="notcomplete-append">
                                @if($not_completed_training->isEmpty())
                                    <p class="p-l-20">No staff in this category.</p>
                                @else
                                    @foreach($not_completed_training as $not_completed)

                                        <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                            <a href="">{{ $not_completed->name }}</a>
                                            <span class="m-l-15 clr-blue settings setting-sze">
                                                <i class="fa fa-cog"></i>
                                                <div class="pop-notifbox">
                                                    <ul type="none" class="pop-notification">
                                                        <li> <a href="{{url('/staff/training/status/update').'/'.$not_completed->id.'?status=activate'}}"> <span> <i class="fa fa-pencil"></i> </span> Mark Active </a> </li>
                                                        <li> <a href="{{url('/staff/training/status/update').'/'.$not_completed->id.'?status=complete'}}"> <span class="color-green"> <i class="fa fa-check"></i> </span> Mark complete </a> </li>
                                                    </ul>
                                                </div>
                                            </span>
                                        </div>
                                    @endforeach
                                @endif
                                <div class="col-md-12 col-sm-12 col-xs-12 clearfix not-completed">
                                    {{ $not_completed_training->links() }}
                                </div>
                            </span>    

                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="below-divider"></div>
                            </div>

                        </div>

                    </div>

                </div>
            </section>
            <!--notification end-->
        </div>
    </section>
</section>

<script type="text/javascript">
    //For Completed trainings
    $(document).on('click','.completed ul li > a', function(e){
        e.preventDefault();
        var page_link = $(this).attr('href');

        if(page_link == undefined){
            return false;
        }
      
        var page_link_array = page_link.split('=');
        var page_number = page_link_array.pop();
        var page_link = "{{ url('/staff/training/completed/view') }}"+"/"+"{{ $training_id }}"+"?page="+page_number;

        $('.loader').show();
        $('body').addClass('body-overflow');

        $.ajax({
            url:page_link,
            type:"get",
            success: function(data){
                $(".complete-append").html(data);

                $('.loader').hide();
                $('body').removeClass('body-overflow');

            },
            error: function(){
                console.error("Failed to load training data");

                $('.loader').hide();
                $('body').removeClass('body-overflow');
            }
        });
    });

    //for not completed trainings
    $(document).on('click','.not-completed ul li > a', function(e){
        e.preventDefault();
        var page_link = $(this).attr('href');

        if(page_link == undefined){
            return false;
        }
      
        var page_link_array = page_link.split('=');
        var page_number = page_link_array.pop();
        var page_link = "{{ url('/staff/training/not-completed/view') }}"+"/"+"{{ $training_id }}"+"?page="+page_number;

        $('.loader').show();
        $('body').addClass('body-overflow');

        $.ajax({
            url:page_link,
            type:"get",
            success: function(data){
                $(".notcomplete-append").html(data);

                $('.loader').hide();
                $('body').removeClass('body-overflow');

            },
            error: function(){
                console.error("Failed to load training data");

                $('.loader').hide();
                $('body').removeClass('body-overflow');
            }
        });
    });

    //for active trainings
    $(document).on('click','.active_training ul li > a', function(e){
        e.preventDefault();
        var page_link = $(this).attr('href');

        if(page_link == undefined){
            return false;
        }
      
        var page_link_array = page_link.split('=');
        var page_number = page_link_array.pop();
        var page_link = "{{ url('/staff/training/active/view') }}"+"/"+"{{ $training_id }}"+"?page="+page_number;

        $('.loader').show();
        $('body').addClass('body-overflow');

        $.ajax({
            url:page_link,
            type:"get",
            success: function(data){
                $(".active-append").html(data);
              
                $('.loader').hide();
                $('body').removeClass('body-overflow');
            },
            error: function(){
                console.error("Failed to load training data");

                $('.loader').hide();
                $('body').removeClass('body-overflow');
            }
            
        });

    });

    $(".js-example-placeholder-single").select2({
          dropdownParent: $('#user-add'),
          placeholder: "Select User"
    });
</script>

@endsection
