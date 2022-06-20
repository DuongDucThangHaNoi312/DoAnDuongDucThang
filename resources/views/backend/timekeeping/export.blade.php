<div style="margin-bottom: 20px">
    <h3>{!! trans('timekeeping.detail_title') !!} {{ $detail->month }}/{{ $detail->year }}</h3>
    <h4>{{ $detail->department->name }} - {{ $detail->company->name }}</h4>
</div>
<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th rowspan="2" align="center" style="line-height: 5">{!! trans('system.no.') !!}</th>
            <th rowspan="2" align="center" width="15" style="line-height: 5">{!! trans('timekeeping.code') !!}</th>
            <th rowspan="2" align="center" line-height="5" width="30" valign="middle">{!! trans('timekeeping.staff') !!}</th>
            
            @if (count($getDays) > 0)
            @foreach ($getDays as $key => $item)
            <th align="center" colspan="1" style="padding: 0 5px 10px">{{ $item }}</th>
            @endforeach
            @endif

            <th align="center" rowspan="2" width="20" style="line-height: 5">{!! trans('timekeeping.number_day') !!}</th>
            <th align="center" width="20" colspan="2">{!! trans('timekeeping.total_number') !!}</th>
            <th align="center" width="50" colspan="6">{!! trans('timekeeping.day_off_number') !!}</th>
            <th align="center" width="20" rowspan="2" style="line-height: 5">{!! trans('timekeeping.total') !!}</th>
        </tr>
        <tr>
            @if (count($getDates) > 0)
            @foreach ($getDates as $key => $item)
            <th align="center" style="padding: 0 5px 10px">{{ $item }}</th>
            @endforeach
            @endif

            <th align="center">{!! trans('timekeeping.hd') !!}</th>
            <th align="center">{!! trans('timekeeping.tv') !!}</th>
            
            <th align="center" width="15">{!! trans('timekeeping.leave_salary') !!}</th>
            <th>Nghỉ ốm</th>
            <th align="center" width="10">{!! trans('timekeeping.take_leave') !!}</th>
            <th align="center" width="15">{!! trans('timekeeping.without_pay') !!}</th>
            <th align="center" width="10">{!! trans('timekeeping.wedding_or_funeral') !!}</th>   
            <th align="center" width="10">Nghỉ phép</th>   
        </tr>

    </thead>
    <tbody>
        @if (count($items) > 0)  
        @foreach ($items as $key => $item)
        <tr>
            <td align="center" class="">{{ $key + 1 }}</td>
            <td align="center">{{ $item->staff->code }}</td>
            <td>{{ $item->staff->fullname }}</td>
            @foreach ($item->detail as $k => $detail)
                <td align="center" style="background: {{ $detail['color'] }}; {{ $detail['color'] != 'white' ? 'border: 2px solid white' : 'border: 1px solid #d2d6de'}}">
                    {{ $detail['time_check_in'] }} - {{ $detail['time_check_out'] }}
                    {{ in_array($detail['status'], [10, 2, 3, 4, 5]) ? 'v/2' : ($detail['status'] == 1 ? 'v' : '') }}
                    <span>{{ \App\StaffDayOff::checkDateHasEvent($item->staff_id, date('Y-m-d', $k)) }}</span>
                </td>
            @endforeach
            <td align="center" class="">{!! $total_day_request !!}</td>
            <td align="center" class="">{{ $item->total_hd }}</td>
            <td align="center" class="">{{ $item->total_tv }}</td>
            <td align="center" class="">{!! !empty($item->day_off_70_salary) ? $item->day_off_70_salary : '0' !!}</td>
            <td align="center" class="">{!! !empty($item->day_off_sick) ? $item->day_off_sick : '0' !!}</td>
            <td align="center">{!! !empty($item->day_off_no_salary) ? $item->day_off_no_salary : '0' !!}</td>
            <td align="center">{!! !empty($item->day_off_wedding) ? $item->day_off_wedding : '0' !!}</td>
            <td align="center">{!! !empty($item->day_off_funeral) ? $item->day_off_funeral : '0' !!}</td>
            <td align="center">{!! !empty($item->day_off_12) ? $item->day_off_12 : '0' !!}</td>
            <td align="center">{{ $item->total }}</td>
        </tr>
        @endforeach
        @endif
    </tbody>           
</table>