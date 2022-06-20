<table>
    <tr>
        <th colspan="10" style="font-family:Arial, Helvetica; font-size:11px; font-weight:bold">Bảng OT tháng {{ $data->month }}/{{ $data->year }}</th>
    </tr>
    <tr>
        <th colspan="50" style="font-family:Arial, Helvetica; font-size:11px; font-weight:bold">{{ $data->department->name }} - {{ $data->company->name }}</th>
    </tr>
</table>
<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th rowspan="3" align="center" style="border: 1px solid black ;line-height: 5;width: 4px;vertical-align: center;text-align :center; font-family:Arial, Helvetica">{!! trans('system.no.') !!}</th>
            <th rowspan="3" align="center" style="border: 1px solid black ;line-height: 5;width: 15px;vertical-align: center;text-align :center; font-family:Arial, Helvetica">Mã</th>
            <th rowspan="3" align="center" style="border: 1px solid black ;line-height: 5;width: 30px;vertical-align: center;text-align :center; font-family:Arial, Helvetica">Họ tên</th>
            @if (count($getDays) > 0)
                @foreach ($getDays as $key => $item)
                    <th colspan="2" align="center" style="border: 1px solid black ;line-height: 5;width: 4px;vertical-align: center;text-align :center; font-family:Arial, Helvetica">{{ $item }}</th>
                @endforeach
            @endif
            <th colspan="4" align="center" style="border: 1px solid black ;line-height: 5;width: 50px;vertical-align: center;text-align :center; font-family:Arial, Helvetica">Tổng OT đêm</th>
            <th colspan="3" align="center" style="border: 1px solid black ;line-height: 5;width: 50px;vertical-align: center;text-align :center; font-family:Arial, Helvetica">Tổng OT ngày</th>
            <th rowspan="3" align="center" style="border: 1px solid black ;line-height: 5;width: 50px;vertical-align: center;text-align :center; font-family:Arial, Helvetica">Số suất ăn chính theo ngày <br> công thực tế</th>
            <th rowspan="3" align="center" style="border: 1px solid black ;line-height: 5;width: 50px;vertical-align: center;text-align :center; font-family:Arial, Helvetica">Số suất <br> ăn phụ</th>
            <th rowspan="3" align="center" style="border: 1px solid black ;line-height: 5;width: 50px;vertical-align: center;text-align :center; font-family:Arial, Helvetica">Số suất <br> ăn chính</th>
        </tr>
        <tr>
            @if (count($getDates) > 0)
                @foreach ($getDates as $key => $item)
                    <th colspan="2"  align="center" height="30" colspan="1" style="border: 1px solid black ;width: 4px;padding: 0 5px 10px; font-family:Arial, Helvetica">
                        {{ $item }}
                    </th>
                @endforeach
            @endif


            <th rowspan="2" align="center" style="border: 1px solid black ;line-height: 5;width: 5px;vertical-align: center;text-align :center; font-family:Arial, Helvetica">
                Ngày thường <br> (Không OT ngày)
            </th>                           
            <th rowspan="2" align="center" style="border: 1px solid black ;line-height: 5;width: 10px;vertical-align: center;text-align :center; font-family:Arial, Helvetica">Ngày thường <br> (Có OT ngày)</th>                           
            <th rowspan="2" align="center" style="border: 1px solid black ;line-height: 5;width: 10px;vertical-align: center;text-align :center; font-family:Arial, Helvetica">Ngày nghỉ</th>
            <th rowspan="2" align="center" style="border: 1px solid black ;line-height: 5;width: 10px;vertical-align: center;text-align :center; font-family:Arial, Helvetica">Ngày lễ</th>

            <th rowspan="2" align="center" style="border: 1px solid black ;line-height: 5;width: 10px;vertical-align: center;text-align :center; font-family:Arial, Helvetica">Ngày thường</th>                           
            <th rowspan="2" align="center" style="border: 1px solid black ;line-height: 5;width: 10px;vertical-align: center;text-align :center; font-family:Arial, Helvetica">Ngày nghỉ</th>
            <th rowspan="2" align="center" style="border: 1px solid black ;line-height: 5;width: 10px;vertical-align: center;text-align :center; font-family:Arial, Helvetica">Ngày lễ</th>
           
        </tr>
        <tr>
            @if (count($getDates) > 0)
                @foreach ($getDates as $key => $item)
                    <th align="center" style="border: 1px solid black ;line-height: 5;width: 10px;vertical-align: center;text-align :center; font-family:Arial, Helvetica">N</th>
                    <th align="center" style="border: 1px solid black ;line-height: 5;width: 10px;vertical-align: center;text-align :center; font-family:Arial, Helvetica">Đ</th>
                @endforeach
            @endif
        </tr>
    </thead>
    <tbody>
        @if ($items > 0)
            @foreach ($items as $key => $item)
                <tr class="hover">
                    <td align="center" style="border: 1px solid black ;line-height: 5;width: 4px;vertical-align: center;text-align :center; font-family:Arial, Helvetica">{{ $key + 1 }}</td>
                    <td align="center" style="border: 1px solid black ;line-height: 5;width: 15px;vertical-align: center;text-align :center; font-family:Arial, Helvetica">{{ $item->staff->code }}</td>
                    <td align="center" style="border: 1px solid black ;line-height: 5;width: 30px;vertical-align: center;text-align :center; font-family:Arial, Helvetica">{{ $item->staff->fullname }}</td>
                    <?php $i = -1 ?>
                    @foreach ($item->detail as $index => $detail)
                        
                        <?php $i++ ?>

                        <?php 
                            $total = 0;
                            $total = $detail['night_tv'] + $detail['night_hd'] + $detail['hours_night_not_day_hd'] + $detail['hours_night_not_day_tv'] + $detail['hours_night_have_day_hd'] + $detail['hours_night_have_day_tv'];
                        ?>
                        <td align="center" style="border: 1px solid black ;line-height: 5;width: 4px;vertical-align: center;text-align :center; font-family:Arial, Helvetica">
                            {{ !empty($detail['ot']) ? $detail['ot'] : '' }}</td>
                        <td align="center" style="border: 1px solid black ;line-height: 5;width: 4px;vertical-align: center;text-align :center; font-family:Arial, Helvetica">
                            {{ $total > 0 ? $total : '' }} 
                        </td>
                        
                    @endforeach

                    <td align="center" style="border: 1px solid black ;line-height: 5;width: 10px;vertical-align: center;text-align :center; font-family:Arial, Helvetica">{{ !empty($item->hours_night_not_day) ? $item->hours_night_not_day : 0}}</td>
                    <td align="center" style="border: 1px solid black ;line-height: 5;width: 10px;vertical-align: center;text-align :center; font-family:Arial, Helvetica">{{ !empty($item->hours_night_have_day) ? $item->hours_night_have_day : 0}}</td>
                    <td align="center" style="border: 1px solid black ;line-height: 5;width: 10px;vertical-align: center;text-align :center; font-family:Arial, Helvetica">{{ !empty($item->night_dayoff) ? $item->night_dayoff : 0 }}</td>
                    <td align="center" style="border: 1px solid black ;line-height: 5;width: 10px;vertical-align: center;text-align :center; font-family:Arial, Helvetica">{{ !empty($item->night_holiday) ? $item->night_holiday : 0 }}</td>
                    
                    <td align="center" style="border: 1px solid black ;line-height: 5;width: 10px;vertical-align: center;text-align :center; font-family:Arial, Helvetica">{{ !empty($item->total_type_normal) ? $item->total_type_normal : 0 }} </td>
                    <td align="center" style="border: 1px solid black ;line-height: 5;width: 10px;vertical-align: center;text-align :center; font-family:Arial, Helvetica">{{ !empty($item->total_type_dayoff) ? $item->total_type_dayoff : 0 }} </td>
                    <td align="center" style="border: 1px solid black ;line-height: 5;width: 10px;vertical-align: center;text-align :center; font-family:Arial, Helvetica">{{ !empty($item->total_type_holiday) ? $item->total_type_holiday : 0 }} </td>

                    <td align="center" style="border: 1px solid black ;line-height: 5;width: 10px;vertical-align: center;text-align :center; font-family:Arial, Helvetica">{{ !empty($item->full_cong) ? $item->full_cong : 0 }} </td>
                    <td align="center" style="border: 1px solid black ;line-height: 5;width: 10px;vertical-align: center;text-align :center; font-family:Arial, Helvetica">{{ !empty($item->an_phu) ? $item->an_phu : 0 }} </td>
                    <td align="center" style="border: 1px solid black ;line-height: 5;width: 10px;vertical-align: center;text-align :center; font-family:Arial, Helvetica">{{ !empty($item->an_chinh) ? $item->an_chinh : 0 }} </td>
                </tr>
            @endforeach    
        @endif
    </tbody>           
</table>