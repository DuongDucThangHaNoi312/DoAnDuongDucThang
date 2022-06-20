<table class="table table-bordered" style="width: 100%">
    <?php $bgColor = ['#4285f4', '#fbbc04'];
    $styleTd1 = "vertical-align: middle; text-align: left; border: 1px solid black; ";
    $styleTd2 = "vertical-align: middle; text-align: center; border: 1px solid black; ";
    $styleHead1 = "text-align: center; vertical-align: middle; background: #4285f4; font-weight: 700; border: 1px solid black;";
    $styleHead2 = "text-align: center; vertical-align: middle; background: {$bgColor[1]}; font-weight: 700; border: 1px solid black; width: 10px";
    ?>
    <thead>
    <tr>
        {{-- @dd($infor) --}}
        <th colspan="6"
            style="{{ $styleHead1 }}  text-transform: uppercase;">
            THỐNG KÊ TỜ KHAI -  CÔNG TY {!! $companyCode[$key] !!}  - NHÓM {!! $departmentGroupCode[$infor['departmentGroup']] !!}
        </th>
    </tr>
    <tr>
        <th colspan="6"
            style="{{ $styleHead2 }}  text-transform: uppercase;">
            THÁNG {!! $infor['month'] !!} / {!! $infor['year'] !!}
        </th>
    </tr>
    <tr style=" border: 1px solid black;">
        <th style="{{ $styleHead1 }} ">Loại hình tờ khai </th>
        <th style="{{ $styleHead1 }} ">Thang điểm</th>
        <th style="{{ $styleHead1 }} ">TK Chính </th>
        <th style="{{ $styleHead1 }} ">TK Nhánh </th>
        <th style="{{ $styleHead1 }}" >Khách tự mở TK</th>
        <th style="{{ $styleHead1 }}" >Tính điểm</th>
    </tr>
    </thead>
    <tbody>
        @foreach ($declarationWithPoint as $key => $vl)
            <tr>
                <td>{!! $key !!}</td>
                <td>{!! $vl !!}</td>
                <td>{!! $value[$key] ? $value[$key]['MAIN'] : 0!!}</td>
                <td>{!! $value[$key] ? $value[$key]['BRANCH'] : 0 !!}</td>
                <td>{!! $value[$key] ? $value[$key]['SELF'] : 0!!}</td>
                <td>{!! $value[$key] ? $value[$key]['POINT'] : 0!!}</td>
            </tr>
        @endforeach
        <tr>
            <th colspan="2">Tổng</th>
            <th colspan="">{!! array_sum(array_column($value, 'MAIN')) !!}</th>
            <th colspan="">{!! array_sum(array_column($value, 'BRANCH')) !!}</th>
            <th colspan="">{!! array_sum(array_column($value, 'SELF')) !!}</th>
            <th colspan="">{!! array_sum(array_column($value, 'POINT')) !!}</th>
        </tr>
    </tbody>
</table>