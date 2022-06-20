@extends('backend.master')
@section('title')
    {!! trans('system.action.detail') !!} - {!! trans('locations.label') !!}
@stop
@section('head')
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/sortable/app.css') !!}">
@stop
@section('content')
    <section class="content-header">
        <h1>
            {!! trans('locations.label') !!}
            <small>{!! trans('system.action.detail') !!}</small>
            {!! $province->name !!}
            <small>
            @if($province->status)
                <label class="label label-success">
                    {!! trans('system.status.active') !!}
                </label>
            @else
                <label class="label label-default">
                    {!! trans('system.status.deactive') !!}
                </label>
            @endif
            </small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.locations.index') !!}">{!! trans('locations.label') !!}</a></li>
        </ol>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-2">
            </div>
            <div class="col-md-4">
                <div>
                    <div class="layer title">Quận/huyện HIỂN THỊ</div>
                    <ul id="used" class="block__list block__list_words">
                        @foreach($showed as $key => $value)
                            <li value="{!! $key !!}">{!! $value !!}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="col-md-4">
                <div>
                    <div class="layer title">Quận/huyện ẨN</div>
                    <ul id="unuse" class="block__list block__list_tags">
                        @foreach($hidden as $key => $value)
                            <li value="{!! $key !!}">{!! $value !!}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 text-center">
                {!! HTML::link(route( 'admin.locations.index' ), trans('system.action.cancel'), array('class' => 'btn btn-danger btn-flat'))!!}
                {!! Form::submit(trans('system.action.save'), array('class' => 'btn btn-primary btn-flat')) !!}
            </div>
        </div>
    </section>
@stop
@section('footer')
    <script src="{!! asset('assets/backend/plugins/sortable/sortable.js') !!}"></script>
    <script type="text/javascript">
        $(function  () {
            // Grouping
            var used = document.getElementById("used");
            Sortable.create(used, { group: "props" });
            var unuse = document.getElementById("unuse");
            Sortable.create(unuse, { group: "props" });

            $("input[type='submit']").click(function(event) {

                box1 = new ajaxLoader('body', {classOveride: 'blue-loader', bgColor: '#000', opacity: '0.3'});
                var values = new Array();
                $("ul#used li").each(function(index, el) {
                    if ($(this).attr('style') == undefined) {
                        values.push($(this).attr('value'));
                    }
                });

                $.ajax({
                    url: "{!! route('admin.locations.store') !!}",
                    data: { 'province': {!! $province->id !!}, ids: JSON.stringify(values) },
                    type: 'POST',
                    datatype: 'json',
                    headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                    success: function(res) {
                        if(res.error)
                            alert(res.message);
                        else
                            window.location.href = res.message;

                    },
                    error: function(obj, status, err) {
                        var error = $.parseJSON(obj.responseText);
                        toastr.error(error.message, '{!! trans('system.info') !!}');
                    }
                }).always(function() {
                    if(box1) box1.remove();
                });
            });
        });
    </script>
@stop
