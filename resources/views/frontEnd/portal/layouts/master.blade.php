<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Care One OS — Family Portal @yield('title', '')</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('public/images/favicon.ico') }}">
    <link href="{{ url('public/frontEnd/css/bs3/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ url('public/frontEnd/css/bootstrap-reset.css') }}" rel="stylesheet">
    <link href="{{ url('public/frontEnd/css/font-awesome/css/font-awesome.css') }}" rel="stylesheet">
    <script src="{{ url('public/frontEnd/js/jquery.min.js') }}"></script>
    <style>
        body {
            background: #f4f6f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .portal-navbar {
            background: #2c3e50;
            border: none;
            border-radius: 0;
            margin-bottom: 0;
        }
        .portal-navbar .navbar-brand {
            color: #fff;
            font-weight: 600;
            font-size: 18px;
        }
        .portal-navbar .navbar-brand:hover {
            color: #ecf0f1;
        }
        .portal-navbar .nav > li > a {
            color: #bdc3c7;
            font-weight: 500;
        }
        .portal-navbar .nav > li > a:hover,
        .portal-navbar .nav > li.active > a {
            color: #fff;
            background: #34495e;
        }
        .portal-navbar .navbar-right > li > a {
            color: #ecf0f1;
        }
        .portal-sidebar {
            background: #fff;
            min-height: calc(100vh - 50px);
            border-right: 1px solid #e0e0e0;
            padding: 20px 0;
        }
        .portal-sidebar .nav > li > a {
            color: #555;
            padding: 12px 20px;
            border-left: 3px solid transparent;
            font-size: 14px;
        }
        .portal-sidebar .nav > li > a:hover {
            background: #f4f6f9;
            border-left-color: #3498db;
            color: #2c3e50;
        }
        .portal-sidebar .nav > li.active > a {
            background: #eef5fb;
            border-left-color: #3498db;
            color: #2c3e50;
            font-weight: 600;
        }
        .portal-sidebar .nav > li > a > i {
            width: 20px;
            text-align: center;
            margin-right: 8px;
        }
        .portal-content {
            padding: 25px 30px;
        }
        .portal-footer {
            background: #fff;
            border-top: 1px solid #e0e0e0;
            padding: 15px 30px;
            text-align: center;
            color: #999;
            font-size: 12px;
        }
    </style>
    @yield('styles')
</head>
<body>
    <nav class="navbar navbar-default portal-navbar" role="navigation">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#portal-nav">
                    <span class="icon-bar" style="background:#fff"></span>
                    <span class="icon-bar" style="background:#fff"></span>
                    <span class="icon-bar" style="background:#fff"></span>
                </button>
                <a class="navbar-brand" href="{{ url('/portal') }}">
                    <i class="fa fa-home"></i> Care One OS
                </a>
            </div>
            <div class="collapse navbar-collapse" id="portal-nav">
                <ul class="nav navbar-nav navbar-right">
                    <li>
                        <a href="#">
                            <i class="fa fa-user"></i> Hi, {{ $portal_access->full_name ?? 'User' }}
                        </a>
                    </li>
                    <li>
                        <a href="#" onclick="event.preventDefault(); document.getElementById('portal-logout-form').submit();">
                            <i class="fa fa-sign-out"></i> Logout
                        </a>
                        <form id="portal-logout-form" action="{{ url('/portal/logout') }}" method="POST" style="display:none">
                            @csrf
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 portal-sidebar hidden-sm hidden-xs">
                <ul class="nav nav-pills nav-stacked">
                    <li class="{{ request()->is('portal') ? 'active' : '' }}">
                        <a href="{{ url('/portal') }}"><i class="fa fa-dashboard"></i> Dashboard</a>
                    </li>
                    <li class="{{ request()->is('portal/schedule') ? 'active' : '' }}">
                        <a href="{{ url('/portal/schedule') }}"><i class="fa fa-calendar"></i> Schedule</a>
                    </li>
                    <li class="{{ request()->is('portal/messages') ? 'active' : '' }}">
                        <a href="{{ url('/portal/messages') }}"><i class="fa fa-envelope"></i> Messages</a>
                    </li>
                    <li class="{{ request()->is('portal/feedback') ? 'active' : '' }}">
                        <a href="{{ url('/portal/feedback') }}"><i class="fa fa-comment"></i> Feedback</a>
                    </li>
                </ul>
            </div>
            <div class="col-md-10 col-sm-12 portal-content">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        {{ session('error') }}
                    </div>
                @endif
                @yield('content')
            </div>
        </div>
    </div>

    <div class="portal-footer">
        &copy; {{ date('Y') }} Omega Life UK. All rights reserved.
    </div>

    <script src="{{ url('public/frontEnd/js/bs3/bootstrap.min.js') }}"></script>
    <script>
        var csrfToken = '{{ csrf_token() }}';
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': csrfToken }
        });
        function esc(str) {
            if (!str) return '';
            var div = document.createElement('div');
            div.appendChild(document.createTextNode(str));
            return div.innerHTML;
        }
    </script>
    @yield('scripts')
</body>
</html>
