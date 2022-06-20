    @extends('backend.master')
    @section('title')
        {!! trans('system.action.create') !!} - Khoản tăng
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
            tr td .money-tax{
                text-align: center;
            }


        </style>
    @stop
    @section('content')
        <section class="content-header">
            <h1>
                Thêm mới các khoản tăng
            </h1>
            <ol class="breadcrumb">
                <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
                <li><a href="{!! route('admin.targets.create') !!}">Các khoản tăng</a></li>
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
                    {!! Form::open([ 'url' => route('admin.payoffs.create'), 'method' => 'GET', 'role' => 'search' ]) !!}
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
                                <div style="" class="col-md-2">
                                    {!! Form::label('filter', 'Tháng') !!}
                                    <div class="form-group">
                                        <select name="month" class="form-control select2 month-serach" id="timestamp">
                                            @for ($month = 1; $month <= 12; $month++)
                                                    <option
                                                        value="{{ $month }}" {{ intval(date('m')) == $month ? 'selected' : '' }}>
                                                        {{ $month }}
                                                    </option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                                <div style="" class="col-md-2">
                                    {!! Form::label('filter', 'Năm') !!}
                                    <div class="form-group">
                                        <select name="year" class="form-control select2 year-search">
                                            @for ($year = 2021; $year <= 2025; $year++)
                                                    <option 
                                                        value="{{ $year }}" {{ intval(date('Y')) == $year ? 'selected' : '' }}>
                                                        {{ $year }}
                                                    </option>
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
            <div class="box">
                @if (count($users) == 0 && $type == '')
                    <div class="alert alert-info">Tìm kiếm nhân viên theo phòng ban </div>
                @elseif (count($users) == 0 && $type == 'search')   
                    <div class="alert alert-info">Không tìm thấy nhân viên của phòng ban </div>
                @else  
                    <span class="" style="color: red; font-size: 14px"> <i>Chú ý: Nhập nội dung khoản tăng mới lưu bản ghi đó</i></span>
                    <div class="box-body no-padding">
                            {!! Form::open(['id' => 'formData', 'method' => 'POST']) !!}
                            <input type="hidden" value="" name="month" class="month">
                            <input type="hidden" value="" name="year" class="year">
                            <input type="hidden" value="{{ $data['department_id'] }}" name="department_id" class="">
                            <table class="table table-striped table-bordered tree" id="table">
                                <thead>
                                    <tr class="">
                                        <th style="text-align: center; vertical-align: middle;">{!! trans('system.no.') !!}</th>
                                        <th style="text-align: center; vertical-align: middle;">Tên nhân viên</th>
                                        <th style="text-align: center; vertical-align: middle;width : 150px">Mã nhân viên</th>
                                        <th style="text-align: center; vertical-align: middle;width : 150px">Danh mục khoản tăng</th>
                                        <th style="text-align: center; vertical-align: middle; width : 150px">Loại</th>
                                        <th style="text-align: center; vertical-align: middle;">Số tiền</th>
                                        <th style="text-align: center; vertical-align: middle;">Ghi chú</th>
                                        <th style="text-align: center; vertical-align: middle;">Người tạo</th>
                                        <th style="text-align: center; vertical-align: middle;"></th>
                                    </tr>
                                </thead>
                                <tbody class="tbody">
                                    @foreach ($users as $key => $item)
                                        <?php $key++; ?>
                                        <?php $payoffs = \App\Models\PayOff::where('user_id', $item->id)->where('month', $data['month'])->where('year', $data['year'])
                                                        ->where('department_id', $data['department_id'])
                                                        ->get();
                                        ?>
                                        @if (count($payoffs) > 0)
                                            <tr class="treegrid-{{ $item->id }} {{ $key == 1 ? '' : '' }} ">
                                                <td style="text-align: center; vertical-align: middle;">{{ $key }}</td>
                                                <td style="text-align: center; vertical-align: middle;">{{ $item->fullname ?? '' }}</td>
                                                <td style="text-align: center; vertical-align: middle;" class="{{ $item->code  }}">{{ $item->code ?? '' }}</td>
                                                <td style="text-align: center; vertical-align: middle;" class="td-content-tax-select2">
                                                    <input type="hidden" name="user_ids[]" value="{{ $item->id }}" class="content-tax-select2">
                                                    {!! Form::select('content_' . $item->id . '[]', ['' => trans('payoffs.content')] + \App\Models\Adjustment::category(), old('content_' . $item->id . '[]', $payoffs[0]->category), ['class' => "form-control select2 content-tax", 'required', 'data-id'=>$item->id]) !!}
                                                </td>
                                                <td style="text-align: center; vertical-align: middle;">
                                                    <input type="text" name="amount_money_non_tax_{{ $item->id }}[]" id="amount_money_non_tax_{{ $item->id }}" readonly class="form-control money-tax" value="{{ ($payoffs[0]->amount_money_non_tax) > 0  ? 'Miễn Thuế' : 'Chịu Thuế' }}">
                                                </td>
                                                <td style="text-align: center; vertical-align: middle;">
                                                    <input type="text" name="amount_money_tax_{{ $item->id }}[]" id="amount_money_tax_{{ $item->id }}" class="form-control money" value="{!! ($payoffs[0]->amount_money_tax) > 0 ? ($payoffs[0]->amount_money_tax) :  ($payoffs[0]->amount_money_non_tax) !!}">
                                                </td>
                                                <td style="text-align: center; vertical-align: middle;">
                                                    <input type="text" name="note_{{ $item->id }}[]" id="note_{{ $item->id }}"  class="form-control " value="{{ ($payoffs[0]->note)   ?? '' }}">
                                                </td>
                                                <td style="text-align: center; vertical-align: middle;" class="fullname">
                                                    {{ $payoffs[0]->createdByPayOff->fullname }}
                                                </td>
                                                @if ($status != 'APPROVED')
                                                    <td style="text-align: center; vertical-align: middle; width: 120px;">
                                                        <input type="text" id="" value="1" class="form-control add-line inputmask" style="width: 35px; height: 24px; float: left;">
                                                        <a href="javascript:void(0);" class="btn btn-success btn-xs add-column"  
                                                            data-id="{{ $item->id }}" data-key="{{ $key }}" data-fullname="{{ $item->fullname}}" data-code="{{ $item->code }}" data-fullnamecurrent="{{ $user_current->fullname}}">
                                                            <span class="glyphicon glyphicon-plus"></span>
                                                        </a>
                                                        <a  class="reset btn  btn-warning btn-xs">
                                                            <i class="fas fa-minus-circle"></i>
                                                        </a>
                                                    </td>
                                                @endif
                                                
                                            </tr>
                                            @foreach ($payoffs as $i => $value)
                                                @if ($i > 0)
                                                    <tr class="treegrid-parent-{{ $item->id }} ">
                                                        <td style="text-align: center; vertical-align: middle;"></td>
                                                        <td style="text-align: center; vertical-align: middle;">{{ $item->fullname ?? '' }}</td>
                                                        <td style="text-align: center; vertical-align: middle;"  class="{!! $item->code !!}">{{ $item->code ?? '' }}</td>
                                                        <td style="text-align: center; vertical-align: middle;" class="td-content-tax-select2">
                                                            <input type="hidden" name="user_ids[]" value="" class="content-tax-select2">
                                                            {!! Form::select('content_' . $item->id . '[]', ['' => trans('payoffs.content')] + \App\Models\Adjustment::category(), old('content_' . $item->id . '[]', $value->category), ['class' => "form-control select2 content-tax", 'required', 'data-id'=>$item->id.$i]) !!}                                                    
                                                        </td>
                                                        <td style="text-align: center; vertical-align: middle;">
                                                            <input type="text" name="amount_money_non_tax_{{ $item->id }}[]" id="amount_money_non_tax_{{ $item->id.$i }}" readonly class="form-control money-tax" value="{{ ($value->amount_money_non_tax > 0) ? 'Miễn Thuế' : 'Chịu Thuế' }}">
                                                        </td>
                                                        <td style="text-align: center; vertical-align: middle;">
                                                            <input type="text" name="amount_money_tax_{{ $item->id }}[]" id="amount_money_tax_{{ $item->id.$i }}" class="form-control money" value="{{ ($value->amount_money_tax) > 0 ? $value->amount_money_tax : $value->amount_money_non_tax }}">
                                                        </td>
                                                        <td style="text-align: center; vertical-align: middle;">
                                                            <input type="text" name="note_{{ $item->id }}[]" id="note_{{ $item->id.$i }}" class="form-control note" value="{{ ($value->note)  ?? '' }}">
                                                        </td>
                                                        <td style="text-align: center; vertical-align: middle;" class="fullname">
                                                            {{ $value->createdByPayOff->fullname }}
                                                        </td>
                                                        @if ($status != 'APPROVED')
                                                            <td style="width: 50px;">
                                                                <a href="javascript:void(0);" class="btn btn-xs btn-default remove-cloumn">
                                                                    <i class="text-danger fa fa-minus"></i>
                                                                </a>
                                                            </td>
                                                        @endif
                                                        
                                                    </tr>
                                                @endif
                                            @endforeach
                                        @else    
                                        
                                            <tr class="treegrid-{{ $item->id }} {{ $key == 1 ? '' : '' }} ">
                                                <td style="text-align: center; vertical-align: middle;">{{ $key }}</td>
                                                <td style="text-align: center; vertical-align: middle;" >{{ $item->fullname ?? '' }}</td>
                                                <td style="text-align: center; vertical-align: middle;" class="{!! $item->code !!}">{{ $item->code ?? '' }}</td>
                                                <td style="text-align: center; vertical-align: middle;" class="td-content-tax-select2">
                                                    <input type="hidden" name="user_ids[]" value="{{ $item->id }}"  class="content-tax-select2">
                                                    {!! Form::select('content_' . $item->id . '[]', ['' => trans('payoffs.content')] + \App\Models\Adjustment::category(), old(), ['class' => "form-control select2 content-tax", 'required', 'data-id'=>$item->id]) !!}
                                                </td>
                                                <td style="text-align: center; vertical-align: middle;">
                                                    <input type="text" name="amount_money_non_tax_{{ $item->id }}[]" id="amount_money_non_tax_{{ $item->id }}" readonly class="form-control money-tax">
                                                </td>
                                                <td style="text-align: center; vertical-align: middle;">
                                                    <input type="text" name="amount_money_tax_{{ $item->id }}[]" id="amount_money_tax_{{ $item->id }}" class="form-control money">
                                                </td>
                                                <td style="text-align: center; vertical-align: middle;">
                                                    <input type="text" name="note_{{ $item->id }}[]" id="note_{{ $item->id }}" class="form-control note">
                                                </td>
                                                <td style="text-align: center; vertical-align: middle;" class="fullname" >{!! $user_current->fullname !!}
                                                </td>
                                                
                                                @if ($status != 'APPROVED')
                                                    <td style="text-align: center; vertical-align: middle; width: 120px;">
                                                        <input type="text" id="" value="1" class="form-control add-line inputmask" style="width: 35px; height: 24px; float: left;">
                                                        <a href="javascript:void(0);" class="btn btn-success btn-xs add-column" 
                                                            data-id="{{ $item->id }}" data-key="{{ $key }}" data-fullname="{{ $item->fullname}}" data-code="{{ $item->code }}" data-fullnamecurrent="{{ $user_current->fullname}}">
                                                            <span class="glyphicon glyphicon-plus"></span>
                                                        </a>
                                                        <a  class="reset btn  btn-warning btn-xs">
                                                            <i class="fas fa-minus-circle"></i>
                                                        </a>
                                                    </td>
                                                @endif
                                                
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="row">
                                <div style="text-align: center" class="col-sm-12">
                                    <a href="{{ route('admin.payoffs.index') }}" class="btn btn-danger btn-flat">Hủy bỏ</a>
                                    @if ($status != 'APPROVED')
                                        {!! Form::button(trans('system.action.save'), ['class' => 'btn btn-primary btn-flat submit']) !!}
                                    @endif
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
            $(".money").inputmask({'alias': 'integer', 'groupSeparator': ',', 'autoGroup': true, 'removeMaskOnSubmit': true});

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
            $('.reset').on('click', function () {
                tr =  $(this).closest("tr")
                tr.find('.money-tax').val('');
                tr.find('.money').val('');
                tr.find('.content-tax').val('').change();
                })
            });
        </script>


        <script>
            $(document).ready(function () {
                $('.tree').treegrid();

                $('.add-column').on('click', function () {
                    // console.log('123');

                    let key = $(this).data('key');
                    let id = $(this).data('id');
                    let index = parseInt($(this).closest('tr').index()) + parseInt(1);
                    let add_line = $(this).parent().find('.add-line').val(); //add số dong
                    let html = '';
                    let fullname = $(this).data('fullname');
                    let fullnamecurrent = $(this).data('fullnamecurrent');
                    let user_code = $(this).data('code');
                    for (let i = 1; i <= add_line; i++) {
                        let data_id = id +'-'+i;
                        html += `
                            <tr class="treegrid-parent-${id}">
                                <td style="text-align: center; vertical-align: middle;"></td>
                                <td style="text-align: center; vertical-align: middle;" >${fullname}</td>
                                <td style="text-align: center; vertical-align: middle;" class = ${user_code} >${user_code}</td>
                                <td style="text-align: center; vertical-align: middle;">
                                    {!! Form::select('content_${id}[] ', ['' => trans('payoffs.content')] + \App\Models\Adjustment::category(), old('content_${id}[]'), ['class' => 'form-control select2 content-tax', 'required', 'data-id'=>'${data_id}']) !!}
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    <input type="text"  name="amount_money_non_tax_${id}[]" id="amount_money_non_tax_${data_id}" readonly  class="form-control money-tax">
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    <input type="text"  name="amount_money_tax_${id}[]" id="amount_money_tax_${data_id}"  class="form-control money">
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    <input type="text"  name="note_${id}[]" id="note_${data_id}"  class="form-control note">
                                </td>
                                <td style="text-align: center; vertical-align: middle;" class="fullname">${fullnamecurrent}
                                </td>
                                <td style="width: 50px;">
                                    <a href="javascript:void(0);" class="btn btn-xs btn-default remove-cloumn">
                                        <i class="text-danger fa fa-minus"></i>
                                    </a>
                                </td>
                            </tr>
                        `;
                    }

                    let tr = $(this).closest("tr")
                    use_code_select = tr.find( "td" ).eq(2).attr("class");
                    let posstion_tr_add_tr  = $("." + use_code_select ).last().closest("tr");
                    posstion_tr_add_tr.after(html);

                    // let tr_class = `table tr:eq(${index})`;
                    // $(tr_class).after(html);
                    $('.tree').treegrid();
                    $(".money").inputmask({'alias': 'integer', 'groupSeparator': ',', 'autoGroup': true, 'removeMaskOnSubmit': true});
                    $(".select2").select2({width: '100%'});
                });

                //remove row
                $(document).on("click", ".remove-cloumn", function (event) {
                    $(this).closest("tr").remove();
                    
                });

                //submit function store 
                $('.submit').on('click', function () {
                    var registerForm = $("#formData");
                    var formData = registerForm.serialize();
                    $.ajax({
                        type: "POST",
                        url: "{{ route('admin.payoffs.store') }}",
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
        <script>
            $(document).ready(function () {
                var type = '';
                var amount = '';
                $(document).on('change', '.content-tax', function () {
                    event.preventDefault();
                    let id = $(this).val();
                    let data_id = $(this).attr('data-id');
                    let amount_money_non_tax =  $(this).closest("tr").find('#amount_money_non_tax_'+data_id) ;
                    let amount_money_tax =$(this).closest("tr").find('#amount_money_tax_'+data_id);
                        if (id) {
                            $.ajax({
                                type: 'GET',
                                url: "{{ route('admin.payoffs.select-tax') }}",
                                data: {
                                    'id': id, 'data_id':data_id,
                                },
                                dataType: 'json',
                                success: function (response) {
                                    if (response.status == 200) {
                                        type = response.data.str;
                                        amount = response.data.amount;
                                        title = response.data.title;
                                        if (type == 'non_tax') {
                                            amount_money_non_tax.val('Miễn Thuế');
                                            amount_money_non_tax.attr('readonly','readonly');
                                            amount_money_tax.val(amount);
                                        }
                                        else if (type == 'tax') {
                                            amount_money_non_tax.val('Chịu Thuế');
                                            amount_money_non_tax.attr('readonly','readonly');
                                            amount_money_tax.val(amount);
                                        }
                                    } else {
                                        toastr.error(response.message);
                                    }
                                }
                            });
                        }else{
                            amount_money_non_tax.val('');
                            amount_money_non_tax.val('');
                        }
                });
            })
        </script>
    @stop
