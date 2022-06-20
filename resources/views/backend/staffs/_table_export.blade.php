<table class="excel">
    @php $bgColors = ['#fbbc04', '#9abb59', '#92cddc', '#fbbc04', '#9abb59', '#92cddc', '#fbbc04', '#9abb59', '#92cddc']; @endphp
    <thead>
    <tr>
        <th style="text-align: center; vertical-align: middle; background: #B7DEE8; font-weight: 500;">{!! trans('system.no.') !!}</th>
        <th style="text-align: center; vertical-align: middle; background: #B7DEE8; font-weight: 500;">{!! trans('staffs.code') !!}</th>
        <th style="text-align: center; vertical-align: middle; background: #B7DEE8; font-weight: 500;">{!! trans('staffs.fullname') !!}</th>
        <th style="vertical-align: middle; background: #B7DEE8; font-weight: 500;">{!! trans('staffs.email') !!}</th>
        <th style="text-align: center; vertical-align: middle; background: #B7DEE8; font-weight: 500;">{!! trans('staffs.addresses') !!}</th>
        <th style="text-align: center; vertical-align: middle; background: #B7DEE8; font-weight: 500;">{!! trans('staffs.nationality') !!}</th>
        <th style="text-align: center; vertical-align: middle; background: #B7DEE8; font-weight: 500;">{!! trans('staffs.date_of_birth') !!}</th>
        <th style="text-align: center; vertical-align: middle; background: #B7DEE8; font-weight: 500;">{!! trans('staffs.phone') !!}</th>
        <th style="text-align: center; vertical-align: middle; background: #B7DEE8; font-weight: 500;">{!! trans('staffs.genders.label') !!}</th>
        <th style="text-align: center; vertical-align: middle; background: #B7DEE8; font-weight: 500;">{!! trans('staffs.id_card_no') !!}</th>
        <th style="text-align: center; vertical-align: middle; background: #B7DEE8; font-weight: 500;">{!! trans('staffs.issued_on') !!}</th>
        <th style="text-align: center; vertical-align: middle; background: #B7DEE8; font-weight: 500;">{!! trans('staffs.issued_at') !!}</th>
        <th style="text-align: center; vertical-align: middle; background: #B7DEE8; font-weight: 500;">Tình trạng kết hôn</th>
        <th style="text-align: center; vertical-align: middle; background: #B7DEE8; font-weight: 500;">Dân tộc</th>
        <th style="text-align: center; vertical-align: middle; background: #B7DEE8; font-weight: 500;">Bằng cấp cao nhất</th>
        <th style="text-align: center; vertical-align: middle; background: #B7DEE8; font-weight: 500;">Liên hệ khẩn cấp</th>
        <th style="text-align: center; vertical-align: middle; background: #B7DEE8; font-weight: 500;">Điện thoại khẩn cấp</th>
        <th style="text-align: center; vertical-align: middle; background: #92D050; font-weight: 500;">Mã số thuế</th>
        <th style="text-align: center; vertical-align: middle; background: #92D050; font-weight: 500;">Số sổ BH</th>
        <th style="text-align: center; vertical-align: middle; background: #92D050; font-weight: 500;">Số tài khoản</th>
        <th style="text-align: center; vertical-align: middle; background: #92D050; font-weight: 500;">Ngân hàng</th>
        <th style="text-align: center; vertical-align: middle; background: #FFFF00; font-weight: 500;">Số Giấy phép lái xe</th>
        <th style="text-align: center; vertical-align: middle; background: #FFFF00; font-weight: 500;">Hạng bằng</th>
        <th style="text-align: center; vertical-align: middle; background: #FFFF00; font-weight: 500;">Thời hạn bằng</th>
        <th style="text-align: center; vertical-align: middle; background: #FFFF00; font-weight: 500;">Mã công chính</th>
        <th style="text-align: center; vertical-align: middle; background: #FFFF00; font-weight: 500;">Mã công phụ</th>
        @if($maxCountFamily)
        @for($i = 0; $i < $maxCountFamily; $i++)
        <th style="text-align: center; vertical-align: middle; background: {{$bgColors[$i]}}; font-weight: 500;">Người phụ thuộc {{$i + 1}}</th>
        <th style="text-align: center; vertical-align: middle; background: {{$bgColors[$i]}}; font-weight: 500;">Mã số thuế</th>
        <th style="text-align: center; vertical-align: middle; background: {{$bgColors[$i]}}; font-weight: 500;">Mối quan hệ</th>
        <th style="text-align: center; vertical-align: middle; background: {{$bgColors[$i]}}; font-weight: 500;">Ngày sinh</th>
        <th style="text-align: center; vertical-align: middle; background: {{$bgColors[$i]}}; font-weight: 500;">Giới tính</th>
        <th style="text-align: center; vertical-align: middle; background: {{$bgColors[$i]}}; font-weight: 500;">Địa chỉ</th>
        @endfor
        @endif
    </tr>
    </thead>
    <tbody>
    <?php $j = 1; ?>
    @foreach ($users as $item)
        <tr>
            <td style="text-align: center; vertical-align: middle;">{!! $j++ !!}</td>
            <td style="vertical-align: middle;text-align: center">
                {!! $item->code !!}
            </td>
            <td style="vertical-align: middle;text-align: center">
                {!! $item->fullname !!}
            </td>
            <td style="vertical-align: middle;">
                {!! $item->email !!}
            </td>
            <td style="vertical-align: middle;">
                {!! $item->addresses !!}
            </td>
            <td style="text-align: center; vertical-align: middle;">
                {!! $item->nationality !!}
            </td>
            <td style="text-align: center; vertical-align: middle;">
                <span class="label label-default">{!! date('d/m/Y', strtotime($item->date_of_birth)) !!}</span>
            </td>
            <td style="text-align: center; vertical-align: middle;">
                {!! $item->phone !!}
            </td>
            <td style="text-align: center; vertical-align: middle;">
                {!! !is_null($item->gender) ? trans('staffs.genders.' . $item->gender) : '' !!}
            </td>
            <td style="text-align: center; vertical-align: middle;">
                {!! $item->id_card_no !!}
            </td>
            <td style="text-align: center; vertical-align: middle;">
                {!! date('d/m/Y', strtotime($item->issued_on)) !!}
            </td>
            <td style="text-align: center; vertical-align: middle;">
                {!! $item->issued_at !!}
            </td>
            <td style="text-align: center; vertical-align: middle;">
                {!! !is_null($item->marital_status) ? trans('staffs.marital_status.' . $item->marital_status) : '' !!}
            </td>
            <td style="text-align: center; vertical-align: middle;">
                {!! $item->ethnicity !!}
            </td>
            <td style="text-align: center; vertical-align: middle;">
                {!! !is_null($item->qualification) ? trans('staffs.qualifications.' . $item->qualification) : '' !!}
            </td>
            <td style="text-align: center; vertical-align: middle;">
                {!! $item->emergency_contact !!}
            </td>
            <td style="text-align: center; vertical-align: middle;">
                {!! $item->emergency_phone !!}
            </td>
            <td style="text-align: center; vertical-align: middle;">
                {!! $item->tax_code !!}
            </td>
            <td style="text-align: center; vertical-align: middle;">
                {!! $item->insurance_no !!}
            </td>
            <td style="text-align: center; vertical-align: middle;">
                {!! $item->bank_account !!}
            </td>
            <td style="text-align: center; vertical-align: middle;">
                {!! $item->bank_name !!}
            </td>
            <td style="text-align: center; vertical-align: middle;">
                {!! $item->driver_license_no !!}
            </td>
            <td style="text-align: center; vertical-align: middle;">
                {!! $item->driver_license_class !!}
            </td>
            <td style="text-align: center; vertical-align: middle;">
                {!! $item->driver_license_expire ? date('d/m/Y', strtotime( $item->driver_license_expire)) : '' !!}
            </td>
            <td style="text-align: center; vertical-align: middle;">
                {!! $item->code_timekeeping !!}
            </td>
            <td style="text-align: center; vertical-align: middle;">
                {!! $item->code_timekeeping_subs !!}
            </td>
            <?php $family = $item->families; ?>
            @if (count($family))
            @for($i = 0; $i < count($family); $i++)
                    <td style="text-align: center; vertical-align: middle;">
                        {!! $family[$i]->fullname !!}
                    </td>
                    <td style="text-align: center; vertical-align: middle;">
                        {!!  $family[$i]->tax_code !!}
                    </td>
                    <td style="text-align: center; vertical-align: middle;">
                        {!! !is_null( $family[$i]->relationship) ? trans('staffs.family_relationships.' .  $family[$i]->relationship) : '' !!}
                    </td>
                    <td style="text-align: center; vertical-align: middle;">
                        {!! date('d/m/Y', strtotime( $family[$i]->dob)) !!}
                    </td>
                    <td style="text-align: center; vertical-align: middle;">
                        {!! !is_null( $family[$i]->gender) ? trans('staffs.genders.' .  $family[$i]->gender) : '' !!}
                    </td>
                    <td style="text-align: center; vertical-align: middle;">
                        {!!  $family[$i]->address !!}
                    </td>
            @endfor
            @endif
        </tr>
    @endforeach
    </tbody>
</table>