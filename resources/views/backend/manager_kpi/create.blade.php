@extends('backend.master')
@section('title')
    {!! trans('system.action.create') !!} - {!! trans('kpi.label') !!}
@stop
@section('head')
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/iCheck/all.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/select2/select2.min.css') !!}"/>
    <link rel="stylesheet" type="text/css"
          href="{!! asset('assets/backend/plugins/bootstrap-datepicker/css/bootstrap-datepicker.css') !!}"/>
    <style type="text/css">
        input[type=number]::-webkit-inner-spin-button {
            -webkit-appearance: none;
        }

        .fa-plus:before {
            content: "\f067";
        }
        #cancel{
            margin-bottom: 2%;
            background-color: #FFFFFF;
            margin-left: 80%;
            display: inline-block;
            border: 1px solid #0c0c0c;
            position: absolute;
            right: 2%;
            border-radius: 5px
        }
        .food{
            position: relative;
            margin-top: 2%;
        }
        #submitForm{
            background-color: #169BD5;
            width: 6%;
            border-radius: 5px;
            border: 1px solid #169BD5;
        }


    </style>
@stop
@section('content')
    <section class="content-header">
        <h1>
            {!! trans('kpi.label') !!}
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.targets.index') !!}">{!! trans('kpi.label') !!}</a></li>
        </ol>
    </section>
    <hr>
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
    <?php $labels = ['default', 'success', 'info', 'danger', 'warning']; ?>
    {{-- {!! Form::open(['role' => 'form', 'id'=>'title']) !!} --}}
    <div class="box">
        <div class="box-header">
            {!! Form::open([ 'url' => route('admin.targets.create'), 'method' => 'GET', 'role' => 'search' ]) !!}
            <div class="row">
                <div style="position: relative" class="col-md-12">
                    <div style="margin-left:80%;position: absolute;top: 33%;" class="col-md-2">
                        <h4 style="display: block;position: absolute;" class="card-title">{!! trans('kpi.kpi') !!}:</h4>
                        <select style="margin-left: 56%;width:63%;display: block " disabled="disabled"
                                name="timestamp[]" class="form-control">
                            <option value="{{ $year.'-'.$mc }}">{{ $mc.'/'.$year }}</option>
                        </select>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
        <div class="row">
            <div class="col-md-10 text-right">
                {!!  $users->appends( Request::except('page') )->render() !!}
            </div>
        </div>
        <div class="box-header">
            <?php $i = (($users->currentPage() - 1) * $users->perPage()) + 1; ?>
            <div class="form-inline">
                <div class="form-group">
                    {!! trans('system.show_from') !!} {!! $i . ' ' . trans('system.to') . ' ' . ($i - 1 + $users->count()) . ' ( ' . trans('system.total') . ' ' . $users->total() . ' )' !!}
                    | <i>Chú giải:</i>&nbsp;&nbsp;
                    <span ><i class="fas fa-edit"></i>{!! trans('system.action.update') !!}</span>&nbsp;&nbsp;
                </div>
            </div>
        </div>
        <div class="box-body no-padding">
            <form action="javascript:void(0)" method="POST" id="title" class="form-login-gragon">
                {{ csrf_field() }}
                <table class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th style="text-align: center; vertical-align: middle;">{!! trans('system.no.') !!}</th>
                        <th style="text-align: center; vertical-align: middle;">{!! trans('kpi.code_staff') !!}</th>
                        <th style="text-align: center; vertical-align: middle;">{!! trans('kpi.name_staff') !!}</th>
                        <th style="text-align: center; vertical-align: middle;">{!! trans('kpi.kpi_value') !!}</th>
                        <th style="text-align: center; vertical-align: middle;">{!! trans('kpi.description') !!}</th>
                        <th style="text-align: center; vertical-align: middle;">{!! trans('system.action.label') !!}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($users as $item)
                        <?php
                        $i = 1;
                        $kpi = current($item->target->toArray())['kpi'];
                        ?>
                            <tr>
                                <td style="text-align: center; vertical-align: middle;">{!! $i++ !!}</td>
                                <td align="center" style="vertical-align: middle;">{!! $item->code!!}</td>
                                <td align="center" style="vertical-align: middle;" data-id="{{$item->id}}">{!! $item->fullname !!}
                                    <input type="hidden" data-id="{{ $item->id }}" name="user_id[]" value="{{ $item->id }}">
                                </td>
                                <td style="vertical-align: middle;">
                                    <input  type="number" data-kpi="{{ $kpi }}" value="{{ $kpi }}" class="form-control" name="kpi[]" id="kpi-value-{{$item->id}}" onkeydown="javascript: return event.keyCode === 8 ||event.keyCode === 46 ? true : !isNaN(Number(event.key))" min="{!! \App\Defines\KPI::KPI_MIN !!}" max="{!! \App\Defines\KPI::KPI_MAX !!}" @if($kpi!=null) readonly @endif required="required">
                                </td>
                                <td style="text-align: center; position:relative;vertical-align: middle;">
                                    <div class="form-group">
                                        <textarea style="position: absolute;width: 90%;top: 19%;height: 68%" class="form-control" rows="1" cols="4" name="description[]" id="description-{{$item->id}}" rows="3" @if($kpi!=null) readonly @endif>{{current($item->target->toArray())['description']}}</textarea>
                                    </div>
                                </td>
                                <td style="text-align: center; vertical-align: middle; white-space: nowrap;">&nbsp;&nbsp;
                                    @if($kpi!=null)
                                        <a data-toggle="tooltip" title="Cập nhật" class="  btn btn-default btn-xs editForm" data-action="{{current($item->target->toArray())['id']}}" data-kpi="{{ $kpi }}" data-user="{{ $item->id }}"><i class="fas fa-edit"></i></a>
                                    @endif
                                </td>
                            </tr>
                    @endforeach
                    <tr>
                        <td colspan="6">
                            <div class="row">
                                <div class="col-sm-12 food">
                                    <button style="margin-left: 87%" type="submit" class="btn btn-success" data-id="{{$item->id}}" data-kpi="{{$item->target['kpi']}}" id="submitForm">{!! trans('kpi.save') !!}</button>
                                    {!! HTML::link(route('admin.targets.index'), trans('system.action.cancel'), array('class' => 'btn btn-default btn-flat','id'=>'cancel'))!!}
                                </div>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <input type="hidden" name="month" id="month">
            {!! Form::close() !!}
        </div>
    </div>
@stop
@section('footer')
    <script src="{!! asset('assets/backend/plugins/iCheck/icheck.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/select2/select2.full.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/moment/min/moment-with-locales.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/input-mask/jquery.inputmask.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js') !!}"></script>
    <!-- Validate -->
    <script src="{{ asset('assets/backend/js/jquery.validate.min.js') }}"></script>
    <script>
        $(function () {
            $('#month_filter').datepicker({
                format: "mm/yyyy",
                viewMode: "months",
                minViewMode: "months",
                clearBtn: true,
                autoclose: true,
            });

            $(".select2").select2({
                width: '100%'
            });
        });
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": false,
            "progressBar": false,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };

        $('#title').validate({
            rules: {
                'kpi[]': {
                    required: true,
                },
            },
            messages: {
                'kpi[]': "<span class='text-danger'>{!! trans('kpi.kpi_error') !!}</span>",
            },
            submitHandler: function (form) {
                $.ajax({
                    url: "{{ route('admin.targets.store') }}",
                    type: 'POST',
                    data: new FormData(form),
                    dataType: 'JSON',
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function (data) {
                        console.log(data);
                        if (data.errors) {
                            toastr.error('{!!trans('staff_titles.errors') !!}');
                        }
                        if (data.success) {
                            toastr.success('{!!trans('staff_titles.add') !!}');
                            setTimeout(function () {
                                location.reload();
                            }, 1500);
                        }
                    }
                })
            }
        });

        $('#month_filter').change(function () {
            $('#month').val($(this).val());
        });
        $(".editForm").click(function (e) {
            var id = $(this).attr('data-user');
            $('#kpi-value-' + id).attr('readonly', false);
            $('#description-' + id).attr('readonly', false);
        });
        // $('body').on('click',"#submitForm",function (e) {
        /*$('#title').on('submit', function(e){
            e.preventDefault();
            // var user_id = $(this).attr('data-id');
            // var kpi = $('#kpi-value-' + user_id).val();
            // var description = $('#description-' + user_id).val();
            // var timestamp= $('#timestamp').val();
            $.ajax({
                url:"{{route('admin.targets.store')}}",
                type:'POST',
                // data:{user_id :user_id ,timestamp:timestamp,description:description,kpi:kpi},
                data: $(this).serialize(),
                headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                success:function(data) {
                    console.log(data);
                    if(data.errors) {
                        // if (data.errors.kpi) {
                        //     $('#kpi-error').html(data.errors.kpi[0]);
                        // }
                        toastr.error('{!!trans('staff_titles.errors') !!}');
                    }
                    if (data.success) {
                        toastr.success('{!!trans('staff_titles.add') !!}');

                    }
                }
            })
        });*/
        /*$(".editForm").click(function (e) {
            e.preventDefault();
            var id = $(this).attr('data-user');
            // var kpi = $(this).attr('data-kpi');
            // var user = $(this).attr('data-user');
            // var timestamp= $('#timestamp').val();
            $('#kpi-value-'+id).attr('readonly', false);
            // $.ajax({
            //     url:"{{route('admin.targets.store')}}",
            //     type:'POST',
            //     data:{id :id ,timestamp:timestamp,description:description,kpi:kpi},
            //     headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
            //     success:function(data) {

            //     }
            // })
        });*/




        {{--$(".select2").select2({--}}
        {{--    width: '100%',--}}
        {{--    height:'100%',--}}
        {{--    tags: true,--}}
        {{--    tokenSeparators: [',', ' '],--}}
        {{--    placeholder: '  {!! trans('kpi.dropdown_choice') !!} '--}}
        {{--});--}}

        {{--$("#month").change(function(){--}}
        {{--    $.ajax({--}}
        {{--        url: "{{ route('admin.load.kpi') }}?month_id=" + $(this).val(),--}}
        {{--        method: 'POST',--}}
        {{--        headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},--}}
        {{--        success: function(data) {--}}
        {{--            $('#staff_kpi').html(data.html);--}}
        {{--        }--}}
        {{--    });--}}
        {{--});--}}

    </script>
@stop
