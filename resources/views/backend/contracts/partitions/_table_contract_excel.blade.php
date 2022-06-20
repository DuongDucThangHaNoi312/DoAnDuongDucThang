<table class="excel" style="font-family: 'Times New Roman'!important;">
    <thead>
    <tr>
        <th colspan="13"
            style="text-align: center; vertical-align: middle; background: #4285f4; font-weight: 500; border: 1px solid black;">
            {!! trans('contracts.info_general') !!}
        </th>
        <th colspan="10"
            style="text-align: center; vertical-align: middle; background: #fbbc04; font-weight: 500; border: 1px solid black;">{!! trans('allowance_categories.label') !!}</th>
        <th colspan="2"
            style="text-align: center; vertical-align: middle; background: #ce04fb; font-weight: 500; border: 1px solid black;"></th>
    </tr>
    <tr style=" border: 1px solid black;">
        <th style="text-align: center; vertical-align: middle; background: #4285f4; font-weight: 500; border: 1px solid black;">{!! trans('system.no.') !!}</th>
        <th style="text-align: center; vertical-align: middle; background: #4285f4; font-weight: 500; border: 1px solid black;">{!! trans('contracts.code') !!}</th>
        <th style="text-align: center; vertical-align: middle; background: #4285f4; font-weight: 500; border: 1px solid black;">{!! trans('contracts.staff_id') !!}</th>
        <th style="vertical-align: middle; text-align: center; background: #4285f4; font-weight: 500; border: 1px solid black;">{!! trans('contracts.company_id') !!}</th>
        <th style="text-align: center; vertical-align: middle; background: #4285f4; font-weight: 500; border: 1px solid black;">{!! trans('contracts.department_id') !!}</th>
        <th style="text-align: center; vertical-align: middle; background: #4285f4; font-weight: 500; border: 1px solid black;">{!! trans('contracts.rank') !!}</th>
        <th style="text-align: center; vertical-align: middle; background: #4285f4; font-weight: 500; border: 1px solid black;">{!! trans('contracts.qualification_id') !!}</th>
        <th width="60" style="text-align: center; vertical-align: middle; background: #4285f4; font-weight: 500; border: 1px solid black;">{!! trans('contracts.desc_qualification') !!}</th>
        <th style="text-align: center; vertical-align: middle; background: #4285f4; font-weight: 500; border: 1px solid black;">{!! trans('contracts.is_main') !!}</th>
        <th style="text-align: center; vertical-align: middle; background: #4285f4; font-weight: 500; border: 1px solid black;">{!! trans('contracts.type') !!}</th>
        <th style="text-align: center; vertical-align: middle; background: #4285f4; font-weight: 500; border: 1px solid black;">{!! trans('contracts.valid_from') !!}</th>
        <th style="text-align: center; vertical-align: middle; background: #4285f4; font-weight: 500; border: 1px solid black;">{!! trans('contracts.valid_to') !!}</th>
        <th style="text-align: center; vertical-align: middle; background: #4285f4; font-weight: 500; border: 1px solid black;">{!! trans('contracts.basic_salary') !!}</th>
        <th style="text-align: center; vertical-align: middle; background: #fbbc04; font-weight: 500; border: 1px solid black;">{!! trans('allowance_categories.meal') !!}</th>
        <th style="text-align: center; vertical-align: middle; background: #fbbc04; font-weight: 500; border: 1px solid black;">{!! trans('allowance_categories.travel') !!}</th>
        <th style="text-align: center; vertical-align: middle; background: #fbbc04; font-weight: 500; border: 1px solid black;">{!! trans('allowance_categories.responsibility') !!}</th>
        <th style="text-align: center; vertical-align: middle; background: #fbbc04; font-weight: 500; border: 1px solid black;">{!! trans('allowance_categories.dedication') !!}</th>
        <th style="text-align: center; vertical-align: middle; background: #fbbc04; font-weight: 500; border: 1px solid black;">{!! trans('allowance_categories.productivity') !!}</th>
        <th style="text-align: center; vertical-align: middle; background: #fbbc04; font-weight: 500; border: 1px solid black;">{!! trans('allowance_categories.phone') !!}</th>
        <th style="text-align: center; vertical-align: middle; background: #fbbc04; font-weight: 500; border: 1px solid black;">{!! trans('allowance_categories.job') !!}</th>
        <th style="text-align: center; vertical-align: middle; background: #fbbc04; font-weight: 500; border: 1px solid black;">{!! trans('allowance_categories.particular') !!}</th>
        <th style="text-align: center; vertical-align: middle; background: #fbbc04; font-weight: 500; border: 1px solid black;">{!! trans('allowance_categories.other') !!}</th>
        <th style="text-align: center; vertical-align: middle; background: #fbbc04; font-weight: 500; border: 1px solid black;">{!! trans('allowance_categories.diligence') !!}</th>
        <th style="text-align: center; vertical-align: middle; background: #ce04fb; font-weight: 500; border: 1px solid black;">Tổng phụ cấp</th>
        <th style="text-align: center; vertical-align: middle; background: #ce04fb; font-weight: 500; border: 1px solid black;">Tổng lương, phụ cấp</th>
    </tr>
    </thead>
    <tbody>
    <?php $j = 1; ?>
    @foreach ($contracts as $item)
        <tr>
            <td style="text-align: center; vertical-align: middle; border: 1px solid black;">{!! $j++ !!}</td>
            <td style="vertical-align: middle;text-align: left; border: 1px solid black;">
                {!! $item->code !!}
            </td>
            <td style="vertical-align: middle;text-align: left; border: 1px solid black;">
                {!! $item->user->fullname !!}
            </td>
            <td style="vertical-align: middle; border: 1px solid black;">
                {!! trim($item->company->shortened_name) !!}
            </td>
            <td style="vertical-align: middle; border: 1px solid black;">
                {!! $item->department->name !!}
            </td>
            <td style="vertical-align: middle; border: 1px solid black;">
                {!! $item->position->name !!}
            </td>
            <td style="vertical-align: middle; border: 1px solid black;">
                {!! $item->qualification->name !!}
            </td>
            <td style="vertical-align: middle; border: 1px solid black;">
                {!! $item->desc_qualification !!}
            </td>
            <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                {!! trans('staffs.status.' . $item->is_main) !!}
            </td>
            <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                {!! $item->type ? trans('contracts.types.' . $item->type) : '' !!}
            </td>
            <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                {!! date('d/m/Y', strtotime($item->valid_from)) !!}
            </td>
            <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                {!! $item->valid_to ? date('d/m/Y', strtotime($item->valid_to)) : '' !!}
            </td>
            <td style="text-align: right; vertical-align: middle; border: 1px solid black;">
                {!! $item->basic_salary !!}
            </td>

            <?php $allowances = $item->allowances->pluck('expense', 'category_id'); $totalAllowance = 0;?>
            <td style="text-align: right; vertical-align: middle; border: 1px solid black;">
                {!! $allowances[1] !!}
            </td>
            <td style="text-align: right; vertical-align: middle; border: 1px solid black;">
                {!! $allowances[2] !!}
            </td>
            <td style="text-align: right; vertical-align: middle; border: 1px solid black;">
                {!! $allowances[3] !!}
            </td>
            <td style="text-align: right; vertical-align: middle; border: 1px solid black;">
                {!! $allowances[4] !!}
            </td>
            <td style="text-align: right; vertical-align: middle; border: 1px solid black;">
                {!! $allowances[5] !!}
            </td>
            <td style="text-align: right; vertical-align: middle; border: 1px solid black;">
                {!! $allowances[6] !!}
            </td>
            <td style="text-align: right; vertical-align: middle; border: 1px solid black;">
                {!! $allowances[7] !!}
            </td>
            <td style="text-align: right; vertical-align: middle; border: 1px solid black;">
                {!! $allowances[8] !!}
            </td>
            <td style="text-align: right; vertical-align: middle; border: 1px solid black;">
                {!! $allowances[9] !!}
            </td>
            <td style="text-align: right; vertical-align: middle; border: 1px solid black;">
                {!! $allowances[10] !!}
            </td>
            <td style="text-align: right; vertical-align: middle; border: 1px solid black;">
                <?php for ($k=1; $k<=10; $k++) $totalAllowance += $allowances[$k];  ?>
                {!! $totalAllowance !!}
            </td>
            <td style="text-align: right; vertical-align: middle; border: 1px solid black;">
                {!! $totalAllowance + $item->basic_salary !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>