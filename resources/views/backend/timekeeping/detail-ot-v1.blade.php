@extends('backend.master')
@section('title')
    {!! trans('system.action.list') !!} {!! trans('timekeeping.label') !!}
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
            {!! trans('timekeeping.ot') !!}
            <small>{{ $timekeeping->company->shortened_name }} - {{ $timekeeping->department->name }} - {{ $timekeeping->month }}/{{ $timekeeping->year }}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.ot.index') !!}">{!! trans('system.action.detail') !!}</a></li>
        </ol>
    </section>
    <section class="content overlay">
        @if (count($items) > 0)
            <div class="box box-default">
                <div class="box-header with-bconsumer">
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    
                    <form action="{!! route('admin.ots.detail', $timekeeping->id) !!}" method="GET">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="" style="display: block;">{!! trans('timekeeping.staff') !!}</label>
                                    <input type="text" name="fullname" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('filter', trans('system.action.label'), ['style' => 'display: block;']) !!}
                                    <button type="submit" class="btn btn-primary btn-flat">
                                        <span class="glyphicon glyphicon-search"></span>&nbsp; {!! trans('system.action.search') !!}
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <p>Ăn chính <br>
                                    + Ngày thường: OT >= 6 <br>
                                    + Ngày đi làm 1/2v: OT > 0 (Lịch phòng ban làm nửa ngày) <br>
                                    + Ngày nghỉ: OT > 4 <br>
                                </p>
                            </div>
                            <div class="col-md-3">
                                <p>Ăn phụ <br>
                                    + Ngày thường: 3 <= OT < 6  <br>
                                    + Ngày đi làm 1/2v: OT >= 7 (Lịch phòng ban làm nửa ngày) <br>
                                    + Ngày nghỉ: OT >= 11 <br>
                                </p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>            
        @endif
        
        @permission('timekeeping.create', 'payrolls.create')
            <a href="{{ route('admin.timekeeping.exportExcelOt', $timekeeping->id) }}" class="btn btn-success" target="_black">
                <span class="far fa-file-excel fa-fw"></span>&nbsp;{{ trans('timekeeping.export_excel') }}
            </a>
        @endpermission

        <div class="box">
               
            <div class="box-body no-padding" style="overflow-x:auto;">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th rowspan="3" class=""style="line-height: 5; vertical-align: middle;">{!! trans('system.no.') !!}</th>
                            <th rowspan="3" style="line-height: 5; padding: 0 20px">Mã</th>
                            <th rowspan="3" class="sticky-col" style="line-height: 5; padding: 0 70px">Họ tên</th>
                            @if (count($getDays) > 0)
                                @foreach ($getDays as $key => $item)
                                    <th colspan="2" style="padding: 0 5px 10px; background: {{  in_array($item, ['Sun', 'Sat']) ? '#d2f5f5' : '' }}">{{ $item }}</th>
                                @endforeach
                            @endif
                            <th colspan="4" style="text-align: center; vertical-align: middle;">Tổng OT đêm</th>
                            <th colspan="3" style="text-align: center; vertical-align: middle;">Tổng OT ngày</th>
                            @if ($timekeeping->department->type == \App\Define\Department::DECLARATION_OFFICE)
                                <th rowspan="3" style="text-align: center; vertical-align: middle;">Số suất ăn chính <br>theo ngày số <br> công thực tế</th>
                                <th rowspan="3" style="text-align: center; vertical-align: middle;">Số suất <br> ăn chính OT</th>
                                <th rowspan="3" style="text-align: center; vertical-align: middle;">Số suất <br> ăn phụ</th>
                                <th rowspan="3" style="text-align: center; vertical-align: middle;">Tổng số suất <br> ăn chính</th>
                            @endif
                            
                        </tr>
                        <tr>
                            @if (count($getDates) > 0)
                                @foreach ($getDates as $key => $item)
                                    <th colspan="2" style="padding: 0 20px 10px; background: {{ in_array($getDays[$key], ['Sun', 'Sat']) ? '#d2f5f5' : '' }};">
                                        {{ $item }}
                                    </th>
                                @endforeach
                            @endif


                            <th rowspan="2">
                                Ngày thường <br> (Không OT ngày) <br> {{ "({$configOt[5]}%)" }}
                            </th>                           
                            <th rowspan="2">Ngày thường <br> (Có OT ngày) <br>{{ "({$configOt[7]}%)" }}</th>                           
                            <th rowspan="2">Ngày nghỉ <br>{{ "({$configOt[4]}%)" }}</th>
                            <th rowspan="2">Ngày lễ <br>{{ "({$configOt[6]}%)" }}</th>

                            <th rowspan="2">Ngày thường <br> {{ "({$configOt[1]}%)" }}</th>                           
                            <th rowspan="2">Ngày nghỉ <br> {{ "({$configOt[2]}%)" }}</th>
                            <th rowspan="2">Ngày lễ <br>{{ "({$configOt[3]}%)" }}</th>
                           
                        </tr>
                        <tr>
                            @if (count($getDates) > 0)
                                @foreach ($getDates as $key => $item)
                                    <th style="background: {{ in_array($getDays[$key], ['Sun', 'Sat']) ? '#d2f5f5' : '' }}">N</th>
                                    <th style="background: {{ in_array($getDays[$key], ['Sun', 'Sat']) ? '#d2f5f5' : '' }}">Đ</th>
                                @endforeach
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @if ($items > 0)
                            <?php $rowIndex = 1; ?> 
                            @foreach ($items as $key => $item)
                                <tr class="hover">
                                    <td>{{ $rowIndex++ }}</td>
                                    <td>{{ $item->staff->code }}</td>
                                    <td class="sticky-col" style="text-align: left">{{ $item->staff->fullname }}</td>
                                    <?php $i = -1 ?>
                                    @foreach ($item->detail as $index => $detail)
                                        
                                        <?php $i++ ?>

                                        <?php 
                                            $total = 0;
                                            $total = $detail['dem_thuong_ko_ot_ngay'] + $detail['dem_thuong_co_ot_ngay'] + $detail['dem_nghi'] + $detail['dem_le'];

                                            if ($detail['dem'] > 0) {
                                                $total = $detail['dem'];
                                            }
                                          
                                        ?>
                                        <td style="background: {{  (in_array($getDays[$i], ['Sun', 'Sat']) && $detail['ngay'] < 4) ? '#d2f5f5' : '' }} {{ ($detail['ngay'] >=4 && $detail['ngay'] < 6) ? '#FFCC66' : ($detail['ngay'] >= 6 ? '#FF0000' : '') }}; 
                                            border-right: {{ $detail['ot_change'] == 'day' ? '1px solid black' : '' }}; border-bottom: {{ $detail['ot_change'] == 'day' ? '1px solid black' : '' }};
                                            " class="{{ !Auth::user()->hasRole('NV') && $item->timekeeping->status != 'APPROVED' ? 'update1' : '' }}"  data-id="{{ $item->id }}" data-link="{{ route('admin.ots.update', $item->id) }}" data-key="{{ $index }}" data-type="day" data-fullname="{{ $item->staff->fullname }}" data-date="{{ date('d/m/Y', $index)}}"
                                            data-toggle="tooltip" data-placement="top" title="{{ $detail['note_edit_ot_day'] ?? '' }}"    
                                        >
                                            {{ !empty($detail['ngay']) ? $detail['ngay'] : '' }}</td>
                                        <td style="background: {{  (in_array($getDays[$i], ['Sun', 'Sat']) && $total < 4) ? '#d2f5f5' : '' }} {{ ($total >=4 && $total < 6) ? '#FFCC66' : ($total >= 6 ? '#FF0000' : '') }};
                                                
                                                border-right: {{ $detail['ot_change'] == 'night' ? '1px solid black' : '' }}; border-bottom: {{ $detail['ot_change'] == 'night' ? '1px solid black' : '' }};
                                                " 
                                            class="{{ !Auth::user()->hasRole('NV') && $item->timekeeping->status != 'APPROVED' ? 'update1' : '' }}" data-id="{{ $item->id }}" data-link="{{ route('admin.ots.update', $item->id) }}" data-key="{{ $index }}" data-type="night" data-fullname="{{ $item->staff->fullname }}" data-date="{{ date('d/m/Y', $index)}}"
                                            data-toggle="tooltip" data-placement="top" title="{{ $detail['note_edit_ot_night'] ?? '' }}"    
                                        >
                                            {{ $total > 0 ? $total : '' }} 
                                            {{-- {{ $detail['total_ot'] }} --}}
                                        </td>
                                        
                                    @endforeach

                                    <td>{{ !empty($item->dem_thuong_ko_ot_ngay) ? $item->dem_thuong_ko_ot_ngay : 0}}</td>
                                    <td>{{ !empty($item->dem_thuong_co_ot_ngay) ? $item->dem_thuong_co_ot_ngay : 0}}</td>
                                    <td>{{ !empty($item->dem_nghi) ? $item->dem_nghi : 0 }}</td>
                                    <td>{{ !empty($item->dem_le) ? $item->dem_le : 0 }}</td>
                                    
                                    <td>{{ !empty($item->ngay_thuong) ? $item->ngay_thuong : 0 }} </td>
                                    <td>{{ !empty($item->ngay_nghi) ? $item->ngay_nghi : 0 }} </td>
                                    <td>{{ !empty($item->ngay_le) ? $item->ngay_le : 0 }} </td>

                                    @if ($timekeeping->department->type == \App\Define\Department::DECLARATION_OFFICE)
                                        <td>{{ !empty($item->an_chinh_ngay_di_lam) ? $item->an_chinh_ngay_di_lam : 0 }} </td>
                                        <td>
                                            <span data-html="true" data-toggle="tooltip" data-placement="top" title="{{ $item->ngay_an_chinh_ot ?? '' }}">{{ !empty($item->an_chinh_ot) ? $item->an_chinh_ot : 0 }} </span>
                                        </td>
                                        <td>
                                            <span data-html="true" data-toggle="tooltip" data-placement="top" title="{{ $item->ngay_an_phu_ot ?? '' }}">
                                                {{ !empty($item->an_phu) ? $item->an_phu : 0 }} 
                                            </span>
                                        </td>
                                        <td>{{ !empty($item->an_chinh) ? $item->an_chinh : 0 }} </td>      
                                    @endif
                                  
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

    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #3c8dbc">
                    <h3 class="modal-title" style="text-align: center; color: white" id="exampleModalLabel">Cập nhật giờ làm thêm</h3>
                </div>
                <div class="modal-body">
                    <form action="" id="update-ot">
                        <input type="hidden" name="key" value="">
                        <input type="hidden" name="type" value="">
                        <div style="width: 90%; margin: auto">
                            <div class="row">
                                <div class="col-md-4">
                                    <h4>Nhân viên</h4>
                                </div>
                                <div class="col-md-8">
                                    <h4 id="fullname"></h4>
                                </div>
                                <div class="col-md-4">
                                    <h4>Ngày làm việc</h4>
                                </div>
                                <div class="col-md-8">
                                    <h4 id="date"></h4>
                                </div>
                                <div class="col-md-4">
                                    <h4>Ca</h4>
                                </div>
                                <div class="col-md-8">
                                    <h4 id="shift"></h4>
                                </div>
                            </div>
                            <div class="row">
                               <div class="col-md-12">
                                    <div class="type-administrative">
                                        <div class="form-group">
                                            <input type="text" name="ot" id="" class="form-control inputmask" placeholder="Số giờ">
                                        </div>
                                        <div class="form-group">
                                            <textarea name="note" id="" cols="30" rows="4" class="form-control note" placeholder="Ghi chú"></textarea>
                                        </div>
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
    <script src="{!! asset('assets/backend/plugins/input-mask/jquery.inputmask.min.js') !!}"></script>


    <script>
        $(".inputmask").inputmask({
            'placeholder' : '',
            regex: "^[0-9]{1,2}[.][05]$"
        });
    </script>
    <script>
        var link = '';
       $(".hover").hover(function(){
                $(this).css("background-color", "#d2f5f5");
                $(this).find('.sticky-col').css("background-color", "#d2f5f5");

            }, function(){
                $(this).css("background-color", "white");
                $(this).find('.sticky-col').css("background-color", "white");
        });

        $('.update1').on('click', function() {
            $('input[name="ot"]').val('');
            $('#fullname').text('').change();
            $('#date').text('').change();
            $('#shift').text('').change();

            var fullname = $(this).data('fullname');
            var date = $(this).data('date');
            var key = $(this).data('key');
            var type = $(this).data('type');
            link = $(this).data('link')
            if (type == 'day') {
                var type_text = 'Ca ngày';
            } else if (type == 'night') {
                var type_text = 'Ca đêm';
            }
            $('#fullname').text(fullname).change();
            $('#date').text(date).change();
            $('#shift').text(type_text).change();

            $('input[name="key"]').val(key).change();
            $('input[name="type"]').val(type).change();

            $('#exampleModal').modal('show');

            
        })
        
        $('.btn-update').on('click', function() {
            let ot = $('input[name="ot"]').val();
            let name = $('.note').val();

            if (!ot || !name) {
                toastr.error('Số giờ ot/ghi chú bắt buộc phải nhập');
                return ;
            }
            let registerForm = $("#update-ot");
            let formData = registerForm.serialize();
            // $(this).addClass('disabled', 'disabled');

            $.ajax({
                type: "POST",
                url: link,
                headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                data: formData,
                success: function (response) {
                    if (response.status == 'FAIL') {
                        $('.btn-update').removeClass('disabled');
                        toastr.error(response.message);
                    } else if (response.status == 'SUCCESS') {
                        toastr.success(response.message);
                        location.reload();
                    }
                }
            });
        });
    </script>
@stop