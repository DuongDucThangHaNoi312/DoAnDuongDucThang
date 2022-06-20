@extends('backend.master')
@section('title')
    {!! trans('system.action.list') !!} {!! trans('departments.label') !!}
@stop
@section('head')
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/iCheck/all.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/select2/select2.min.css') !!}"/>

    <style>
        .dataTables_filter {
            display: none;
        }

        table {
            width: 100% !important;
        }
    </style>
@stop
@section('content')
    <section class="content-header">
        <h1>
            Thêm mới
            <small>nhóm nhân viên - {{ $team->department->name }}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.departments.index') !!}">Nhóm nhân viên</a></li>
        </ol>
    </section>
    <section class="content overlay">
        <div class="box">
            <div class="box-body no-padding">
                {!! Form::open([ 'url' => route('admin.departments.save-edit-team', $team->id), 'method' => 'POST']) !!}
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group" style="margin-left: 50px">
                                <label for="">Tên nhóm <span class="text-danger">(*)</span></label>
                                <input type="text" name="name" value="{{ $team->name }}" id="" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Vai trò</label>
                                <input type="text" name="description" value="{{ $item->description }}" id="" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Thêm thành viên khác</label>
                                {{-- <input type="text" name="description" id="" class="form-control" 
                                    placeholder="Nhân viên phòng ban khác" style="width: 205px"> --}}
                                {{-- {!! Form::select('user_id', ['' => trans('system.dropdown_choice')], old('user_id'), ['id' =>'userSelect', 'class' => 'form-control select2 staffSelect', 'style' => 'width: 205px' ]) !!} --}}
                                <select name="" id="" class="form-control select2 userSelect" style="width: 205px"></select>

                                <button type="button" class="btn btn-sm btn-primary btn-flat btn-info-staff" style="float: right; width: 50px; height: 34px; margin-top: -34px">Thêm</button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8" style="margin-left: 50px">
                            <table class="table table-bordered table-hover" id="tableDepartment">
                                <thead>
                                    <tr>
                                        <th style="text-align: center; vertical-align: middle; width: 100px">Thành viên</th>
                                        <th style="text-align: center; vertical-align: middle;">Họ tên</th>
                                        <th style="text-align: center; vertical-align: middle; width: 150px">Nhóm trưởng</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody-user">
                                    @if (count($users) > 0)
                                        @foreach ($users as $item)
                                            <tr>
                                                <td style="text-align: center">
                                                    <input class="checkbox-{{ $item->id }}" {{ $item->id == $team->user_id ? 'disabled' : '' }}  {{ in_array($item->id, $users_ids) ? 'checked' : '' }} type="checkbox" name="user_id[]" id="" value="{{ $item->id }}">
                                                </td>
                                                <td>{{ $item->fullname . ' - ' . $item->code }}</td>
                                                <td style="text-align: center">
                                                    <input data-id="{{ $item->id }}" {{ $item->id == $team->user_id ? 'checked' : '' }} type="radio" name="lead_user_id" id="" value="{{ $item->id }}" required>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8" style="text-align: center">
                            <a href="{{ route('admin.departments.list-team', $team->department_id) }}" class="btn btn-danger btn-sm">Trở lại</a>
                            <button type="submit" class="btn btn-primary btn-sm">Lưu lại</button>
                        </div>
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
        
    </section>
@stop
@section('footer')
    <script src="{!! asset('assets/backend/plugins/select2/select2.full.min.js') !!}"></script>

    <script>
        $(function() {
            $(".select2").select2({
                width: '80%',
            });

            function checkbox() {
                $('input[name="lead_user_id"]').on('change', function() {
                    $('input[name="user_id[]"]').removeAttr('disabled', 'disabled');
                    let id = $(this).data('id');
                    if (id) {
                        $('.checkbox-' + id).attr('disabled', 'disabled')
                    }
                })
            }
            checkbox();

            $(".userSelect").select2({
                ajax: {
                    url: '{!! route("admin.team.searchUser", $team->department_id) !!}',
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
                placeholder: 'Tìm kiếm nhân viên',
                templateSelection: function(response) {
                    return response.text;
                },
            });

            $('.btn-info-staff').on('click', function() {
                let id = $('.userSelect').val();
                let text = $('.userSelect option:selected').text();
                if (id && text) {
                    let html = `
                        <tr>
                            <td style="text-align: center">
                                <input class="checkbox-${id}" type="checkbox" name="user_id[]" id="" value="${id}">
                            </td>
                            <td>${text}</td>
                            <td style="text-align: center">
                                <input data-id="${id}" type="radio" name="lead_user_id" id="" value="${id}" required>
                            </td>
                        </tr>
                    `;
                    $('#tbody-user').append(html);
                    checkbox();
                }
            })

        });
    </script>
@stop