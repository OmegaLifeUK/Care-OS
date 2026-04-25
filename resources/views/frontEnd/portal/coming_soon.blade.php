@extends('frontEnd.portal.layouts.master')

@section('title', '— ' . $page_name)

@section('content')
<div style="text-align:center; padding:80px 20px;">
    <i class="fa fa-wrench" style="font-size:64px; color:#bdc3c7; margin-bottom:20px; display:block;"></i>
    <h2 style="color:#555; margin-bottom:10px;">{{ $page_name }} — Coming Soon</h2>
    <p style="color:#999; font-size:15px; max-width:500px; margin:0 auto;">
        This feature is currently under development. We are working hard to bring you the best experience.
    </p>
    <a href="{{ url('/portal') }}" class="btn btn-primary" style="margin-top:25px;">
        <i class="fa fa-arrow-left"></i> Back to Dashboard
    </a>
</div>
@endsection
