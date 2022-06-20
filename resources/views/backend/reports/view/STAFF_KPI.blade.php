<table class="table table-bordered" style="width: 100%">
    <?php $bgColor = ['#4285f4', '#ffe599'];
    $styleTd1 = "vertical-align: middle; text-align: left; border: 1px solid black; ";
    $styleTd2 = "vertical-align: middle; text-align: center; border: 1px solid black; ";
    $styleHead1 = "text-align: center; vertical-align: middle; background: #4285f4; font-weight: 700; border: 1px solid black;";
    $styleHead2 = "text-align: center; vertical-align: middle; background: {$bgColor[1]}; font-weight: 700; border: 1px solid black;";
    $countMonth = $data['toMonth']-$data['fromMonth']+ 1;
    ?>
    <thead>
    <tr>
        <th colspan="5" style="{{ $styleHead1 }}  text-transform: uppercase;">
            {!! trans('contracts.info_general') !!}
        </th>
        <th colspan="{{ $countMonth }}" style="{{ $styleHead2 }}  text-transform: uppercase;">
            BÁO CÁO KPI/{{ $data['year'] }}
        </th>
        <th rowspan="2" style="text-align: center; vertical-align: middle; background: #e06666; font-weight: 700; border: 1px solid black;">Trung bình năm</th>
    </tr>
    <tr style=" border: 1px solid black;">
        <th style="{{ $styleHead1 }} ">{!! trans('system.no.') !!}</th>
        <th style="{{ $styleHead1 }} ">{!! trans('staffs.code') !!}</th>
        <th style="{{ $styleHead1 }} ">{!! trans('contracts.staff_id') !!}</th>
        <th style="{{ $styleHead1 }} ">{!! trans('contracts.department_id') !!}</th>
        <th style="{{ $styleHead1 }}" >{!! trans('contracts.company_id') !!}</th>

        @for($i = $data['fromMonth']; $i <= $data['toMonth']; $i++)
            <th style="{{ $styleHead2 }}">Tháng {{$i}}</th>
        @endfor
    </tr>
    </thead>
    <tbody>
    <?php $k = 1; ?>
    @if(count($data['data']) > 0)
    @foreach($data['data'] as $companyId => $comData)
        @foreach($comData as $departmentId => $deptData)
            <?php $deptName = $data['departments'][$departmentId]; $companyName = $data['companies'][$companyId]; ?>
            @foreach($deptData as $userId => $item)
                <?php ?>
                    <tr>
                        <td style="{{ $styleTd1 }} ">{!! $k++ !!}</td>
                        <td style="{{ $styleTd1 }} ">{!! $item->code !!}</td>
                        <td style="{{ $styleTd1 }}">{!! $item->fullname !!}</td>
                        <td style="{{ $styleTd1 }}">{!! $deptName !!}</td>
                        <td style="{{ $styleTd1 }}">{!! $companyName !!}</td>
                        @for($i = $data['fromMonth']; $i <= $data['toMonth']; $i++)
                            <td style="{{ $styleTd2 }}">{{ $data['kpiUsers'][$userId][$data['year']][$i] ?? '-' }}</td>
                        @endfor
                        <?php
                        $countMonthAvg = $data['kpiUsers'][$userId][$data['year']]['countMonthAvg'];
                        $avg = $countMonthAvg ? round($data['kpiUsers'][$userId][$data['year']]['total']/$countMonthAvg, 1) : '-';
                        ?>
                        <td style="{{ $styleTd1 }}; text-align: center">{{ $avg }}</td>
                    </tr>
            @endforeach
        @endforeach
    @endforeach
    @else
        <tr><th colspan="12" style="text-align: center"><span class='text-size center'><i class='fas fa-search'></i>Không tìm thấy dữ liệu.</span></th></tr><th >
    @endif
    </tbody>
</table>