<table class="excel" style="font-family: 'Times New Roman'!important;">
    @php $bgColors = ['#fbbc04', '#9abb59', '#92cddc', '#fbbc04', '#9abb59', '#92cddc']; @endphp
    <thead>
    <tr>
        <th colspan="7"
            style="text-align: center; vertical-align: middle; background: #4285f4; font-weight: 500; border: 1px solid black;">
            Thông tin chung
        </th>
        @if($maxCountConcurrent)
            @for($i = 0; $i < $maxCountConcurrent; $i++)
                <th colspan="7"
                    style="text-align: center; vertical-align: middle; background: {!! $bgColors[$i] !!}; font-weight: 500; border: 1px solid black; border-right: 2px solid black">
                   Hợp đồng kiêm nhiệm {{ $i+1 }}
                </th>
            @endfor
        @endif
    </tr>
    <tr style=" border: 1px solid black;">
        <th style="text-align: center; vertical-align: middle; background: #4285f4; font-weight: 500; border: 1px solid black;">{!! trans('system.no.') !!}</th>
        <th style="text-align: center; vertical-align: middle; background: #4285f4; font-weight: 500; border: 1px solid black;">{!! trans('contracts.code') !!}</th>
        <th style="text-align: center; vertical-align: middle; background: #4285f4; font-weight: 500; border: 1px solid black;">{!! trans('staffs.code') !!}</th>
        <th style="text-align: center; vertical-align: middle; background: #4285f4; font-weight: 500; border: 1px solid black;">{!! trans('contracts.staff_id') !!}</th>
        <th style="vertical-align: middle; text-align: center; background: #4285f4; font-weight: 500; border: 1px solid black;">{!! trans('contracts.company_id') !!}</th>
        <th style="text-align: center; vertical-align: middle; background: #4285f4; font-weight: 500; border: 1px solid black;">{!! trans('contracts.department_id') !!}</th>
        <th style="text-align: center; vertical-align: middle; background: #4285f4; font-weight: 500; border: 1px solid black;">{!! trans('contracts.basic_salary') !!}</th>
        @if($maxCountConcurrent)
            @for($i = 0; $i < $maxCountConcurrent; $i++)
                <th style="vertical-align: middle; text-align: center; background: {!! $bgColors[$i] !!}; font-weight: 500; border: 1px solid black;">{!! trans('contracts.company_id') !!}</th>
                <th style="text-align: center; vertical-align: middle; background: {!! $bgColors[$i] !!}; font-weight: 500; border: 1px solid black;">{!! trans('contracts.department_id') !!}</th>
                <th style="text-align: center; vertical-align: middle; background: {!! $bgColors[$i] !!}; font-weight: 500; border: 1px solid black;">{!! trans('contracts.position_id') !!}</th>
                <th style="text-align: center; vertical-align: middle; background: {!! $bgColors[$i] !!}; font-weight: 500; border: 1px solid black;">{!! trans('contracts.title_id') !!}</th>
                <th style="text-align: center; vertical-align: middle; background: {!! $bgColors[$i] !!}; font-weight: 500; border: 1px solid black;">{!! trans('contracts.salary') !!}</th>
                <th style="text-align: center; vertical-align: middle; background: {!! $bgColors[$i] !!}; font-weight: 500; border: 1px solid black;">{!! trans('contracts.valid_from') !!}</th>
                <th style="text-align: center; vertical-align: middle; background: {!! $bgColors[$i] !!}; font-weight: 500; border: 1px solid black;">{!! trans('contracts.valid_to') !!}</th>
            @endfor
        @endif
    </tr>
    </thead>
    <tbody>
    <?php $i = 1; ?>
    @foreach ($contracts as $item)
        <tr>
            <td style="text-align: center; vertical-align: middle; border: 1px solid black;">{!! $i++ !!}</td>
            <td style="vertical-align: middle;text-align: left; border: 1px solid black;">
                {!! $item->code !!}
            </td>
            <td style="vertical-align: middle;text-align: center; border: 1px solid black;">
                {!! $item->user->code !!}
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
            <?php $concurrents = $item->concurrentContracts;?>
            @if(count($concurrents))
                @foreach($concurrents as $item)
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
                    <td style="text-align: right; vertical-align: middle; border: 1px solid black;">
                        {!! $item->salary !!}
                    </td>
                    <td style="vertical-align: middle;text-align: center; border: 1px solid black;">
                        {!! date('d/m/Y', strtotime($item->valid_from))  !!}
                    </td>
                    <td style="vertical-align: middle;text-align: center; border: 1px solid black;">
                        {!! date('d/m/Y', strtotime($item->valid_to))  !!}
                    </td>
                @endforeach
                <?php $countRemain = $maxCountConcurrent - count($concurrents); ?>
                @if($countRemain > 0)
                    @for($h = 0; $h < 7*$countRemain; $h++)
                        <td style="text-align: center; vertical-align: middle; border: 1px solid black;"></td>
                    @endfor
                @endif
            @else
                @for($k = 0; $k < 7 * $maxCountConcurrent ; $k++)
                    <td style="text-align: center; vertical-align: middle; border: 1px solid black;"></td>
                @endfor
            @endif
        </tr>
    @endforeach
    </tbody>
</table>