@extends('backend.master')
@section('title')
    {!! trans('payrolls.detail') !!} {!! trans('payrolls.salary_user') !!}
@stop
@section('head')
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/iCheck/all.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/select2/select2.min.css') !!}"/>
    <style>
        table tr td {
            width: 200px;
        }

        .modal-header {
            background-color: #3c8dbc;
            color: white;
            text-align: center;
        }

        .modal-footer {
            text-align: center;
        }

        input {
            border-color: #f1f1f3;
        }

        ul.timeline {
            list-style-type: none;
            position: relative;
        }
        ul.timeline:before {
            content: ' ';
            background: #d4d9df;
            display: inline-block;
            position: absolute;
            left: 5px;
            width: 2px;
            height: 100%;
            z-index: 400;
        }
        ul.timeline > li {
            margin: 20px 0;
            padding-left: 50px;
        }
        ul.timeline > li:before {
            content: ' ';
            background: white;
            display: inline-block;
            position: absolute;
            border-radius: 50%;
            border: 3px solid #22c0e8;
            left: -4px;
            width: 20px;
            height: 20px;
            z-index: 400;
        }
    </style>
@stop
@section('content')
    <section class="content-header">
        <h1>
            {!! trans('payrolls.salary_user') !!}
            <small>{!! trans('payrolls.detail') !!}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.timekeeping.index') !!}">{!! trans('payrolls.detail') !!} {!! trans('payrolls.salary_user') !!}</a></li>
        </ol>
        <h4>{!! trans('payrolls.salary_month') !!} {{ $payroll->month }}/{{ $payroll->year }} <i class="far fa-user" style="font-size: 15px; margin-left: 10px"></i> {!! trans('payrolls.created_by') !!}: {{ $payroll->user_by->fullname }}</h4>
    </section>
    <section class="content overlay">
        <div class="row">
            <div class="col-md-4">
                <div class="box" style="border-top: none">
                    <div class="box-header">
                        <p style="font-size: 18px; font-weight: 500">{!! trans('payrolls.info') !!}</p>
                        <span>{{ $payroll->company->name }} - {{ $payroll->department->name }}</span><br>
                        <span>{{ $user_payroll->staff->fullname }} - {{ $user_payroll->staff->code }}</span><br>
                        <span>{{ $user_payroll->position}} - {{ $user_payroll->qualification }}</span>
                    </div>
                    @if (count($logs) > 0)
                    <div class="box-body no-padding">
                        <div class="container mt-5">
                            <div class="row">
                                <div class="col-md-12">
                                    <h4>Lịch sử chỉnh sửa</h4>
                                    <ul class="timeline" style="line-height: 18px">
                                        @foreach ($logs as $log)
                                        <li>
                                            <div style="width: 250px; margin-left: -20px">
                                                <p style="color: #3c8dbc"><i class="far fa-calendar-alt"></i> {{ $log['action_at'] }}&emsp;<i class="far fa-user"></i> {{ $log['user'] }}</p>
                                                <p>{{ $log['content'] }}</p>
                                                <p><u>{{ $log['data_old'] }}</u> &emsp;<span class="text-danger" style="font-size: 16px"><strong>{{ $log['data_new'] }}</strong></span></p>
                                                <p>{{ $log['note'] }}</p>
                                            </div>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            <div class="col-md-8">
                <div class="box" style="border-top: none">
                    <div class="box-header">
                        <h4 style="font-size: 18px; font-weight: 500">{!! trans('payrolls.total_ttn') !!}: {{ number_format(intval($user_payroll->total_salary), 0, ',', '.') }} vnđ</h4>

                        <h4 style="font-size: 18px; font-weight: 500">{!! trans('payrolls.total_ttl') !!}: {{ number_format(intval($user_payroll->total_real_salary), 0, ',', '.') }} vnđ</h4>
                        @if ($user_payroll->status == 1)
                        <span class="badge" style="background-color: #00a65a">{!! trans('payrolls.approved') !!}</span>
                        @else
                        <span class="badge" style="background-color: red">{!! trans('payrolls.pending') !!}</span>
                        @endif
                    </div>

                    <div class="box-header">
                        <p style="font-size: 18px; font-weight: 500">{!! trans('payrolls.general_info') !!}</p>
                    </div>
                    <div class="box-body no-padding">
                        <table class="table table-striped table-bordered" style="width: 97%; margin: auto">
                            <thead>
                                <tr>
                                    <th>{!! trans('payrolls.seniority') !!}</th>
                                    <td>{{ $getSeniority }} năm</td>
                                </tr>
                                <tr>
                                    <th>{!! trans('payrolls.number_working') !!}</th>
                                    <td>{{ $user_payroll->total_day_request }}</td>
                                </tr>
                                <tr>
                                    <th>{!! trans('payrolls.number_reality') !!}</th>
                                    <td>
                                        <span>{{ $user_payroll->actual_workdays }}</span>
                                        <button type="button" class="btn btn-info btn-xs" style="float: right" data-toggle="modal" data-target="#totalWorkingDays">
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        <div class="modal fade" id="totalWorkingDays" tabindex="-1" role="dialog" aria-labelledby="totalWorkingDaysLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                            <div class="modal-dialog" role="document">
                                              <div class="modal-content">
                                                <div class="modal-header">
                                                  <h4 class="modal-title" id="totalWorkingDaysLabel">{!! trans('payrolls.number_reality') !!}</h4>
                                                  
                                                </div>
                                                <div class="modal-body">
                                                    <table class="table table-striped table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th colspan="2" style="text-align: center; vertical-align: middle;">Thử việc</th>
                                                                <th colspan="2" style="text-align: center; vertical-align: middle;">Hợp đồng</th>
                                                                <th rowspan="2" style="text-align: center; vertical-align: middle;">Nghỉ nguyên lương</th>
                                                                <th rowspan="2" style="text-align: center; vertical-align: middle;">Đình chỉ</th>
                                                            </tr>
                                                            <tr>
                                                                <th colspan="1">Ngày</th>
                                                                <th colspan="1">Đêm</th>
                                                                <th colspan="1">Ngày</th>
                                                                <th colspan="1">Đêm</th>                                                        
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>{{ $user_payroll->ca_ngay_tv }}</td>
                                                                <td>{{ $user_payroll->ca_dem_tv }}</td>
                                                                <td>{{ $user_payroll->ca_ngay_hd }}</td>
                                                                <td>{{ $user_payroll->ca_dem_hd }}</td>
                                                                <td>{!! !empty($user_payroll->day_off_70_salary) ? $user_payroll->day_off_70_salary : '0' !!}</td>
                                                                <td>{{ $user_payroll->day_off_luong }}</td>
                                                            </tr>
                                                        </tbody>           
                                                    </table>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">{!! trans('payrolls.close') !!}</button>
                                                </div>
                                              </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>{!! trans('payrolls.number_of_main_meals') !!}</th>
                                    <td>{{ $user_payroll->an_chinh }}</td>
                                </tr>
                                <tr>
                                    <th>{!! trans('payrolls.number_of_snacks') !!}</th>
                                    <td>{{ $user_payroll->an_phu }}</td>
                                </tr>
                            </thead>
                        </table>
                    </div>

                    <div class="box-header">
                        <p style="font-size: 18px; font-weight: 500">{!! trans('payrolls.salary') !!}</p>
                    </div>
                    <div class="box-body no-padding">
                        <table class="table table-striped table-bordered" style="width: 97%; margin: auto">
                            <thead>
                                <tr>
                                    <th>{!! trans('payrolls.food_allowance') !!}</th>
                                    <td>
                                        <span>{{ number_format(intval($user_payroll->food_allowance_nonTax + $user_payroll->food_allowance_tax), 0, ',', '.') }}</span>
                                        @if (Auth::user()->hasRole('TP') || Auth::user()->hasRole('system'))
                                        <button type="button" class="btn btn-default btn-xs" style="float: right" data-toggle="modal" data-target="#foodAllowance">
                                            <i class="text-warning glyphicon glyphicon-edit"></i>
                                        </button>
                                        @endif
                            
                                        <div class="modal fade" id="foodAllowance" tabindex="-1" role="dialog" aria-labelledby="foodAllowanceLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                            <div class="modal-dialog" role="document">
                                              <div class="modal-content">
                                                <div class="modal-header">
                                                  <h4 class="modal-title" id="foodAllowanceLabel">{!! trans('payrolls.food_allowance') !!}</h4>
                                                </div>
                                                <form action="{{ route('admin.payrolls.log', $user_payroll->id) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="type" value="FOOD_ALLOWANCE">
                                                    <div class="modal-body">
                                                        <table class="table table-striped table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th>Chịu thuế</th>
                                                                    <td><input type="text" required="required" minlength="0" class="form-control currency" name="food_allowance_tax" value="{{ intval($user_payroll->food_allowance_tax) }}"></td>
                                                                    <td><input type="text" placeholder="Ghi chú" class="form-control" name="note_food_allowance_tax" id=""></td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Miễn thuế</th>
                                                                    <td><input type="text" required="required" minlength="0" class="form-control currency" name="food_allowance_nonTax" value="{{ intval($user_payroll->food_allowance_nonTax) }}"></td>
                                                                    <td><input type="text" placeholder="Ghi chú" class="form-control" name="note_food_allowance_nonTax" id=""></td>
                                                                </tr>
                                                            </thead>      
                                                        </table>
                                                    </div>
                                                    <div class="modal-footer" style="text-align: center">
                                                        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">{!! trans('payrolls.close') !!}</button>
                                                        <button type="submit" class="btn btn-primary btn-sm">Lưu lại</button>
                                                    </div>
                                                </form>
                                              </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>{!! trans('payrolls.basic_salary') !!}</th>
                                    <td>
                                        <span>{{ number_format($user_payroll->basic_salary_hd, 0, ',', '.') }}</span>
                                        <button type="button" class="btn btn-info btn-xs" style="float: right" data-toggle="modal" data-target="#basicSalary" data-backdrop="static" data-keyboard="false">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <div class="modal fade" id="basicSalary" tabindex="-1" role="dialog" aria-labelledby="basicSalaryLabel" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                              <div class="modal-content">
                                                <div class="modal-header">
                                                  <h4 class="modal-title" id="basicSalaryLabel">{!! trans('payrolls.basic_salary') !!}</h4>
                                                  
                                                </div>
                                                <div class="modal-body">
                                                    <table class="table table-striped table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>Thử việc</th>
                                                                <td>{{ number_format($user_payroll->basic_salary_tv, 0, ',', '.') }}</td>
                                                            </tr>
                                                            <tr>
                                                                <th>Hợp đồng</th>
                                                                <td>{{ number_format($user_payroll->basic_salary_hd, 0, ',', '.') }}</td>
                                                            </tr>
                                                        </thead>      
                                                    </table>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">{!! trans('payrolls.close') !!}</button>
                                                </div>
                                              </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>{!! trans('payrolls.salary_bh') !!}</th>
                                    <td>{{ number_format(intval($user_payroll->salary_bh), 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>{!! trans('payrolls.total_allowance') !!}</th>
                                    <td>
                                        <span>{{ number_format(intval($user_payroll->total_allowances), 0, ',', '.') }}</span>
                                        <button type="button" class="btn btn-info btn-xs" style="float: right" data-toggle="modal" data-target="#totalAllowances">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <div class="modal fade" id="totalAllowances" tabindex="-1" role="dialog" aria-labelledby="totalAllowancesLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                            <div class="modal-dialog" role="document">
                                              <div class="modal-content">
                                                <div class="modal-header">
                                                  <h4 class="modal-title" id="totalAllowancesLabel">{!! trans('payrolls.allowance') !!}</h4>
                                                  
                                                </div>
                                                <div class="modal-body">
                                                    <table class="table table-striped table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th class="text-center">Tên phụ cấp</th>
                                                                <th class="text-center">Mức phụ cấp</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($allowances as $key => $allowance)
                                                            <tr>
                                                                <td class="text-center">{{ $allowance->allowanceCategory == true ? $allowance->allowanceCategory->name : $allowance->category->name }}</td>
                                                                <td class="text-center">{{ number_format(intval($allowance->money), 0, ',', '.') }}</td>
                                                            </tr>
                                                            @endforeach
                                                            @foreach ($allowances1 as $key => $allowance)
                                                            <tr>
                                                                <td class="text-center">{{ $allowance->allowanceCategory == true ? $allowance->allowanceCategory->name : $allowance->category->name }}</td>
                                                                <td class="text-center">{{ number_format(intval($allowance->money), 0, ',', '.') }}</td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>      
                                                    </table>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">{!! trans('payrolls.close') !!}</button>
                                                </div>
                                              </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>{!! trans('payrolls.working_salary') !!}</th>
                                    <td>
                                        <span>{{ number_format(intval($user_payroll->working_salary_non_tax + $user_payroll->working_salary_tax), 0, ',', '.') }}</span>
                                        @if (Auth::user()->hasRole('TP') || Auth::user()->hasRole('system'))
                                        <button type="button" class="btn btn-default btn-xs" style="float: right" data-toggle="modal" data-target="#realWages">
                                            <i class="text-warning glyphicon glyphicon-edit"></i>
                                        </button>
                                        @endif
              
                                        <div class="modal fade" id="realWages" tabindex="-1" role="dialog" aria-labelledby="realWagesLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                            <div class="modal-dialog" role="document">
                                              <div class="modal-content">
                                                <div class="modal-header">
                                                  <h4 class="modal-title" id="realWagesLabel">{!! trans('payrolls.working_salary') !!}</h4>
                                                </div>
                                                <form action="{{ route('admin.payrolls.log', $user_payroll->id) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="type" value="WORKING_SALARY">
                                                    <div class="modal-body">
                                                        <table class="table table-striped table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th>Chịu thuế</th>
                                                                    <td><input type="text" required="required" class="form-control currency" name="working_salary_tax" value="{{ intval($user_payroll->working_salary_tax) }}"></td>
                                                                    <td><input type="text" placeholder="Ghi chú" class="form-control" name="note_working_salary_tax" id=""></td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Miễn thuế</th>
                                                                    <td><input type="text" required="required" class="form-control currency" name="working_salary_non_tax" value="{{ intval($user_payroll->working_salary_non_tax) }}"></td>
                                                                    <td><input type="text" placeholder="Ghi chú" class="form-control" name="note_working_salary_non_tax" id=""></td>
                                                                </tr>
                                                            </thead>      
                                                        </table>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">{!! trans('payrolls.close') !!}</button>
                                                        <button type="submit" class="btn btn-primary btn-sm">Lưu lại</button>
                                                    </div>
                                                </form>
    
                                              </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>{!! trans('payrolls.total_salary_ot') !!}</th>
                                    <td>
                                        <span>{{ number_format(intval($user_payroll->salary_ot_non_tax + $user_payroll->salary_ot_tax), 0, ',', '.') }}</span>
                                        @if (Auth::user()->hasRole('TP') || Auth::user()->hasRole('system'))
                                        <button type="button" class="btn btn-default btn-xs" style="float: right" data-toggle="modal" data-target="#salaryOT" data-backdrop="static" data-keyboard="false">
                                            <i class="text-warning glyphicon glyphicon-edit"></i>
                                        </button>
                                        @endif
                                       
                                        <div class="modal fade" id="salaryOT" tabindex="-1" role="dialog" aria-labelledby="salaryOTLabel" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title" id="salaryOTLabel">{!! trans('payrolls.total_salary_ot') !!}</h4>
                                                    </div>
                                                    <form action="{{ route('admin.payrolls.log', $user_payroll->id) }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="type" value="SALARY_OT">
                                                        <div class="modal-body">
                                                            <table class="table table-striped table-bordered">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Chịu thuế</th>
                                                                        <td><input type="text" required="required" class="form-control currency" name="salary_ot_tax" id="" value="{{ intval($user_payroll->salary_ot_tax) }}"></td>
                                                                        <td><input type="text" placeholder="Ghi chú" class="form-control" name="note_salary_ot_tax" id=""></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Miễn thuế</th>
                                                                        <td><input type="text" required="required" class="form-control currency" name="salary_ot_non_tax" id="" value="{{ intval($user_payroll->salary_ot_non_tax) }}"></td>
                                                                        <td><input type="text" placeholder="Ghi chú" class="form-control" name="note_salary_ot_non_tax" id=""></td>
                                                                    </tr>
                                                                </thead>      
                                                            </table>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">{!! trans('payrolls.close') !!}</button>
                                                            <button type="submit" class="btn btn-primary btn-sm">Lưu lại</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>{!! trans('payrolls.salary_concurrent') !!}</th>
                                    <td>{{ number_format(intval($user_payroll->salary_concurrent), 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>{!! trans('payrolls.others') !!}</th>
                                    <td>
                                        <span id="total_other_amounts">{{ number_format($total_other_amounts, 0, ',', '.') }}</span>
                                        @if (Auth::user()->hasRole('TP') || Auth::user()->hasRole('system'))
                                        <button type="button" class="btn btn-success btn-xs btn-other-amounts" style="float: right" data-toggle="modal" data-target="#otherAmounts">
                                            <span class="glyphicon glyphicon-plus">
                                        </button>      
                                        @endif
                                      
                                        <div class="modal fade" id="otherAmounts" tabindex="-1" role="dialog" aria-labelledby="otherAmountsLabel" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                              <div class="modal-content">
                                                <div class="modal-header">
                                                  <h4 class="modal-title" id="otherAmountsLabel">{!! trans('payrolls.others') !!}</h4>
                                                 
                                                </div>
                                                {!! Form::open(['id' => 'payroll-user', 'url' => route('admin.payrolls.other-amounts'), 'method' => 'POST']) !!}
                                                    <input type="hidden" name="payroll_user_id" value="{{ $user_payroll->id }}">
                                                    <input type="hidden" name="type" value="1">
                                                    <div class="modal-body">
                                                        <table class="table table-striped table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th class="text-center">STT</th>
                                                                    <th class="text-center">Tên khoản mục</th>
                                                                    <th class="text-center">Mức</th>
                                                                    <th class="text-center">Thao tác</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="add-template">
                                                                @if (count($other_amounts) > 0)
                                                                    @foreach ($other_amounts as $key => $item)
                                                                    <input type="hidden" value="{{ $item->id }}" name="ids[]">
                                                                    <tr>
                                                                        <td style="width: 50px" class="text-center">{{ $key + 1}}</td>
                                                                        <td>
                                                                            <input type="text" name="name[]" value="{{ $item->name }}" class="form-control" required>
                                                                        </td>
                                                                        <td>
                                                                            <input type="text" name="money[]" value="{{ $item->money }}" class="form-control currency" required>
                                                                        </td>
                                                                        <td class="text-center" style="width: 100px">
                                                                            @if ($key == 0)
                                                                            <a href="javascript:void(0);" class="btn btn-default btn-xs btn-add-other-amounts">
                                                                                <i class="text-success fa fa-plus"></i>
                                                                            </a>
                                                                            @else
                                                                            <a href="javascript:void(0);" class="btn btn-xs btn-default remove-other-amounts">
                                                                                <i class="text-danger fa fa-minus"></i>
                                                                            </a>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                    @endforeach
                                                                @else
                                                                <tr>
                                                                    <td style="width: 50px" class="text-center">1</td>
                                                                    <td>
                                                                        <input type="text" name="name[]" class="form-control" required>
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" name="money[]" class="form-control currency" required>
                                                                    </td>
                                                                    <td class="text-center" style="width: 100px">
                                                                        <a href="javascript:void(0);" class="btn btn-default btn-xs btn-add-other-amounts">
                                                                            <i class="text-success fa fa-plus"></i>
                                                                        </a>
                                                                    </td>
                                                                </tr>
                                                                @endif
                                                            </tbody>      
                                                        </table>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">{!! trans('payrolls.close') !!}</button>
                                                        <button type="submit" class="btn btn-primary btn-sm">{!! trans('system.action.save') !!}</button>
                                                    </div>
                                                {!! Form::close() !!}
                                              </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <th>Các khoản khấu trừ khác</th>
                                    <td>
                                        <span>{{ number_format($total_deductions, 0, ',', '.') }}</span>
                                        {{-- <button type="button" class="btn btn-success btn-xs btn-other-amounts" style="float: right" data-toggle="modal" data-target="#deductions">
                                            <span class="glyphicon glyphicon-plus">
                                        </button> --}}
                                        {{-- <div class="modal fade" id="deductions" tabindex="-1" role="dialog" aria-labelledby="deductionsLabel" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                              <div class="modal-content">
                                                <div class="modal-header">
                                                  <h4 class="modal-title" id="deductionsLabel">Các khoản khấu trừ khác</h4>
                                                  
                                                </div>
                                                {!! Form::open(['url' => route('admin.payrolls.other-amounts'), 'method' => 'POST']) !!}
                                                    <input type="hidden" name="payroll_user_id" value="{{ $user_payroll->id }}">
                                                    <input type="hidden" name="type" value="2">
                                                    <div class="modal-body">
                                                        <table class="table table-striped table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th class="text-center">STT</th>
                                                                    <th class="text-center">Tên khoản khẩu trừ</th>
                                                                    <th class="text-center">Mức</th>
                                                                    <th class="text-center">Thao tác</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="add-template-deductions">
                                                                @if (count($deductions) > 0)
                                                                    @foreach ($deductions as $key => $item)
                                                                    <input type="hidden" value="{{ $item->id }}" name="ids[]">
                                                                    <tr>
                                                                        <td style="width: 50px" class="text-center">{{ $key + 1}}</td>
                                                                        <td>
                                                                            <input type="text" name="name[]" value="{{ $item->name }}" class="form-control" required>
                                                                        </td>
                                                                        <td>
                                                                            <input type="text" name="money[]" value="{{ $item->money }}" class="form-control currency" required>
                                                                        </td>
                                                                        <td class="text-center" style="width: 100px">
                                                                            @if ($key == 0)
                                                                            <a href="javascript:void(0);" class="btn btn-default btn-xs btn-add-deductions">
                                                                                <i class="text-success fa fa-plus"></i>
                                                                            </a>
                                                                            @else
                                                                            <a href="javascript:void(0);" class="btn btn-xs btn-default remove-deductions">
                                                                                <i class="text-danger fa fa-minus"></i>
                                                                            </a>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                    @endforeach
                                                                @else
                                                                <tr>
                                                                    <td style="width: 50px" class="text-center">1</td>
                                                                    <td>
                                                                        <input type="text" name="name[]" class="form-control" required>
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" name="money[]" class="form-control currency" required>
                                                                    </td>
                                                                    <td class="text-center" style="width: 100px">
                                                                        <a href="javascript:void(0);" class="btn btn-default btn-xs btn-add-deductions">
                                                                            <i class="text-success fa fa-plus"></i>
                                                                        </a>
                                                                    </td>
                                                                </tr>
                                                                @endif
                                                            </tbody>      
                                                        </table>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">{!! trans('payrolls.close') !!}</button>
                                                        <button type="submit" class="btn btn-primary btn-sm">{!! trans('system.action.save') !!}</button>
                                                    </div>
                                                {!! Form::close() !!}
                                              </div>
                                            </div>
                                        </div> --}}
                                    </td>
                                </tr>
                            </thead>
                        </table>
                    </div>

                    <div class="box-header">
                        <p style="font-size: 18px; font-weight: 500">{!! trans('payrolls.bh') !!}</p>
                        <span>{!! trans('payrolls.user_deduction') !!}</span>
                    </div>
                    <div class="box-body no-padding">
                        <table class="table table-striped table-bordered" style="width: 97%; margin: auto">
                            <thead>
                                <tr>
                                    <th>BHXH (8%)</th>
                                    <td>{{ number_format(intval($user_payroll->bh->bhxh_user), 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>BHYT (1.5%)</th>
                                    <td>{{ number_format(intval($user_payroll->bh->bhyt_user), 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>BHTN (1%)</th>
                                    <td>{{ number_format(intval($user_payroll->bh->bhtn_user), 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>{!! trans('payrolls.union') !!} (1%)</th>
                                    <td>{{ number_format(intval($user_payroll->bh->union_user), 0, ',', '.') }}</td>
                                </tr>
                            </thead>
                        </table>
                    </div>

                    <div class="box-header">
                        <span>{!! trans('payrolls.company_deduction') !!}</span>
                    </div>
                    <div class="box-body no-padding">
                        <table class="table table-striped table-bordered" style="width: 97%; margin: auto">
                            <thead>
                                <tr>
                                    <th>BHXH (17.5%)</th>
                                    <td>{{ number_format(intval($user_payroll->bh->bhxh_company), 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>BHYT (3%)</th>
                                    <td>{{ number_format(intval($user_payroll->bh->bhyt_company), 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>BHTN (1%)</th>
                                    <td>{{ number_format(intval($user_payroll->bh->bhtn_company), 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>{!! trans('payrolls.union') !!} (2%)</th>
                                    <td>{{ number_format(intval($user_payroll->bh->union_company), 0, ',', '.') }}</td>
                                </tr>
                            </thead>
                        </table>
                    </div>

                    <div class="box-header">
                        <p style="font-size: 18px; font-weight: 500">{!! trans('payrolls.tax') !!}</p>
                    </div>
                    <div class="box-body no-padding">
                        <table class="table table-striped table-bordered" style="width: 97%; margin: auto">
                            <thead>
                                <tr>
                                    <th>{!! trans('payrolls.family_allowances') !!}</th>
                                    <td>{{ number_format(intval($user_payroll->family_allowances), 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>{!! trans('payrolls.income_taxes') !!}</th>
                                    <td>{{ number_format(intval($user_payroll->income_taxes), 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>{!! trans('payrolls.taxable_income') !!}</th>
                                    <td>{{ $user_payroll->taxable_income < 0 ? 0 : number_format(intval($user_payroll->taxable_income), 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>{!! trans('payrolls.personal_income') !!}</th>
                                    <td>{{ $user_payroll->personal_income_tax < 0 ? 0 : number_format(intval($user_payroll->personal_income_tax), 0, ',', '.') }}</td>
                                </tr>
                            </thead>
                        </table>
                    </div>

                    <div class="box text-center" style="border-top: none">
                        <div class="box-header">
                            @if ($user_payroll->status != 1 && Auth::user()->hasRole('TP') || Auth::user()->hasRole('system'))
                                <button data-toggle="modal" data-target="#recalculate" type="button" class="btn btn-sm btn-info">Tính lại</button>
                                <button data-toggle="modal" data-target="#update" type="button" class="btn btn-sm btn-primary">Cập nhật</button>
                                <button data-toggle="modal" data-target="#approved" type="button" class="btn btn-sm btn-success">Duyệt</button>
                            @endif


                            @include('backend.payroll.history')

                             <!-- Modal edit-->
                             <div class="modal fade" id="recalculate" tabindex="-1" role="dialog" aria-labelledby="recalculateLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document" style="text-align: left">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                        <h5 class="modal-title" id="recalculateLabel">Xác nhận</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        </div>
                                        <div class="modal-body">
                                            Bạn có muốn tính lại lương nhân viên
                                        </div>
                                        <div class="modal-footer">
                                        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Đóng</button>
                                        <button type="button" class="btn btn-primary btn-recalculate btn-sm" data-url="{{ route('admin.payrolls.recalculate', $user_payroll->id) }}">Lưu lại</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal edit-->
                            <div class="modal fade" id="update" tabindex="-1" role="dialog" aria-labelledby="updateLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document" style="text-align: left">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                        <h5 class="modal-title" id="updateLabel">Xác nhận</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        </div>
                                        <div class="modal-body">
                                            Bạn có muốn cập nhật lại lương nhân viên
                                        </div>
                                        <div class="modal-footer">
                                        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Đóng</button>
                                        <button type="button" class="btn btn-primary btn-edit btn-sm" data-url="{{ route('admin.payrolls.update', $user_payroll->id) }}">Lưu lại</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal approved-->
                            <div class="modal fade" id="approved" tabindex="-1" role="dialog" aria-labelledby="approvedLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document" style="text-align: left">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                        <h5 class="modal-title" id="approvedLabel">Xác nhận</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        </div>
                                        <div class="modal-body">
                                            Bạn chắc chắn duyệt lương nhân viên
                                        </div>
                                        <div class="modal-footer">
                                        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Đóng</button>
                                        <button type="button" class="btn btn-primary btn-approved btn-sm" data-url="{{ route('admin.payrolls.approved', $user_payroll->id) }}">Duyệt</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop
@section('footer')
    <script src="{!! asset('assets/backend/plugins/input-mask/jquery.inputmask.min.js') !!}"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    <script>
        $('.btn-info').on('click', function() {
            $('form').trigger("reset");
        });
        $(".currency").inputmask({'alias': 'integer', 'groupSeparator': '.', 'autoGroup': true, 'max': 999999999.99, 'removeMaskOnSubmit': true});
       
        var count = {{ count($other_amounts) > 0 ? (count($other_amounts) + 1) : 2}}
        $('.btn-add-other-amounts').on('click', function() {
            let html = `
                <tr class="tr-add-other">
                    <td style="width: 50px" class="text-center">${count++}</td>
                    <td>
                        <input type="text" name="name[]" class="form-control" required>
                    </td>
                    <td>
                        <input type="text" name="money[]" class="form-control currency" required>
                    </td>
                    <td style="width: 50px; text-align: center;">
                        <a href="javascript:void(0);" class="btn btn-xs btn-default remove-other-amounts">
                            <i class="text-danger fa fa-minus"></i>
                        </a>
                    </td>
                </tr>
            `;
            $('#add-template').append(html);
            $(".currency").inputmask({'alias': 'integer', 'groupSeparator': '.', 'autoGroup': true, 'removeMaskOnSubmit': true});
        });
        $(document).on("click", ".remove-other-amounts", function (event) {
            $(this).closest("tr").remove();
            count -= 1;
            let tmp = 1;
            $("#add-template td:first-child").each(function() {
                $(this).html(tmp++);
            });
        });
        var index = {{ count($deductions) > 0 ? (count($deductions) + 1) : 2}}
        // console.log(index);
        $('.btn-add-deductions').on('click', function() {
            let html = `
                <tr class="tr-add-other">
                    <td style="width: 50px" class="text-center">${index++}</td>
                    <td>
                        <input type="text" name="name[]" class="form-control" required>
                    </td>
                    <td>
                        <input type="text" name="money[]" class="form-control currency" required>
                    </td>
                    <td style="width: 50px; text-align: center;">
                        <a href="javascript:void(0);" class="btn btn-xs btn-default remove-deductions">
                            <i class="text-danger fa fa-minus"></i>
                        </a>
                    </td>
                </tr>
            `;
            $('#add-template-deductions').append(html);
            $(".currency").inputmask({'alias': 'integer', 'groupSeparator': '.', 'autoGroup': true, 'removeMaskOnSubmit': true});
        });
        $(document).on("click", ".remove-deductions", function (event) {
            $(this).closest("tr").remove();
            index -= 1;
            let tmp = 1;
            $("#add-template-deductions td:first-child").each(function() {
                $(this).html(tmp++);
            });
        });

        $('body').on('click', '.btn-edit', function(event){
            event.preventDefault();
            let url = $(this).data('url');

            $.ajax({
                url: url,
                type: "POST",
                headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                success:function(response) {
                    if (response.status == 'FAIL') {
                        toastr.error(response.message);
                    } else if (response.status == 'SUCCESS') {
                        toastr.success(response.message);
                        location.reload();
                    }
                }
            });
        });

        $('body').on('click', '.btn-approved', function(event){
            event.preventDefault();
            let url = $(this).data('url');

            $.ajax({
                url: url,
                type: "POST",
                headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                success:function(response) {
                    if (response.status == 'FAIL') {
                        toastr.error(response.message);
                    } else if (response.status == 'SUCCESS') {
                        toastr.success(response.message);
                        location.reload();
                    }
                }
            });
        });

        $('body').on('click', '.btn-recalculate', function(event) {
            event.preventDefault();
            let url = $(this).data('url');

            $.ajax({
                type: "POST",
                url: url,
                headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                success: function (response) {
                    if (response.status == 'FAIL') {
                        toastr.error(response.message);
                    } else if (response.status == 'SUCCESS') {
                        toastr.success(response.message);
                        window.location.href = response.url;
                    }
                }
            });
        });
    </script>
@stop