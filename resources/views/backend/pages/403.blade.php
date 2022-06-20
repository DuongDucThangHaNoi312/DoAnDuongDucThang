@extends('backend.master')
@section('title')
    Access Denied
@stop
@section('content')
    <section class="content-header">
        <h1>
            Thông báo
            <small>Access Denied</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
        </ol>
    </section>
    <section class="content overlay">
        <div class="box box-default">
            <div class="box-body">
                Bạn không có quyền truy nhập nội dung này.
            </div>
        </div>
    </section>
@stop
@section('footer')
@stop
