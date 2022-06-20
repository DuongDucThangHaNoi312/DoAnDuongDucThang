<div class="box">
    <div class="box-body no-padding">
        <table class="table table-striped table-bordered" id="tablePayrolls">
            <thead>
            <tr>
                <th style="text-align: center; vertical-align: middle; width: 100px;">{!! trans('system.no.') !!}</th>
                <th style="text-align: center; vertical-align: middle; width: 150px;">Phòng ban</th>
                <th style="text-align: center; vertical-align: middle;">Nhóm</th>
                <th style="text-align: center; vertical-align: middle; width: 150px;">{!! trans('timekeeping.month') !!}</th>
                <th style="text-align: center; vertical-align: middle; width: 200px;">Số thành viên</th>
                <th style="text-align: center; vertical-align: middle; width: 200px;">{{ trans('timekeeping.created_by') }}</th>
                <th style="text-align: center; vertical-align: middle; width: 100px;">{!! trans('system.action.label') !!}</th>
            </tr>
            </thead>
            <tbody>
                @if (count($teams) > 0)
                    @foreach ($teams as $key => $item)
                        <?php
                            $timekeeping_detail = \App\Models\TimeKeepingDetail::teamTimeKeeping($user_ids[$item->id]);
                        ?>
                        @if (!empty($timekeeping_detail))
                            @foreach ($timekeeping_detail as $k => $value)
                            <?php 
                                $timekeeping = \App\Models\TimeKeeping::getTimekeeping($k)
                            ?>
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $item->department->name }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $timekeeping->month }}/{{ $timekeeping->year }}</td>
                                <td>{{ count($timekeeping_detail[$timekeeping->id]) }}</td>
                                <td>{{ $timekeeping->user_by->fullname }}</td>
                                <td>
                                    <a href="{{ route('admin.timekeepings.team-detail', ['teamId' => $item->id, 'timekeepingId' => $timekeeping->id]) }}" class="btn btn-info btn-xs">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        @endif
                        
                        
                    @endforeach
                @endif
            </tbody>           
        </table>
        @if (count($teams) == 0)
        <div class="text-center error">
            <span class="text-size"><i class="fas fa-search"></i> {!! trans('timekeeping.no_data') !!}</span>
        </div>
        @endif
    </div>
</div>