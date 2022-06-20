<table class="excel" style="font-family: 'Times New Roman'!important;">
    @php $bgColors = ['#fbbc04', '#9abb59', '#92cddc', '#fbbc04', '#9abb59', '#92cddc', '#fbbc04', '#9abb59', '#92cddc']; @endphp
    <thead>
    <tr>
        <th colspan="6"
            style="text-align: center; vertical-align: middle; background: #4285f4; font-weight: 500; border: 1px solid black;">
            {!! trans('contracts.info_general') !!}
        </th>
        @if($maxCountAppendix)
            @for($i = 0; $i < $maxCountAppendix; $i++)
                <th colspan="14"
                    style="text-align: center; vertical-align: middle; background: {!! $bgColors[$i] !!}; font-weight: 500; border: 1px solid black; border-right: 2px solid black">
                    Phụ lục {{ $i+1 }}
                </th>
            @endfor
        @endif
    </tr>
    <tr style=" border: 1px solid black;">
        <th style="text-align: center; vertical-align: middle; background: #4285f4; font-weight: 500; border: 1px solid black;">{!! trans('system.no.') !!}</th>
        <th style="text-align: center; vertical-align: middle; background: #4285f4; font-weight: 500; border: 1px solid black;">{!! trans('contracts.code') !!}</th>
        <th style="text-align: center; vertical-align: middle; background: #4285f4; font-weight: 500; border: 1px solid black;">{!! trans('contracts.staff_id') !!}</th>
        <th style="vertical-align: middle; text-align: center; background: #4285f4; font-weight: 500; border: 1px solid black;">{!! trans('contracts.company_id') !!}</th>
        <th style="text-align: center; vertical-align: middle; background: #4285f4; font-weight: 500; border: 1px solid black;">{!! trans('contracts.department_id') !!}</th>
        <th style="text-align: center; vertical-align: middle; background: #4285f4; font-weight: 500; border: 1px solid black;">{!! trans('contracts.basic_salary') !!}</th>
        @if($maxCountAppendix)
            @for($i = 0; $i < $maxCountAppendix; $i++)
                <th style="text-align: center; vertical-align: middle; background: {!! $bgColors[$i] !!}; font-weight: 500; border: 1px solid black;">{!! trans('appendixes.code') !!}</th>
                <th style="text-align: center; vertical-align: middle; background: {!! $bgColors[$i] !!}; font-weight: 500; border: 1px solid black;">{!! trans('contracts.basic_salary') !!}</th>
                <th style="text-align: center; vertical-align: middle; background: {!! $bgColors[$i] !!}; font-weight: 500; border: 1px solid black;">{!! trans('contracts.valid_from') !!}</th>
                <th style="text-align: center; vertical-align: middle; background: {!! $bgColors[$i] !!}; font-weight: 500; border: 1px solid black;">{!! trans('contracts.valid_to') !!}</th>
                <th style="text-align: center; vertical-align: middle; background: {!! $bgColors[$i] !!}; font-weight: 500; border: 1px solid black;">{!! trans('allowance_categories.meal') !!}</th>
                <th style="text-align: center; vertical-align: middle; background: {!! $bgColors[$i] !!}; font-weight: 500; border: 1px solid black;">{!! trans('allowance_categories.travel') !!}</th>
                <th style="text-align: center; vertical-align: middle; background: {!! $bgColors[$i] !!}; font-weight: 500; border: 1px solid black;">{!! trans('allowance_categories.responsibility') !!}</th>
                <th style="text-align: center; vertical-align: middle; background: {!! $bgColors[$i] !!}; font-weight: 500; border: 1px solid black;">{!! trans('allowance_categories.dedication') !!}</th>
                <th style="text-align: center; vertical-align: middle; background: {!! $bgColors[$i] !!}; font-weight: 500; border: 1px solid black;">{!! trans('allowance_categories.productivity') !!}</th>
                <th style="text-align: center; vertical-align: middle; background: {!! $bgColors[$i] !!}; font-weight: 500; border: 1px solid black;">{!! trans('allowance_categories.phone') !!}</th>
                <th style="text-align: center; vertical-align: middle; background: {!! $bgColors[$i] !!}; font-weight: 500; border: 1px solid black;">{!! trans('allowance_categories.job') !!}</th>
                <th style="text-align: center; vertical-align: middle; background: {!! $bgColors[$i] !!}; font-weight: 500; border: 1px solid black;">{!! trans('allowance_categories.particular') !!}</th>
                <th style="text-align: center; vertical-align: middle; background: {!! $bgColors[$i] !!}; font-weight: 500; border: 1px solid black;">{!! trans('allowance_categories.other') !!}</th>
                <th style="text-align: center; vertical-align: middle; background: {!! $bgColors[$i] !!}; font-weight: 500; border: 1px solid black;">{!! trans('allowance_categories.diligence') !!}</th>
            @endfor
        @endif
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
            <td style="text-align: right; vertical-align: middle; border: 1px solid black;">
                {!! $item->basic_salary !!}
            </td>

            <?php $appendixes = $item->appendixAllowances3 ? $item->appendixAllowances3->sortByDesc('created_at')->groupBy('code')->all() : [];?>
            @if(count($appendixes))
                @foreach($appendixes as $key => $item)
                    <td style="vertical-align: middle;text-align: left; border: 1px solid black;" >
                        {!! $item[0]->code_global !!}
                    </td>
                    <td style="text-align: right; vertical-align: middle; border: 1px solid black;">
                        {!! $item[0]->salary !!}
                    </td>
                    <td style="vertical-align: middle;text-align: center; border: 1px solid black;">
                        {!! date('d/m/Y', strtotime($item[0]->valid_from))  !!}
                    </td>
                    <td style="vertical-align: middle;text-align: center; border: 1px solid black;">
                        {!! date('d/m/Y', strtotime($item[0]->valid_to))  !!}
                    </td>

                    <?php $temp = $item->pluck('expense', 'allowance_id'); ?>
                    <td style="text-align: right; vertical-align: middle; border: 1px solid black;">
                        {!! $temp[1] !!}
                    </td>
                    <td style="text-align: right; vertical-align: middle; border: 1px solid black;">
                        {!! $temp[2] !!}
                    </td>
                    <td style="text-align: right; vertical-align: middle; border: 1px solid black;">
                        {!! $temp[3] !!}
                    </td>
                    <td style="text-align: right; vertical-align: middle; border: 1px solid black;">
                        {!! $temp[4] !!}
                    </td>
                    <td style="text-align: right; vertical-align: middle; border: 1px solid black;">
                        {!! $temp[5] !!}
                    </td>
                    <td style="text-align: right; vertical-align: middle; border: 1px solid black;">
                        {!! $temp[6] !!}
                    </td>
                    <td style="text-align: right; vertical-align: middle; border: 1px solid black;">
                        {!! $temp[7] !!}
                    </td>
                    <td style="text-align: right; vertical-align: middle; border: 1px solid black;">
                        {!! $temp[8] !!}
                    </td>
                    <td style="text-align: right; vertical-align: middle; border: 1px solid black;">
                        {!! $temp[9]->expense !!}
                    </td>
                    <td style="text-align: right; vertical-align: middle; border: 1px solid black;">
                        {!! $temp[10] !!}
                    </td>
                @endforeach
            @else
                @for($k = 0; $k < 14 * $maxCountAppendix ; $k++)
                    <td style="text-align: center; vertical-align: middle; border: 1px solid black;"></td>
                @endfor
            @endif

        </tr>
    @endforeach
    </tbody>
</table>