@extends('backend.master')
@section('title')
    {!! trans('payrolls.detail') !!} {!! trans('payrolls.label') !!}
@stop
@section('head')
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/iCheck/all.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/select2/select2.min.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.css">

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

        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type=number] {
            -moz-appearance: textfield;
        }

        b, strong {
            font-weight: 500;
        }

        table {
            border: 1px solid #bbb;
            border-collapse: collapse;
            border-spacing: 0;
            
        }

        .tdbreak {
            word-break: break-all
        }
        
        thead tr th {
            white-space: nowrap;
            text-overflow: clip;
        }

        .uppercase {
            text-transform: uppercase;
        }

        .sticky-col1 {
            position: -webkit-sticky;
            position: sticky;
            background-color: white;
            left: 0;
            z-index: 220 !important;
        }

        .sticky-col {
            position: -webkit-sticky;
            position: sticky;
            background-color: white;
            left: 0;
            z-index: 200 !important;
        }

        .table-scroll {
            position: relative;
            width:100%;
            margin: auto;
            overflow: auto;
            max-height: 500px;
        }
        .table-scroll table {
            width: 100%;
            min-width: 1280px;
            margin: auto;
            border-collapse: separate;
        }
        .table-wrap {
            position: relative;
        }
        .table-scroll th,
        .table-scroll td {
            padding: 8px;
            vertical-align: top;
            border-right: 1px solid #D2D6DE;
            border-bottom: 1px solid #D2D6DE;
        }
        thead tr th.fixed-1 {
            background: #EBEEF4;
            color: #367FA9;
            z-index: 101;
            position: -webkit-sticky;
            position: sticky;
            top: 0;
            /*border-right: 1px solid #D2D6DE;*/
            /*border-top: 1px solid #D2D6DE;*/
        }

        thead tr th.fixed-2 {
            background: #EBEEF4;
            color: #367FA9;
            z-index: 101;
            position: -webkit-sticky;
            position: sticky;
            top: 36px;
            /*border-right: 1px solid #D2D6DE;*/
            /*border-top: 1px solid #D2D6DE;*/
        }

        thead tr th.fixed-3 {
            background: #EBEEF4;
            color: #367FA9;
            z-index: 101;
            position: -webkit-sticky;
            position: sticky;
            top: 92px;
            /*border-right: 1px solid #D2D6DE;*/
            /*border-top: 1px solid #D2D6DE;*/
        }

       
        /*th.col-fixed {*/

            
        /* .sticky-col {
            position: -webkit-sticky;
            position: sticky;
            left: 0;
            z-index: 8;
            background-color: white;
        } */
        
        .th-tc{
            min-width: 180px;
            text-align: center;
        }
    </style>
@stop
@section('content')
    <section class="content-header">
        <h1>
            {!! trans('salary_cont.label_table') !!}
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.salary-choose-containers.index') !!}">{!! trans('payrolls.detail') !!} {!! trans('payrolls.label') !!}</a></li>
        </ol>
    </section>
    <div class="text-center">
        <h3> Chi tiết phân bổ thưởng tờ khai cho nhân viên  {!! $departmentGroupCode[$departmentGroup] !!} - {!! $companyCode[$company] !!} </h3>
        <h4>Tháng: {!! $monthYear !!}</h4>
    </div>
    <section class="content overlay">
        <div class="text-left">
           <h4>Tổng tiền thưởng : <b>{!! \App\Helper\HString::currencyFormat($rewardPoint) !!}</b></h4>
        </div>
        <div class="text-center">
           
        </div>
        <div class="text-center">
           
        </div>
        {!! Form::open([ 'url' => route('admin.salary-declarations.saveSalaryUser'), 'method' => 'POST', 'id' => 'saveSalaryUser']) !!}
            <input type="hidden" name="company_id" value="{!! $company !!}">
            <input type="hidden" name="department_group_id" value="{!! $departmentGroup !!}">
            <input type="hidden" name="month" value="{!! $month !!}">
            <input type="hidden" name="year" value="{!! $year !!}">
            <div class="box">
                <div class="box-body no-padding" style="overflow-x:auto; overflow-x:auto;">
                    <div id="table-scroll" class="table-scroll">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th style="text-align: center; vertical-align: middle;" colspan="6">
                                        <span>THỐNG KÊ NHÂN SỰ</span><br>
                                    </th>
                                </tr>
                                <tr>
                                    <th style="text-align: center; vertical-align: middle;">
                                        <span>STT</span><br>
                                    </th>
                                    <th style="text-align: center; vertical-align: middle;">
                                        <span>Mã NV</span>
                                    </th>
                                    <th style="text-align: center; vertical-align: middle;">
                                        <span>Họ tên </span>
                                    </th>
                                    <th style="text-align: center; vertical-align: middle;">
                                        <span>Tỷ lệ (%) </span>
                                    </th>
                                    <th  style="text-align: center; vertical-align: middle;">
                                        <span class="">Phần thưởng</span>
                                    </th>
                                    <th  style="text-align: center; vertical-align: middle;">
                                        <span class="">Ghi chú</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $i=1;  @endphp
                                @foreach ($user as $key  => $value)
                                @php $user = explode('-', $userCodes[$value]);  @endphp
                                    <tr >
                                        <td style="text-align: center; vertical-align: middle;">{!! $i !!}</td>
                                        <td style="text-align: center; vertical-align: middle;">{!! $user[1] !!}</td>
                                        <td style="text-align: center; vertical-align: middle;">{!! $user[0] !!}</td>
                                        <td style="text-align: center; vertical-align: middle;">
                                            {!! Form::text('ratio[]',old('ratio', ($money[$value]['ratio']) ? $money[$value]['ratio'] : 0) , ["class" => "form-control ratio currency disable", 'autocomplete' => 'off']) !!}
                                        </td>
                                        <td style="text-align: center; vertical-align: middle;">
                                            {!! Form::text('money[]',old('money', ($money[$value]['money']) ? $money[$value]['money'] : 0), ["class" => "form-control money currency disable", 'autocomplete' => 'off', 'readonly']) !!}
                                        </td>
                                        <td style="text-align: center; vertical-align: middle;">
                                            {!! Form::text('note[]',old('note', ($money[$value]['note']) ? $money[$value]['note'] : ''), ["class" => "form-control note disable", 'autocomplete' => 'off']) !!}
                                        </td>
                                        {!! Form::hidden('user[]',old('user',  $value), ["class" => "form-control disable", 'autocomplete' => 'off']) !!}
                                    </tr>
                                    @php $i++ ; @endphp
                                @endforeach
                                <tr>
                                    <th style="text-align: center; vertical-align: middle;" colspan="3">
                                        <span>Tổng</span><br>
                                    </th>
                                    <th  style="text-align: center; vertical-align: middle;">
                                        {!! Form::text('total_ratio',old('total_ratio', array_sum(array_column($money, 'ratio'))), ["class" => "form-control total_ratio currency disable", 'autocomplete' => 'off']) !!}
                                    </th>
                                    <th  style="text-align: center; vertical-align: middle;">
                                        {!! Form::text('total_money',old('total_money', array_sum(array_column($money, 'money'))), ["class" => "form-control total_money currency disable", 'autocomplete' => 'off']) !!}
                                    </th>
                                    <th  style="text-align: center; vertical-align: middle;"></th>
                                </tr>    
                            </tbody>
                        </table>
                        <br>
                        @if (!$ktApproved)
                            <div class="text-center">
                                <a href="{!! route( 'admin.salary-declarations.index' ) !!}" class="btn btn-danger btn-flat disable">Hủy bỏ</a>
                                <a class="btn btn-primary btn-flat btn-save disable" id="btnSave" >Lưu lại</a>
                            </div>
                        @else 
                            <div class="text-center">
                                <a href="{!! route( 'admin.salary-declarations.index' ) !!}" class="btn btn-danger btn-flat ">Trở lại</a>
                            </div>
                        @endif
                        <br>
                    </div>
                </div>
            </div>
        {!! Form::close() !!}
    </section>
@stop
@section('footer')
<script src="{!! asset('assets/backend/plugins/input-mask/jquery.inputmask.min.js') !!}"></script>
<script src="{!! asset('assets/backend/js/call-plugins.js') !!}?v=12-05-2022"></script>
<script>
    $(document).ready(function () {
        const REWARD_POINT = @json($rewardPoint);
        $(".ratio, .total_ratio").inputmask('currency', {
            'alias': 'integer',
            'rightAlign': true,
            'autoGroup': true,
            'digits': 2,
            'max': 999,
        });
        
        $(".money, .total_money").inputmask('currency', {
            'alias': 'integer',
            'rightAlign': true,
            'autoGroup': true,
            'digits': 0,
            'max': 9999999999,
        });

        $(document).on('change', '.ratio', function () {
            maxLimitRatio();
            console.log(error);
            if (error) {
                $('#btnSave').prop('readonly', true);
            }
        })

        error = false;
        function maxLimitRatio() {
            var totalRatio = parseFloat(0.00);
            var totalMoney = 0;
            var ratio = $('.ratio');
            ratio.each(function () {
                var ratio = parseFloat(0.00);
                if ($(this).val() != '') {
                    ratio = parseFloat($(this).val());
                }
                var money = calMoney(ratio);
                $(this).closest('tr').find('.money').val(money);
                totalRatio += parseFloat(ratio);
                totalMoney += parseFloat(money);

                $('.total_ratio').val(totalRatio);
                $('.total_money').val(totalMoney);
                if (parseFloat(totalRatio) > parseFloat(100.00)) {
                    toastr.error("Tổng tỉ lệ không được lớn hơn 100 %");
                    error = true;
                    return false;
                } else {
                    error = false;
                }
            })
        }

        function calMoney(ratio) {
            return parseInt((ratio/100)*REWARD_POINT).toFixed(2);
        }

    });
</script>

<script>
    $('.btn-save').on('click', function(){
        var data = $('#saveSalaryUser').serialize();
        var url = @json(route('admin.salary-declarations.saveSalaryUser'));
        $.ajax({
            type: "post",
            url: url,
            data: data,
            dataType: "json",
            headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
            success: function(res) {
                toastr.success(res.message);
                window.location.reload();
            },
            error: function(err) {
                console.log(err);
                toastr.error(err.responseJSON.message);
            },
        })
    })
</script>

<script>
    // disabled
    var ktApproved = @json($ktApproved);
    if (ktApproved)  $('.disable').prop('disabled', true);
</script>


@stop