@extends('backend.master')
@section('title')
    {!! trans('system.action.list') !!} {!! trans('appendixes.label') !!}
@stop
@section('head')
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/daterangepicker/daterangepicker.css') !!}" />
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/iCheck/all.css') !!}" />
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/select2/select2.min.css') !!}"/>
@stop
@section('content')
    <section class="content-header">
        <h1>
            {!! trans('appendixes.label') !!}
            <small>{!! trans('system.action.list') !!}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.appendixes.index') !!}">{!! trans('appendixes.label') !!}</a></li>
        </ol>
    </section>
    <section class="content overlay">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">{!! trans('system.action.filter') !!}</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                </div>
            </div>
            <div class="box-body">
                {!! Form::open(['url' =>route('admin.appendixes.index') , 'role'=>'search', 'method' => 'GET']) !!}
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('name', trans('appendixes.name')) !!}
                            {!! Form::text('name', Request::input('name'), ['class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('update_range', trans('system.update_range')) !!}
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                {!! Form::text('date_range', Request::input('date_range'), ['class' => 'form-control pull-right date_range']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            {!! Form::label('status', trans('system.status.label')) !!}
                            {!! Form::select('status', [ -1 => trans('system.dropdown_all'), 0 => trans('system.status.noactive'), 1 => trans('system.status.active') ], Request::input('status'), ['class' => 'form-control select2'])!!}
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            {!! Form::label('page_num', trans('system.page_num')) !!}
                            {!! Form::select('page_num', [ 10 => '10' . trans('system.items'), 20 => '20' . trans('system.items'), 50 => '50' . trans('system.items') , 100 => '100' . trans('system.items'), 500 => '500' . trans('system.items') ], Request::input('page_num', 20), ['class' => 'form-control select2',  "style" => "width: 100%;"]) !!}
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            {!! Form::label('filter', trans('system.action.label')) !!}
                            <button type="submit" class="btn btn-default btn-flat">
                                <span class="glyphicon glyphicon-search"></span>&nbsp; {!! trans('system.action.search') !!}
                            </button>
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
        <div class="row">
            <div class="col-md-2">
                <div id="create-modal" class='btn btn-primary btn-flat' data-toggle="modal" data-target="#modal-appendix">
                    <span class="glyphicon glyphicon-plus"></span>&nbsp;{!! trans('system.action.create') !!}
                </div>
            </div>
            <div class="col-md-10 text-right">
                {!!  $appendixes->appends( Request::except('page') )->render() !!}
            </div>
        </div>
        @if (count($appendixes) > 0)
            <?php $labels = ['default', 'success', 'info', 'danger', 'warning']; ?>
            <div class="box">
                <div class="box-header">
                    <?php $i = (($appendixes->currentPage() - 1) * $appendixes->perPage()) + 1; ?>
                    <div class="form-inline">
                        <div class="form-group">
                            {!! trans('system.show_from') !!} {!! $i . ' ' . trans('system.to') . ' ' . ($i - 1 + $appendixes->count()) . ' ( ' . trans('system.total') . ' ' . $appendixes->total() . ' )' !!}
                            | <i>Chú giải: </i>&nbsp;&nbsp;
                            <span class="text-warning"><i class="glyphicon glyphicon-edit"></i>{!! trans('system.action.update') !!}</span>&nbsp;&nbsp;
                            <span class="text-danger"><i class="glyphicon glyphicon-remove"></i>{!! trans('system.action.delete') !!}</span>
                        </div>
                    </div>
                </div>
                <div class="box-body no-padding">
                    <table class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th style="text-align: center; vertical-align: middle;">{!! trans('system.no.') !!}</th>
                            <th style="text-align: center; vertical-align: middle;">{!! trans('appendixes.name') !!}</th>
                            <th style="text-align: center; vertical-align: middle;">{!! trans('appendixes.expense') !!}</th>
                            <th style="text-align: center; vertical-align: middle;">{!! trans('system.status.label') !!}</th>
                            <th style="text-align: center; vertical-align: middle; white-space: nowrap;">{!! trans('appendixes.created_at') !!}</th>
                            <th style="text-align: center; vertical-align: middle;">{!! trans('system.action.label') !!}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($appendixes as $item)
                            <tr>
                                <td style="text-align: center; vertical-align: middle;">{!! $i++ !!}</td>
                                <td style="vertical-align: middle;">
                                    <a>{!! $item->name !!}</a><br/>
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    {!! $item->expense !!}<br/>
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    @if($item->status == 0)
                                        <span class="label label-danger"><span class='glyphicon glyphicon-remove'></span></span>
                                    @elseif($item->status == 1)
                                        <span class="label label-success"><span class='glyphicon glyphicon-ok'></span></span>
                                    @endif
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    {!! $item->created_at->format('d-m-Y') !!}
                                </td>
                                <td style="text-align: center; vertical-align: middle; white-space: nowrap;">&nbsp;&nbsp;
                                    <a id="editModalBtn"
                                       class="btn btn-xs btn-default"
                                       data-toggle="modal"
                                       data-target="#modal-appendix"
                                       data-id="{!! $item->id !!}"
                                       link="{!! route('admin.appendixes.edit', $item->id) !!}">
                                        <i class="text-warning glyphicon glyphicon-edit"></i>
                                    </a>&nbsp;&nbsp;
                                    <a href="javascript:void(0)"
                                       link="{!! route('admin.appendixes.destroy', $item->id) !!}"
                                       class="btn-confirm-del btn btn-default btn-xs">
                                        <i class="text-danger glyphicon glyphicon-remove"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="alert alert-info">{!! trans('system.no_record_found') !!}</div>
        @endif
        @include('backend.appendixes._form')
    </section>
@stop
@section('footer')
    <script src="{!! asset('assets/backend/plugins/select2/select2.full.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/input-mask/jquery.inputmask.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/iCheck/icheck.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/daterangepicker/moment.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/daterangepicker/daterangepicker.js') !!}"></script>
    <script>
        !function ($) {
            $(function(){
                $(".select2").select2({width: '100%'});
                $('input[type="checkbox"].minimal').iCheck({
                    checkboxClass: 'icheckbox_minimal-blue'
                });
                $('input[type="checkbox"].minimal-red').iCheck({
                    checkboxClass: 'icheckbox_minimal-red'
                });
                $("input[name='expense']").inputmask({
                    'alias': 'decimal',
                    'groupSeparator': ',',
                    'autoGroup': true,
                    'min': 0,
                    max: 999999999.99,
                    'digits': 0,
                    'removeMaskOnSubmit': true
                });
                $('.date_range').daterangepicker({
                    autoUpdateInput: false,
                    "locale": {
                        "format": "DD/MM/YYYY",
                        "separator": " - ",
                        "applyLabel": "Áp dụng",
                        "cancelLabel": "Huỷ bỏ",
                        "fromLabel": "Từ ngày",
                        "toLabel": "Tới ngày",
                        "customRangeLabel": "Custom",
                        "weekLabel": "W",
                        "daysOfWeek": [
                            "CN",
                            "T2",
                            "T3",
                            "T4",
                            "T5",
                            "T6",
                            "T7"
                        ],
                        "monthNames": [
                            "Thg 1",
                            "Thg 2",
                            "Thg 3",
                            "Thg 4",
                            "Thg 5",
                            "Thg 6",
                            "Thg 7",
                            "Thg 8",
                            "Thg 9",
                            "Thg 10",
                            "Thg 11",
                            "Thg 12"
                        ],
                        "firstDay": 1
                    },
                    ranges: {
                        'Hôm nay': [moment(), moment()],
                        'Hôm qua': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        '7 ngày trước': [moment().subtract(6, 'days'), moment()],
                        '30 ngày trước': [moment().subtract(29, 'days'), moment()],
                        'Tháng này': [moment().startOf('month'), moment()],
                        'Tháng trước': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    },
                    "alwaysShowCalendars": true,
                    maxDate: moment(),
                    minDate: moment().subtract(1, "years"),
                }, function(start, end, label) {
                    $('.date_range').val(start.format('DD/MM/YYYY') + " - " + end.format('DD/MM/YYYY'));
                });

                $('body').on('click', '#create-modal', function (e) {
                    e.preventDefault();
                    $('#modal-appendix-label').find('i').removeClass('glyphicon-edit')
                    $('#modal-appendix-label').find('i').addClass('glyphicon-plus')
                    $('#modal-appendix-label').find('span').html("{!! trans('system.action.create') !!}");
                    // $('#modal-appendix').modal('show');
                    $('.text-danger').find('strong').html('')
                    $('#appendix-id').val('');
                    $('#form-appendix').trigger("reset");
                });

                $('body').on('click', '#editModalBtn', function (e)  {
                    e.preventDefault();
                    $('#modal-appendix-label').find('span').html("{!! trans('system.action.update') !!}");
                    $('#modal-appendix-label').find('i').removeClass('glyphicon-plus')
                    $('#modal-appendix-label').find('i').addClass('glyphicon-edit')
                    $('.text-danger').find('strong').html('')
                    let id = $(this).attr('data-id');
                    let url = $(this).attr('link');
                    console.log('id', url)
                    $.ajax({
                        type : 'get',
                        url  : url,
                        data : {'id':id},
                        success:function(res){
                            $('#appendix-id').val(res.data.id);
                            $("input[name='name']").val(res.data.name)
                            $("input[name='expense']").val(res.data.expense)
                            $("input[name='description']").val(res.data.description)
                            $("input[name='status']").val(res.data.status)
                            $('#modal-appendix').modal('show');
                        }
                    });
                })
                let base_url = {!! json_encode(url('/')) !!} + "/admin/appendixes"
                $('#submitForm').on('click', function(){
                    let tagForm = $("#form-appendix");
                    let formData = tagForm.serialize();
                    let id = $('#appendix-id').val()
                    $( '#name-error').html("");
                    $( '#expense-error').html("");
                    let method = id ? 'PUT' : ''
                    let url = id ? base_url + '/' + id : base_url
                    $.ajax({
                        url: url,
                        type:'POST',
                        dataType: 'json',
                        data: formData + '&_method=' + method,
                        headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                        success:function(res) {
                            console.log('validate err', res.errors);
                            if(res.errors) {
                                if(res.errors.name){
                                    $('#name-error').html( res.errors.name );
                                }
                                if(res.errors.expense){
                                    $('#expense-error').html( res.errors.expense )
                                }
                                return false
                            }
                            if(res.data) {
                                location.reload();
                                $('#form-appendix').trigger('reset')
                                $("#modal-appendix").modal("hide");
                                toastr.success(res.message, "{!! trans('system.info') !!}")
                            } else {
                                toastr.error(res.message, "{!! trans('system.have_error') !!}")
                            }
                        },
                    });
                });
                function getAppendixes() {

                    $.ajax({
                        url: base_url,
                        type:'GET',
                        data: { }
                    }).done(function(data){

                    });
                }
            });
        }(window.jQuery);
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": false,
            "progressBar": false,
            "positionClass": "toast-top-center",
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
        }
    </script>
@stop