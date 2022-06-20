
<table class="table table-striped table-bordered" id="tableWorkShedule">
    <thead>
    <tr class="view">
        <th style="text-align: center; vertical-align: middle;">{!! trans('system.no.') !!}</th>
        <th style="text-align: center; vertical-align: middle;" class="company_id">{!! trans('companies.label') !!}</th>
        <th style="text-align: center; vertical-align: middle; width: 20%"  class="department_id">{!! trans('workschedule.department') !!}</th>
        <th style="text-align: center; vertical-align: middle; width: 10%"  class="">Ca</th>
        <th style="text-align: center; vertical-align: middle;"  class="">Giờ vào</th>
        <th style="text-align: center; vertical-align: middle;"  class="">Giờ ra</th>
        <th style="text-align: center; vertical-align: middle;"  class="">Nghỉ giữa ca</th>
        <th style="text-align: center; vertical-align: middle;"  class="">Bắt đầu giữa ca</th>
        <th style="text-align: center; vertical-align: middle;"  class="">Giờ sớm nhất</th>
        <th style="text-align: center; vertical-align: middle;"  class="">Giờ sau cùng</th>
        <th  style="text-align: center; vertical-align: middle;">Update bởi</th>
        <th style="text-align: center; vertical-align: middle;">Thời gian update</th>
        <th style="text-align: center; vertical-align: middle;">{!! trans('system.action.label') !!}</th>
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
                    {!! $item->category->title !!} -
                    @switch($item->category->type)
                        @case(1)
                            {{ 'N' }}
                            @break
                        @case(2)
                            {{ 'HC' }}
                            @break
                        @case(3)
                            {{ 'Đ' }}
                            @break
                            
                    @endswitch
                </td>
                <td style="text-align: center; vertical-align: middle;">
                    {!! $item->time_in !!}
                </td>
                <td style="text-align: center; vertical-align: middle;">
                    {!! $item->time_out !!}
                </td>
                <td style="text-align: center; vertical-align: middle;">
                    {!! $item->off_mid_shift !!}
                </td>
                <td style="text-align: center; vertical-align: middle;">
                    {!! $item->start_mid_shift !!}
                </td>
                <td style="text-align: center; vertical-align: middle;">
                    {!! $item->limit_time_in !!}
                </td>
                <td style="text-align: center; vertical-align: middle;">
                    {!! $item->limit_time_out !!}
                </td>
                <td style="text-align: center; vertical-align: middle;">
                    {!! $item->updateBy->fullname ?? '' !!}
                </td>
                <td style="text-align: center; vertical-align: middle;">
                    {!! date('d/m/Y H:i:s', strtotime($item->updated_at)) !!}
                </td>
                <td style="text-align: center; vertical-align: middle; white-space:nowrap;">
                    <button type="button" class="btn btn-default btn-xs open-modal" value="{!! $item->id !!}"
                        data-get-url="{!! route("admin.workschedule.edit", $item->id) !!}" data-url="{!! route("admin.workschedules.update1", $item->id) !!}">
                        <i class="text-warning glyphicon glyphicon-edit"></i>
                    </button>
                    {{-- <a href="javascript:void(0)"
                        link="{!! route('admin.workschedule.destroy1', $item->id) !!}"
                        class="btn-confirm-del btn btn-default btn-xs">
                        <i class="text-danger glyphicon glyphicon-remove"></i>
                    </a> --}}
                </td>
            </tr>
        @endforeach 
        @endif                      
    </tbody>
</table>