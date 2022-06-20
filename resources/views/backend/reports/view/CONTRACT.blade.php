<table class="table table-bordered" style="width: 100%" id="data">
    <thead>
        <tr style="">
            <td align="center" valign="middle" width="20" style="border: 1px solid #000000; text-transform: uppercase; white-space: nowrap;background-color: #92D050;"><b>STT</b></td>
            <td align="center" valign="middle" width="50" style="border: 1px solid #000000; text-transform: uppercase; white-space: nowrap;background-color: #92D050;"><b>Mã hợp đồng</b></td>
            <td align="center" valign="middle" width="20" style="border: 1px solid #000000; text-transform: uppercase; white-space: nowrap;min-width: 120px;background-color: #92D050;"><b>Nhân viên</b></td>
            <td align="center" valign="middle" width="20" style="border: 1px solid #000000; text-transform: uppercase; white-space: nowrap;background-color: #92D050;"><b>Công ty</b></td>
            <td align="center" valign="middle" width="20" style="border: 1px solid #000000; text-transform: uppercase; white-space: nowrap;background-color: #92D050;"><b>Phòng ban</b></td>
            <td align="center" valign="middle" width="20" style="border: 1px solid #000000; text-transform: uppercase; white-space: nowrap;background-color: #92D050;"><b>Nhóm văn phòng</b></td>
            <td align="center" valign="middle" width="20" style="border: 1px solid #000000; text-transform: uppercase; white-space: nowrap;background-color: #92D050;"><b>Chức vụ</b></td>
            <td align="center" valign="middle" width="20" style="border: 1px solid #000000; text-transform: uppercase; white-space: nowrap;background-color: #92D050;"><b>Loại hợp đồng</b></td>
            <td align="center" valign="middle" width="20" style="border: 1px solid #000000; text-transform: uppercase; white-space: nowrap;background-color: #92D050;"><b>Chức danh</b></td>
            <td align="center" valign="middle" width="20" style="border: 1px solid #000000; text-transform: uppercase; white-space: nowrap;background-color: #92D050;"><b>Lương cơ bản</b></td>
            <td align="center" valign="middle" width="20" style="border: 1px solid #000000; text-transform: uppercase; white-space: nowrap;background-color: #92D050;"><b>Chi tiết chuyên môn</b></td>
            <td align="center" valign="middle" width="20" style="border: 1px solid #000000; text-transform: uppercase; white-space: nowrap;background-color: #92D050;"><b>Thời hạn hợp đồng</b></td>
            <td align="center" valign="middle" width="20" style="border: 1px solid #000000; text-transform: uppercase; white-space: nowrap;background-color: #92D050;"><b>Hiệu lực từ</b></td>
            <td align="center" valign="middle" width="20" style="border: 1px solid #000000; text-transform: uppercase; white-space: nowrap;background-color: #92D050;"><b>Hiệu lực đến</b></td>
            <td align="center" valign="middle" width="20" style="border: 1px solid #000000; text-transform: uppercase; white-space: nowrap;background-color: #92D050;"><b>Trạng thái</b></td>
            {{-- <td align="center" valign="middle" width="20" style="border: 1px solid #000000; text-transform: uppercase; white-space: nowrap;"><b>Trợ cấp ăn trưa</b></td>
            <td align="center" valign="middle" width="20" style="border: 1px solid #000000; text-transform: uppercase; white-space: nowrap;"><b>Trợ cấp đi lại</b></td>
            <td align="center" valign="middle" width="20" style="border: 1px solid #000000; text-transform: uppercase; white-space: nowrap;"><b>Phụ cấp trách nhiệm</b></td>
            <td align="center" valign="middle" width="20" style="border: 1px solid #000000; text-transform: uppercase; white-space: nowrap;"><b>Phụ cấp cống hiến</b></td>
            <td align="center" valign="middle" width="20" style="border: 1px solid #000000; text-transform: uppercase; white-space: nowrap;"><b>Phụ cấp năng suất</b></td>
            <td align="center" valign="middle" width="20" style="border: 1px solid #000000; text-transform: uppercase; white-space: nowrap;"><b>Trợ cấp điện thoại</b></td>
            <td align="center" valign="middle" width="20" style="border: 1px solid #000000; text-transform: uppercase; white-space: nowrap;"><b>Phụ cấp công việc</b></td>
            <td align="center" valign="middle" width="20" style="border: 1px solid #000000; text-transform: uppercase; white-space: nowrap;"><b>Trợ cấp đặc thù công việc</b></td>
            <td align="center" valign="middle" width="20" style="border: 1px solid #000000; text-transform: uppercase; white-space: nowrap;"><b>Trợ cấp khác</b></td>
            <td align="center" valign="middle" width="20" style="border: 1px solid #000000; text-transform: uppercase; white-space: nowrap;"><b>Phụ cấp chuyên cần</b></td> --}}
            @foreach ($data['allowanceCategory'] as $item)
                <td align="center" valign="middle" width="20" style="border: 1px solid #000000; text-transform: uppercase; white-space: nowrap;background-color: #ffe599;"><b>{!! $item !!}</b></td>
            @endforeach
            <td align="center" valign="middle" width="20" style="border: 1px solid #000000; text-transform: uppercase; white-space: nowrap;background-color: #ffe599;"><b>Tổng phụ cấp</b></td>
            <td align="center" valign="middle" width="20" style="border: 1px solid #000000; text-transform: uppercase; white-space: nowrap;background-color: #ffe599;"><b>Tổng lương, phụ cấp</b></td>
        </tr>
    </thead>
    @php
        $i =1;
    @endphp
    @foreach ($data['contracts'] as $contract)
        <tr>
            <td align="center" valign="middle" style="border: 1px solid #000000; white-space: nowrap; vertical-align: middle;" rowspan="">
                {!! $i !!}
            </td>
            <td align="center" valign="middle" style="border: 1px solid #000000; white-space: nowrap; vertical-align: middle;" rowspan="">
               {!! $contract->code !!}
            </td>
            <td align="center" valign="middle" style="border: 1px solid #000000; vertical-align: middle;" rowspan="">
                {!! $contract->user->fullname !!}
            </td>
            <td align="left" valign="left" width="200" style="border: 1px solid #000000;vertical-align: middle;" rowspan="">
                {!! $contract->company->shortened_name !!}
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
                {!! \App\Helper\HString::currencyFormat($contract->basic_salary) !!}
            </td>
            <td align="left" valign="middle" style="border: 1px solid #000000;vertical-align: middle;">
                {!! $contract->desc_qualification !!}
            </td>
            <td align="center" valign="middle" style="border: 1px solid #000000;vertical-align: middle;">
                {!! isset($data['types'][$contract->type]) ? $data['types'][$contract->type] : '' !!}
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
                    {!! isset($allowances[$key]) ? \App\Helper\HString::currencyFormat($allowances[$key]['expense']) : ''  !!}
                </td>
            @endforeach
            <td align="right" valign="middle" style="border: 1px solid #000000;vertical-align: middle;">
                {!! \App\Helper\HString::currencyFormat($allAllowances) !!}
            </td>
            <td align="right" valign="middle" style="border: 1px solid #000000;vertical-align: middle;">
                {!! \App\Helper\HString::currencyFormat($allMoney) !!}
            </td>
        </tr>
        @php
            $i ++;
        @endphp
    @endforeach
</table>