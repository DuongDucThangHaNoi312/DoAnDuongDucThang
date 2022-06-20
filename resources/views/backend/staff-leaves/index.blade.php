@extends('backend.master')
@section('title')
    {!! trans('staffs.take-leave') !!}
@stop
@section('head')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.css">
    <style>
        .isDisabled {
            color: currentColor;
            cursor: not-allowed;
            opacity: 0.5;
            text-decoration: none;
        }
    </style>
@stop
@section('content')
    @include('backend.header.content_header',['name'=>'staffs.label','key'=>'staffs.take-leave'])
    <section class="content overlay">
        <div class="box box-default">
            <div class="box-body">
                <table class="table table-striped table-bordered" id="staff-leave">
                    <thead>
                    <tr>
                        <th style="text-align: center; vertical-align: middle;">{!! trans('system.no.') !!}</th>
                        <th style="text-align: center; vertical-align: middle;">{!! trans('staff_titles.type') !!}</th>
                        <th style="text-align: center; vertical-align: middle;">{!! trans('staff_titles.start') !!}
                        <th style="text-align: center; vertical-align: middle;">{!! trans('staff_titles.end') !!}
                        <th style="text-align: center; vertical-align: middle;">{!! trans('staff_titles.day_off') !!}</th>
                        <th style="text-align: center; vertical-align: middle;">{!! trans('staff_titles.status') !!}</th>
                        <th style="text-align: center; vertical-align: middle;">{!! trans('staff_titles.action') !!}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if($leaves->total()>0)
                        @foreach ($leaves as $item)
                            <tr id="row-{{ $item->id  }}">
                                <td style="text-align: center; vertical-align: middle;">{{++$i}}</td>
                                <td style="text-align: center; vertical-align: middle;">{{$item->title}}</td>
                                <?php $langOption = $item->half_shift == 1 ? 'time-shift-offs.' : 'time-offs.' ?>
                                <td style="text-align: center; vertical-align: middle;">
                                    {!!date('d/m/Y',strtotime($item->start))!!}<br>
                                    @if ($item->from_type == 1  || $item->from_type == 2)
                                        (<span style="color: #D29E3B">{!!  trans('schedules.'.$langOption.$item->from_type) !!}</span>)
                                    @endif
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    {!!date('d/m/Y',strtotime($item->end))!!}<br>
                                    @if ( $item->to_type == 1 || $item->to_type == 2)
                                        (<span style="color: #A94442"> {!! trans('schedules.'.$langOption.$item->to_type) !!}</span>)
                                    @endif
                                </td>
                                <td style="text-align: center; vertical-align: middle;">{{$item->total}}</td>
                                <td style="text-align: center; vertical-align: middle; white-space: nowrap;">
                                    <span> {!! trans('staff_titles.statu.'. $item->status) !!}</span>
                                </td>
                                <td style="text-align: center; vertical-align: middle; white-space: nowrap;">
                                    @if($item->status==0)
                                        <a data-toggle="tooltip" title="Hủy đơn" href="javascript:void(0)"
                                           link="{!! route('admin.take-leave.staffs.forceDelete',$item->id) !!}"
                                           class=" btn-confirm-canel   btn btn-default btn-xs"><i
                                                    class="text-danger glyphicon glyphicon-remove"></i></a>
                                    @endif
                                    @if($item->status==1&&$item->deleted_at==null && $item->start >= date('Y-m-d'))
                                        <a data-toggle="tooltip" title="Gửi yêu cầu hủy đơn"
                                           href="{{route('admin.take-leave.staffs.action',$item->id)}}"
                                           class="btn btn-xs btn-default"><i class="fas fa-share-square"></i></a>
                                    @endif
                                    @if($item->status==1&&$item->deleted_at!=null)
                                        <a data-toggle="tooltip" title="Chờ duyệt yêu cầu hủy đơn"
                                           class="btn btn-xs btn-default " data-target="#EditModal"><i
                                                    class="fas fa-check"></i></a>
                                    @endif

                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr style="height: 40px">
                            <td align="center" colspan="7"><span class="text-size"><i class="fas fa-search"></i> {!! trans('schedules.no_data') !!}</span>
                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@stop
@section('footer')
    <script type="text/javascript" charset="utf8"
            src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js"></script>

    <script>
        $(".deleteRecord").click(function () {
            if (!confirm("Bạn có chắc chắn muốn hủy đơn không?")) {
                return false;
            }
            var url = $(this).data('url');
            $('#row-' + $(this).data("id")).remove();
            $.ajax(
                {
                    url: url,
                    type: 'DELETE',
                    headers: {"X-CSRF-TOKEN": "{!! csrf_token() !!}"},
                    success: function (response) {
                        toastr.success('{!!trans('staff_titles.destroys') !!}');
                    },
                });
        });

        var table = $('#staff-leave').DataTable({
            orderCellsTop: true,
            fixedHeader: true,
            pageLength: 20,
            lengthChange: false,
            rowReorder: true,
            ordering: false,
            pagingType: "full_numbers",
            language: {
                "info": "Hiển thị _START_ - _END_ của _TOTAL_ kết quả",
                "paginate": {
                    "first": "«",
                    "last": "»",
                    "next": "→",
                    "previous": "←"
                },
                "infoFiltered": " ( trong tổng số _MAX_ kết quả)",
                'emptyTable': "<span class='text-size center'><i class='fas fa-search'></i> {!! trans('staff_positions.no_data') !!}</span>"


            },
            dom: '<"top "i>rt<"bottom"flp>',

        });
    </script>
@stop
