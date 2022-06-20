@extends('backend.master')
@section('title')
    {!! trans('system.action.create') !!} - Điều chỉnh khác
@stop
@section('head')
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/iCheck/all.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/select2/select2.min.css') !!}"/>
    <link rel="stylesheet" type="text/css"
          href="{!! asset('assets/backend/plugins/bootstrap-datepicker/css/bootstrap-datepicker.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/treegrid/css/jquery.treegrid.css') !!}">

    <style type="text/css">
        input[type=number]::-webkit-inner-spin-button {
            -webkit-appearance: none;
        }

        .fa-plus:before {
            content: "\f067";
        }
        #cancel{
            margin-bottom: 2%;
            background-color: #FFFFFF;
            margin-left: 80%;
            display: inline-block;
            border: 1px solid #0c0c0c;
            position: absolute;
            right: 2%;
            border-radius: 5px
        }
        .food{
            position: relative;
            margin-top: 2%;
        }
        #submitForm{
            background-color: #169BD5;
            width: 6%;
            border-radius: 5px;
            border: 1px solid #169BD5;
        }


    </style>
@stop
@section('content')
    <section class="content-header">
        <h1>
            Thêm mới các khoản điều chỉnh khác
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.targets.index') !!}">Các khoản thưởng</a></li>
        </ol>
    </section>
    @if($errors->count())
        <div class="alert alert-warning alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h4><i class="icon fa fa-warning"></i> {!! trans('messages.error') !!}</h4>
            <ul>
                @foreach($errors->all() as $message)
                    <li>{!! $message !!}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <?php $labels = ['default', 'success', 'info', 'danger', 'warning']; ?>
    {{-- {!! Form::open(['role' => 'form', 'id'=>'title']) !!} --}}
    <section class="content overlay">
        <div class="box box-default">
            <div class="box-header with-bconsumer">
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                </div>
            </div>
            <div class="box-body">
                {!! Form::open([ 'url' => route('admin.impales.create'), 'method' => 'GET', 'role' => 'search' ]) !!}
                <div class="row">
                    <div style="position: relative;" class="col-md-12">
                        <div class="col-md-3">
                            <label>{!! trans('timekeeping.company') !!}</label>
                            <select name="company_id" id="company" class="companySelect form-control select2">
                                <option value="" selected="selected">{{ trans('system.dropdown_choice') }}</option>
                                @foreach (\App\Helpers\GetOption::getCompaniesForOption() as $key => $item)
                                <option value="{{ $key }}">{{ $item }}</option>
                                @endforeach
                            </select>
                           
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
                            {{-- <div style="" class="col-md-3">
                                <div  class="form-group">
                                    <div class="form-group">
                                        {!! Form::label('department_filter', trans('kpi.name_department')) !!}
                                        {!! Form::select('department_filter', ['' =>  'Chọn phòng ban'] + $companies, old('department_filter'), ['class' => 'search-form departmentSelect select2', 'disabled', 'id' => "department_filter"]) !!}
                                    </div>
                                </div>
                            </div>
                            <div style="" class="col-md-4">
                                {!! Form::label('filter', trans('system.action.label')) !!}
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-flat">
                                        <span class="glyphicon glyphicon-search"></span>&nbsp; {!! trans('system.action.search') !!}
                                    </button>
                                </div>
                            </div> --}}
                           
                            <div style="" class="col-md-2">
                                {!! Form::label('filter', 'Tháng') !!}
                                <div class="form-group">
                                    <select name="month" class="form-control select2 month-serach" id="timestamp">
                                        @for ($month = 1; $month <= 12; $month++)
                                            {{-- @if (intval($mc) <= $i) --}}
                                                <option
                                                    value="{{ $month }}" {{ intval(date('m')) == $month ? 'selected' : '' }}>
                                                    {{ $month }}
                                                </option>
                                            {{-- @endif --}}
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div style="" class="col-md-2">
                                {!! Form::label('filter', 'Năm') !!}
                                <div class="form-group">
                                    <select name="year" class="form-control select2" class="year-search">
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
        {{-- <div class="row">
            <div class="col-md-12 text-right">
                {!!  $users->appends( Request::except('page') )->render() !!}
            </div>
        </div> --}}
        <div class="box">
            
            @if (count($users) == 0 && $type == '')
                <div class="alert alert-info">Tìm kiếm nhân viên theo phòng ban </div>
            @elseif (count($users) == 0 && $type == 'search')   
                <div class="alert alert-info">Không tìm thấy nhân viên của phòng ban </div>
            @else  
                <span class="" style="color: red; font-size: 14px"> <i>Chú ý: Nhập nội dung khoản điều chỉnh mới lưu bản ghi đó</i></span>
                <div class="box-body no-padding">
                    
                    <form id="formData" class="" action="{{ route('admin.impales.store') }}" method="POST">
                        @csrf
                        <input type="hidden" value="" name="month" class="month">
                        <input type="hidden" value="" name="year" class="year">
                        <table class="table table-striped table-bordered tree" id="table">
                            <thead>
                                <tr class="">
                                    <th style="text-align: center; vertical-align: middle;">{!! trans('system.no.') !!}</th>
                                    <th style="text-align: center; vertical-align: middle;">Tên nhân viên</th>
                                    <th style="text-align: center; vertical-align: middle;">Mã nhân viên</th>
                                    <th style="text-align: center; vertical-align: middle;">Nội dung khoản điều chỉnh</th>
                                    <th style="text-align: center; vertical-align: middle;">Số tiền</th>
                                    <th style="text-align: center; vertical-align: middle;">Người tạo</th>
                                    {{-- <th style="text-align: center; vertical-align: middle;">Chú thích</th> --}}
                                    <th style="text-align: center; vertical-align: middle;"></th>
                                    {{-- <th style="text-align: center; vertical-align: middle;">{!! trans('system.action.label') !!}</th> --}}
                                </tr>

                            </thead>
                            <tbody class="tbody">

                                @foreach ($users as $key => $item)
                                    <?php $key++; ?>
                                    <?php $impale = \App\Models\Impale::where('user_id', $item->id)->where('month', $data['month'])->where('year', $data['year'])->get(); ?>
                                    @if (count($impale) > 0)
                                        <tr class="treegrid-{{ $item->id }} {{ $key == 1 ? '' : '' }} ">
                                            <td style="text-align: center; vertical-align: middle;">{{ $key }}</td>
                                            <td style="text-align: center; vertical-align: middle;">{{ $item->fullname ?? '' }}</td>
                                            <td style="text-align: center; vertical-align: middle;">{{ $item->code ?? '' }}</td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                <input type="hidden" name="user_ids[]" value="{{ $item->id }}">
                                                <input type="text" name="content_{{ $item->id }}[]" id="" class="form-control" value="{{ $impale[0]->content }}">
                                            </td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                <input type="text" name="amount_money_{{ $item->id }}[]" id="" class="form-control money" value="{{ $impale[0]->amount_money }}">
                                            </td>
                                            
                                            <td style="text-align: center; vertical-align: middle;" class="fullname">
                                                {{ $impale[0]->createdByImpale->fullname }}
                                            </td>
                                            <td style="text-align: center; vertical-align: middle; width: 100px;">
                                                <input type="text" id="" value="1" class="form-control add-line inputmask" style="width: 50px; height: 24px; float: left;">
                                                <a href="javascript:void(0);" class="btn btn-success btn-xs add-column" style="float: left;" 
                                                    data-id="{{ $item->id }}" data-key="{{ $key }}" data-fullname="{{ $item->fullname}}" data-code="{{ $item->code }}">
                                                    <span class="glyphicon glyphicon-plus"></span>
                                                </a>
                                            </td>
                                        </tr>
                                        @foreach ($impale as $i => $value)
                                            @if ($i > 0)
                                                <tr class="treegrid-parent-{{ $item->id }} ">
                                                    <td style="text-align: center; vertical-align: middle;"></td>
                                                    <td style="text-align: center; vertical-align: middle;">{{ $item->fullname ?? '' }}</td>
                                                    <td style="text-align: center; vertical-align: middle;">{{ $item->code ?? '' }}</td>
                                                    <td style="text-align: center; vertical-align: middle;">
                                                        <input type="hidden" name="user_ids[]" value="">
                                                        <input type="text" name="content_{{ $item->id }}[]" id="" class="form-control" value="{{ $value->content }}">
                                                    </td>
                                                    <td style="text-align: center; vertical-align: middle;">
                                                        <input type="text" name="amount_money_{{ $item->id }}[]" id="" class="form-control money" value="{{ $value->amount_money }}">
                                                    </td>
                                                   
                                                    <td style="text-align: center; vertical-align: middle;" class="fullname">
                                                        {{ $value->createdByImpale->fullname }}
                                                    </td>
                                                    <td style="width: 50px;">
                                                        <a href="javascript:void(0);" class="btn btn-xs btn-default remove-cloumn">
                                                            <i class="text-danger fa fa-minus"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                            
                                        
                                    @else     
                                        <tr class="treegrid-{{ $item->id }} {{ $key == 1 ? '' : '' }} ">
                                            <td style="text-align: center; vertical-align: middle;">{{ $key }}</td>
                                            <td style="text-align: center; vertical-align: middle;">{{ $item->fullname ?? '' }}</td>
                                            <td style="text-align: center; vertical-align: middle;">{{ $item->code ?? '' }}</td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                <input type="hidden" name="user_ids[]" value="{{ $item->id }}">
                                                <input type="text" name="content_{{ $item->id }}[]" id="" class="form-control">
                                            </td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                <input type="text" name="amount_money_{{ $item->id }}[]" id="" class="form-control money">
                                            </td>
                                           
                                            <td style="text-align: center; vertical-align: middle;" class="fullname">
                                            </td>
                                            
                                            <td style="text-align: center; vertical-align: middle; width: 100px;">
                                                <input type="text" id="" value="1" class="form-control add-line inputmask" style="width: 50px; height: 24px; float: left;">
                                                <a href="javascript:void(0);" class="btn btn-success btn-xs add-column" style="float: left;" 
                                                    data-id="{{ $item->id }}" data-key="{{ $key }}" data-fullname="{{ $item->fullname}}" data-code="{{ $item->code }}">
                                                    <span class="glyphicon glyphicon-plus"></span>
                                                </a>
                                            </td>
                                        </tr>
                                    @endif
                                    
                                @endforeach
                                
                                
                            </tbody>
                        </table>
                        <div class="row">
                            <div style="text-align: center" class="col-sm-12">
                                {!! Form::submit(trans('system.action.save'), ['class' => 'btn btn-primary btn-flat submit']) !!}
                            </div>
                        </div>
                    {!! Form::close() !!}
                </div>
            @endif
            
        </div>
    </section>
    
@stop
@section('footer')
    <script src="{!! asset('assets/backend/plugins/iCheck/icheck.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/select2/select2.full.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/moment/min/moment-with-locales.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/input-mask/jquery.inputmask.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.vi.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/treegrid/js/jquery.treegrid.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/input-mask/jquery.inputmask.min.js') !!}"></script>

    <script type="text/javascript" charset="utf8"
            src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js"></script>    <script>
        !function ($) {
            
            $(function () {
                $(".select2").select2({width: '100%'});
            });

            $('.inputmask').inputmask({
                'placeholder': '',
                regex: '^[0-9]{2}',
            });

           


        }(window.jQuery);
        $(".money").inputmask({'alias': 'integer', 'groupSeparator': '.', 'autoGroup': true, 'removeMaskOnSubmit': true});

        var url_string  = window.location.href;
        var url = new URL(url_string);
        var month = url.searchParams.get('month');
        var year = url.searchParams.get('year');
        var company_id = url.searchParams.get('company_id');
        var department_id = url.searchParams.get('department_id');

        if (month) {
            $('.month').val(month);   
            $('.month-serach').val(month).change();
        }
        if (year) {
            $('.year').val(year);
            $('.year-search').val(year).change();

        }
        if (company_id) {
            $('.company_id').val(company_id);
            $('#company').val(company_id).change();

        }
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
                            let isSelected = oldDepartmentId == index || department_id == index  ? 'selected' : '';
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
        
    </script>
    <script>
        $(document).ready(function () {
            $('.tree').treegrid();

            $('.add-column').on('click', function () {
                let key = $(this).data('key');
                let id = $(this).data('id');
                let index = parseInt($(this).closest('tr').index()) + parseInt(1);
                let add_line = $(this).parent().find('.add-line').val();
                let html = '';
                let fullname = $(this).data('fullname');
                let user_code = $(this).data('code');

                for (let i = 1; i <= add_line; i++) {
                    html += `
                        <tr class="treegrid-parent-${id}">
                            <td style="text-align: center; vertical-align: middle;"></td>
                            <td style="text-align: center; vertical-align: middle;">${fullname}</td>
                            <td style="text-align: center; vertical-align: middle;">${user_code}</td>
                            <td style="text-align: center; vertical-align: middle;">
                                <input type="text" name="content_${id}[]" id="" class="form-control">
                            </td>
                            <td style="text-align: center; vertical-align: middle;">
                                <input type="text" name="amount_money_${id}[]" id="" class="form-control money">
                            </td>
                          
                            <td style="text-align: center; vertical-align: middle;" class="fullname">
                            </td>
                            <td style="width: 50px;">
                                <a href="javascript:void(0);" class="btn btn-xs btn-default remove-cloumn">
                                    <i class="text-danger fa fa-minus"></i>
                                </a>
                            </td>
                        </tr>
                    `;
                }

                let tr_class = `table tr:eq(${index})`;
                $(tr_class).after(html);
                
                $('.tree').treegrid();
                $(".money").inputmask({'alias': 'integer', 'groupSeparator': '.', 'autoGroup': true, 'removeMaskOnSubmit': true});

            });

            $(document).on("click", ".remove-cloumn", function (event) {
                $(this).closest("tr").remove();
                
            });

            $('.submit').on('click', function () {
                var registerForm = $("#formData");
                var formData = registerForm.serialize();

                $.ajax({
                    type: "POST",
                    url: "{{ route('admin.impales.store') }}",
                    data: formData,
                    headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                    success: function (response) {
                        if (response.status == 200) {
                            toastr.success(response.message);
                            location.reload();
                        }

                        if (response.status == 400) {
                            toastr.error(response.message);
                        }
                    }
                });
            })
        });
    </script>
@stop
