<table class="table table-bordered" style="width: 100%">
    <?php $bgColor = ['#4285f4', '#fbbc04'];
        $styleHead1 = "text-align: center; vertical-align: middle; background: #4285f4; font-weight: 700; border: 1px solid black;";
    ?>
    <thead>
        <tr style=" border: 1px solid black;">
            <th align="center" valign="middle" colspan="1" style="{{ $styleHead1 }} border: 1px solid #000000; text-transform: uppercase; white-space: nowrap;"><b>STT</b></th>
            <th align="center" valign="middle" colspan="1" style="{{ $styleHead1 }} border: 1px solid #000000; text-transform: uppercase; white-space: nowrap;"><b>Nhân viên</b></th>
            <th align="center" valign="middle" colspan="1" style="{{ $styleHead1 }} border: 1px solid #000000; text-transform: uppercase; white-space: nowrap;"><b>Mã Job</b></th>
            <th align="center" valign="middle" colspan="1" style="{{ $styleHead1 }} border: 1px solid #000000; text-transform: uppercase; white-space: nowrap;"><b>Loại chi phí</b></th>
            <th align="center" valign="middle" colspan="1" style="{{ $styleHead1 }} border: 1px solid #000000; text-transform: uppercase; white-space: nowrap;"><b>Tên KH</b></th>
            <th align="center" valign="middle" colspan="1" style="{{ $styleHead1 }} border: 1px solid #000000; text-transform: uppercase; white-space: nowrap;"><b>Số Cont</b></th>
            <th align="center" valign="middle" colspan="1" style="{{ $styleHead1 }} border: 1px solid #000000; text-transform: uppercase; white-space: nowrap;"><b>Số tiền</b></th>
            <th align="center" valign="middle" colspan="1" style="{{ $styleHead1 }} border: 1px solid #000000; text-transform: uppercase; white-space: nowrap;"><b>Ghi chú</b></th>
        </tr>
    </thead>
    <tbody>
    @if (count($data) > 0)        
        @php $i =1; @endphp
        @foreach ($data as $value)
            <tr>
                <td align="center" valign="middle" style="border: 1px solid #000000; white-space: nowrap; vertical-align: middle;" >
                    {!! $i !!}
                </td>
                <td align="center" valign="middle" style="border: 1px solid #000000; white-space: nowrap; vertical-align: middle;" >
                    {!! $value['user']['fullname'] !!}
                </td>
                <td align="center" valign="middle" style="border: 1px solid #000000; vertical-align: middle;" >
                    {!! $value['booking_detail'] !!}
                </td>
                <td align="center" valign="middle" style="border: 1px solid #000000; vertical-align: middle;" >
                    {!! $value['type_cost'] !!}
                </td>
                <td align="center" valign="middle" style="border: 1px solid #000000; vertical-align: middle;" >
                    {!! $value['customer']['code'] !!}
                </td>
                <td align="center" valign="middle" style="border: 1px solid #000000; vertical-align: middle;" >
                    {!! $value['cont_no'] !!}
                </td>
                <td align="right" valign="middle" style="border: 1px solid #000000; vertical-align: middle;" >
                    {!! $value['money'] !!}
                </td>
                <td align="center" valign="middle" style="border: 1px solid #000000; vertical-align: middle;" >
                </td>
            </tr>
            @php $i ++; @endphp
        @endforeach
        <tr>
            <th align="center" colspan="6" valign="middle" style="border: 1px solid #000000; vertical-align: middle;" >Tổng</th>
            <th align="right" valign="middle" style="border: 1px solid #000000; vertical-align: middle;" >{!! array_sum(array_column($data, 'money')) !!}</th>
            <th align="center" valign="middle" style="border: 1px solid #000000; vertical-align: middle;" ></th>
        </tr>
        @else
            <tr><th colspan="8" style="text-align: center"><span class='text-size center'><i class='fas fa-search'></i>Không tìm thấy dữ liệu.</span></th></tr><th >
        @endif    
    </tbody>
</table>