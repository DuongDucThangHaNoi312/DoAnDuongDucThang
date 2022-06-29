<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Đăng nhập | Quản trị | {!! isset($staticPages['website-title']['description']) ? $staticPages['website-title']['description'] : env('APP_NAME') !!}</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="apple-touch-icon" sizes="180x180" href="{!! asset('assets/favicon.ico/apple-touch-icon.png') !!}">
    <link rel="icon" type="image/png" href="{!! asset('assets/favicon.ico/favicon-32x32.png" sizes="32x32') !!}">
    <link rel="icon" type="image/png" href="{!! asset('assets/favicon.ico/favicon-16x16.png" sizes="16x16') !!}">
    <link rel="manifest" href="{!! asset('assets/favicon.ico/site.webmanifest') !!}">
    <meta name="theme-color" content="#ffffff">
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/css/bootstrap.min.css') !!}" />
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/fonts/font-awesome.min.css') !!}" />
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/css/AdminLTE.min.css') !!}" />
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/iCheck/square/blue.css') !!}" />
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body class="hold-transition login-page">
    <div class="login-box">
        <div class="login-logo">
            Quản trị <b>{!! isset($staticPages['website-title']['description']) ? $staticPages['website-title']['description'] : env('APP_NAME') !!}</b>
        </div>
        <div class="login-box-body">
            <p class="login-box-msg">Đăng nhập để bắt đầu phiên làm việc</p>
            {!! Form::open(['url' => route('admin.login')]) !!}
                <div class="form-group has-feedback">
                    {!! Form::text('code', null, ['class' => 'form-control', 'placeholder' => 'Mã nhân viên', 'required']) !!}
                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                </div>
                <div class="form-group has-feedback">
                    {!! Form::password('password', ['class' => 'form-control', 'placeholder' => trans('forms.password'), 'required']) !!}
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                </div>
                <div class="row">
                    <div class="col-xs-4">
                        {!! Form::submit('Đăng Nhập', ['class' => 'btn btn-primary btn-block btn-flat']) !!}
                    </div>
                    {{-- <div class="col-xs-8">
                        <div class="checkbox icheck">
                            <label>
                                <input type="checkbox"> Ghi nhớ đăng nhập
                            </label>
                        </div>
                    </div> --}}
                </div>
            {!! Form::close() !!}
            @if($errors->count())
            <div class='alert alert-danger'>
                {!! trans('messages.error') !!}
                <ul>
                    @foreach($errors->all() as $message)
                    <li>{!! $message !!}</li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
    </div>
    <script src="{!! asset('assets/backend/plugins/jQuery/jQuery-2.1.4.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/js/bootstrap.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/iCheck/icheck.min.js') !!}"></script>
    <script>
        $(function () {
            $('input').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%',
            });
        });
    </script>
</body>
</html>
