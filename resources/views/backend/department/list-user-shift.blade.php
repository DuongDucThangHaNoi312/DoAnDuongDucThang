@extends('backend.master')
@section('title')
    Lịch ca nhân viên {!! trans('timekeeping.label') !!}
@stop
@section('head')
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/iCheck/all.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/select2/select2.min.css') !!}"/>
    <style>
        .error {
            width: 100%;
            height: 100px;
            line-height: 100px;
        }

        .text-size {
            font-size: 16px;
        }

        tr td {
            text-align: center;
        }

        b, strong {
            font-weight: 500;
        }

        tr th {
            text-align: center;
            vertical-align: middle;
        }

        thead tr th {
            white-space: nowrap;
            text-overflow: clip;
        }

        table {
            border-collapse: collapse;
            border-spacing: 0;
            
            border: 1px solid #ddd;
        }

        th, td {
            text-align: left;
            padding: 8px;
        }

        .sticky-col {
            position: -webkit-sticky;
            position: sticky;
            background-color: white;
            left: 0;
        }

    </style>
@stop
@section('content')
    <section class="content-header">
        <h1>
            Lịch ca nhân viên
            <small>{!! $department->name !!} - {{ $data['month'] }}/{{ $data['year'] }}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="#">Lịch ca nhân viên</a></li>
        </ol>
    </section>
    <section class="content overlay">
        <div class="box box-default">
            <div class="box-header with-bconsumer">
                <h3 class="box-title">{!! trans('system.action.filter') !!}</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                </div>
            </div>
            <div class="box-body">
                
                <form action="{!! route('admin.departments.getShift', ['departmentId' => $department->id, 'month' => $data['month'], 'year' => $data['year']]) !!}" method="GET">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="" style="display: block;">{!! trans('timekeeping.staff') !!}</label>
                                <input type="text" name="fullname" class="form-control">
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('filter', trans('system.action.label'), ['style' => 'display: block;']) !!}
                                <button type="submit" class="btn btn-primary btn-flat">
                                    <span class="glyphicon glyphicon-search"></span>&nbsp; {!! trans('system.action.search') !!}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    {{ \App\Models\Team::selectTeamByDepartment($department->id) }}
                    <select name="team_id" id="search" class="form-control" data-link="{{ route("admin.departments.getShift", ["departmentId" => $department->id, "month" => $data['month'], "year" => $data['year']]) }}">
                        <option value="">Nhóm</option>
                        @foreach (\App\Models\Team::selectTeamByDepartment($department->id) as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="box">
            <div class="box-body no-padding" style="overflow-x:auto;">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th rowspan="2" style="text-align: 5; vertical-align: middle;">{!! trans('system.no.') !!}</th>
                            <th rowspan="2" style="line-height: 5; padding: 0 100px" class="sticky-col">Họ tên</th>
                            <th rowspan="2" style="line-height: 5; padding: 0 20px">Mã nhân viên</th>
                            @if (count($getDays) > 0)
                                @foreach ($getDays as $key => $item)
                                    <th colspan="1" style="padding: 0 5px 10px">{{ $item }}</th>
                                @endforeach
                            @endif
                        </tr>
                        <tr>
                            @if (count($getDates) > 0)
                                @foreach ($getDates as $key => $item)
                                    <th style="padding: 0 5px 10px">{{ $item }}</th>
                                @endforeach
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @if (!Auth::user()->hasRole('LEADER'))
                            @if ($users > 0)
                                @foreach ($users as $key => $user)
                                    <tr class="hover">
                                        <td>{{ $key + 1 }}</td>
                                        <td style="text-align: left" class="sticky-col">{{ $user->fullname }}</td>
                                        <td>{{ $user->code }}</td>
                                        @foreach ($date as $i => $item)
                                            <td>
                                                {{ \App\Models\Shift::getShiftUser($item, $user->id) }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach    
                            @endif
                        @endif
                    </tbody>           
                </table>
                @if (count($users) == 0)
                <div class="text-center error">
                    <span class="text-size"><i class="fas fa-search"></i> {!! trans('timekeeping.no_data') !!}</span>
                </div>
                @endif
            </div>
        </div>
    </section>
@stop
@section('footer')
    <script>
        $(document).ready(function(){
            $(".hover").hover(function(){
                    $(this).css("background-color", "#9ad0ff");
                    $(this).find('.sticky-col').css("background-color", "#9ad0ff");

                }, function(){
                    $(this).css("background-color", "white");
                    $(this).find('.sticky-col').css("background-color", "white");
            });
        });
    </script>
    <script type="text/javascript">
        $('#search').on('change',function(){
            var value = $(this).val();
            var departmentId = '{{ $department->id }}';
            var month = '{{ $data["month"] }}';
            var year = '{{ $data["year"] }}';
            var url = $(this).data('link');
            $.ajax({
                type: 'GET',
                url: url,
                data: {
                    'team_id': value
                },
                success:function(data){
                    if (data == 1) {
                        location.reload();
                    } else {
                        $('tbody').html(data);
                    }
                }
            });
        })
        $.ajaxSetup({ headers: { 'csrftoken' : '{{ csrf_token() }}' } });
    </script>
@stop