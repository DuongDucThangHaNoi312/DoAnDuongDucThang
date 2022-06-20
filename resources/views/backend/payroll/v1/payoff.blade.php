@extends('backend.master')
@section('title')
    Các khoản tăng
@stop
@section('head')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.css">
    <style>
        
        .th-tc{
            min-width: 180px;
            text-align: center;
        }

        .tab {
            padding: 7px 0;
            margin-top: 5px;
        }
        .tab span {
            margin: 0 1px;
        }
        .tab span:first-child {
            margin-left: 0;
        }
        .tab span a {
            background-color: #c8d2e0;
            border-color: #c8d2e0;
            color: #FFFFFF;
            padding: 8px 9px;
        }
        .active-tab {
            background: #3c8dbc !important;
            border-color: #3c8dbc !important;
        }
    </style>
@stop
@section('content')
    <section class="content-header">
        <h1>
            <small style="font-weight: 600">
                Chi tiết khoản tăng NV: {{ Auth::user()->fullname }}
            </small>
        </h1>
        <div class="tab">
            <span class="all">
                 <a href="{!! route('admin.payrolls1.salary_user') !!}" class="hc-tab"
                    data-toggle="tooltip" data-placement="top" title="Lương"
                    style="outline: none;">
                    Lương
                 </a>
            </span>
            <span class="all">
                <a href="{{ route('admin.payroll.payoff') }}" class="shift-tab" target="_blank"
                   data-toggle="tooltip" data-placement="top" title="khoản tăng"
                   style="outline: none;">
                   Khoản tăng
                </a>
           </span>
        </div>
    </section>
    <section class="content overlay">
        <div class="box">
            @if (count($payoffs) > 0)
                <table class="table table-striped table-bordered" id="">
                    <thead>
                        <tr>
                            <th style="text-align: center; vertical-align: middle;">{!! trans('system.no.') !!}</th>
                            <th style="text-align: center; vertical-align: middle;" class="company_id">{!! trans('companies.name') !!}</th>
                            <th style="text-align: center; vertical-align: middle;"  class="department_id">{!! trans('workschedule.department') !!}</th>
                            <th style="text-align: center; vertical-align: middle;">Tháng</th>
                            <th style="text-align: center; vertical-align: middle;">Khoản tăng</th>
                            <th style="text-align: center; vertical-align: middle;">Số tiền</th>
                            <th style="text-align: center; vertical-align: middle;">Ghi chú</th>
                        </tr>
                    </thead>
                    <tbody id="list-item">
                        <?php $i = 0; ?>
                        @if (count($payoffs) > 0)  
                            @foreach ($payoffs as $key => $item)
                                <?php 
                                    if (intval($item->amount_money_tax) == 0 && intval($item->amount_money_non_tax) == 0) continue;
                                ?>
                                <?php $i++; ?>
                                <tr>
                                    <td style="text-align: center; vertical-align: middle;">{!! $i !!}</td>
                                    <td style="text-align: center; vertical-align: middle;" class="company_id">
                                        {!! $item->department->company->shortened_name !!}
                                    </td>
                                    <td style="text-align: center; vertical-align: middle;;"  class="department_id">
                                        {!! $item->department->name !!}
                                    </td>
                                    <td style="text-align: center; vertical-align: middle;">
                                        {{ $item->month . '/' . $item->year }} 
                                    </td>
                                    <td style="text-align: center; vertical-align: middle;">
                                        {{ $item->adjustment->title }}
                                    </td>
                                    <td style="text-align: right; vertical-align: middle;">
                                        {{ number_format(intval($item->amount_money_tax) + intval($item->amount_money_non_tax)) }}
                                    </td>
                                    <td style="text-align: center; vertical-align: middle;">
                                        {!! $item->note !!}
                                    </td>
                                </tr>
                            </div>
                            @endforeach 
                        @endif                      
                    </tbody>
                </table>
            @else   
                <div class="alert alert-primary" role="alert">
                    Không có khoản tăng nào!
                </div>
            @endif
        </div>
    </section>
    
@stop
@section('footer')

    <script>
        $(document).ready(function(){
            var url_href = '{{ route("admin.payroll.payoff") }}';
            if (url_href == window.location) {
                $('.shift-tab').addClass('active-tab');
            }
        });
    </script>
@stop