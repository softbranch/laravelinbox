<!DOCTYPE html>
<html lang="en">
<head>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{asset('assets/image/favicon.png')}}" type="image/x-icon">
    <meta name="description" content="">
    <!-- Twitter meta-->
    <meta property="twitter:card" content="">
    <meta property="twitter:site" content="">
    <meta property="twitter:creator" content="">
    <!-- Open Graph Meta-->
    <meta property="og:type" content="">
    <meta property="og:site_name" content="">
    <meta property="og:title" content="">
    <meta property="og:url" content="">
    <meta property="og:image" content="">
    <meta property="og:description" content="">
    <title>{{$general['title']}} | @yield('title')</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Main CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('assets/admin/css/main.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/admin/css/toastr.min.css')}}">
    <!-- Font-icon css-->
    <link rel="stylesheet" href="{{asset('assets/home/fonts/flaticon.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/admin/css/font-awesome.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/admin/css/bootstrap-toggle.min.css')}}">

    <link rel="stylesheet" type="text/css" href="{{asset('assets/fileInput/bootstrap-fileinput.css')}}">
    @yield('style')
</head>
<body class="app sidebar-mini rtl">
<!-- Navbar-->

<header class="app-header"><a class="app-header__logo" href="{{route('admin.dashboard')}}">{{$general['title']}} </a>
    <a class="app-sidebar__toggle" href="#" data-toggle="sidebar" aria-label="Hide Sidebar"></a>
    <ul class="app-nav">
        <li class="dropdown"><a class="app-nav__item" href="#" data-toggle="dropdown" aria-label="Open Profile Menu"><i class="fa fa-user fa-lg"></i>&nbsp;&nbsp;{{ Auth::guard('admin')->user()->name }}</a>
            <ul class="dropdown-menu settings-menu dropdown-menu-right">
                <li><a class="dropdown-item" href="{{route('admin.profile')}}"><i class="fa fa-user fa-lg"></i> Profile</a></li>
                <li><a class="dropdown-item" href="{{route('admin.change.password')}}"><i class="fa fa-key"></i>Change password</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.logout') }}"
                       onclick="event.preventDefault();
                       document.getElementById('logout-form').submit();">
                        <i class="fa fa-sign-out fa-lg"></i> Logout</a></li>
                <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>

            </ul>
        </li>
    </ul>
</header>


<!-- Sidebar menu-->

<aside class="app-sidebar">

    <ul class="app-menu">
        <li><a class="app-menu__item @yield('dashboard')" href="{{route('admin.dashboard')}}"><i class="app-menu__icon fa fa-dashboard"></i><span class="app-menu__label">Dashboard</span></a></li>

<!-- Start Copy Code -->

        
        <li class="treeview  @if(request()->route()->getName() == 'admin.inboxwebmails') is-expanded
            @elseif(request()->route()->getName() == 'admin.inboxwebmail.add') is-expanded
            @elseif(request()->route()->getName() == 'admin.inboxwebmail.edit') is-expanded
            @elseif(request()->route()->getName() == 'admin.inboxwebmail.view') is-expanded
            @elseif(request()->route()->getName() == 'admin.inboxwebmail.compose') is-expanded
            @endif">
            <a class="app-menu__item " href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-envelope"></i><span class="app-menu__label">INBOX</span><i class="treeview-indicator fa fa-angle-right"></i></a>
            <ul class="treeview-menu">
                <li><a class="treeview-item @if(request()->route()->getName() == 'admin.inboxwebmails' || request()->route()->getName() == 'admin.inboxwebmail.add' || request()->route()->getName() == 'admin.inboxwebmail.edit') active @endif" href="{{route('admin.inboxwebmails')}}"><i class="icon fa fa-cogs"></i>Settings</a></li>

                @if(!empty($inboxwebmailMenu))
                    @foreach($inboxwebmailMenu as $labelMenu)
                <li><a class="treeview-item @if(request()->route()->getName() == 'admin.inboxwebmail.view') active @endif" href="{{route('admin.inboxwebmail.view',$labelMenu->id)}}"><i class="icon fa fa-inbox"></i>{{$labelMenu->email}}</a></li>
                    @endforeach
                @endif

            </ul>
        </li>

<!-- End Copy Code -->



    </ul>
</aside>

@yield('content')

<!-- Essential javascripts for application to work-->
<script type="text/javascript" src="{{asset('assets/admin/js/jquery-3.2.1.min.js')}}"></script>
<script type="text/javascript" src="{{asset('assets/admin/js/popper.min.js')}}"></script>
<script type="text/javascript" src="{{asset('assets/admin/js/bootstrap.min.js')}}"></script>
<script type="text/javascript" src="{{asset('assets/admin/js/main.js')}}"></script>
<!-- The javascript plugin to display page loading on top-->
<script type="text/javascript" src="{{asset('assets/admin/js/plugins/pace.min.js')}}"></script>
<!-- Page specific javascripts-->
<script type="text/javascript" src="{{asset('assets/admin/js/bootstrap-toggle.min.js')}}"></script>
<script type="text/javascript" src="{{asset('assets/fileInput/bootstrap-fileinput.js')}}"></script>
<script type="text/javascript" src="{{asset('assets/admin/js/jscolor.js')}}"></script>
<script type="text/javascript" src="{{asset('assets/admin/js/toastr.min.js')}}"></script>
<script type="text/javascript" src="{{asset('assets/admin/js/chart.js')}}"></script>

@yield('script')
@include('notification.notification')
</body>
</html>
