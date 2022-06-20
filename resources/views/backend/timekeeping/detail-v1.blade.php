@extends('backend.master')
@section('title')
    {!! trans('timekeeping.detail') !!} {!! trans('timekeeping.label') !!}

@stop
@section('head')
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/select2/select2.min.css') !!}"/>

    <style>  
        .error {
            width: 100%;
            height: 100px;
            line-height: 100px;
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

        .flex-container {
            display: flex;
        }

        .flex-container > div {
            width: 50px;
            height: 20px;
            margin: 10px;
            padding: 10px;
        }

        .note {
            margin-top: 10px;
            margin-right: 10px;
        }

        h4 {
            font-size: 16px;
        }

        h3 {
            font-size: 18px;
        }
        /* Absolute Center Spinner */
        .loading {
            position: fixed;
            z-index: 999;
            height: 2em;
            width: 2em;
            overflow: visible;
            margin: auto;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
        }
        
        /* Transparent Overlay */
        .loading:before {
            content: '';
            display: block;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.3);
        }
        
        /* :not(:required) hides these rules from IE9 and below */
        .loading:not(:required) {
            /* hide "loading..." text */
            font: 0/0 a;
            color: transparent;
            text-shadow: none;
            background-color: transparent;
            border: 0;
        }
        
        .loading:not(:required):after {
            content: '';
            display: block;
            font-size: 10px;
            width: 1em;
            height: 1em;
            margin-top: -0.5em;
            -webkit-animation: spinner 1500ms infinite linear;
            -moz-animation: spinner 1500ms infinite linear;
            -ms-animation: spinner 1500ms infinite linear;
            -o-animation: spinner 1500ms infinite linear;
            animation: spinner 1500ms infinite linear;
            border-radius: 0.5em;
            -webkit-box-shadow: rgba(0, 0, 0, 0.75) 1.5em 0 0 0, rgba(0, 0, 0, 0.75) 1.1em 1.1em 0 0, rgba(0, 0, 0, 0.75) 0 1.5em 0 0, rgba(0, 0, 0, 0.75) -1.1em 1.1em 0 0, rgba(0, 0, 0, 0.5) -1.5em 0 0 0, rgba(0, 0, 0, 0.5) -1.1em -1.1em 0 0, rgba(0, 0, 0, 0.75) 0 -1.5em 0 0, rgba(0, 0, 0, 0.75) 1.1em -1.1em 0 0;
            box-shadow: rgba(0, 0, 0, 0.75) 1.5em 0 0 0, rgba(0, 0, 0, 0.75) 1.1em 1.1em 0 0, rgba(0, 0, 0, 0.75) 0 1.5em 0 0, rgba(0, 0, 0, 0.75) -1.1em 1.1em 0 0, rgba(0, 0, 0, 0.75) -1.5em 0 0 0, rgba(0, 0, 0, 0.75) -1.1em -1.1em 0 0, rgba(0, 0, 0, 0.75) 0 -1.5em 0 0, rgba(0, 0, 0, 0.75) 1.1em -1.1em 0 0;
        }
        
        /* Animation */
        
        @-webkit-keyframes spinner {
            0% {
            -webkit-transform: rotate(0deg);
            -moz-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
            -o-transform: rotate(0deg);
            transform: rotate(0deg);
            }
            100% {
            -webkit-transform: rotate(360deg);
            -moz-transform: rotate(360deg);
            -ms-transform: rotate(360deg);
            -o-transform: rotate(360deg);
            transform: rotate(360deg);
            }
        }
        @-moz-keyframes spinner {
            0% {
            -webkit-transform: rotate(0deg);
            -moz-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
            -o-transform: rotate(0deg);
            transform: rotate(0deg);
            }
            100% {
            -webkit-transform: rotate(360deg);
            -moz-transform: rotate(360deg);
            -ms-transform: rotate(360deg);
            -o-transform: rotate(360deg);
            transform: rotate(360deg);
            }
        }
        @-o-keyframes spinner {
            0% {
            -webkit-transform: rotate(0deg);
            -moz-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
            -o-transform: rotate(0deg);
            transform: rotate(0deg);
            }
            100% {
            -webkit-transform: rotate(360deg);
            -moz-transform: rotate(360deg);
            -ms-transform: rotate(360deg);
            -o-transform: rotate(360deg);
            transform: rotate(360deg);
            }
        }
        @keyframes spinner {
            0% {
            -webkit-transform: rotate(0deg);
            -moz-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
            -o-transform: rotate(0deg);
            transform: rotate(0deg);
            }
            100% {
            -webkit-transform: rotate(360deg);
            -moz-transform: rotate(360deg);
            -ms-transform: rotate(360deg);
            -o-transform: rotate(360deg);
            transform: rotate(360deg);
            }
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
            {!! trans('timekeeping.label') !!}
            <small>{!! trans('timekeeping.detail') !!}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.timekeeping.index') !!}">{!! trans('timekeeping.label') !!}</a></li>
        </ol>
    </section>
    <section class="content overlay">
        
        <div class="box box-default">
            @if (Auth::user()->hasRole('TP') || Auth::user()->hasRole('system'))
                <div class="box-header with-bconsumer">
                    <h3 class="box-title">{!! trans('system.action.filter') !!}</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    
                    <form action="{!! route('admin.timekeepings.detail', $detail->id) !!}" method="GET">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="" style="display: block;">{!! trans('timekeeping.staff') !!}</label>
                                    <input type="text" name="fullname" class="form-control" value="{{ Request::input('fullname')  }}">
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
            @endif
        </div>
        
        <div class="text-center">
            <h3>{!! trans('timekeeping.detail_title') !!} {{ $detail->month }}/{{ $detail->year }}</h3>
            <h4>{{ $detail->company->shortened_name }} - {{ $detail->department->name }}</h4>
            <h4 class="text-approved">
                @if ($detail->status == 'APPROVED')
                    Người duyệt: {{ $detail->userApproved->fullname }} <span class="label label-success" style="font-size: 14px">Đã chốt</span>
                @endif
            </h4>
        </div>
        <div class="row">
            <div class="col-md-8">
                {{-- @if (Auth::user()->hasRole('TP') || Auth::user()->hasRole('system'))
                    <a href="{{ route('admin.timekeepings.exportExcel', $detail->id) }}" class="btn btn-success">
                        <span class="far fa-file-excel fa-fw"></span>&nbsp;{{ trans('timekeeping.export_excel') }}
                    </a>
                    @if ($detail->status != 'APPROVED')
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#update">
                        <i class="fas fa-sync"></i> Đổ lại
                    </button>      
                    @endif
                     
                @endif --}}
                @permission('timekeeping.create')
                    <a href="{{ route('admin.timekeeping.exportExcel', $detail->id) }}" class="btn btn-success" target="_blank">
                        <span class="far fa-file-excel fa-fw"></span>&nbsp;{{ trans('timekeeping.export_excel') }}
                    </a>
                    <a href="{{ route('admin.timekeeping.exportExcel.log', $detail->id) }}" class="btn btn-success" target="_blank">
                        <span class="far fa-file-excel fa-fw"></span>&nbsp;{{ trans('timekeeping.export_excel_log') }}
                    </a>
                    @if ($detail->status != 'APPROVED')
                    <button type="button" class="btn btn-primary btn-chot" data-toggle="modal" data-target="#update">
                        <i class="fas fa-sync"></i> Đổ lại
                    </button>      
                    @endif
                    <a href="{!! route('admin.departments.calendar', $detail->department_id) !!}" target="_blank" class="btn btn-primary"><i class="fas fa-calendar"></i> Lịch làm việc</a>
                @endpermission
                
                @if (in_array(Auth::user()->code, \App\Define\Timekeeping::userApprovedTimekeeping()) && $detail->status != 'APPROVED')
                <button type="button" class="btn btn-primary btn-chot" data-toggle="modal" data-target="#approved">
                    <i class="fas fa-check"></i> Chốt công
                </button>      
                @endif
               

                <div class="modal fade" id="approved" tabindex="-1" role="dialog" aria-labelledby="approvedLabel"  aria-hidden="true" data-backdrop="static" data-keyboard="false">
                    <div class="modal-dialog" role="document" style="text-align: left">
                        <form>
                            @csrf
                            <div class="modal-content">
                                <div class="modal-header" style="background-color: #3c8dbc; color: white; text-align: center">
                                    <h3 class="modal-title" id="approvedLabel">Chốt công tháng {{ $detail->month.'/'.$detail->year }} {{ $detail->company->shortened_name }} - {{ $detail->department->name }}</h3>
                                </div>
                                <div class="modal-body text-center">
                                    <h4 style="color: red">Lưu ý bảng công đã chốt không thể chỉnh sửa và tính lại</h4>
    
                                </div>
                                <div class="modal-footer" style="text-align: center">
                                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Đóng</button>
                                    <button type="button" data-link="{!! route('admin.timekeeping.approved', $detail->id) !!}" class="btn btn-primary btn-sm btn-approved">Xác nhận</button>
                                </div>
                            </div>
                        </form>
                        
                    </div>
                </div>

                
                <div class="modal fade" id="update" tabindex="-1" role="dialog" aria-labelledby="updateLabel"  aria-hidden="true" data-backdrop="static" data-keyboard="false">
                    <div class="modal-dialog" role="document" style="text-align: left">
                        <div class="modal-content">
                            <div class="modal-header" style="background-color: #3c8dbc; color: white; text-align: center">
                                <h4 class="modal-title" id="updateLabel">Xác nhận</h4>
                            </div>
                            <div class="modal-body modal-body11">
                                Bạn có muốn đổ lại bảng tính công
                            </div>
                            <div class="modal-footer" style="text-align: center">
                                <button type="button" class="btn btn-danger btn-sm btn-flat" data-dismiss="modal">Đóng</button>
                                <button type="button" class="btn btn-primary btn-edit11 btn-sm btn-flat" data-url="{{ route('admin.timekeeping.recalculate', $detail->id) }}">Lưu lại</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="box">
            <div class="box-header">
                @permission('timekeeping.create')
                    <div style="margin-left: 12px; font-size: 15px">
                        @if ($detail->department->type == \App\Define\Department::FUNCTIONAL_OFFICE)
                            <?php 
                                $thoiGianLam = \App\Models\WorkSchedule::where('department_id', $detail->department_id)
                                                ->first();
                            ?>
                            @if (!is_null($thoiGianLam))
                                Các ngày từ thứ 2 đến thứ 6: Buổi sáng {{ $thoiGianLam->from_morning.' - '.$thoiGianLam->to_morning }}, Buổi chiều {{ $thoiGianLam->from_afternoon.' - '.$thoiGianLam->to_afternoon }},
                                Thứ 7 {{ $thoiGianLam->type == 1 ? 'Làm tại nhà' : '' }}: Buổi sáng {{ $thoiGianLam->from_sa_morning.' - '.$thoiGianLam->to_sa_morning }}, Buổi chiều {{ $thoiGianLam->from_sa_afternoon.' - '.$thoiGianLam->to_sa_afternoon }}, Thời gian tính làm thêm: {{ $thoiGianLam->ot }}
                            @endif
                        @else  
                            <?php 
                                $thoiGianLam = \App\Models\ShiftTime::with('category')->where('department_id', $detail->department_id)
                                            
                                            ->get()->sortBy('category.id');
                            ?>
                            @if (count($thoiGianLam) > 0)
                                Thời gian làm việc#:
                                @foreach ($thoiGianLam as $item)
                                    {{ $item->category->shortened_name . ': ' . $item->time_in . ' - ' . $item->time_out . ', ' }}
                                @endforeach
                            @endif
                        @endif
                    </div>
                @endpermission
                
                <div class="flex-container">
                    <div style="background: red;"></div><span class="note">{!! trans('timekeeping.leave') !!}</span>
                    <div style="background: white; border: 0.5px solid"></div><span class="note">{!! trans('timekeeping.go_to_work') !!}</span>
                    <div style="background: silver;"></div><span class="note">{!! trans('timekeeping.a_half_of_day') !!}</span>
                    @if ($detail->department->type != \App\Define\Department::HOURS)
                        <div style="background: lime;"></div><span class="note">{!! trans('timekeeping.late') !!}</span>
                        <div style="background: yellow;"></div><span class="note">{!! trans('timekeeping.come_back') !!}</span>
                        <div style="background: #F433FF;"></div><span class="note">{!! trans('timekeeping.late_come_back') !!}</span>
                        <div style="background: olive;"></div><span class="note">{!! trans('timekeeping.forgot_timekeeping') !!}</span>
                    @endif
                    <div style="background: #FFA54F;"></div><span class="note">{!! trans('timekeeping.edit') !!}</span>

                </div>
            </div>
            <div class="box-body no-padding" style="overflow-x:auto;">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th rowspan="2"  style="line-height: 5">{!! trans('system.no.') !!}</th>
                            <th rowspan="2"  style="line-height: 5">{!! trans('timekeeping.code') !!}</th>
                            <th rowspan="2" class="sticky-col" style="line-height: 5; padding: 0 100px">{!! trans('timekeeping.staff') !!}</th>
                            
                            @if (count($getDays) > 0)
                            @foreach ($getDays as $key => $item)
                            <th colspan="1" style="padding: 0 5px 10px">{{ $item }}</th>
                            @endforeach
                            @endif

                            <th rowspan="2" style="line-height: 5">{!! trans('timekeeping.number_day') !!}</th>
                            <th colspan="2">{!! trans('timekeeping.total_number') !!}</th>
                            <th colspan="9">{!! trans('timekeeping.day_off_number') !!}</th>
                            @if ($detail->department->type == \App\Define\Department::DECLARATION_OFFICE)
                                <th colspan="3">Tổng theo ca</th>
                            @endif
                            <th rowspan="2" style="line-height: 5">{!! trans('timekeeping.total') !!}</th>
                            <th rowspan="2" style="line-height: 5; padding: 0 50px">Thao tác</th>
                        </tr>
                        <tr>
                            @if (count($getDates) > 0)
                            @foreach ($getDates as $key => $item)
                            <th style="padding: 0 5px 10px">{{ $item }}</th>
                            @endforeach
                            @endif

                            <th>{!! trans('timekeeping.hd') !!}</th>
                            <th>{!! trans('timekeeping.tv') !!}</th>
                            
                            <th>{!! trans('timekeeping.leave_salary') !!}</th>
                            <th>Nghỉ ốm</th>
                            <th>{!! trans('timekeeping.without_pay') !!}</th>
{{--                            <th>{!! trans('timekeeping.maternity') !!}</th>--}}
                            <th>Nghỉ cưới</th>
                            <th>Nghỉ hiếu</th>                           
                            <th>Nghỉ phép</th>
                            <th>Nghỉ công tác</th>
                            <th>Nghỉ lễ</th>
                            <th>Làm tại nhà</th>

                            @if ($detail->department->type == \App\Define\Department::DECLARATION_OFFICE)
                                <th>Ngày</th>                           
                                <th>Hành Chính</th>   
                                <th>Đêm</th>                          
                            @endif
                        </tr>

                    </thead>
                    <tbody>
                        @if (count($items) > 0)  
                        <?php $rowIndex = 1; ?> 
                        @foreach ($items as $key => $item)
                        <tr>
                            <td class="text-center">{{ $rowIndex++ }}</td>
                            <td class=""><span class="code">{{ $item->staff->code }}</span></td>
                            <td class="sticky-col"><span class="fullname">{{ $item->staff->fullname }}</span></td>
                            <?php $i = -1 ?>
                            @foreach ($item->detail as $k => $v)
                                <?php $i++; $color = ''; $check_th = \App\Define\Timekeeping::checkSaturday($k, $detail->department, $item->staff_id); ?>

                                @if ($v['edit'] == 1)
                                    <?php $color = '#FFA54F' ?>
                                @else 
                                    @switch($v['status'])
                                        @case (0)
                                            <?php $color = 'red' ?>
                                            @break
                                        @case(1)
                                            <?php $color = 'white' ?>
                                            @break
                                        @case(2)
                                            <?php $color = 'lime' ?>
                                            @break
                                        @case(3)
                                            <?php $color = 'yellow' ?>
                                            @break
                                        @case(4)
                                            <?php $color = 'olive' ?>
                                            @break
                                        @case(5)
                                            <?php $color = '#F433FF' ?>
                                        @case(10)
                                            <?php $color = 'silver' ?>
                                            @break
                                        @default
                                            
                                    @endswitch
                                @endif

                                <td data-date="{{ date('d-m-Y', $k) }}" data-fullname="{{ $item->staff->fullname }}" data-key="{{ $k }}" data-id="{{ $item->id }}"
                                    data-link="{{ route('admin.timekeepings.warning', $item->id) }}"
                                    data-url="{{ route('admin.timekeeping.update-timekeeping', $item->id) }}" class="{{ ( (!Auth::user()->hasRole('NV') || in_array("update", $moreActions)  ) && $detail->status != 'APPROVED' && $item->concurrent_contract != 1) ? 'update' : '' }} text-center {{ $k.'_'.$item->id }}" 
                                    data-toggle="tooltip" data-placement="top" title="{{ $v['time_check_in'] != 0 ? date('H:i:s', strtotime($v['time_check_in'])) : 0 }} - {{ $v['time_check_out'] != 0 ? date('H:i:s', strtotime($v['time_check_out'])) : 0 }}" 
                                    style="background: {{  $getDays[$i] == 'Sun' ? '#4dd7d7' : $color }}; cursor:pointer; padding: 0px" data-total-status="{!! $v['total'].'_'.$v['total_work'].'_'.$v['status'] !!}">
                                    
                                    @if ($item->timekeeping->department_id == $detail->department_id)
                                        @if ($detail->department->type == \App\Define\Department::DECLARATION_OFFICE)
                                                @if (!in_array($holiday, ['H', 'T']))
                                                    {{ $v['status'] == 1 ? $getShift[$v['shift']] : (in_array($v['status'], [2, 3, 4, 5, 10]) ? $getShift[$v['shift']] . '/2' : '') }}
                                                @endif
                                        @elseif ($detail->department->type == \App\Define\Department::FUNCTIONAL_OFFICE)
                                            {{ $v['status'] == 1 ? 'v' : (in_array($v['status'], [2, 3, 4, 5, 10]) ? 'v/2' : '') }}
                                        @else
                                            {{ $v['status'] == 1 ? 'v' : ($v['status'] == 10 ? 'v/2' : '') }}
                                        @endif
                                        {{ $v['day_off'] }}

                                    @else    
                                        @if ($item->timekeeping->department->type == \App\Define\Department::DECLARATION_OFFICE)
                                                @if (!in_array($holiday, ['H', 'T']))
                                                    {{ $v['status'] == 1 ? $getShift[$v['shift']] : (in_array($v['status'], [2, 3, 4, 5, 10]) ? $getShift[$v['shift']] . '/2' : '') }}
                                                @endif
                                        @elseif ($item->timekeeping->department->type == \App\Define\Department::FUNCTIONAL_OFFICE)
                                            {{ $v['status'] == 1 ? 'v' : (in_array($v['status'], [2, 3, 4, 5, 10]) ? 'v/2' : '') }}
                                        @else
                                            {{ $v['status'] == 1 ? 'v' : ($v['status'] == 10 ? 'v/2' : '') }}
                                        @endif
                                        {{ $v['day_off'] }}
                                    @endif
                                </td>
                            @endforeach
                            
                            <td class="text-center">{!! $item->total_day_request !!}</td>
                            <td class="text-center total_hd_{{ $item->id }}">{{ $item->total_hd }}</td>
                            <td class="text-center">{{ $item->total_tv }}</td>

                            <td class="text-center">{{ $item->nghi_70_luong }}</td>
                            <td class="text-center">{{ $item->nghi_om }}</td>
                            <td class="text-center">{{ $item->nghi_khong_luong }}</td>   
                            <td class="text-center">{{ $item->nghi_cuoi }}</td>
                            <td class="text-center">{{ $item->nghi_hieu }}</td>
                            <td class="text-center">{{ $item->nghi_phep }}</td>
                            <td class="text-center">{{ $item->nghi_cong_tac }}</td>
                            <td class="text-center">{{ $item->nghi_le }}</td>
                            <td class="text-center">{{ $item->lam_tai_nha }}</td>

                            @if ($detail->department->type == \App\Define\Department::DECLARATION_OFFICE)
                                <td class="text-center">{!! !empty($item->shift_day) ? $item->shift_day : '0' !!}</td>
                                <td class="text-center">{!! !empty($item->shift_hc) ? $item->shift_hc : '0' !!}</td>
                                <td class="text-center">{!! !empty($item->shift_night) ? $item->shift_night : '0' !!}</td>
                            @endif
                           
                            <td class="text-center total_{{ $item->id }}">{{ $item->total }}</td>
                            <td class="text-center">
                                <button data-url="{{ route('admin.timekeeping.log', $detail->id) . '?user_id=' . $item->staff_id}}" type="button" class="btn btn-xs btn-info btn-log" data-toggle="modal" data-target="#log">
                                    <i class="fas fa-history" data-toggle="tooltip" data-placement="top" title="Cập nhật"></i>
                                </button>

                                @permission('timekeeping.create')
                                @if ($detail->status != 'APPROVED' )
                                    {{-- <button data-reset="{{ route('admin.timekeepings.reset', $item->id)}}" type="button" class="btn btn-xs btn-danger btn-reset" data-toggle="modal" data-target="#reset" data-fullname="{{ $item->staff->fullname }}" data-code="{{ $item->staff->code }}">
                                        <i class="fas fa-power-off" data-toggle="tooltip" data-placement="top" title="Reset chỉnh sửa"></i>
                                    </button> --}}
                                    {{-- @if ($detail->department->type == \App\Define\Department::DECLARATION_OFFICE)
                                        <button data-url-an="{{ route('admin.timekeepings.suat-an', $item->id)}}" type="button" class="btn btn-xs btn-primary suat-an" data-toggle="modal" data-target="#suatAn" data-fullname="{{ $item->staff->fullname }}" data-code="{{ $item->staff->code }}">
                                            Suất ăn
                                        </button>
                                    @endif --}}
                                @endif
                                @endpermission

                                
                            </td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>           
                </table>
                @if (count($items) == 0)
                <div class="text-center error">
                    <span class="text-size"><i class="fas fa-search"></i> {!! trans('timekeeping.no_data') !!}</span>
                </div>
                @endif
            </div>
        </div>
    </section>

    <div class="modal fade" id="suatAn" tabindex="-1" role="diasuatAn" aria-labelledby="suatAnLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" role="document" style="width: 900px">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #3c8dbc; text-align: center">
                    <h2 class="modal-title" style="color: white" id="logLabel">Tính lại suất ăn</h2>
                </div>
                <div class="modal-body body-log" style="text-align: center">
                    <span class="text-suat-an"></span>
                    <h3 style="color: red">Chỉ tính lại suất ăn, dữ liệu không thay đổi</h3>
                </div>
                <div class="modal-footer" style="text-align: center">
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-primary btn-sm save-suat-an">Lưu lại</button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="reset" tabindex="-1" role="diareset" aria-labelledby="resetLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" role="document" style="width: 900px">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #3c8dbc">
                    <h2 class="modal-title" style="color: white" id="logLabel">Reset lại công</h2>
                </div>
                <div class="modal-body body-log" style="text-align: center">
                    <span class="text-code"></span>
                    <h3 style="color: red">Toàn bộ chỉnh sửa mới sẽ được xóa, cập nhập lại dữ liệu cũ</h3>
                </div>
                <div class="modal-footer" style="text-align: center">
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary btn-sm btn-reset-save" data-dismiss="modal">Lưu lại</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="log" tabindex="-1" role="dialog" aria-labelledby="logLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" role="document" style="width: 900px">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #3c8dbc">
                    <h4 class="modal-title" style="color: white" id="logLabel">Lịch sử cập nhật</h4>
                </div>
                <div class="modal-body body-log" style="text-align: left">
                    <table class="table table-striped table-bordered table-log">
                        <thead>
                            <tr>
                                <th style="width: 250px">Nội dung cũ</th>
                                <th style="width: 250px">Nội dung mới</th>
                                <th style="width: 250px">Ghi chú</th>
                                <th>Ngày cập nhật</th>
                                <th>Người cập nhật</th>
                            </tr>
                        </thead>
                        <tbody class="tbody-log">
                        </tbody>
                    </table>
                    <div class="text-log"></div>
                </div>
                <div class="modal-footer" style="text-align: center">
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="exampleModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #3c8dbc">
                    <h3 class="modal-title" style="text-align: center; color: white" id="exampleModalLabel">Cập nhật chấm công</h3>
                </div>
                <div class="modal-body">
                    <form action="" id="update-timekeeping">
                        <input type="hidden" name="key" value="">
                        <div style="width: 90%; margin: auto">
                            <div class="row">
                                <div class="col-md-4">
                                    <h4>Nhân viên</h4>
                                </div>
                                <div class="col-md-8">
                                    <h4 class="fullname1"></h4>
                                </div>
                                <div class="col-md-4">
                                    <h4>Ngày làm việc</h4>
                                </div>
                                <div class="col-md-8">
                                    <h4 class="date"></h4>
                                </div>
                            </div>
                            <div class="row">
                               <div class="col-md-12">
                                    <div class="type-administrative">
                                        <div class="form-group">
                                            <select name="status" id="" class="form-control select2">
                                                <option value="1">Cả ngày</option>
                                                <option value="10">Làm nửa buổi</option>
                                                <option value="0">Không đi làm</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="type-shift">
                                        <div class="form-group">
                                            {!! Form::select('shift', ['' =>  'Chọn ca làm'] + \App\Define\Shift::getShiftByDepartment($detail->department_id), old('shift'), ['class' => 'form-control select2 form-shift']) !!}
                                        </div>
                                    </div>
                                    {{-- <div class="type-kip">
                                        <div class="form-group">
                                            <select name="type-shift" id="" class="form-control">
                                                <option value="4">Kíp 1</option>
                                                <option value="5">Kíp 2</option>
                                            </select>
                                        </div>
                                    </div> --}}
                                    <div class="form-group">
                                        <textarea name="note" id="" cols="30" rows="5" class="form-control" placeholder="Ghi chú"></textarea>
                                    </div>
                               </div>
                            </div>
                        </div>
                    </form>
                    <h4 class="warning-content" style="text-align: right; margin-right: 30px; color: red"></h4>
                </div>
                <div class="modal-footer" style="text-align: center">
                    <button type="button" class="btn btn-danger btn-sm btn-close" data-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-success btn-sm btn-update">Lưu lại</button>
                </div>
            </div>
        </div>
    </div>
@stop
@section('footer')
<script src="{!! asset('assets/backend/plugins/select2/select2.full.min.js') !!}"></script>
    <script>
         !function ($) {
            $(function () {
                $(".select2").select2({width: '100%'});
            });
        }(window.jQuery);
    </script>
    <script>
        var type = '{!! $detail->department->type !!}';
        var url = '';

        $('.warning-content').text('');
        $('.type-administrative').hide();
        $('.type-shift').hide();
        $('.type-kip').hide();
        $('.btn-close').on('click', function() {
            $('#update-timekeeping').trigger("reset");
        });

        $('.update').on('click', function() {
            $('#update-timekeeping').trigger("reset");
            $('.form-shift').val('').change();

            $('.btn-update').removeAttr('disabled');

            url = $(this).data('url');
            key = $(this).data('key');
            let type = {{ $detail->department->type }};
            
            $('.fullname1').text($(this).data('fullname'));
            $('.date').text($(this).data('date'));
            $('input[name="key"]').val(key).change();
            $('#exampleModal').modal('show');
            
            if (type == {{ \App\Define\Department::FUNCTIONAL_OFFICE }}) {
                $('.type-administrative').show();
            } else if (type == {{ \App\Define\Department::DECLARATION_OFFICE }}) {
                $('.type-shift').show();
            } else {
                $('.type-kip').show();
            }

            $.ajax({
                type: "GET",
                url: $(this).data('link'),
                data: {
                    key: key
                },
                success: function (response) {
                    if (response.status == 'SUCCESS') {
                        $('.warning-content').text(response.content)
                    } else {
                        $('.warning-content').text('');
                    }
                }
            });

            
        });

        $('.btn-update').on('click', function() {
            // if (!$('.form-shift').val()) {
            //     toastr.error('Chưa chọn ca làm');
            //     return;
            // }
            let registerForm = $("#update-timekeeping");
            let formData = registerForm.serialize();
            $(this).attr('disabled', 'disabled');
            $.ajax({
                type: "POST",
                url: url,
                headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                data: formData,
                success: function (response) {
                    if (response.status == 'FAIL') {
                        $('.btn-update').removeAttr('disabled');
                        toastr.error(response.message);
                    } else if (response.status == 'SUCCESS') {
                        toastr.success(response.message);
                        location.reload();
                    }

                    return ;
                }
            });
        });
    
        $('.btn-log').on('click', function() {
            $('.text-log').html('');
            $('.tbody-log').html('');

            let get_url = $(this).data('url');
            let html = '';
            $.get(get_url, function (response) {
                if (response.status == 'FAIL') {
                    let p = `<p class="text-center">${response.message}</p>`;
                    $('.text-log').html(p);
                } else {
                    $.each(response.data, function (key, value) {
                        html += `
                            <tr>
                                <td>${value.content_old}</td>
                                <td>${value.content}</td>
                                <td>${value.note}</td>
                                <td class="text-center">${value.action_at}</td>
                                <td class="text-center">${value.user}</td>
                            </tr>
                        `;
                    })

                    $('.body-log .tbody-log').html(html);
                }
            });
        });

        $('.btn-edit11').on('click', function() {
            let url = $(this).data('url');

            $(this).addClass('disabled', 'disabled');
            let load = `<div class="loading">Loading&#8230;</div>`;
            $('.modal-body11').append(load);

            $.ajax({
                url: url,
                type: "POST",
                headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                success:function(response) {
                    if (response.status == 'FAIL') {
                        $('.btn-edit11').removeClass('disabled');
                        $('.loading').remove();
                        toastr.error(response.message);
                    } else if (response.status == 'SUCCESS') {
                        $('.loading').remove();
                        toastr.success(response.message);
                        location.reload();
                    }
                }
            });
        });

        $('.btn-approved').on('click', function() {
            let link = $(this).data('link');
            let text = '';

            $.ajax({
                url: link,
                type: "POST",
                headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                success:function(response) {
                    $('#approved').modal('hide');

                    if (response.status == 'FAIL') {
                        toastr.error(response.message);
                    } else if (response.status == 'SUCCESS') {
                        $('.btn-chot').hide();
                        $('.btn-reset').hide();
                        text = `
                            <h4 class="text-approved">
                                Người duyệt: ${response.fullname} <span class="label label-success" style="font-size: 14px">Đã chốt</span>
                            </h4>
                        `;
                       
                        $('.text-approved').append(text);
                        toastr.success(response.message);

                    }
                }
            });

        })

        $('.btn-reset').on('click', function () {
            $('.btn-reset-save').removeAttr('disabled');
            
            $('.fullname-code').remove();
            let fullname = $(this).data('fullname');
            let code = $(this).data('code');
            let html = `
                <h3 style="color: red;" class="fullname-code">Nhân viên: ${fullname} - ${code}</h3>
            `;

            $('.text-code').append(html);
            url_reset = $(this).data('reset');
        })

        $('.btn-reset-save').on('click', function() {
            if (url_reset) {
                $(this).attr('disabled', 'disabled');
                $.ajax({
                    url: url_reset,
                    type: "POST",
                    headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                    success:function(response) {
                        $('#reset').modal('hide');

                        if (response.status == 'FAIL') {
                            toastr.error(response.message);
                        } else if (response.status == 'SUCCESS') {
                            
                            toastr.success(response.message);
                            location.reload();

                        }
                    }
                });
            } else {
                toastr.error('Có lỗi xảy ra');
            }
        })
    </script>
    <script>
        $(document).ready(function () {
            var url_suat_an = '';
            $('.suat-an').on('click', function () {
                $('.save-suat-an').removeAttr('disabled');
                
                $('.fullname-code').remove();
                let fullname = $(this).data('fullname');
                let code = $(this).data('code');
                let html = `
                    <h3 style="color: red;" class="fullname-code">Nhân viên: ${fullname} - ${code}</h3>
                `;

                $('.text-suat-an').append(html);
                url_suat_an = $(this).data('url-an');
            })


            $('.save-suat-an').on('click', function() {
                if (url_suat_an) {
                    $(this).attr('disabled', 'disabled');
                    $.ajax({
                        url: url_suat_an,
                        type: "POST",
                        headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                        success:function(response) {
                            $('#reset').modal('hide');

                            if (response.status == 'FAIL') {
                                toastr.error(response.message);
                            } else if (response.status == 'SUCCESS') {
                                toastr.success(response.message);
                                location.reload();
                            }
                        }
                    });
                } else {
                    toastr.error('Có lỗi xảy ra');
                }
            })
        });
    </script>
@stop