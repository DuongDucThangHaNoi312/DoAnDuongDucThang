@extends('backend.master')
@section('title')
    {!! trans('system.action.list') !!} Các khoản khấu trừ
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
    </style>
@stop
@section('content')
    <section class="content-header">
        <h3>
            Các khoản khấu trừ: Công ty {{ $company->shortened_name }}
            {{-- <small>{!! trans('system.action.create') !!}</small> --}}
        </h3>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.timekeeping.index') !!}">{!! trans('system.action.create') !!}</a></li>
        </ol>
    </section>
    <section class="content overlay">

        <div class="text-center">
            {{-- <h3>Công ty {{ $company->shortened_name }}</h3> --}}
        </div>
        <div class="box box-default">
            <div class="box-header with-bconsumer">
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                </div>
            </div>
            <div class="box-body">
                {!! Form::open([ 'url' => route('admin.deductions.create',$company->id), 'method' => 'GET', 'role' => 'search' ]) !!}
                <div class="row">
                    <div style="position: relative;" class="col-md-12">
                        {{-- <div class="col-md-3">
                            <label>Tên nhân viên</label>
                            {!! Form::text('name_user', old('name_user',$name_user), ['class' => 'form-control name_user','id'=>'name_user' ]) !!}
                        </div> --}}
                        <div class="col-md-3">
                            <label>{!! trans('timekeeping.year') !!}</label>
                            <select name="year" id="" class="form-control select2">
                                @if (!is_null(Request::input('year')))
                                    @foreach (\App\Define\Timekeeping::getYear1() as $key => $item)
                                        <option value="{{ $key }}" {{  Request::input('year') == $key ? "selected" : '' }}>{{ $item }}</option>
                                    @endforeach
                                @else   
                                    @foreach (\App\Define\Timekeeping::getYear1() as $key => $item)
                                        <option value="{{ $key }}" {{  $key == date('Y') ? "selected" : '' }}>{{ $item }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>{!! trans('timekeeping.department') !!}</label>
                            <select name="department_id" id="departmentSelect" class="form-control select2 department_id" >
                                <option value="" {!! old('department_id') !!}>{!! trans('system.dropdown_choice') !!}</option>
                            </select>
                            <span class="text-danger">
                                <strong id="department-error"></strong>
                            </span>
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
            <div class="box-body no-padding" style="overflow-x:auto;">
                <table class="table table-striped table-bordered" id="tableDeducitonsCreate">
                    <thead id="a">
                    <tr >
                        <th rowspan="3"  style="text-align: center; vertical-align: middle; width: 50px;" >{!! trans('system.no.') !!}</th>
                        <th class="sticky-col" rowspan="3"  style="vertical-align: middle; padding: 05px 20px 10px;" >&emsp; Họ và tên &emsp; </th>
                        <th rowspan="3"  style="text-align: center; vertical-align: middle; width: 100px" >Mã nhân viên</th>
                        <th rowspan="3"  style="text-align: center; vertical-align: middle; width: 100px" >Mức khấu trừ</th>
                        <th rowspan="3"  style="text-align: center; vertical-align: middle; width: 100px" >Còn lại</th>
                        @for ($i = 1; $i <= 12; $i++)
                            <th colspan="3" style="text-align: center; vertical-align: middle; width: 100px" >{{ $i.'/'. (!is_null(Request::input('year')) ? Request::input('year') : date('Y'))  }}</th>
                        @endfor
                        <th rowspan="3"  style="text-align: center; vertical-align: middle; width: 70px;" >{!! trans('system.action.label') !!}</th>
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
                        @if (count($users) > 0)
                            @foreach ($users as $key => $item)
                                <tr class="hover">
                                    <td style="text-align: center">{{ $key + 1 }}</td>
                                    <td class="sticky-col">{{ $item->fullname }}</td>
                                    <td style="text-align: center">{{ $item->code }}</td>
                                    <td style="text-align: center">
                                        {{-- {{ $total_arr[$item->id]['total'] }} --}}
                                    </td>
                                    <td style="text-align: center">
                                        {{-- {{ $total_arr[$item->id]['con_lai'] }} --}}
                                    </td>
                                    @for ($i = 1; $i <= 12; $i++)
                                        <?php
                                            if (array_key_exists($item->id, $deductions)) {
                                                $deduction = $deductions[$item->id][$i];
                                            } else {
                                                $deduction = [];
                                            }
                                        ?>
                                        <td style="width: 350px; text-align: right">
                                            @if (!empty($deduction))
                                                    {{-- {{ $deduction['total_non_tax'] }} --}}
                                                    {{ \App\Helper\HString::currencyFormatVn(str_replace(',','',$deduction['total_non_tax']))  }}
                                            @endif
                                        </td>
                                        <td style="text-align: right">
                                            @if (!empty($deduction))
                                                {{-- {{ $deduction['total_tax'] }} --}}
                                                {{ \App\Helper\HString::currencyFormatVn(str_replace(',','',$deduction['total_tax']))  }}
                                            @endif
                                        </td >
                                        <td style="text-align: right">
                                            @if (!empty($deduction))
                                                <span>
                                                    {{ $deduction['money'] }}
                                                    @if (intval(date('m') - 1) <= intval($deduction['month']))
                                                    {{-- @if ($deduction['status' == 0]) --}}
                                                        <button type="button" data-user-id="{{ $item->id }}" data-month="{{ $deduction['month'] }}" data-link={{ route('admin.deductions.get-deduction', $deduction['id']) }}  
                                                            class="btn btn-default btn-xs btn-detail-deduction" data-toggle="modal" data-target="#detailMonth">
                                                            <i class="text-warning glyphicon glyphicon-edit"></i>
                                                        </button>
                                                    @else
                                                        <button type="button" data-user-id="{{ $item->id }}" data-month="{{ $deduction['month'] }}" data-link={{ route('admin.deductions.get-deduction', $deduction['id']) }}  
                                                            class="btn btn-info btn-xs btn-detail-info" data-toggle="modal" data-target="#detailMonth">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                    @endif
                                                </span>
                                                
                                                <div class="modal fade" data-backdrop="static" data-keyboard="false" id="detailMonth" tabindex="-1" role="dialog" aria-labelledby="detailMonthLabel" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h4 class="modal-title" id="detailMonthLabel">Chi tiết khoản khấu trừ</h4>
                                                            </div>
                                                            {!! Form::open(['url' => route('admin.deductions.insert'), 'method' => 'POST']) !!}
                                                                <input type="hidden" name="company_id" value="{{ $company->id }}">
                                                                <input type="hidden" name="month" value="">
                                                                <input type="hidden" name="user" value="">

                                                                <div class="modal-body modal-detail-deduction">
                                                                    <table class="table table-striped table-bordered">
                                                                        <thead>
                                                                            <tr>
                                                                                <th class="text-center">STT</th>
                                                                                <th class="text-center">Tên khoản khẩu trừ</th>
                                                                                <th class="text-center" style="width:100px">Mức</th>
                                                                                <th class="text-center" style="width : 100px">Loại</th>
                                                                                <th class="text-center">Ghi chú</th>
                                                                                <th class="text-center action" >
                                                                                    <a href="javascript:void(0);" class="btn btn-default btn-xs btn-add-deductions1">
                                                                                        <i class="text-success fa fa-plus"></i>
                                                                                    </a>
                                                                                </th>
                                                                                
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody id="table-detail-deduction">

                                                                        </tbody> 
                                                                    </table>
                                                                    <h4 class="text-right total-deduction"></h4>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-danger btn-sm btn-cancel" data-dismiss="modal">Đóng</button>
                                                                    <button type="submit" class="btn btn-primary btn-sm btn-save">Lưu lại</button>
                                                                </div>
                                                            {!! Form::close() !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <span>0</span>
                                            @endif
                                        </td>



                                    @endfor
                                    
                                    {{-- <td style="width: 50px">{{ date('Y') }}</td> --}}
                                    <td style="text-align: center;">
                                        @if (!is_null(Request::input('department_id')))
                                            <button type="button" class="btn btn-success btn-xs btn-deductions" data-user="{{ $item->id }}" data-toggle="modal" data-placement="top" title="Thêm mới khấu trừ" data-target="#deductions">
                                                <span class="glyphicon glyphicon-plus">
                                            </button>     
                                        @endif

                                        <div class="modal fade" id="deductions" tabindex="" role="dialog" aria-labelledby="deductionsLabel" aria-hidden="true">
                                            <div class="modal-dialog" role="document" style="width: 50%;">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="deductionsLabel">Các khoản khấu trừ khác</h4>
                                                </div>
                                                {!! Form::open(['url' => route('admin.deductions.store'), 'method' => 'POST', 'id' => 'deduction']) !!}
                                                    <input type="hidden" name="user_id" value="">
                                                    <input type="hidden" name="company_id" value="{{ $company->id }}">
                                                    <input type="hidden" name="department_id" value="{{ Request::input('department_id') }}">
                                                    <div class="modal-body">
                                                        <table class="table table-striped table-bordered table-hover">

                                                                <tr>
                                                                    <td class="text-center " style="font-weight: bold">STT</td>
                                                                    <td class="text-center " style="font-weight: bold">Tên khoản khẩu trừ</td>
                                                                    <td class="text-center " style="font-weight: bold;width:100px">Mức</td>
                                                                    <td class="text-center " style="font-weight: bold">Loại</td>
                                                                    <td class="text-center " style="font-weight: bold">Ghi chú</td>
                                                                    <td class="text-center " style="font-weight: bold">Thao tác</td>
                                                                </tr>

                                                            <tbody id="add-template-deductions">
                                                                <tr>
                                                                    <td style="width: 50px" class="text-center">1</td>
                                                                    <td>
                                                                        {!! Form::select('name[]', ['' => trans('deductions.select')] + \App\Models\Deduction::category(), old(), ['class' => "form-control select2 category", 'required', ]) !!}
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" name="money[]" class="form-control currency currency-money" required>
                                                                    </td>
                                                                    <td>
                                                                        <select name="type[]" id="" class="form-control select2 select2-type" style="width: 150px;">   
                                                                            <option value="">Chọn 1 mục </option>
                                                                            <option value="CHIU_THUE" >Chịu thuế</option>
                                                                            <option value="MIEN_THUE">Miễn thuế</option>
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" name="note[]" class="form-control note " >
                                                                    </td>
                                                                    <td class="text-center" style="width: 80px">
                                                                        <a href="javascript:void(0);" class="btn btn-default btn-xs btn-add-deductions">
                                                                            <i class="text-success fa fa-plus"></i>
                                                                        </a>
                                                                    </td>
                                                                </tr>
                                                            </tbody>      
                                                        </table>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group one-month">
                                                                    <label for="" style="float: left">Từ tháng <span class="text-danger">(*)</span></label>
                                                                    <select name="month_start" id="" class="form-control select2" required>
                                                                        @foreach (\App\Define\Timekeeping::getMonth() as $key => $item)
                                                                            <option value="{{ $key }}">{{ $item }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group one-month">
                                                                    <label for="" style="float: left">Đến tháng <span class="text-danger">(*)</span></label>
                                                                    <select name="month_end" id="" class="form-control select2" required>
                                                                        @foreach (\App\Define\Timekeeping::getMonth() as $key => $item)
                                                                            <option value="{{ $key }}">{{ $item }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
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
                            @endforeach
                            <tr>
                                <th colspan="5" style="text-align: center; vertical-align: middle;" class="th-job">Tổng cộng</th>
                                @for ($i = 1; $i <= 12; $i++)
                                     <th data-html="true"  data-toggle="tooltip" data-placement="top" title="{{ $content_non_tax }}" style="text-align: right">
                                        {{  $total_non_tax_arr[$i] > 0 ? \App\Helper\HString::currencyFormatVn( $total_non_tax_arr[$i]) : '' }}
                                    </th>
                                    <th  data-html="true" data-toggle="tooltip" data-placement="top" title="{{ $content_tax }}" style="text-align: right">
                                        {{ $total_tax_arr[$i] > 0 ? \App\Helper\HString::currencyFormatVn($total_tax_arr[$i]) : '' }}
                                    </th>
                                    <th style="text-align: right">
                                        {{ $total_money_arr[$i] > 0 ? \App\Helper\HString::currencyFormatVn($total_money_arr[$i]) : 0 }}
                                    </th>
                                @endfor
                            </tr>

                            
                        @endif
                        
                    </tbody>           
                </table>
                @if (count($company->users) == 0)
                    <div class="text-center error">
                        <span class="text-size"><i class="fas fa-search"></i> {!! trans('timekeeping.no_data') !!}</span>
                    </div>
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
                    $(".currency").inputmask({'alias': 'integer', 'groupSeparator': ',', 'autoGroup': true, 'removeMaskOnSubmit': true});
                // });
        });
    </script>

    <script>
        // $(function() {
            $(document).ready(function () {
            // $(".select2").select2({
            //         width: '100%',
            // });
            // $('#table-detail-deduction').html('');
            // $(".currency").inputmask({'alias': 'integer', 'groupSeparator': '.', 'autoGroup': true, 'removeMaskOnSubmit': true});

            var index = 2;
            var index1 = '';

            $('.btn-add-deductions').on('click', function() {
                // $(document).on("click", ".btn-add-deductions", function () {
            let html = `
                <tr class="tr-add-other">
                    <td style="width: 50px" class="text-center">${index++}</td>
                    <td>
                        {!! Form::select('name[]', ['' => trans('deductions.select')] + \App\Models\Deduction::category(), old(), ['class' => "form-control select2 category", 'required', ]) !!}
                    </td>
                    <td>
                        <input type="text" name="money[]" class="form-control currency currency-money" required>
                    </td>
                    <td>
                        <select name="type[]" id="" class="form-control select2 select2-type" style="width: 150px;">
                            <option value="">Chọn 1 mục </option>
                            <option value="CHIU_THUE">Chịu thuế</option>
                            <option value="MIEN_THUE">Miễn thuế</option>
                        </select>
                    </td>
                    <td>
                        <input type="text" name="note[]" class="form-control note " >
                    </td>
                    <td style="width: 50px; text-align: center;">
                        <a href="javascript:void(0);" class="btn btn-xs btn-default remove-deductions">
                            <i class="text-danger fa fa-minus"></i>
                        </a>
                    </td>
                </tr>
            `;
                $('#add-template-deductions').append(html);
                $(".currency").inputmask({'alias': 'integer', 'groupSeparator': ',', 'autoGroup': true, 'removeMaskOnSubmit': true});
                // $(".select2").select2({width: '100%'});
                $(".tr-add-other").find('.select2').select2({width: '100%'});
            });
            $(document).on("click", ".remove-deductions", function (event) {
                $(this).closest("tr").remove();
                index -= 1;
                let tmp = 1;
                $("#add-template-deductions td:first-child").each(function() {
                    $(this).html(tmp++);
                });
            });
        });

        $('.btn-deductions').on('click', function() {
            let id = $(this).data('user');
            $('input[name="user_id"]').val(id);
            
            // console.log('123123');
        });

        // sửa detail
        $('.btn-detail-deduction').on('click', function() {
            $('#table-detail-deduction').find('tr').remove();
            $('.action').show();
            $('.btn-save').show();
            let html = '';
            let get_url = $(this).data('link');
            let user_id = $(this).data('user-id');
            let month = $(this).data('month');
            $('input[name="month"]').val(month);
            $('input[name="user"]').val(user_id);
            let arr_push = [];

            var data = [];

            $.get(get_url, function (response) {
                index1 = response.data.length + 1;
                
                $.each(response.data, function (key, value) {
                    arr_push.push({
                        type: value.type
                    });
                    data = value.adjustment;
                    html = `

                        <tr class ="tr-add-other-2">
                            <td style="width: 50px" class="text-center">${key + 1}</td>
                            <td>
                                @php 
                                    $category = \App\Models\Deduction::category()
                                @endphp
                                <select name="name[]" id="" class="form-control select2 select2  category category-${value.name}" >
                                    <option value="" >{!! trans('deductions.select') !!} </option>
                                    @foreach($category as $value => $name)
                                        <option value="{!! $value !!}">{!! $name !!} </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="text" value="${value.money }" name="money[]" class="form-control currency currency-money money-edit">
                            </td>
                            <td>
                                <select name="type[]" id="" class="form-control select2 type_${key} select2-type" style="width: 150px;">
                                    <option value="">Chọn 1 mục </option>
                                    <option value="CHIU_THUE">Chịu thuế</option>
                                    <option value="MIEN_THUE">Miễn thuế</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" value="${value.note ?? ''}" name="note[]" class="form-control note">
                            </td>
                            <td style="width: 50px; text-align: center;">
                                <a href="javascript:void(0);" class="btn btn-xs btn-default remove-deductions1">
                                    <i class="text-danger fa fa-minus"></i>
                                </a>
                            </td>
                        </tr>
                    `;

                    $('#table-detail-deduction').append(html);
                    $('.category-' + value.name).val(value.name)
                })

                $.each(arr_push, function (k, v) { 
                     $('.type_'+k).val(v.type).change();
                });
                let total = response.total;
                total = total.replaceAll('.', ''); 
                console.log(total);
                $('.total-deduction').text('Tổng: ' +  String(total).replace(/\B(?=(\d{3})+(?!\d))/g, ',')   + ' VNĐ');
                $(".currency").inputmask({'alias': 'integer', 'groupSeparator': ',', 'autoGroup': true, 'removeMaskOnSubmit': true});
                $(".tr-add-other-2").find('.select2').select2({width: '100%'});
            });





        });

        function getTotal(element) {
            return $(element).map(function() {
            if(isNaN(parseInt(($(this).val()).replaceAll(',','')))){
                 return 0;
             }
             return parseInt(($(this).val()).replaceAll(',',''));
             }).get().reduce((a, b) => parseInt(a) + parseInt(b), 0);
        }



            $(document).on('keyup', '.currency-money', function () {
                let total_money_edit = (getTotal('input[name="money[]"]'));
                total_money_edit =  String(total_money_edit).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
               $('.total-deduction').text('Tổng: ' + total_money_edit + ' VNĐ'); 
                
            });



        // xem detail
        $('.btn-detail-info').on('click', function() {
            
            $('.btn-save').hide();
            $('.action').hide();
            let html = '';
            let get_url = $(this).data('link');
            let user_id = $(this).data('user-id');
            let month = $(this).data('month');
            $('input[name="month"]').val(month);
            $('input[name="user"]').val(user_id);
            let arr_push = [];
            
            $.get(get_url, function (response) {
                index1 = response.data.length + 1;

                $.each(response.data, function (key, value) {
                    arr_push.push({
                        type: value.type
                    });

                    html += `
                        <tr>
                            <td style="width: 50px" class="text-center">${key + 1}</td>
                            <td>
                                <input type="text" value="${value.adjustment.code ?? ''}" name="name[]" disabled class="form-control" >
                            </td>
                            <td>
                                <input type="text" value="${value.money ?? ''}" name="money[]" disabled class="form-control currency currency-money">
                            </td>
                            <td>
                                <select name="type[]" id="" class="form-control select2 type_${key} select2-type" style="width: 150px;" disabled>
                                    <option value="">Chọn 1 mục </option>
                                    <option value="CHIU_THUE">Chịu thuế</option>
                                    <option value="MIEN_THUE">Miễn thuế</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" value="${value.note ?? ''} " name="note[]" disabled class="form-control note ">
                            </td>
                        </tr>
                    `;
                })
                $('#table-detail-deduction').html(html);
                $.each(arr_push, function (k, v) { 
                     $('.type_'+k).val(v.type).change();
                });
                
                let total = response.total;
                total = total.replaceAll('.', ''); 
                console.log(total);
                $('.total-deduction').text('Tổng: ' + String(total).replace(/\B(?=(\d{3})+(?!\d))/g, ',')  + ' VNĐ');
                $(".currency").inputmask({'alias': 'integer', 'groupSeparator': ',', 'autoGroup': true, 'removeMaskOnSubmit': true});

            });
        });






        // add lúc sửa detail
        $('.btn-add-deductions1').on('click', function() {
            let html1 = `
                <tr class="tr-add-other-1">
                    <td style="width: 50px" class="text-center">${index1++}</td>
                    <td>
                        {!! Form::select('name[]', ['' => trans('deductions.select')] + \App\Models\Deduction::category(), old(), ['class' => "form-control select2 category", 'required', ]) !!}  
                    </td>
                    <td>
                        <input type="text" name="money[]" class="form-control currency currency-money money-edit" required>
                    </td>
                    <td>
                        <select name="type[]" id="" class="form-control select2 select2-type" style="width: 150px;">
                            <option value="">Chọn 1 mục </option>
                            <option value="CHIU_THUE">Chịu thuế</option>
                            <option value="MIEN_THUE">Miễn thuế</option>
                        </select>
                    </td>
                    <td>
                        <input type="text" name="note[]" class="form-control note " >
                    </td>
                    <td style="width: 50px; text-align: center;">
                        <a href="javascript:void(0);" class="btn btn-xs btn-default remove-deductions1">
                            <i class="text-danger fa fa-minus"></i>
                        </a>
                    </td>
                </tr>
            `;
            $('#table-detail-deduction').append(html1);
            $(".tr-add-other-1").find('.select2').select2({width: '100%'});
            $(".currency").inputmask({'alias': 'integer', 'groupSeparator': ',', 'autoGroup': true, 'removeMaskOnSubmit': true});
        });


        // remove lúc sửa detail
        $(document).on("click", ".remove-deductions1", function (event) {

            $(this).closest("tr").remove();
            let total_money_edit = (getTotal('input[name="money[]"]'));
            total_money_edit =  String(total_money_edit).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            $('.total-deduction').text('Tổng: ' + total_money_edit + ' VNĐ'); 
            index1 -= 1;
            let tmp1 = 1;
            $("#table-detail-deduction td:first-child").each(function() {
                $(this).html(tmp1++);
            });
        });
        
    </script>
    <script>
        !function ($) {
            $(function () {

                // $('#tableDeducitonsCreate #a tr').clone(true).appendTo('#tableDeducitonsCreate #a');
                // $('#tableDeducitonsCreate #a tr:eq(1) th').each(function (i) {
                //    if (i ==1  || i==2 ) {
                //         $(this).html('<input type="text" style="width: 200px" class="search-form input-text" autocomplete="off" />');
                //     } else {
                //         $(this).html('');
                //     }

                //     $('input', this).on('keyup change', function () {
                //         if (table.column(i).search() !== this.value) {
                //             table
                //                 .column(i)
                //                 .search(this.value)
                //                 .draw();
                //         }
                //     });
                // });

                // var table = $('#tableDeducitonsCreate').DataTable({
                //     orderCellsTop: true,
                //     fixedHeader: true,
                //     pageLength: 20,
                //     lengthChange: false,
                //     ordering: false,
                //     pagingType: "full_numbers",
                //     language: {
                //         "info": "Hiển thị _START_ - _END_ của _TOTAL_ kết quả",
                //         "paginate": {
                //             "first": "«",
                //             "last": "»",
                //             "next": "→",
                //             "previous": "←"
                //         },
                //         "infoFiltered": " ( trong tổng số _MAX_ kết quả)",
                //     },
                //     dom: '<"top "i>rt<"bottom"flp>'

                // });

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
  



  {{-- Thay đổi category --}}
<script>
    $(document).ready(function () {
        var type = '';
        var amount = '';
        var optionSelected = '';
        var valueSelected = '';
        let amount_money =  '' ;
        let select2_type ='';
        $(document).on('change', '.category', function () {
            event.preventDefault();
            optionSelected = $("option:selected", this);
            valueSelected = this.value;
            let trSelected =  $(this).closest("tr");
            let amount_money = trSelected.find('.currency-money') ;
            let select2_type = trSelected.find('.select2-type');
        
                    $.ajax({
                        type: 'GET',
                        url: "{{ route('admin.deductions.select-tax') }}",
                        data: {
                            'valueSelected': valueSelected,
                        },
                        dataType: 'json',
                        success: function (response) {
                            if (response.status == 200) {
                                type = response.data.str;
                                amount = response.data.amount;
                                title = response.data.title;

                                // console.log(type);
                                // console.log(amount);
                                // console.log(title);
                                if (type == 'non_tax') {
                                    select2_type.val('MIEN_THUE').change();
                                    amount_money.val(amount).change();
                                }
                                else if (type == 'tax') {
                                    select2_type.val('CHIU_THUE').change();
                                    amount_money.val(amount).change();
                                }
                                
                                let total_money_edit = (getTotal('input[name="money[]"]'));
                                total_money_edit =  String(total_money_edit).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                                $('.total-deduction').text('Tổng: ' + total_money_edit + ' VNĐ'); 
                                } 
                            else 
                                {
                                toastr.error(response.message);
                                 }

                        }
                  });
             })
        })


        function getTotal(element) {
            return $(element).map(function() {
            if(isNaN(parseInt(($(this).val()).replaceAll(',','')))){
                 return 0;
             }
             return parseInt(($(this).val()).replaceAll(',',''));
             }).get().reduce((a, b) => parseInt(a) + parseInt(b), 0);
        }

    </script>

<script>
        
    $(document).ready(function () {
    var url_string  = window.location.href;
    var url = new URL(url_string);
    var month = url.searchParams.get('month');
    var year = url.searchParams.get('year');
    var company_id = url.searchParams.get('company_id');
    var department_id = url.searchParams.get('department_id');
    let companyId = @json($company->id);
    let $currentRoute = {!! json_encode(\App\PermissionUserObject::getCurrentModule(\Route::getCurrentRoute())) !!};
    var oldDepartmentId = {!! old('department_id') ?? 0 !!};
    function setDepartmentOption() {
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
    setDepartmentOption();
    });
</script>
@stop
