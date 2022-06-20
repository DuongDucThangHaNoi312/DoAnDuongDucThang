<table class="table table-bordered" style="width: 100%">
    <?php $bgColor = ['#4285f4', '#fbbc04'];
        $styleHead1 = "text-align: center; vertical-align: middle; background: #4285f4; font-weight: 700; border: 1px solid black;";
    ?>
    <thead>
        <tr style=" border: 1px solid black;">
            <th align="center" valign="middle" colspan="1" style="{{ $styleHead1 }} border: 1px solid #000000; text-transform: uppercase; white-space: nowrap;"><b>STT</b></th>
            <th align="center" valign="middle" colspan="1" style="{{ $styleHead1 }} border: 1px solid #000000; text-transform: uppercase; white-space: nowrap;"><b>Mã hợp đồng</b></th>
            <th align="center" valign="middle" colspan="1" style="{{ $styleHead1 }} border: 1px solid #000000; text-transform: uppercase; white-space: nowrap;"><b>Nhân viên</b></th>
            <th align="center" valign="middle" colspan="1" style="{{ $styleHead1 }} border: 1px solid #000000; text-transform: uppercase; white-space: nowrap;"><b>Công ty</b></th>
            <th align="center" valign="middle" colspan="1" style="{{ $styleHead1 }} border: 1px solid #000000; text-transform: uppercase; white-space: nowrap;"><b>Phòng ban</b></th>
            <th align="center" valign="middle" colspan="1" style="{{ $styleHead1 }} border: 1px solid #000000; text-transform: uppercase; white-space: nowrap;"><b>Nhóm văn phòng</b></th>
            <th align="center" valign="middle" colspan="1" style="{{ $styleHead1 }} border: 1px solid #000000; text-transform: uppercase; white-space: nowrap;"><b>Chức vụ</b></th>
            <th align="center" valign="middle" colspan="1" style="{{ $styleHead1 }} border: 1px solid #000000; text-transform: uppercase; white-space: nowrap;"><b>Loại hợp đồng</b></td>
            <th align="center" valign="middle" colspan="1" style="{{ $styleHead1 }} border: 1px solid #000000; text-transform: uppercase; white-space: nowrap;"><b>Chức danh</b></td>
            <th align="center" valign="middle" colspan="1" style="{{ $styleHead1 }} border: 1px solid #000000; text-transform: uppercase; white-space: nowrap;"><b>Lương cơ bản</b></td>
            <th align="center" valign="middle" colspan="1" style="{{ $styleHead1 }} border: 1px solid #000000; text-transform: uppercase; white-space: nowrap;"><b>Chi tiết chuyên môn</b></td>
            <th align="center" valign="middle" colspan="1" style="{{ $styleHead1 }} border: 1px solid #000000; text-transform: uppercase; white-space: nowrap;"><b>Thời hạn hợp đồng</b></td>
            <th align="center" valign="middle" colspan="1" style="{{ $styleHead1 }} border: 1px solid #000000; text-transform: uppercase; white-space: nowrap;"><b>Hiệu lực từ</b></td>
            <th align="center" valign="middle" colspan="1" style="{{ $styleHead1 }} border: 1px solid #000000; text-transform: uppercase; white-space: nowrap;"><b>Hiệu lực đến</b></td>
            <th align="center" valign="middle" colspan="1" style="{{ $styleHead1 }} border: 1px solid #000000; text-transform: uppercase; white-space: nowrap;"><b>Trạng thái</b></td>
                @foreach ($data['allowanceCategory'] as $item)
                <td align="center" valign="middle" width="20" style="border: 1px solid #000000; text-transform: uppercase; white-space: nowrap;background-color: #ffe599;"><b>{!! $item !!}</b></td>
            @endforeach
            <td align="center" valign="middle" width="20" style="border: 1px solid #000000; text-transform: uppercase; white-space: nowrap;background-color: #ffe599;"><b>Tổng phụ cấp</b></td>
            <td align="center" valign="middle" width="20" style="border: 1px solid #000000; text-transform: uppercase; white-space: nowrap;background-color: #ffe599;"><b>Tổng lương, phụ cấp</b></td>
        </tr>
    </thead>
    <tbody>
    @if (count($data['contracts']) > 0)        
        @php $i =1; @endphp
        @foreach ($data['contracts'] as $contract)
            <tr>
                <td align="center" valign="middle" style="border: 1px solid #000000; white-space: nowrap; vertical-align: middle;" >
                    {!! $i !!}
                </td>
                <td align="center" valign="middle" style="border: 1px solid #000000; white-space: nowrap; vertical-align: middle;" >
                    {!! $contract->code !!}
                </td>
                <td align="center" valign="middle" style="border: 1px solid #000000; vertical-align: middle;" >
                    {!! $contract->user->fullname !!}
                </td>
                <td align="left" valign="left"  style="border: 1px solid #000000;vertical-align: middle;" >
                    {!! $contract->company->code !!}
                </td>
                <td align="center" valign="middle" style="border: 1px solid #000000;vertical-align: middle;">
                    {!! $contract->department->name !!}
                </td>
                <td align="center" valign="middle" style="border: 1px solid #000000;vertical-align: middle;">
                    {!! $contract->departmentGroup->name !!}
                </td>
                <td align="center" valign="middle" style="border: 1px solid #000000;vertical-align: middle;">
                    {!! $contract->position->name !!}
                </td>
                <td align="center" valign="middle" style="border: 1px solid #000000;vertical-align: middle;">
                    {!! $contract->is_main == 1 ? 'Thử Việc' : 'Chính thức' !!}
                </td>
                <td align="left"  style="border: 1px solid #000000;vertical-align: middle;">
                    {!! $contract->qualification->name !!}
                </td>
                <td align="right" valign="middle" style="border: 1px solid #000000;vertical-align: middle;">
                    {!! $contract->basic_salary !!}
                </td>
                <td align="left" valign="middle" style="border: 1px solid #000000;vertical-align: middle;">
                    {!! $contract->desc_qualification !!}
                </td>
                <td align="center" valign="middle" style="border: 1px solid #000000;vertical-align: middle;">
                    {!! data_get($data['types'], $contract->type, '') !!}
                </td>
                <td align="center" valign="middle" style="border: 1px solid #000000;vertical-align: middle;">
                    {!! strtotime($contract->valid_from) > 0 ? date('d/m/Y', strtotime($contract->valid_from)) : '' !!}
                </td>
                <td align="center" valign="middle" style="border: 1px solid #000000;vertical-align: middle;">
                    {!! strtotime($contract->valid_to) > 0 ? date('d/m/Y', strtotime($contract->valid_to)) : '' !!}
                </td>
                <td align="center" valign="middle" style="border: 1px solid #000000;vertical-align: middle;">
                    {!! data_get($data['typeStatus'], $contract->type_status, '') !!}
                </td>
                @php 
                $allowances =  $contract->allowances->keyBy('category_id')->toArray(); 
                $allAllowances = array_sum(array_column($allowances, 'expense'));
                $allMoney = $allAllowances + intval($contract->basic_salary);
                @endphp 
                @foreach ($data['allowanceCategory'] as $key =>  $item)
                    <td align="right" valign="middle" style="border: 1px solid #000000;vertical-align: middle;">
                        {!! isset($allowances[$key]) ? $allowances[$key]['expense'] : ''  !!}
                    </td>
                @endforeach
                <td align="right" valign="middle" style="border: 1px solid #000000;vertical-align: middle;">
                    {!! $allAllowances!!}
                </td>
                <td align="right" valign="middle" style="border: 1px solid #000000;vertical-align: middle;">
                    {!! $allMoney !!}
                </td>
            </tr>
            @php $i ++; @endphp
            @endforeach
        @else
            <tr><th colspan="15" style="text-align: center"><span class='text-size center'><i class='fas fa-search'></i>Không tìm thấy dữ liệu.</span></th></tr><th >
        @endif    
    </tbody>
</table>