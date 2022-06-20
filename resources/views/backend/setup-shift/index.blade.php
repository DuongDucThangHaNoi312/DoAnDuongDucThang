@extends('backend.master')
@section('title')
    {!! trans('system.action.list') !!} Ca làm
@stop

@section('head')
    
@stop

@section('content')
    <section class="content-header">
        <h1>
            Ca làm việc
            <small>{!! trans('system.action.list') !!}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.setupshifts.index') !!}">Ca làm việc</a></li>
        </ol>
    </section>
    <section class="content overlay">
        <div class="row">
            <div class="col-md-2">
                <a href="{!! route('admin.setupshifts.create') !!}" class="btn btn-primary btn-flat">
                    <span class="glyphicon glyphicon-plus"></span>&nbsp;{!! trans('system.action.create') !!}
                </a>
            </div>
            <div class="col-md-10">
                
            </div>
        </div>
        @if (count($items) > 0)
            <div class="box">
                <div class="box-header" style="padding: 0">
                </div>
                <div class="box-body no-padding">
                    <table class="table table-striped table-bordered table-hover" id="">
                        <thead>
                            <tr>
                                <th style="text-align: center; vertical-align: middle; width: 5%">{!! trans('system.no.') !!}</th>
                                <th style="text-align: center; vertical-align: middle;">Tên ca</th>
                                <th style="text-align: center; vertical-align: middle;">Loại ca</th>
                                <th style="text-align: center; vertical-align: middle;">Ký hiệu</th>
                                <th style="text-align: center; vertical-align: middle;">Mã màu</th>
                                <th style="text-align: center; vertical-align: middle;">Trạng thái</th>
                                <th style="text-align: center; vertical-align: middle; width: 10%" width="500px">{!! trans('system.action.label') !!}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $key => $item)
                                <tr>
                                    <td class="text-center">{{ $key + 1 }}</td>
                                    <td class="text-center">{{ $item->title }}</td>
                                    <td class="text-center">
                                        @switch ($item->type)
                                            @case (1)
                                                <span>Ngày</span>
                                                @break
                                            @case (2)
                                                <span>Hành chính</span>
                                                @break
                                            @case (3)
                                                <span>Đêm</span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td class="text-center">{{ $item->shortened_name }}</td>
                                    <td style="background: {{ $item->color }}; width: 70px; ">
                                    </td>
                                    <td class="text-center">
                                        @switch ($item->status)
                                            @case (1)
                                                <span class="badge btn-success">Hoạt động</span>
                                                @break
                                            @default
                                                <span class="badge btn-danger">Không hoạt động</span>
                                        @endswitch
                                    </td>
                                    
                                    <td class="text-center">
                                        <a data-toggle="tooltip" title="" href="{{ route('admin.setupshifts.edit', $item->id) }}" class="btn btn-xs btn-default" data-original-title="Cập nhật">
                                            <i class="text-warning glyphicon glyphicon-edit"></i>
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
    </section>
@stop

@section('footer')


<script type="text/javascript" charset="utf8"
        src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js"></script>
<script>
    
</script>
@stop