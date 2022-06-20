<!DOCTYPE html>
<html>
<?php $appName = isset($staticPages['website-title']['description']) ? $staticPages['website-title']['description'] : env('APP_NAME'); ?>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="apple-touch-icon" sizes="180x180" href="{!! asset('assets/favicon.ico/apple-touch-icon.png') !!}">
    <link rel="icon" type="image/png" href="{!! asset('assets/favicon.ico/favicon-32x32.png" sizes="32x32') !!}">
    <link rel="icon" type="image/png" href="{!! asset('assets/favicon.ico/favicon-16x16.png" sizes="16x16') !!}">
    <link rel="manifest" href="{!! asset('assets/favicon.ico/site.webmanifest') !!}">
    <meta name="theme-color" content="#ffffff">
    <title>@yield('title') | {!! $appName !!}</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.5 -->
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/css/bootstrap.min.css') !!}"/>
    <!-- Font Awesome -->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css"/>
    <!-- Ionicons -->
    {{-- <link href="https://unpkg.com/ionicons@4.2.2/dist/css/ionicons.min.css" rel="stylesheet"> --}}
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/toastr/toastr.min.css') !!}">
    @yield('head')
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap-glyphicons.css" rel="stylesheet">
    <!-- Theme style -->
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/css/AdminLTE.min.css') !!}"/>
    <!-- AdminLTE Skins. Choose a skin from the css/skins
    folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/css/skins/_all-skins.min.css') !!}"/>

    <style>
        table.dataTable.no-footer {
            border-bottom: 0 !important;
        }
        table.dataTable thead th {
            padding: 4px 6px;
        }
        .search-form{
            width: 100%;
            background-color: #fff;
            color: #2780d1;
            transition: .3s;
            margin: 1px 0;
            outline: 0;
            box-shadow: inset 0 0 0 transparent;
            height: 28px;
            font-size: 13px;
            line-height: 1.42857143;
            padding: 2px 10px;
            border-radius: 3px;
            border: 1px solid #e7e6e6;
            background-size: 10px;
            background-position: 95% 8px;
            font-weight: normal
        }
        .input-text{

            background: url(https://upload.wikimedia.org/wikipedia/commons/thumb/0/0b/Search_Icon.svg/1024px-Search_Icon.svg.png) no-repeat;
            background-size: 15px;
            background-position: 95% 6px;
        }
        .date{

            background: url(https://images.echocommunity.org/85032db6-de87-47fc-abaf-d1fa3a5f498f/calendar-icon-marketing-image.png?w=600) no-repeat;
            background-size: 10px;
            background-position: 95% 8px;
        }
        .dataTables_filter {
            display: none;
        }
    </style>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesnt work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/css/fixed.css') !!}?v=20220506"/>
    <!-- jQuery 2.1.4 -->
    <script src="{!! asset('assets/backend/plugins/jQuery/jQuery-2.1.4.min.js') !!}"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="{!! asset('assets/backend/js/jquery-ui-1.11.4.min.js') !!}"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script src="{!! asset('assets/backend/plugins/daterangepicker/moment.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/daterangepicker/daterangepicker.js') !!}"></script>
    <script src="{!! asset('assets/backend/js/call-plugins.js') !!}?v=18-05-2022"></script>
    <script>
        var csrfGlobal = @json(csrf_token());
        var _VND_CODE = @json(\App\Defines\Contract::VND);
        var _URL_LOG = @json(route('admin.list-logs.show-log'));
    </script>
</head>
<?php $user = auth()->guard('admin')->getUser(); ?>
<body class="hold-transition skin-blue sidebar-mini">
<div class="element1">
    <div class="loading1">
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
    </div>
</div>
<div class="wrapper">
    <header class="main-header">
        <a href="{!! route('admin.home') !!}" class="logo">
            <span class="logo-mini">HR</span>
            <span class="logo-lg"><b>{!! trans('system.home') !!}</b></span>
        </a>
        <nav class="navbar navbar-static-top" role="navigation">
            <a href="javascript:void(0)" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <i class="fas fa-bars"></i>
            </a>
            <label style="color: white; padding-top: 15px; max-width: 250px; white-space: nowrap;" id="clock">
            </label>
            <script class="secret-source">
                function refrClock() {
                    var d = new Date();
                    var s = d.getSeconds();
                    var m = d.getMinutes();
                    var h = d.getHours();
                    var day = d.getDay();
                    var date = d.getDate();
                    var month = d.getMonth();
                    var year = d.getFullYear();
                    var days = new Array("Chủ nhật,", "Thứ hai,", "Thứ ba,", "Thứ tư,", "Thứ năm,", "Thứ sáu,", "Thứ bảy,");
                    var months = new Array("01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12");
                    var am_pm;
                    if (date < 10) {
                        date = "0" + date
                    }
                    if (s < 10) {
                        s = "0" + s
                    }
                    if (m < 10) {
                        m = "0" + m
                    } else {
                        am_pm = "Sáng"
                    }
                    if (h < 10) {
                        h = "0" + h
                    }
                    document.getElementById("clock").innerHTML = days[day] + " " + date + "/" + months[month] + "/" + year + " | " + h + ":" + m + ":" + s;
                    setTimeout("refrClock()", 1000);
                }
                refrClock();
            </script>
            @if (Session::has('is_admin'))
                <?php
                $allUsers = App\User::where('active', 1)
                    ->orderBy('code')
                    ->selectRaw("CONCAT(code, ' - ', fullname) as name, id, fullname")
                    ->pluck('name', 'id')
                    ->toArray();
                ?>
                <div class="collapse navbar-collapse pull-left" id="navbar-collapse">
                    <div class="navbar-form">
                        {!! Form::select('login_as', $allUsers, $user->id, ['class' => 'form-control1 select2']) !!}
                    </div>
                </div>
            @endif
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <li class="dropdown user user-menu">
                        <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown">
                            <img src="{!! asset('assets/backend/img/user.png') !!}" class="user-image" alt="User Image">
                            <span class="hidden-xs">{!! $user->fullname !!}</span>
                            @if(\Auth::user()->company->name)
                                | {!! \Auth::user()->position->name !!}
                                - {!! \Auth::user()->department->name !!}
                                - {!! \Auth::user()->company->shortened_name !!}
                            @endif
                        </a>
                        <ul class="dropdown-menu">
                            <li class="user-header">
                                <img src="{!! asset('assets/backend/img/user.png') !!}" class="img-circle"
                                     alt="User Image">
                                <p>
                                    {!! $user->fullname !!}
                                    <small>Lần cuối đăng
                                        nhập {!! date('d/m/Y H:i', strtotime($user->last_login)) !!}</small>
                                </p>
                            </li>
                            <li class="user-body">
                                <div class="col-xs-12 text-center">
                                    <a href="{!! route('admin.change-password') !!}" class="btn btn-warning"
                                       style="color: white !important;"><span class="fa fa-gear"></span> Đổi mật
                                        khẩu</a>
                                </div>
                            </li>
                            <li class="user-footer">
                                <div class="pull-left">
                                    <a href="{!! route('admin.account') !!}" class="btn btn-default btn-flat">Tài
                                        khoản</a>
                                </div>
                                <div class="pull-right">
                                    <a href="{!! route('admin.logout') !!}" class="btn btn-default btn-flat">Đăng xuất</a>
                                </div>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
    <aside class="main-sidebar">
        <section class="sidebar">
            <ul class="sidebar-menu">
                <?php
                $currentRoute = explode('.', Route::getCurrentRoute()->getName())[1];
                $roles = auth()->guard('admin')->user()->roles()->pluck('roles.name')->toArray();
                ?>3
                @foreach(config('menu')  as $menu)
                    @if(isset($menu['child']) && count($menu['child']) > 0)
                        @if (isset($menu['role']) && $menu['role'])
                            @role([$menu['role']])
                            <li class="treeview">
                                <a href="">
                                    <i class="{!!$menu['glyphicon']!!}"></i>@if(isset($menu['number_partner_request']) && $menu['number_partner_request'])
                                        <small class="label bg-red">{!! $counter !!}</small>@endif
                                    <span>{!! trans('menus.'.$menu['name']) !!}</span> <i
                                            class="fa fa-angle-left pull-right"></i>
                                </a>
                                <ul class="treeview-menu">
                                    @foreach($menu['child'] as $child)
                                        @if (isset($child['role']) && $child['role'])
                                            @role($child['role'])
                                            <li class="{!! (explode('.', $child['route'] )[1] == $currentRoute) ? 'active' : '' !!}">
                                                <a href="{!! route( $child['route'] ) !!}">
                                                    @if( isset($child['glyphicon']) )<i
                                                            class="{!!$child['glyphicon']!!}"></i>@if(isset($child['number_partner_request']) && $child['number_partner_request'])
                                                        <small class="label bg-red">{!! $counter !!}</small>@endif @else
                                                        <i class="fa fa-circle-o"></i>@endif {!! trans('menus.' . $child['name']) !!}
                                                </a>
                                            </li>
                                            @endrole
                                        @else
                                            @if (isset($child['permissions']) && is_array($child['permissions']) && !empty($child['permissions']))
                                                @ability('', $child['permissions'])
                                                <li class="{!! (explode('.', $child['route'] )[1] == $currentRoute) ? 'active' : '' !!}">
                                                    <a href="{!! route( $child['route'] ) !!}">
                                                        @if( isset($child['glyphicon']) )<i
                                                                class="{!!$child['glyphicon']!!}"></i>@if(isset($child['number_partner_request']) && $child['number_partner_request'])
                                                            <small class="label bg-red">{!! $counter !!}</small>@endif @else
                                                            <i class="fa fa-circle-o"></i>@endif {!! trans('menus.' . $child['name']) !!}
                                                    </a>
                                                </li>
                                                @endability
                                            @else
                                                <li class="{!! (explode('.', $child['route'] )[1] == $currentRoute) ? 'active' : '' !!}">
                                                    <a href="{!! route( $child['route'] ) !!}">
                                                        @if( isset($child['glyphicon']) )<i
                                                                class="{!!$child['glyphicon']!!}"></i>@if(isset($child['number_partner_request']) && $child['number_partner_request'])
                                                            <small class="label bg-red">{!! $counter !!}</small>@endif @else
                                                            <i class="fa fa-circle-o"></i>@endif {!! trans('menus.' . $child['name']) !!}
                                                    </a>
                                                </li>
                                            @endif
                                        @endif
                                    @endforeach
                                </ul>
                            </li>
                            @endrole
                        @else
                            @if (isset($menu['permissions']) && is_array($menu['permissions']) && !empty($menu['permissions']))
                                @ability('', $menu['permissions'])
                                <li class="treeview">
                                    <a href="">
                                        <i class="{!!$menu['glyphicon']!!}"></i>@if(isset($menu['number_partner_request']) && $menu['number_partner_request'])
                                            <small class="label bg-red">{!! $counter !!}</small>@endif
                                        <span>{!! trans('menus.'.$menu['name']) !!}</span> <i
                                                class="fa fa-angle-left pull-right"></i>
                                    </a>
                                    <ul class="treeview-menu">
                                        @foreach($menu['child'] as $child)
                                            @if (isset($child['role']) && $child['role'])
                                                @role($child['role'])
                                                <li class="{!! (explode('.', $child['route'] )[1] == $currentRoute) ? 'active' : '' !!}">
                                                    <a href="{!! route( $child['route'] ) !!}">
                                                        @if( isset($child['glyphicon']) )<i
                                                                class="{!!$child['glyphicon']!!}"></i>@if(isset($child['number_partner_request']) && $child['number_partner_request'])
                                                            <small class="label bg-red">{!! $counter !!}</small>@endif @else
                                                            <i class="fa fa-circle-o"></i>@endif {!! trans('menus.' . $child['name']) !!}
                                                    </a>
                                                </li>
                                                @endrole
                                            @else
                                                @if (isset($child['permissions']) && is_array($child['permissions']) && !empty($child['permissions']))
                                                    @if (Auth::guard('admin')->user()->hasPermission($child['permissions']))
                                                        <li class="{!! (explode('.', $child['route'] )[1] == $currentRoute) ? 'active' : '' !!}">
                                                            <a href="{!! route( $child['route'] ) !!}">
                                                                @if( isset($child['glyphicon']) )<i
                                                                        class="{!!$child['glyphicon']!!}"></i>@if(isset($child['number_partner_request']) && $child['number_partner_request'])
                                                                    <small class="label bg-red">{!! $counter !!}</small>@endif @else
                                                                    <i class="fa fa-circle-o"></i>@endif {!! trans('menus.' . $child['name']) !!}
                                                            </a>
                                                        </li>
                                                    @else

                                                    @endif
                                                @else
                                                    <li class="{!! (explode('.', $child['route'] )[1] == $currentRoute) ? 'active' : '' !!}">
                                                        <a href="{!! route( $child['route'] ) !!}">
                                                            @if( isset($child['glyphicon']) )<i
                                                                    class="{!!$child['glyphicon']!!}"></i>@if(isset($child['number_partner_request']) && $child['number_partner_request'])
                                                                <small class="label bg-red">{!! $counter !!}</small>@endif @else
                                                                <i class="fa fa-circle-o"></i>@endif {!! trans('menus.' . $child['name']) !!}
                                                        </a>
                                                    </li>
                                                @endif
                                            @endif
                                        @endforeach
                                    </ul>
                                </li>
                                @endability
                            @else
                                <li class="treeview">
                                    <a href="">
                                        <i class="{!!$menu['glyphicon']!!}"></i>
                                        <span>{!! trans('menus.'.$menu['name']) !!}</span> <i
                                                class="fa fa-angle-left pull-right"></i>
                                    </a>
                                    <ul class="treeview-menu">
                                        @foreach($menu['child'] as $child)
                                            @if (isset($child['role']) && $child['role'])
                                                @role($child['role'])
                                                <li class="{!! (explode('.', $child['route'] )[1] == $currentRoute) ? 'active' : '' !!}">
                                                    <a href="{!! route( $child['route'] ) !!}">
                                                        @if( isset($child['glyphicon']) )<i
                                                                class="{!!$child['glyphicon']!!}"></i>@if(isset($child['number_partner_request']) && $child['number_partner_request'])
                                                            <small class="label bg-red">{!! $counter !!}</small>@endif @else
                                                            <i class="fa fa-circle-o"></i>@endif {!! trans('menus.' . $child['name']) !!}
                                                    </a>
                                                </li>
                                                @endrole
                                            @else
                                                @if (isset($child['permissions']) && is_array($child['permissions']) && !empty($child['permissions']))
                                                    @ability('', $child['permissions'])
                                                    <li class="{!! (explode('.', $child['route'] )[1] == $currentRoute) ? 'active' : '' !!}">
                                                        <a href="{!! route( $child['route'] ) !!}">
                                                            @if( isset($child['glyphicon']) )<i
                                                                    class="{!!$child['glyphicon']!!}"></i>@if(isset($child['number_partner_request']) && $child['number_partner_request'])
                                                                <small class="label bg-red">{!! $counter !!}</small>@endif @else
                                                                <i class="fa fa-circle-o"></i>@endif {!! trans('menus.' . $child['name']) !!}
                                                        </a>
                                                    </li>
                                                    @endability
                                                @else
                                                    <li class="{!! (explode('.', $child['route'] )[1] == $currentRoute) ? 'active' : '' !!}">
                                                        <a href="{!! route( $child['route'] ) !!}">
                                                            @if( isset($child['glyphicon']) )<i
                                                                    class="{!!$child['glyphicon']!!}"></i>@if(isset($child['number_partner_request']) && $child['number_partner_request'])
                                                                <small class="label bg-red">{!! $counter !!}</small>@endif @else
                                                                <i class="fa fa-circle-o"></i>@endif {!! trans('menus.' . $child['name']) !!}
                                                        </a>
                                                    </li>
                                                @endif
                                            @endif
                                        @endforeach
                                    </ul>
                                </li>
                            @endif
                        @endif
                    @else
                        @if (isset($menu['role']) && $menu['role'])
                            @role($menu['role'])
                            <li class="{!! (explode('.', $menu['route'] )[1] == $currentRoute) ? 'active' : '' !!}">
                                <a href="{!! route( $menu['route'] ) !!}">
                                    <i class="{!!$menu['glyphicon']!!}"></i>@if(isset($menu['number_partner_request']) && $menu['number_partner_request'])
                                        <small class="label bg-red">{!! $counter !!}</small>@endif
                                    <span>{!! trans('menus.'.$menu['name']) !!}</span>
                                </a>
                            </li>
                            @endrole
                        @else
                            @if (isset($menu['permissions']) && is_array($menu['permissions']) && !empty($menu['permissions']))
                                @ability('', $menu['permissions'])
                                <li class="{!! (explode('.', $menu['route'] )[1] == $currentRoute) ? 'active' : '' !!}">
                                    <a href="{!! route( $menu['route'] ) !!}">
                                        <i class="{!!$menu['glyphicon']!!}"></i>@if(isset($menu['number_partner_request']) && $menu['number_partner_request'])
                                            <small class="label bg-red">{!! $counter !!}</small>@endif
                                        <span>{!! trans('menus.'.$menu['name']) !!}</span>
                                    </a>
                                </li>
                                @endability
                            @else
                                <li class="{!! (explode('.', $menu['route'] )[1] == $currentRoute) ? 'active' : '' !!}">
                                    <a href="{!! route( $menu['route'] ) !!}">
                                        <i class="{!!$menu['glyphicon']!!}"></i>@if(isset($menu['number_partner_request']) && $menu['number_partner_request'])
                                            <small class="label bg-red">{!! $counter !!}</small>@endif
                                        <span>{!! trans('menus.'.$menu['name']) !!}</span>
                                    </a>
                                </li>
                            @endif
                        @endif
                    @endif
                @endforeach
            </ul>
        </section>
    </aside>
    <div class="content-wrapper">
        @if(Session::has('message'))
            <div class="box box-default notify">
                <div class="box-body">
                    <div class="alert alert-{!! Session::get('alert-class', 'default') !!} alert-dismissable"
                         style="text-align: center;">
                        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span
                                    class="sr-only">Close</span></button>
                        <span style="font-size: 18px;">{!! Session::get('message') !!}</span>

                    </div>
                </div>
            </div>
            <script type="text/javascript">
                window.setTimeout(function () {
                    $(".notify").fadeTo(1500, 0).slideUp(500, function () {
                        $(this).remove();
                    });
                }, 5000);
            </script>
        @endif
        @yield('content')
    </div>
    <footer class="main-footer">
        <div class="pull-right hidden-xs">
            <b>Version</b> 1.0
        </div>
        <strong>Copyright &copy; 2021 <a href="https://bctech.vn" target="_blank">BCTech .,JSC</a>.</strong> All rights
        reserved.
    </footer>
    <div class="control-sidebar-bg"></div>
</div>
<div id="confirm-modal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"> Xác nhận </h4>
            </div>
            <div class="modal-body">
                <p> {!! trans('system.confirm_msg') !!} </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                        data-dismiss="modal"> {!! trans('system.confirm_no') !!} </button>
                <a href="javascript:void(0)" id="confirm-delete"
                   class="btn btn-danger"> {!! trans('system.confirm_yes') !!} </a>
            </div>
        </div>
    </div>
</div>
<div id="confirm-modal-del" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"> Xác nhận </h4>
            </div>
            <div class="modal-body">
                <p> {!! trans('system.confirm_msg') !!} </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"
                        style="float: left;"> {!! trans('system.confirm_no') !!} </button>
                <form action="" method="POST">
                    <input type="hidden" name="_token" value="{!! csrf_token() !!}">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-danger"
                            id="confirm-delete-del"> {!! trans('system.confirm_yes') !!} </button>
                </form>
            </div>
        </div>
    </div>
</div>
<div id="confirm-modal-canel" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"> Xác nhận </h4>
            </div>
            <div class="modal-body">
                <p> {!! trans('schedules.confirm_msg') !!} </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"
                        style="float: left;"> {!! trans('system.confirm_no') !!} </button>
                <form action="" method="POST">
                    <input type="hidden" name="_token" value="{!! csrf_token() !!}">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-danger"
                            id="confirm-delete-canel"> {!! trans('schedules.confirm_yes') !!} </button>
                </form>
            </div>
        </div>
    </div>
</div>
<div id="confirm-not-ajax-modal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"> Xác nhận </h4>
            </div>
            <div class="modal-body">
                <p> Bạn chắc chắn muốn thực hiện thao tác này? </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                        data-dismiss="modal"> {!! trans('system.action.cancel') !!} </button>
                <a class="btn btn-danger confirm-not-ajax-id"> {!! trans('system.action.ok') !!} </a>
            </div>
        </div>
    </div>
</div>
<div id="confirm-action-modal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"> Xác nhận </h4>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                        data-dismiss="modal"> {!! trans('system.action.cancel') !!} </button>
                <a class="btn btn-danger confirm-action-id"> {!! trans('system.action.ok') !!} </a>
            </div>
        </div>
    </div>
</div>
<div id="show-log-detail" class="modal fade">
</div>
<script>
    //$(".element1").css('display', 'block');
    $.widget.bridge('uibutton', $.ui.button);
</script>
<!-- jQuery -->
{{--<script src="{{ asset('assets/backend/js/jquery.min.js') }}"></script>--}}
<!-- Bootstrap 3.3.5 -->
<script src="{!! asset('assets/backend/js/bootstrap.min.js') !!}"></script>
<!-- Slimscroll -->
<script src="{!! asset('assets/backend/plugins/slimScroll/jquery.slimscroll.min.js') !!}"></script>
<!-- FastClick -->
<script src="{!! asset('assets/backend/plugins/fastclick/fastclick.min.js') !!}"></script>
<!-- AdminLTE App -->
<script src="{!! asset('assets/backend/js/app.min.js') !!}"></script>
<script src="{!! asset('assets/backend/plugins/toastr/toastr.min.js') !!}"></script>
<!-- Fixed js -->
<script src="{!! asset('assets/backend/js/fixed.js') !!}?v=01-04-2022"></script>
<!-- Validate -->
<script src="{{ asset('assets/backend/js/jquery.validate.min.js') }}"></script>
<script src="{!! asset('assets/backend/js/log.js') !!}?v=01-04-2022"></script>
<script>
    <!-- Set number column STT after search datatables -->
     function setNoAfterSearchDatatables(table, noColumn) {
        table.on('order.dt search.dt', function () {
            table.column(noColumn, {search: 'applied', order: 'applied'}).nodes().each(function (cell, i) {
                cell.innerHTML = i + 1;
            });
        }).draw();
    }

    $(document).ready(function () {
        // $(document).ajaxStart(function(){
        //     showLoading()
        // }).ajaxStop(function(){
        //     hideLoading()
        // });
        $("select[name='login_as']").change(function(event) {
            $(".element").show();
            $.ajax({
                url: "{!! route('admin.login-as') !!}",
                data: {
                    user_id: $(this).val()
                },
                type: 'POST',
                datatype: 'json',
                headers: {
                    'X-CSRF-Token': "{!! csrf_token() !!}"
                },
                success: function(res, status, xhr) {
                    window.location.href = "{{ route('admin.home') }}";
                },
                error: function(err) {
                    let error = $.parseJSON(err.responseText);
                    toastr.warning(error.message, "{!! trans('system.have_an_error') !!}")
                }
            }).always(function() {
                $(".element").hide();
            });
        });

        @if(Session::has('errors'))
        let errors = <?php echo Session::get('errors'); ?>;
        $.each(errors, function (key, value) {
            $('input:not([type="checkbox"]):not([type="radio"])').attr('name', function (i) {
                let nameClass = this.name;
                if (key == nameClass) {
                    if ($(this).parent().hasClass('input-group')) {
                        $("input[name='" + key + "']").parent().after('<p class="text-danger" style="margin: 4px 0 -9px!important;">' + value + '</p>').css('border-color', '#dd4b39');
                    } else {
                        $("input[name='" + key + "']").after('<p class="text-danger" style="margin: 4px 0 -9px!important;">' + value + '</p>').css('border-color', '#dd4b39');
                    }
                }
            });
            $('select').attr('name', function (i) {
                let nameClass = this.name;
                nameClass = nameClass.replace('[]', '')
                if (key == nameClass) {
                    $("select[name='" + key + "']").after('<p class="text-danger" style="margin: 4px 0 -9px!important;">' + value + '</p>').css('border-color', '#dd4b39');
                    $("select[name='" + key + '[]' + "']").after('<p class="text-danger" style="margin: 4px 0 -9px!important;">' + value + '</p>').css('border-color', '#dd4b39');
                }
            });
            $('textarea').attr('name', function (i) {
                let nameClass = this.name;
                if (key == nameClass) {
                    $("textarea[name='" + key + "']").after('<p class="text-danger" style="margin: 4px 0 -9px!important;">' + value + '</p>').css('border-color', '#dd4b39');
                }
            });
        });
        @endif
    });
    Number.prototype.format = function (n, x) {
        var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\.' : '$') + ')';
        return this.toFixed(Math.max(0, ~~n)).replace(new RegExp(re, 'g'), '$&,');
    };
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": true,
        "positionClass": "toast-bottom-left",
        "preventDuplicates": true,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "4000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "slideDown",
        "hideMethod": "slideUp"
    }
</script>
<script>
    !function (a) {
        a(function () {
            a(document).on("click", ".btn-confirm", function (b) {
                b.preventDefault(), $delete_url = a(this).attr("href"), a("#confirm-delete").attr("href", $delete_url), a("#confirm-modal").modal("show")
            }), a(document).on("click", ".btn-confirm-del", function (b) {
                var c = $("#confirm-delete-del").closest("form");
                c.attr('action', a(this).attr("link"));
                b.preventDefault(), $("#confirm-modal-del").modal({
                    backdrop: "static",
                    keyboard: !1
                });
            }),
                a(document).on("click", ".btn-confirm-canel", function (b) {
                    var c = $("#confirm-delete-canel").closest("form");
                    c.attr('action', a(this).attr("link"));
                    b.preventDefault(), $("#confirm-modal-canel").modal({
                        backdrop: "static",
                        keyboard: !1
                    });
                }),
                a(document).on("click", ".a-action-confirm", function (b) {
                    b.preventDefault(), a(".confirm-action-id").attr("id", a(this).attr("attr-id")), a("#confirm-action-modal .modal-body").html("").append(a(this).attr("attr-message")), a("#confirm-action-modal").modal("show")
                }), a(document).on("click", ".a-not-ajax-confirm", function (b) {
                b.preventDefault(), a(".confirm-not-ajax-id").attr("href", a(this).attr("href")), a("#confirm-not-ajax-modal").modal("show")
            }), a("ul.sidebar-menu > li").each(function (b, c) {
                a("ul").each(function (b, c) {
                    a("li").each(function (b, c) {
                        a(this).hasClass("active") && a(this).parent().parent().addClass("active")
                    })
                })
            })
        })
    }(window.jQuery);
</script>
@yield('footer')
<div class="scroll-top-wrapper">
    <span class="scroll-top-inner">
        <i class="fas fa-angle-double-up"></i>
    </span>
</div>
<script>
    $(window).load(function() {
        hideLoading();
    });
</script>
</body>
</html>
