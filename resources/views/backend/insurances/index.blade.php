@extends('backend.master')
@section('title')
    {!! trans('system.action.list') !!} phân bổ bảo hiểm
@stop
@section('head')
    {{-- <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/iCheck/all.css') !!}"/> --}}
    {{-- <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/select2/select2.min.css') !!}"/> --}}
    <link rel="stylesheet" type="text/css"
          href="{!! asset('assets/backend/plugins/bootstrap-datepicker/css/bootstrap-datepicker.css') !!}"/>
    {{-- <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.css"> --}}
   

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

        .modal-header {
            background-color: #3c8dbc;
            color: white;
            text-align: center;
        }

        .modal-footer {
            text-align: center;
        }
        .select2-container--default .select2-selection--single {
            height: 28px !important;
            border-radius: 3px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 24px !important;
            font-weight: normal;
        }

    </style>
     <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/datatables/dataTables.bootstrap.css') !!}" />
@stop
@section('content')
    <section class="content-header">
        <h1>
            Bảng phân bổ chi phí lương theo xe
            <small>{!! trans('system.action.list') !!}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.insurances.index') !!}">Chi phí lương theo xe</a></li>
        </ol>
    </section>
    <section class="content overlay">
        <div class="row">
            <div class="col-md-4">
                {{-- @if (Auth::user()->hasRole('TP') || Auth::user()->hasRole('system')) --}}
                @permission('insurances.create')
                    <div class="btn-group">
                        <a href="{!! route('admin.insurances.create-bulk') !!}" class='btn btn-info btn-flat'>
                            <span class="glyphicon glyphicon-import"></span>&nbsp;{!! trans('system.action.import') !!}
                        </a>
                    </div>
                @endpermission
               
                {{-- @endif --}}
               
                
            </div>
            <div class="col-md-10">
                
            </div>
        </div>
        <div class="box">
            <div class="box-body no-padding">
                <table class="table table-striped table-bordered" id="tableInsurances">
                    <thead>
                    <tr>
                        <th style="text-align: center; vertical-align: middle; width: 50px">{!! trans('system.no.') !!}</th>
                        <th style="text-align: center; vertical-align: middle; width: 150px" class="">Tiêu đề</th>
                        <th style="text-align: center; vertical-align: middle; width: 100px" class="company_id">{!! trans('timekeeping.company') !!}</th>
                        <th style="text-align: center; vertical-align: middle; width: 100px">{!! trans('timekeeping.month') !!}</th>
                        {{-- @if (Auth::user()->hasRole('TP') || Auth::user()->hasRole('system')) --}}
                        {{-- @endif --}}
                        
                        <th style="text-align: center; vertical-align: middle; width: 100px;">{{ trans('timekeeping.created_by') }}</th>
                        <th style="text-align: center; vertical-align: middle; width: 80px">{!! trans('system.action.label') !!}</th>
                    </tr>
                    </thead>
                    <tbody>
                        @if (count($insurances) > 0)
                            @foreach ($insurances as $key => $item)
                                <tr class="hover">
                                    <td>{{ $key + 1 }}</td>
                                    <td class="">{{ $item->title }}</td>
                                    <td class="company_id">{{ $item->company->shortened_name }}</td>
                                    <td>{{ $item->month < 10 ? '0'.$item->month : $item->month    }}/{{ $item->year }}</td>
                                    
                                  
                                    <td>{{ $item->user_by->fullname }}</td>
                                   
                                    <td>
                                        <a href="{{ route('admin.insurances.detail', $item->id) }}" class="btn btn-info btn-xs">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @permission('insurances.delete')
                                            @if ($item->status != 'APPROVED')
                                                <a href="javascript:void(0)" link="{!! route('admin.insurances.destroy', $item->id) !!}" class="btn-confirm-del btn btn-default btn-xs"><i class="text-danger glyphicon glyphicon-remove"></i></a>
                                            @endif
                                        @endpermission
                                        
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>           
                </table>
                @if (count($insurances) == 0)
                <div class="text-center error">
                    <span class="text-size"><i class="fas fa-search"></i> {!! trans('timekeeping.no_data') !!}</span>
                </div>
                @endif
            </div>
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
    <script src="{!! asset('assets/backend/plugins/datatables/jquery.dataTables.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/datatables/dataTables.bootstrap.min.js') !!}"></script>
    <script>
        $(document).ready(function() {
            $(".select2").select2({width: '100%'});
            $('#tableInsurances thead tr').clone(true).appendTo('#tableInsurances thead');
            $('#tableInsurances thead tr:eq(1) th').each(function (i) {
                if (i == 3) {
                        $(this).html('<input type="text" class="search-form month_filter date" autocomplete="off" />');
                } else if (i != 1 && i != 2 ) {
                    $(this).html('');
                } else {
                    $(this).html('<input type="text" class="search-form input-text" autocomplete="off" />');
                }
                $('.month_filter').datepicker({
                    format: "mm/yyyy",
                    viewMode: "months",
                    minViewMode: "months",
                    clearBtn: true,
                    autoclose: true,
                    language: 'vi'
                });
                
                $('input', this).on('keyup change', function () {
                    if (table.column(i).search() !== this.value) {
                        table
                            .column(i)
                            .search(this.value)
                            .draw();
                    }
                });
                
            });
            
            var table = $('#tableInsurances').DataTable({
                orderCellsTop: true,
                fixedHeader: true,
                pageLength: 20,
                lengthChange: false,
                responsive: true,
                rowReorder: true,
                // ordering: false,
                pagingType: "full_numbers",
                columnDefs: [
                    {orderable: false, className: 'reorder', targets: 3},
                    {orderable: false, targets: 0}
                ],
                language: {
                    "info": "Hiển thị _START_ - _END_ của _TOTAL_ kết quả",
                    "paginate": {
                        "first": "«",
                        "last": "»",
                        "next": "→",
                        "previous": "←"
                    },
                    "infoFiltered": " ( trong tổng số _MAX_ kết quả)",
                    'emptyTable': "<span class='text-size center'><i class='fas fa-search'></i> {!! trans('staff_positions.no_data') !!}</span>",
                    'zeroRecords': "<span class='text-size center'><i class='fas fa-search'></i> {!! trans('staff_positions.no_data') !!}</span>",
                },
                dom: '<"top "i>rt<"bottom"flp>',
            });
            window.setNoAfterSearchDatatables(table, 0)
        });
    </script>
@stop