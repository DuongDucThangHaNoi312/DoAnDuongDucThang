@extends('backend.master')
@section('title')
    {!! trans('system.action.list') !!} {!! trans('departments.label') !!}
@stop
@section('head')
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/iCheck/all.css') !!}"/>
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
            Nhóm nhân viên
            <small>{!! trans('system.action.list') !!} - {{ $department->name }}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.departments.index') !!}">Nhóm nhân viên</a></li>
        </ol>
    </section>
    <section class="content overlay">
        <div class="row">
            <div class="col-md-6">
                @if (!Auth::user()->hasRole('LEADER'))
                    <a href="{!! route('admin.departments.create-team', $department->id) !!}" class='btn btn-primary btn-flat'>
                        <span class="glyphicon glyphicon-plus"></span>&nbsp;{!! trans('system.action.create') !!}
                    </a>
                @endif

            </div>
        </div>
        @if (count($teams) > 0)
            <div class="box">
                <div class="box-body no-padding">
                    <table class="table table-bordered table-hover" id="tableDepartment">
                        <thead>
                            <tr>
                                <th style="text-align: center; vertical-align: middle; width: 50px;">{!! trans('system.no.') !!}</th>
                                <th style="text-align: center; vertical-align: middle;">Tên nhóm</th>
                                <th style="text-align: center; vertical-align: middle;">Vai trò</th>
                                <th style="text-align: center; vertical-align: middle; width: 150px">Số nhân viên</th>
                                <th style="text-align: center; vertical-align: middle; width: 150px">Nhóm trưởng</th>
                                <th style="text-align: center; vertical-align: middle; width: 100px">{!! trans('system.action.label') !!}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($teams as $key => $item)
                                <tr>
                                    <td style="text-align: center; vertical-align: middle;">{{ $key + 1}}</td>
                                    <td style="text-align: center; vertical-align: middle;">{{ $item->name }}</td>
                                    <td style="text-align: center; vertical-align: middle;">{{ $item->description }}</td>
                                    <td style="text-align: center; vertical-align: middle;">{{ count($item->users) }}</td>
                                    <td style="text-align: center; vertical-align: middle;">{{ $item->user->fullname }}</td>
                                    <td style="text-align: center; vertical-align: middle;">
                                        <button data-url="{{ route('admin.departments.users-team', $item->id) }}" type="button" class="btn btn-xs btn-info btn-detail" data-toggle="modal" data-target="#exampleModal">
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        <!-- Modal -->
                                        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header" style="background-color: #3c8dbc; color: white">
                                                        <h4 class="modal-title" id="exampleModalLabel">Thành viên</h4>
                                                    </div>
                                                    <div class="modal-body">
                                                        <table class="table table-bordered table-hover">
                                                            <thead>
                                                                <tr>
                                                                    <th style="text-align: center; vertical-align: middle; width: 100px;"></th>
                                                                    <th style="text-align: center; vertical-align: middle;">Ho tên</th>
                                                                    <th style="text-align: center; vertical-align: middle;">Email</th>
                                                                    <th style="text-align: center; vertical-align: middle;">Số điện thoại</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="html">

                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="modal-footer" style="text-align: center">
                                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Đóng</button>
                                                    </div>
                                                </div>  
                                            </div>
                                        </div>

                                        @if (!Auth::user()->hasRole('LEADER'))
                                            <a href="{{ route('admin.departments.edit-team', $item->id) }}" class="btn btn-xs btn-default">
                                                <i class="text-warning glyphicon glyphicon-edit"></i>
                                            </a>
                                            <a data-toggle="tooltip" title="Xóa" href="javascript:void(0)"
                                                link="{!! route('admin.departments.delete-team', $item->id) !!}"
                                                class="btn-confirm-del btn btn-default btn-xs"><i
                                                            class="text-danger glyphicon glyphicon-remove"></i></a>
                                        @endif
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
    <script>
        $('.btn-detail').on('click', function() {
            $('#html').html('');

            let get_url = $(this).data('url');
            let html = '';
            let html1 = '';

            $.get(get_url, function (response) {
                if (response.status == 'SUCCESS') {
                    html += `
                            <tr>
                                <td>Nhóm trưởng</td>
                                <td>${response.data.user.fullname}</td>
                                <td>${response.data.user.email}</td>
                                <td>${response.data.user.phone}</td>
                            </tr>
                        `;
                    $('#html').html(html1);
                    $.each(response.data.users_detail, function (key, value) {
                        if (response.data.user.id != value.id) {
                            html += `
                                <tr>
                                    <td></td>
                                    <td>${value.fullname}</td>
                                    <td>${value.email}</td>
                                    <td>${value.phone}</td>
                                </tr>
                            `;
                        }
                        
                    })
                }
                $('#html').html(html);
            });
        });
    </script>
@stop