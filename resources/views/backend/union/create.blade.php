@extends('backend.master')
@section('title')
    {!! trans('system.action.create') !!} - Kinh phí công đoàn
@stop
@section('head')
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/select2/select2.min.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/iCheck/all.css') !!}"/>
    <link rel="stylesheet" type="text/css"
          href="{!! asset('assets/backend/plugins/bootstrap-datepicker/css/bootstrap-datepicker.css') !!}"/>
    
    <style>
          .loading {
            position: fixed;
            z-index: 999;
            height: 2em;
            width: 2em;
            overflow: visible;
            margin: auto;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
        }
        
        /* Transparent Overlay */
        .loading:before {
            content: '';
            display: block;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.3);
        }

              /* :not(:required) hides these rules from IE9 and below */
        .loading:not(:required) {
            /* hide "loading..." text */
            font: 0/0 a;
            color: transparent;
            text-shadow: none;
            background-color: transparent;
            border: 0;
        }
        
        .loading:not(:required):after {
            content: '';
            display: block;
            font-size: 10px;
            width: 1em;
            height: 1em;
            margin-top: -0.5em;
            -webkit-animation: spinner 1500ms infinite linear;
            -moz-animation: spinner 1500ms infinite linear;
            -ms-animation: spinner 1500ms infinite linear;
            -o-animation: spinner 1500ms infinite linear;
            animation: spinner 1500ms infinite linear;
            border-radius: 0.5em;
            -webkit-box-shadow: rgba(0, 0, 0, 0.75) 1.5em 0 0 0, rgba(0, 0, 0, 0.75) 1.1em 1.1em 0 0, rgba(0, 0, 0, 0.75) 0 1.5em 0 0, rgba(0, 0, 0, 0.75) -1.1em 1.1em 0 0, rgba(0, 0, 0, 0.5) -1.5em 0 0 0, rgba(0, 0, 0, 0.5) -1.1em -1.1em 0 0, rgba(0, 0, 0, 0.75) 0 -1.5em 0 0, rgba(0, 0, 0, 0.75) 1.1em -1.1em 0 0;
            box-shadow: rgba(0, 0, 0, 0.75) 1.5em 0 0 0, rgba(0, 0, 0, 0.75) 1.1em 1.1em 0 0, rgba(0, 0, 0, 0.75) 0 1.5em 0 0, rgba(0, 0, 0, 0.75) -1.1em 1.1em 0 0, rgba(0, 0, 0, 0.75) -1.5em 0 0 0, rgba(0, 0, 0, 0.75) -1.1em -1.1em 0 0, rgba(0, 0, 0, 0.75) 0 -1.5em 0 0, rgba(0, 0, 0, 0.75) 1.1em -1.1em 0 0;
        }
        
        /* Animation */
        
        @-webkit-keyframes spinner {
            0% {
            -webkit-transform: rotate(0deg);
            -moz-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
            -o-transform: rotate(0deg);
            transform: rotate(0deg);
            }
            100% {
            -webkit-transform: rotate(360deg);
            -moz-transform: rotate(360deg);
            -ms-transform: rotate(360deg);
            -o-transform: rotate(360deg);
            transform: rotate(360deg);
            }
        }
        @-moz-keyframes spinner {
            0% {
            -webkit-transform: rotate(0deg);
            -moz-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
            -o-transform: rotate(0deg);
            transform: rotate(0deg);
            }
            100% {
            -webkit-transform: rotate(360deg);
            -moz-transform: rotate(360deg);
            -ms-transform: rotate(360deg);
            -o-transform: rotate(360deg);
            transform: rotate(360deg);
            }
        }
        @-o-keyframes spinner {
            0% {
            -webkit-transform: rotate(0deg);
            -moz-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
            -o-transform: rotate(0deg);
            transform: rotate(0deg);
            }
            100% {
            -webkit-transform: rotate(360deg);
            -moz-transform: rotate(360deg);
            -ms-transform: rotate(360deg);
            -o-transform: rotate(360deg);
            transform: rotate(360deg);
            }
        }
        @keyframes spinner {
            0% {
            -webkit-transform: rotate(0deg);
            -moz-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
            -o-transform: rotate(0deg);
            transform: rotate(0deg);
            }
            100% {
            -webkit-transform: rotate(360deg);
            -moz-transform: rotate(360deg);
            -ms-transform: rotate(360deg);
            -o-transform: rotate(360deg);
            transform: rotate(360deg);
            }
        }
        
        /* Animation */
    </style>
@stop
@section('content')
    <section class="content-header">

        <h1>
            Kinh phí công đoàn
            <small>{!! trans('system.action.create') !!}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.newborns.index') !!}">Kinh phí công đoàn</a></li>
        </ol>
    </section>
    @if($errors->count())
        <div class="alert alert-warning alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h4><i class="icon fa fa-warning"></i> {!! trans('messages.error') !!}</h4>
            <ul>
                @foreach($errors->all() as $message)
                    <li>{!! $message !!}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div style="width: 700px; margin: auto; margin-top: 50px;">
        {!! Form::open(['id' => 'formData', 'role' => 'form']) !!}
        <div class="box-body">
        <div class="loading"></div>

            <div>
          
                <label>Nhân viên <span style="color: red">*</span></label>
                <select name="user_id[]" id="" class="form-control select2 userSelect " multiple></select>

            </div>
            <div style="margin-top: 10px;">
                <div class="form-group">
                    <label>Ngày áp dụng<span
                                style="color: red">*</span></label>
                    <div class='input-group'>
                        {!! Form::text('start', old('start'), ['class' => 'form-control datepicker start_date','id'=>'start_date','autocomplete'=>'off']) !!}
                        <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                    </div>
                </div>
            </div>
            
            <div style="margin-top: 10px">
                <label for="">Ghi chú</label>
                {!! Form::textarea('note', old('note', $note), ['rows' => 2, 'class' => 'form-control note']) !!}
            </div>
           
            <div style="margin-top: 50px" align="center">
                {!! HTML::link(route( 'admin.unionfunds.index' ), trans('system.action.cancel'), ['class' => 'btn btn-danger btn-flat']) !!}
                {!! Form::button(trans('system.action.save'), ['class' => 'btn btn-primary btn-flat', 'id' => 'submitForm']) !!}
            </div>
            
        </div>
        {!! Form::close() !!}
    </div>
@stop

@section('footer')
    <script src="{!! asset('assets/backend/plugins/iCheck/icheck.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/select2/select2.full.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.vi.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/input-mask/jquery.inputmask.min.js') !!}"></script>

    <script>
        !function ($) {
            $('.loading').hide();

            $('.inputmask').inputmask({
                'placeholder': '',
                regex: '^[0-9][.][05]$',
            })
            $('.datepicker').datepicker({
                format: 'dd/mm/yyyy',
                autoclose: true,
                language: 'vi'
            })
            

            $(".select2").select2({
                    width: '100%',
            });

            $(function() {
                $('input[type="checkbox"].minimal-red').iCheck({
                    checkboxClass: 'icheckbox_minimal-red'
                });
            });

            // $('.time').on('keyup', function () {
                
            //     let note = `Làm việc ${$(this).val()}h/ngày, thời gian còn lại tính OT`;
            //     console.log(note);
            //     $('.note').text(note);
            // })

            $(".userSelect").select2({
                ajax: {
                    url: '{!! route("admin.unionfunds.searchUser") !!}',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term,
                            page: params.page
                        };
                    },
                    processResults: function (response, params) {
                        params.page = params.page || 1;
                        return {
                            results: response.data,
                            pagination: {
                                more: (params.page * 10) < response.total
                            }
                        };
                    },
                },
                placeholder: ' Tìm kiếm nhân viên',
                templateSelection: function(response) {
                    return response.text;
                },
            });

        }(window.jQuery);
        
        $('body').on('click', '#submitForm', function(){
            let user_id = $('.userSelect').val();
            let start = $('input[name="start"]').val();
            let err = [];
            
            if (!user_id) {
                err.push('Nhân viên không được để trống');
            }
            if (!start) {
                err.push('Ngày áp dụng không được để trống');
            }

            if (err.length > 0) {
                $.each(err, function (k, v) { 
                    toastr.error(v);
                });

                return ;
            }

            var registerForm = $("#formData");
            var formData = registerForm.serialize();
            $('.loading').show();
            $.ajax({
                url: "{!! route('admin.unionfunds.store') !!}",
                type: "POST",
                headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                data: formData,
                success:function(response) {
                    if (response.status == 400) {
                        toastr.error(response.message);
                    } 

                    if (response.status == 200) {
                        toastr.success(response.message);
                        window.location.href = response.link;
                    }
                },
            }).always(function() {
                // NProgress.done();
                $('.loading').hide();
            });
        });
        
        
    </script>
@stop