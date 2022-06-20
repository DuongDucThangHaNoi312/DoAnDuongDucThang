
<table class="table table-striped table-bordered" id="tableWorkShedule">
    <thead>
    <tr class="view">
        <th rowspan="2" style="text-align: center; vertical-align: middle;">{!! trans('system.no.') !!}</th>
        <th rowspan="2" style="text-align: center; vertical-align: middle;" class="company_id">{!! trans('companies.label') !!}</th>
        <th rowspan="2" style="text-align: center; vertical-align: middle;"  class="department_id">{!! trans('workschedule.department') !!}</th>
        <th colspan="2" style="text-align: center; vertical-align: middle;">{!! trans('workschedule.title') !!}</th>
        <th colspan="2" style="text-align: center; vertical-align: middle;">{!! trans('workschedule.saturday') !!}</th>
        <th rowspan="2" style="text-align: center; vertical-align: middle;">{!! trans('workschedule.ot') !!}</th>
        <th rowspan="2" style="text-align: center; vertical-align: middle;">Update bởi</th>
        <th rowspan="2" style="text-align: center; vertical-align: middle;">Thời gian update</th>
        <th rowspan="2" style="text-align: center; vertical-align: middle;">{!! trans('system.action.label') !!}</th>
    </tr>
    <tr >
        <th class="time">{!! trans('workschedule.morning') !!}</th>
        <th class="time">{!! trans('workschedule.afternoon') !!}</th>

        <th class="time">{!! trans('workschedule.morning') !!}</th>
        <th class="time">{!! trans('workschedule.afternoon') !!}</th>
    </tr>
    </thead>
    <tbody id="list-item">
        @if (count($workschedule) > 0)  
        @foreach ($workschedule as $key => $item)
            <tr id="row-{!! $item->id !!}">
                <td style="text-align: center; vertical-align: middle;">{!! $key+1 !!}</td>
                <td style="text-align: center; vertical-align: middle;" class="company_id">
                    {!! $item->company->shortened_name !!}
                </td>
                <td style="text-align: center; vertical-align: middle;;"  class="department_id">
                    {!! $item->department->name !!}
                </td>
                <td style="text-align: center; vertical-align: middle;">
                    {!! $item->from_morning !!} - {!! $item->to_morning !!}
                </td>
                <td style="text-align: center; vertical-align: middle;">
                    {!! $item->from_afternoon !!} - {!! $item->to_afternoon !!}
                </td>
                <td style="text-align: center; vertical-align: middle;">
                    {!! $item->from_sa_morning !!} - {!! $item->to_sa_morning !!}
                </td>
                <td style="text-align: center; vertical-align: middle;">
                    {!! $item->from_sa_afternoon !!} - {!! $item->to_sa_afternoon !!}
                </td>
                <td style="text-align: center; vertical-align: middle;">
                    {!! $item->ot !!}
                </td>
                <td style="text-align: center; vertical-align: middle;">
                    {!! $item->updateBy->fullname ?? '' !!}
                </td>
                <td style="text-align: center; vertical-align: middle;">
                    {!! date('d/m/Y H:i:s', strtotime($item->updated_at)) !!}
                </td>
                <td style="text-align: center; vertical-align: middle; white-space:nowrap;">
                    <button type="button" class="btn btn-default btn-xs open-modal" value="{!! $item->id !!}"
                        data-get-url="{!! route("admin.workschedule.edit", $item->id) !!}" data-url="{!! route("admin.workschedule.update", $item->id) !!}">
                        <i class="text-warning glyphicon glyphicon-edit"></i>
                    </button>
                    {{-- <a href="javascript:void(0)"
                        link="{!! route('admin.workschedule.destroy', $item->id) !!}"
                        class="btn-confirm-del btn btn-default btn-xs">
                        <i class="text-danger glyphicon glyphicon-remove"></i>
                    </a> --}}
                </td>
            </tr>
        @endforeach 
        @endif                      
    </tbody>
</table>
{{--@if (count($workschedule) == 0)--}}
{{--<div class="text-center error">--}}
{{--    <span class="text-size"><i class="fas fa-search"></i> {!! trans('timekeeping.no_data') !!}</span>--}}
{{--</div>--}}
{{--@endif--}}