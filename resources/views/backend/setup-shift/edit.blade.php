@extends('backend.master')
@section('title')
    {!! trans('system.action.list') !!} Thêm mới ca làm
@stop

@section('head')
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/iCheck/all.css') !!}"/>

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
        <div class="box box-primary">
            <div class="box-header with-border" style="text-align: center">
                <h3 class="box-title">Thêm mới ca làm việc</h3>
            </div>
            {!! Form::open(['url' => route('admin.setupshifts.update', $item->id), 'method'=>'PUT']) !!}
                <div class="row" style="margin-top: 20px">
                    <div class="col-md-8">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6" style="text-align: right">
                                    <label for="">Tên ca <span class="text-danger">(*)</span></label>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" value="{{ !is_null($item->title) ? $item->title : ''}}" name="title" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6" style="text-align: right">
                                    <label for="">Ký hiệu <span class="text-danger">(*)</span></label>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" name="shortened_name" value="{{ !is_null($item->shortened_name) ? $item->shortened_name : ''}}" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6" style="text-align: right">
                                    <label for="">Mã màu <span class="text-danger">(*)</span></label>
                                </div>
                                <div class="col-md-6">
                                    <input type="color" name="color" value="{{ !is_null($item->color) ? $item->color : ''}}" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6" style="text-align: right">
                                    <label for="">Loại ca <span class="text-danger">(*)</span></label>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="type" id="type1" value="1" {{ $item->type == 1 ? 'checked' : '' }} required>
                                        <label class="form-check-label" for="type1">
                                          Ngày
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-check disabled">
                                        <input class="form-check-input" type="radio" name="type" id="type3" value="2" {{ $item->type == 2 ? 'checked' : '' }} required>
                                        <label class="form-check-label" for="type3">
                                          Hành chính
                                        </label>
                                      </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="type" id="type2" value="3" {{ $item->type == 3 ? 'checked' : '' }} required>
                                        <label class="form-check-label" for="type2">
                                          Đêm
                                        </label>
                                      </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    
                </div>
                <div class="form-group" style="text-align: center; margin-top: 10px;">
                    <label for="">Hoạt động </label>
                    <input type="checkbox" name="status" class="minimal" value="1" {{ $item->status == 1 ? 'checked' : '' }}>
                </div>
                <div class="form-group" style="text-align: center">
                    <a href="{{ route('admin.setupshifts.index') }}" class="btn btn-danger btn-sm">Trở lại</a>
                    <button type="submit" class="btn btn-primary btn-sm">Lưu lại</button>
                </div>
            {!! Form::close() !!}
        </div>
    </section>
@stop

@section('footer')

    <script src="{!! asset('assets/backend/plugins/iCheck/icheck.min.js') !!}"></script>

    <script type="text/javascript" charset="utf8"
        src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js"></script>
    <script>
        !function ($) {
            $(function () {
                $('input[type="checkbox"].minimal').iCheck({
                    checkboxClass: 'icheckbox_minimal-red'
                });
            });
        }(window.jQuery);
    </script>
@stop