<table class="table table-bordered" style="width: 100%">
    <?php $bgColor = ['#ffe599', '#b6d7a8'];
    $month = ['Dec', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Jan'];
    $monthF = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    $start = '26'; $end = '25';
    $style1 = "text-align: center; vertical-align: center; background: {$bgColor[0]}; font-weight: 700; border: 1px solid black;";
    $style2 = "text-align: center; vertical-align: middle; background: {$bgColor[1]}; font-weight: 700; border: 1px solid black;";
    ?>
    <thead>
    <tr>
        <th colspan="21"
            style="text-align: left; vertical-align: middle; font-weight: 700; font-size: 24px; text-transform: uppercase;">
            BẢNG TỔNG HỢP NGÀY PHÉP NĂM {{ $data['year'] }}
        </th>
    </tr>
    <tr>
        <th colspan="9"></th>
        <th colspan="12"
            style="text-align: center; vertical-align: middle; font-weight: 700;  text-transform: uppercase;">
            TÍNH SỐ NGÀY PHÉP THEO THÁNG TRÊN BẢNG CHẤM CÔNG
        </th>
    </tr>
    <tr style=" border: 1px solid black;">
        <th rowspan="3" style="{{ $style1 }}">{!! trans('system.no.') !!}</th>
        <th rowspan="3" style="{{ $style1 }}">{!! trans('staffs.code') !!}</th>
        <th rowspan="3" style="{{ $style1 }}">{!! trans('contracts.staff_id') !!}</th>
        <th rowspan="3" style="{{ $style1 }}">{!! trans('contracts.department_id') !!}</th>
        <th rowspan="3" style="{{ $style1 }}">{!! trans('contracts.company_id') !!}</th>
        <th rowspan="3" style="{{ $style1 }}">Ngày vào cty</th>
        <th rowspan="3" style="{{ $style1 }}">Tổng phép</th>
        <th rowspan="3" style="{{ $style1 }}">Tổng phép đã sử dụng</th>
        <th rowspan="3" style="{{ $style1 }}">Còn dư</th>
        @for($i = 0; $i < 12; $i++)
            <th style="{{ $style2 }}">{{ $start . '-' . $month[$i] }}</th>
        @endfor
    </tr>
    <tr>
        @for($i = 0; $i < 12; $i++)
            <th style="{{ $style2 }}">{{ $end . '-' . $monthF[$i] }}</th>
        @endfor
    </tr>
    <tr>
        @for($i = 0; $i < 12; $i++)
            <th style="{{ $style2 }}">T{{$i+1}}</th>
        @endfor
    </tr>
    </thead>
    <tbody>
    <?php $j = 1; ?>
    @if(count($data) > 0)
        @foreach($data['data'] as $companyId => $comData)
            @foreach($comData as $departmentId => $deptData)
                <?php $deptName = $data['departments'][$departmentId]; $companyName = $data['companies'][$companyId]; ?>
                @foreach($deptData as $userId => $item)
                    <tr>
                        <td style="text-align: center; vertical-align: middle; border: 1px solid black;">{!! $j++ !!}</td>
                        <td style="vertical-align: middle;text-align: left; border: 1px solid black;">{!! $item->code !!}</td>
                        <td width="20" style="vertical-align: middle;text-align: left; border: 1px solid black;">{!! $item->fullname !!}</td>
                        <td width="20" style="vertical-align: middle; border: 1px solid black;">{!!$deptName !!}</td>
                        <td width="20" style="vertical-align: middle; border: 1px solid black;">{!! $companyName !!}</td>
                        <td width="20" style="vertical-align: middle; border: 1px solid black;">{!! $item->staff_start ? date('d/m/Y', strtotime($item->staff_start)) : '' !!}</td>
                        <td style="text-align: center; vertical-align: middle; border: 1px solid black;">{{ $item->original_rest }}</td>
                        <td width="20" style="text-align: center; vertical-align: middle; border: 1px solid black;">{{ $item->original_rest - $item->rest }}</td>
                        <td style="text-align: center; vertical-align: middle; border: 1px solid black;">{{ $item->rest }}</td>
                        @for($i = 1; $i <= 12; $i++)
                            <td style="{{ $style2 }}">{{  $data['leave'][$userId][\App\Defines\Schedule::DAY_OFF_12][$i] ?? '-' }}</td>
                        @endfor
                    </tr>
                @endforeach
            @endforeach
        @endforeach
    @else
        <tr><th colspan="12" style="text-align: center"><span class='text-size center'><i class='fas fa-search'></i>Không tìm thấy dữ liệu.</span></th></tr><th >
    @endif
    </tbody>
</table>