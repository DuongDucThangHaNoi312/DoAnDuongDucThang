@extends('backend.master')
@section('title')
    {!! trans('system.action.list') !!} {!! trans('locations.label') !!}
@stop
@section('head')
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-switch/3.3.4/css/bootstrap3/bootstrap-switch.min.css" />
@stop
@section('content')
    <section class="content-header">
        <h1>
            {!! trans('locations.label') !!}
            <small>{!! trans('system.action.list') !!}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.locations.index') !!}">{!! trans('locations.label') !!}</a></li>
        </ol>
    </section>
    <section class="content overlay">
        <div class="box box-default">
            <div class="box-header with-bprovider">
                <h3 class="box-title">{!! trans('system.action.filter') !!}</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                </div>
            </div>
            <div class="box-body">
                {!! Form::open([ 'url' => route('admin.locations.index'), 'method' => 'GET', 'role' => 'search' ]) !!}
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            {!! Form::label('status', trans('system.status.label')) !!}
                            {!! Form::select('status', [-1 => trans('system.dropdown_all'), 0 => trans('system.status.deactive'), 1 => trans('system.status.active')], Request::input('status'), ['class' => 'form-control'])!!}
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            {!! Form::label('page_num', trans('system.page_num')) !!}
                            {!! Form::select('page_num', [ 10 => '10' . trans('system.items'), 20 => '20' . trans('system.items'), 50 => '50' . trans('system.items') , 100 => '100' . trans('system.items'), 500 => '500' . trans('system.items') ], Request::input('page_num', 20), ['class' => 'form-control select2',  "style" => "width: 100%;"]) !!}
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            {!! Form::label('filter', trans('system.action.label')) !!}
                            <button type="submit" class="btn btn-default btn-flat" style="display: block;">
                                <span class="glyphicon glyphicon-search"></span>&nbsp; {!! trans('system.action.search') !!}
                            </button>
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 text-right">
                {!! $provinces->appends( Request::except('page') )->render() !!}
            </div>
        </div>
        @if (count($provinces) > 0)
            <div class="box">
                <div class="box-header">
                    <?php $i = (($provinces->currentPage() - 1) * $provinces->perPage()) + 1; ?>
                    <form class="form-inline">
                        <div class="form-group">
                            {!! trans('system.show_from') !!} {!! $i . ' ' . trans('system.to') . ' ' . ($i - 1 + $provinces->count()) . ' ( ' . trans('system.total') . ' ' . $provinces->total() . ' )' !!}
                        </div>
                    </form>
                </div>
                <div class="box-body no-padding">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th style="text-align: center; vertical-align: middle;">{!! trans('system.no.') !!}</th>
                                <th style="vertical-align: middle;"> {!! trans('locations.label') !!} </th>
                                <th style="text-align: center; vertical-align: middle;"> Số Quận/Huyện </th>
                                <th style="text-align: center; vertical-align: middle;"> {!! trans('system.status.label') !!} </th>
                                <th style="text-align: center; vertical-align: middle;"> {!! trans('system.action.label') !!} </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($provinces as $item)
                                <tr>
                                    <td style="text-align: center; vertical-align: middle;">{!! $i++ !!}</td>
                                    <td style="text-align: justify; vertical-align: middle;">
                                        {!! HTML::link( route('admin.locations.show', $item->id), $item->name, array('class' => '', 'title' => trans('system.action.detail'))) !!}
                                    </td>
                                    <td style="text-align: center; vertical-align: middle;">
                                        {!! $item->districts()->where('districts.status', 1)->count() !!}
                                    </td>
                                    <td style="text-align: center; vertical-align: middle;">
                                        <input type="checkbox" data-on-text="{!! trans('system.status.short_visible') !!}" data-off-text="{!! trans('system.status.invisible') !!}" data-size="mini" class="my-checkbox" value="{!! $item->id !!}" @if($item->status) checked @endif />
                                    </td>
                                    <td style="text-align: center; vertical-align: middle;">
                                        <a href="{!! route('admin.locations.show', $item->id) !!}" class="text-success"><i class="fa fa-eye"></i> {!! trans('system.action.detail') !!} </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="alert alert-info"> {!! trans('system.no_record_found') !!}</div>
        @endif
    </section>
@stop
@section('footer')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-switch/3.3.4/js/bootstrap-switch.min.js"></script>
    <script>
        !function ($) {
            $(function() {
                $('.my-checkbox').bootstrapSwitch().on('switchChange.bootstrapSwitch', function(event, state) {
                    var tmp = $(this);
                    box1 = new ajaxLoader('body', {classOveride: 'blue-loader', bgColor: '#000', opacity: '0.3'});
                    $.ajax({
                        url: "{!! route('admin.locations.update') !!}",
                        type: 'POST',
                        data: { province_id: $(this).attr('value'), status: state },
                        headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                        datatype: 'json',
                        success: function(data) {
                            toastr.success(data.message, "{!! trans('system.info') !!}");
                        },
                        error: function(obj, status, err) {
                            var error = $.parseJSON(obj.responseText);
                            toastr.error(error.message, '{!! trans('system.have_an_error') !!}');
                            tmp.bootstrapSwitch('state', false, true);
                        }
                    }).always(function() {
                        if(box1) box1.remove();
                    });
                });
            });
        }(window.jQuery);
    </script>
@stop
