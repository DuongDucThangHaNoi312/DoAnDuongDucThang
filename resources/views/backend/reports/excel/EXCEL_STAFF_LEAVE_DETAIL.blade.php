<table class="table table-bordered" style="width: 100%">
    <?php $bgColor = ['#4285f4', '#fbbc04'];
    $styleTd1 = "vertical-align: middle; text-align: left; border: 1px solid black; ";
    $styleTd2 = "vertical-align: middle; text-align: center; border: 1px solid black; ";
    $styleHead1 = "text-align: center; vertical-align: middle; background: #4285f4; font-weight: 700; border: 1px solid black;";
    $styleHead2 = "text-align: center; vertical-align: middle; background: {$bgColor[1]}; font-weight: 700; border: 1px solid black; width: 10px";
    ?>
    <thead>
    <tr>
        <th colspan="5"
            style="{{ $styleHead1 }}  text-transform: uppercase;">
            {!! trans('contracts.info_general') !!}
        </th>
        <th colspan="8"
            style="{{ $styleHead2 }}  text-transform: uppercase;">
            Tổng hợp
        </th>
    </tr>
    <tr style=" border: 1px solid black;">
        <th style="{{ $styleHead1 }}">{!! trans('system.no.') !!}</th>
        <th style="{{ $styleHead1 }} ">{!! trans('staffs.code') !!}</th>
        <th style="{{ $styleHead1 }} ">{!! trans('contracts.staff_id') !!}</th>
        <th style="{{ $styleHead1 }} ">{!! trans('contracts.department_id') !!}</th>
        <th style="{{ $styleHead1 }}" >{!! trans('contracts.company_id') !!}</th>

        <th style="{{ $styleHead2 }} ">{!! \App\Defines\Schedule::DAY_OFF_12 !!}</th>
        <th style="{{ $styleHead2 }} ">{!! \App\Defines\Schedule::DAY_OFF_SICK !!}</th>
        <th style="{{ $styleHead2 }} ">{!! \App\Defines\Schedule::DAY_OFF_BABE !!}</th>
        <th style="{{ $styleHead2 }} ">{!! \App\Defines\Schedule::DAY_OFF_NO_SALARY !!}</th>
        <th style="{{ $styleHead2 }} ">{!! \App\Defines\Schedule::DAY_OFF_FUNERAL !!}</th>
        <th style="{{ $styleHead2 }} ">{!! \App\Defines\Schedule::DAY_OFF_WEDDING !!}</th>
        <th style="{{ $styleHead2 }} ">{!! \App\Defines\Schedule::DAY_OFF_70_SALARY !!}</th>
        <th style="{{ $styleHead2 }} ">{!! \App\Defines\Schedule::DAY_OFF_MISSION !!}</th>
    </tr>
    </thead>
    <tbody>
    <?php $k = 1; ?>
    @if(count($data['data']) > 0)
        @foreach($data['data'] as $companyId => $comData)
            @foreach($comData as $departmentId => $deptData)
                <?php $deptName = $data['departments'][$departmentId]; $companyName = $data['companies'][$companyId]; ?>
                @foreach($deptData as $userId => $item)
                    <tr>
                        <td style="{{ $styleTd1 }} ">{!! $k++ !!}</td>
                        <td style="{{ $styleTd1 }} ">{!! $item->code !!}</td>
                        <td width="20" style="{{ $styleTd1 }}">{!! $item->fullname !!}</td>
                        <td width="20" style="{{ $styleTd1 }}">{!! $deptName !!}</td>
                        <td width="20" style="{{ $styleTd1 }}">{!! $companyName !!}</td>
                        @foreach(\App\Defines\Schedule::arrTypeLeaveForExcel() as $type)
                            <td style="{{ $styleTd2 }} ">{!! $data['leave'][$userId][$type]['total'] ?? '-' !!}</td>
                        @endforeach
                    </tr>
                @endforeach
            @endforeach
        @endforeach
    @else
        <tr><th colspan="12" style="text-align: center"><span class='text-size center'><i class='fas fa-search'></i>Không tìm thấy dữ liệu.</span></th></tr><th >
    @endif
    </tbody>
</table>