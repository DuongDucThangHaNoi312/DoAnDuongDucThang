@extends('backend.master')
@section('title')
    {!! trans('system.action.create') !!} - Chế độ con nhỏ
@stop
@section('head')
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/select2/select2.min.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/iCheck/all.css') !!}"/>
    <link rel="stylesheet" type="text/css"
          href="{!! asset('assets/backend/plugins/bootstrap-datepicker/css/bootstrap-datepicker.css') !!}"/>
    
@stop
@section('content')
    <section class="content-header">
        <h1>
            Chế độ con nhỏ
            <small>{!! trans('system.action.edit') !!}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.newborns.index') !!}">Chế độ con nhỏ</a></li>
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
            <div>
          
                <label>Nhân viên <span style="color: red">*</span></label>
                <input type="text" name="" id="" value="{!! $newborn->user->fullname ?? '' !!}" class="form-control" disabled>

            </div>
            <div style="margin-top: 10px;">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{!! trans('overtimes.start_date') !!} <span
                                        style="color: red">*</span></label>
                            <div class='input-group'>
                                {!! Form::text('start', old('start', date('d/m/Y', strtotime($newborn->start))), ['class' => 'form-control datepicker start_date','id'=>'start_date','autocomplete'=>'off']) !!}
                                <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                      </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group ">
                            <label>{!! trans('overtimes.end_date') !!} <span style="color: red">*</span></label>

                            <div class='input-group'>
                                {!! Form::text('end', old('end', date('d/m/Y', strtotime($newborn->end))), ['class' => 'form-control datepicker','id'=>'end_date','autocomplete'=>'off']) !!}
                                <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div style="margin-top: 10px">
                <label for="">Số giờ làm việc <span style="color: red">*</span></label>
                {!! Form::text('time', old('time', $time), ['class' => 'form-control inputmask time']) !!}
            </div>
            <div style="margin-top: 10px">
                {!! Form::label(trans('system.desc') ) !!}
                {!! Form::textarea('note', old('note', $note), ['rows' => 4, 'class' => 'form-control note']) !!}
            </div>
            
            
            {{-- <div align="center" style="margin-top: 30px; font-weight: 600;">
                <span>
                    {!! Form::checkbox('type', 1, old('type', 1), [ 'class' => 'minimal-red ' ]) !!}
                    Giờ làm việc
                </span>
                
            </div> --}}
            <div style="margin-top: 50px" align="center">
                {!! HTML::link(route( 'admin.newborns.index' ), trans('system.action.cancel'), ['class' => 'btn btn-danger btn-flat']) !!}
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
                    url: '{!! route("admin.newborns.searchUser") !!}',
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

        $('.userSelect').on('change', function () {
            let user_id = $(this).val();

            $.ajax({
                type: "GET",
                url: "{!! route('admin.newborns.check') !!}",
                data: {
                    userId: user_id
                },
                dataType: "json",
                success: function (response) {
                    if (response.status == 400) {
                        toastr.warning(response.message);
                    }
                    if (response.status == 200) {
                        $.each(response.message, function (k, v) { 
                            toastr.warning(v);
                        });
                    }
                }
            });
        })
        
        $('body').on('click', '#submitForm', function(){
            let time = $('input[name="time"]').val();
            let user_id = $('.userSelect').val();
            let start = $('input[name="start"]').val();
            let end = $('input[name="end"]').val();
            let err = [];

            if (!time) {
                err.push('Số giờ làm việc không được để trống');
            }
            // if (!user_id) {
            //     err.push('Nhân viên không được để trống');
            // }
            if (!start) {
                err.push('Ngày bắt đầu không được để trống');
            }
            if (!end) {
                err.push('Ngày kết thúc không được để trống');
            }

            if (err.length > 0) {
                $.each(err, function (k, v) { 
                    toastr.error(v);
                });

                return ;
            }

            var registerForm = $("#formData");
            var formData = registerForm.serialize();
            $.ajax({
                url: `{!! route('admin.newborns.update', $newborn->id) !!}`,
                type: "PUT",
                headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                data: formData,
                success:function(response) {
                    if (response.status == 400) {
                        toastr.error(response.message);
                    } 

                    if (response.status == 200) {
                        toastr.success(response.message);
                        location.reload();
                    }
                },
            });
        });
        
        
    </script>
@stop