@extends('backend.master')
@section('title')
    {!! trans('system.action.list') !!} Các khoản tăng
@stop
@section('head')
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/select2/select2.min.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.css">
    <style>
        .treeview {
            margin-top: 10px; 
            margin-bottom: 10px; 
            margin-left: 10px;
        }

        .child {
            border: 1px solid #ecf0f5; 
            width: 90%; height: 40px; 
            /* margin-top: 10px;  */
            /* border-radius: 5px; */
        }

        .modal-header {
            background-color: #3c8dbc;
            color: white;
            text-align: center;
        }

        .modal-footer {
            text-align: center;
        }

        .error {
            width: 100%;
            height: 100px;
            line-height: 100px;
        }

        .text-size {
            font-size: 16px;
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

        tbody tr th.th-job {
            position:sticky;
            left:0;
            z-index: 102;
            background:  white
        }

        /* .tooltip-inner {
            max-width: 150px !important;
        } */
    </style>
@stop
@section('content')
    <section class="content-header">
        <h1>
            Các khoản tăng
            <small>{!! trans('system.action.list') !!}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.payoffs.index') !!}">{!! trans('system.action.list') !!}</a></li>
        </ol>
    </section>
    <section class="content overlay">

        <div class="box box-default">
            <div class="box-header with-bconsumer">
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                </div>
            </div>
            <div class="box-body">
                {!! Form::open([ 'url' => route('admin.payoffs.index'), 'method' => 'GET', 'role' => 'search' ]) !!}
                <div class="row">
                    <div style="position: relative;" class="col-md-12">
                        @permission('payoffs.create')
                        <input type="hidden" name="type" value="1">
                        @endpermission
                        <div class="col-md-3">
                            <label>{!! trans('timekeeping.company') !!}</label>
                            <select name="company_id" id="company" class="companySelect form-control select2">
                                <option value="" selected="selected">{{ trans('system.dropdown_choice') }}</option>
                                @foreach (\App\Helpers\GetOption::getCompaniesForOption() as $key => $item)
                                <option value="{{ $key }}">{{ $item }}</option>
                                @endforeach
                            </select>
                            <span class="text-danger">
                                <strong id="company-error"></strong>
                            </span>
                        </div>
                        <div class="col-md-3">
                            <label>{!! trans('timekeeping.department') !!}</label>
                            <select name="department_id" id="departmentSelect" class="form-control select2 department_id" disabled="true">
                                <option value="" {!! old('department_id') !!}>{!! trans('system.dropdown_choice') !!}</option>
                            </select>
                            <span class="text-danger">
                                <strong id="department-error"></strong>
                            </span>
                        </div>

                        {{-- <div class="col-md-3">
                            <label>{!! trans('timekeeping.company') !!}</label>
                            <select name="company_id" id="company" class="companySelect form-control select2">
                                <option value="" selected="selected">{{ trans('system.dropdown_choice') }}</option>
                                @foreach (\App\Helpers\GetOption::getCompaniesForOption() as $key => $item)
                                <option value="{{ $key }}">{{ $item }}</option>
                                @endforeach
                            </select>
                           
                        </div> --}}
                         
                        <div style="" class="col-md-2">
                            {!! Form::label('filter', 'Năm') !!}
                            <div class="form-group">
                                <select name="year" class="form-control select2 year-search">
                                    @for ($year = 2021; $year <= 2025; $year++)
                                        {{-- @if (intval($mc) <= $i) --}}
                                            <option 
                                                value="{{ $year }}" {{ intval(date('Y')) == $year ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        {{-- @endif --}}
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div style="" class="col-md-2">
                            {!! Form::label('filter', trans('system.action.label')) !!}
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-flat">
                                    <span class="glyphicon glyphicon-search"></span>&nbsp; {!! trans('system.action.search') !!}
                                </button>
                            </div>
                        </div>
                        
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
        @permission('payoffs.create')
        <div class="row">
            <div class="col-md-4">
                <a href="{{ route('admin.payoffs.create') }}" class="btn btn-primary btn-flat">
                    <span class="glyphicon glyphicon-plus"></span>&nbsp; Thêm mới
                </a>
                <div class="btn-group">
                    <a href="{!! route('admin.payoffs.create-bulk') !!}" class='btn btn-info btn-flat'>
                        <span class="glyphicon glyphicon-import"></span>&nbsp;{!! trans('system.action.import') !!}
                    </a>
                </div>
            </div>
        </div>
        @endpermission
        
        @if (count($users) == 0)
                <div class="alert alert-info">Tìm kiếm công ty / phòng ban để xem dach sách </div>
        @endif
        <div class="text-center">
            @if (count($users) > 0)
                <h3>Công ty {{ $users[0]->company->shortened_name }} - Năm {{ $data['year'] }}</h3>
            @endif
        </div>
        <div class="box">
            <div class="box-body no-padding" style="overflow-x:auto;">
                @if (count($users) > 0)
                <table class="table table-striped table-bordered" id="tableDeducitonsCreate">
                    <thead id="a">
                    <tr >
                        <th rowspan="3"  style="text-align: center; vertical-align: middle; width: 50px;" >{!! trans('system.no.') !!}</th>
                        <th class="sticky-col" rowspan="3"  style="vertical-align: middle; padding: 05px 20px 10px;" >&emsp; &emsp;  Họ và tên &emsp; &emsp; </th>
                        <th rowspan="3"  style="text-align: center; vertical-align: middle; width: 100px" >Mã nhân viên</th>
                        @for ($i = 1; $i <= 12; $i++)
                            <th colspan="3" style="text-align: center; vertical-align: middle; width: 100px" >{{ $i }}/{{ $data['year'] }}</th>
                        @endfor
                    </tr >
                    <tr>
                        @for ($i = 1; $i <= 12; $i++)
                            <th>Miễn thuế</th>
                            <th>Chịu thuế</th>
                            <th>Tổng</th>
                        @endfor
                    </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $payoffs = App\Models\PayOff::where('year', $data['year'])->whereIn('user_id', $users->pluck('id'))->get();
                        ?>
                        @foreach ($users as $key => $item)
                            <tr class="hover">
                                <td style="text-align: center; vertical-align: middle;">{{ $key + 1 }}</td>
                                <td class="sticky-col " style="text-align: center; vertical-align: middle;">{{ $item->fullname }}</td>
                                <td style="text-align: center; vertical-align: middle;">{{ $item->code }}</td>
                                @for ($i = 1; $i <= 12; $i++)
                                    <?php 
                                        $content_non_tax = '';
                                        $content_tax = '';
                                        if (!is_null($data['department_id'])) {
                                            $payoff = $payoffs->where('user_id', $item->id)->where('month', $i)->where('department_id', $data['department_id']);
                                        } else {
                                            $payoff = $payoffs->where('user_id', $item->id)->where('month', $i);
                                        }
                                        foreach ($payoff as $value) {
                                            if ($value['amount_money_non_tax'] > 0) {
                                                $content_non_tax .= ' ' . $value->adjustment->title  . ': ' . \App\Helper\HString::currencyFormatVn($value['amount_money_non_tax']) . '<br/>'; 
                                            }

                                            if ($value['amount_money_tax'] > 0) {
                                                $content_tax .=  $value->adjustment->title . ': ' . \App\Helper\HString::currencyFormatVn($value['amount_money_tax']) . '<br/>'; 
                                            }
                                        }

                                        if (!is_null($data['department_id'])) {
                                            $amount_money_non_tax = $payoffs->where('user_id', $item->id)->where('month', $i)->where('department_id', $data['department_id'])->sum('amount_money_non_tax');
                                            $amount_money_tax = $payoffs->where('user_id', $item->id)->where('month', $i)->where('department_id', $data['department_id'])->sum('amount_money_tax');
                                        } else {
                                            $amount_money_non_tax = $payoffs->where('user_id', $item->id)->where('month', $i)->sum('amount_money_non_tax');
                                            $amount_money_tax = $payoffs->where('user_id', $item->id)->where('month', $i)->sum('amount_money_tax');
                                        }
                                        $total = $amount_money_non_tax + $amount_money_tax;
                                    ?>
                                    
                                    <td style="text-align: right" data-html="true"  data-toggle="tooltip" data-placement="top" title="{{ $content_non_tax }}">
                                        {{ $amount_money_non_tax > 0 ? \App\Helper\HString::currencyFormatVn($amount_money_non_tax) : '' }}
                                    </td>
                                    <td style="text-align: right " data-html="true" data-toggle="tooltip" data-placement="top" title="{{ $content_tax }}">
                                        {{ $amount_money_tax > 0 ? \App\Helper\HString::currencyFormatVn($amount_money_tax) : '' }}
                                    </td>
                                    <td style="text-align: right">
                                        {{ $total > 0 ? \App\Helper\HString::currencyFormatVn($total) : '' }}
                                    </td>
                                @endfor

                            </tr>
                        @endforeach
                        <tr>
                            <th colspan="3" style="text-align: center; vertical-align: middle;" class="th-job">Tổng cộng</th>
                            @for ($i = 1; $i <= 12; $i++)
                            <?php 
                            $content_non_tax = '';
                            $content_tax = '';
                            $payoff = $payoffs->where('user_id', $item->id)->where('month', $i);
                            foreach ($payoff as $value) {
                                if ($value['amount_money_non_tax'] > 0) {
                                    $content_non_tax .= ' ' . $value['content'] . ': ' . \App\Helper\HString::currencyFormatVn($value['amount_money_non_tax']) . '<br/>'; 
                                }

                                if ($value['amount_money_tax'] > 0) {
                                    $content_tax .=  $value['content'] . ': ' . \App\Helper\HString::currencyFormatVn($value['amount_money_tax']) . '<br/>'; 
                                }
                            }
                            $amount_money_non_tax = $payoffs->where('month', $i)->where('department_id', $data['department_id'])->sum('amount_money_non_tax');
                            $amount_money_tax = $payoffs->where('month', $i)->where('department_id', $data['department_id'])->sum('amount_money_tax');
                            $total = $amount_money_non_tax + $amount_money_tax;
                        ?>

                             <th data-html="true"  data-toggle="tooltip" data-placement="top" title="{{ $content_non_tax }}" style="text-align: right">
                                {{ $amount_money_non_tax > 0 ? \App\Helper\HString::currencyFormatVn($amount_money_non_tax) : '' }}
                            </th>
                            <th  data-html="true" data-toggle="tooltip" data-placement="top" title="{{ $content_tax }}" style="text-align: right">
                                {{ $amount_money_tax > 0 ? \App\Helper\HString::currencyFormatVn($amount_money_tax) : '' }}
                            </th>
                            <th style="text-align: right">
                                {{ $total > 0 ? \App\Helper\HString::currencyFormatVn($total) : '' }}
                            </th>
                            @endfor
                        </tr>
                        
                    </tbody>           
                </table>
                @endif
                
            </div>
        </div>
    </section>
@stop
@section('footer')
    <script src="{!! asset('assets/backend/plugins/input-mask/jquery.inputmask.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/select2/select2.full.min.js') !!}"></script>
    <script type="text/javascript" charset="utf8"
            src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js"></script>
    <script>
        $(function() {
            $(".select2").select2({
                    width: '100%',
            });
            $('#table-detail-deduction').html('');
            $(".currency").inputmask({'alias': 'integer', 'groupSeparator': '.', 'autoGroup': true, 'removeMaskOnSubmit': true});

            var url_string  = window.location.href;
            var url = new URL(url_string);
            var year = url.searchParams.get('year');
            var company_id = url.searchParams.get('company_id');
            var department_id = url.searchParams.get('department_id');
            if (year) {
                $('.year-search').val(year).change();
            }
            if (company_id) {
                $('.company_id').val(company_id);
                $('#company').val(company_id).change();
            }
            
            !function ($) {
                $(function () {
                    $(".select2").select2({width: '100%'});
                });
            }(window.jQuery);

            let $currentRoute = {!! json_encode(\App\PermissionUserObject::getCurrentModule(\Route::getCurrentRoute())) !!};

            var oldDepartmentId = {!! old('department_id') ?? 0 !!};
            function setDepartmentOption() {
                let companyId = $('.companySelect'). val();
                if (companyId) {
                    $('#departmentSelect').attr('disabled', false)
                    $.ajax({
                        url: "{!! route('admin.contracts.setDepartmentOption') !!}",
                        data: {companyId: companyId, route: $currentRoute},
                        type: 'POST',
                        headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                        success: function (res) {
                            $('#departmentSelect option').remove()
                            $('#departmentSelect').append('<option value="">'+ '{!! trans('system.dropdown_choice') !!}'  + '</option>')
                            $.each(res, function (index, value) {
                                let isSelected = (oldDepartmentId == index || department_id == index) ? 'selected' : ''
                                $('#departmentSelect').append('<option value="' + index + '"' + isSelected + '>' + value + '</option>')
                            })
                        },
                        error: function (data) {
                            console.log(data)
                        }
                    })
                } else {
                    $('#departmentSelect').attr('disabled', true)
                }
            }
            $(document).on('change', '.companySelect', setDepartmentOption)
            if ($('.companySelect'). val()) {
                $('#departmentSelect').attr('disabled', false)
                setDepartmentOption()
            }

        });
        

    </script>
   
    <script>
        !function ($) {
            $(function () {

                $(".hover").hover(function(){
                     $(this).css("background-color", "#9ad0ff");
                     $(this).find('.sticky-col').css("background-color", "#9ad0ff");

                    }, function(){
                        $(this).css("background-color", "white");
                        $(this).find('.sticky-col').css("background-color", "white");
                });
            });
        }(window.jQuery);
    </script>
@stop
