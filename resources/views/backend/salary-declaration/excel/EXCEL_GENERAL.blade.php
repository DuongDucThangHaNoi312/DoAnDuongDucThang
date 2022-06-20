<table class="table table-bordered" style="width: 100%">
    <?php $bgColor = ['#4285f4', '#fbbc04'];
        $styleTd1 = "vertical-align: middle; text-align: left; border: 1px solid black; ";
        $styleTd2 = "vertical-align: middle; text-align: center; border: 1px solid black; ";
        $styleHead1 = "text-align: center; vertical-align: middle; background: #4285f4; font-weight: 700; border: 1px solid black;";
        $styleHead2 = "text-align: center; vertical-align: middle; background: {$bgColor[1]}; font-weight: 700; border: 1px solid black; width: 10px";
    ?>
    <thead>
        <tr>
            <th colspan="{!! count($data) + 5 !!}"
                style="{{ $styleHead1 }}  text-transform: uppercase;">
                THỐNG KÊ TỜ KHAI - NHÓM - THÁNG  
            </th>
        </tr>
        <tr style=" border: 1px solid black;">
            <th colspan="2" style="{{ $styleHead1 }} "></th>
            <th colspan="4" style="{{ $styleHead1 }} ">Tổng công ty</th>
            <th colspan="{!! count($data) - 1 !!}" style="{{ $styleHead1 }} ">Chi tiết từng công ty</th>
        </tr>
        <tr style=" border: 1px solid black;">
            <th style="{{ $styleHead1 }} ">Loại hình tờ khai </th>
            <th style="{{ $styleHead1 }} ">Thang điểm</th>
            <th style="{{ $styleHead1 }} ">TK Chính </th>
            <th style="{{ $styleHead1 }} ">TK Nhánh </th>
            <th style="{{ $styleHead1 }}" >Khách tự mở TK</th>
            <th style="{{ $styleHead1 }}" >Tính điểm</th>
            @foreach ($data as $key => $item)
                @if ($key == "TOTAL")
                @continue
                @endif
                <th style="{{ $styleHead1 }}" >{!! $companyCode[$key] !!}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        {{-- @dd($data) --}}
        @php
            $allMain = $allBranch = $allSelf = $allPoint = 0
        @endphp
        @foreach ($declarationWithPoint as $key => $vl)
            <tr>
                <th  >{!! $key !!}</th>
                <th  >{!! $vl !!}</th>
                <th  >{!! array_sum(array_column(array_column($data, $key), 'MAIN')) !!}</th>
                <th  >{!! array_sum(array_column(array_column($data, $key), 'BRANCH')) !!}</th>
                <th  >{!! array_sum(array_column(array_column($data, $key), 'SELF')) !!}</th>
                <th  >{!! array_sum(array_column(array_column($data, $key), 'POINT')) !!}</th>
                @foreach ($data as $key1 => $item1)
                @if ($key1 == "TOTAL")
                @continue
                @endif
                    <th >{!! !is_null($data[$key1][$key]) ? ($data[$key1][$key]['POINT']) : 0 !!}</th>
                @endforeach
            </tr>

            @php
                $allMain += array_sum(array_column(array_column($data, $key), 'MAIN'));
                $allBranch += array_sum(array_column(array_column($data, $key), 'BRANCH'));
                $allSelf += array_sum(array_column(array_column($data, $key), 'SELF'));
                $allPoint += array_sum(array_column(array_column($data, $key), 'POINT'));
            @endphp
        @endforeach
        <tr>
            <th style="{{ $styleHead1 }}" colspan="2">Tổng</th>
            <th style="{{ $styleHead1 }}" >{!! $allMain !!}</th>
            <th style="{{ $styleHead1 }}" >{!! $allBranch !!}</th>
            <th style="{{ $styleHead1 }}" >{!! $allSelf !!}</th>
            <th style="{{ $styleHead1 }}" >{!! $allPoint !!}</th>
            @foreach ($data as $key1 => $item1)
                @if ($key1 == "TOTAL")
                @continue
                @endif
                    <th style="{{ $styleHead1 }}">{!! array_sum(array_column($data[$key1], 'POINT')) !!}</th>
            @endforeach
        </tr>
    </tbody>
</table>

<table class="table table-bordered" style="width: 100%">
    <thead>
        <tr>
            <th>Số điểm thực tế </th> 
            <th> </th> 
        </tr>
    </thead>
    <tbody>
        <tr style=" border: 1px solid black;">
            <th style="{{ $styleHead1 }} ">Số điểm thực tế</th>
            <th style="{{ $styleHead1 }} ">{!! $total['POINT'] !!}</th>
            <th style="{{ $styleHead1 }} "></th>
        </tr>
        <tr style=" border: 1px solid black;">
            <th style="{{ $styleHead1 }} ">Số điểm target</th>
            <th style="{{ $styleHead1 }} ">{!! count($total['USER'])*100 !!}</th>
            <th style="{{ $styleHead1 }} ">(100 điểm/1 người/1 tháng)</th>
        </tr>
        <tr style=" border: 1px solid black;">
            <th style="{{ $styleHead1 }} ">Số điểm vượt target</th>
            <th style="{{ $styleHead1 }} ">{!! ($total['POINT'] > count($total['USER'])*100) ? ($total['POINT'] > count($total['USER'])) : 0  !!}</th>
            <th style="{{ $styleHead1 }} "></th>
        </tr>
        <tr style=" border: 1px solid black;">
            <th style="{{ $styleHead1 }} ">Điểm thưởng</th>
            <th style="{{ $styleHead1 }} ">{!! ($total['POINT'] > count($total['USER'])*100) ? ($total['POINT'] > count($total['USER'])*100)*40000 : 0  !!}</th>
            <th style="{{ $styleHead1 }} ">(40.000 VNĐ/1 điểm vượt)</th>
        </tr>
    </tbody>
</table>

<table class="table table-bordered" style="width: 100%">
    <thead>
        <tr>
            <th>Công ty</th> 
            <th>Điểm</th> 
            <th>Tỉ lệ (%)</th> 
            <th>Số tiền chi trả</th> 
        </tr>
    </thead>
    <tbody>
        @foreach ( $data as $key => $value)
        @if ($key == "TOTAL")
        @continue
        @endif
            <tr style=" border: 1px solid black;">
                <th >{!! $companyCode[$key] !!}</th>
                <th >{!! array_sum(array_column($value, 'POINT')) !!}</th>
                <th >{!! round((array_sum(array_column($value, 'POINT'))/$total['POINT'])*100, 2, PHP_ROUND_HALF_DOWN)  !!}</th>
                <th ></th>
            </tr>
        @endforeach
        

        <tr style=" border: 1px solid black;">
            <th style="{{ $styleHead1 }} ">Tổng số</th>
            <th style="{{ $styleHead1 }} ">{!! $total['POINT'] !!}</th>
            <th style="{{ $styleHead1 }} ">100</th>
            <th style="{{ $styleHead1 }} "></th>
        </tr>
    </tbody>
</table>