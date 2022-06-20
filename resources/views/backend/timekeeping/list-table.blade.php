<div class="box">
    <div class="box-body no-padding">
        <table class="table table-striped table-bordered table-hover" style="width: 100%" id="tableTimeKeeping">
            <thead>
            <tr>
                <th style="text-align: center; vertical-align: middle;">{!! trans('system.no.') !!}</th>
                <th style="text-align: center; vertical-align: middle;" class="company_id">{!! trans('timekeeping.company') !!}</th>
                <th style="text-align: center; vertical-align: middle;" class="department_id">{!! trans('timekeeping.department') !!}</th>
                <th style="text-align: center; vertical-align: middle;">{!! trans('timekeeping.month') !!}</th>
                <th style="text-align: center; vertical-align: middle;">{{ trans('timekeeping.created_by') }}</th>
                <th class="status" style="text-align: center; vertical-align: middle;">{{ trans('timekeeping.status') }}</th>
                <th style="text-align: center; vertical-align: middle;">{{ trans('timekeeping.user_approved') }}</th>
                {{-- <th style="text-align: center; vertical-align: middle;">{{ trans('timekeeping.date_approved') }}</th> --}}
                <th style="text-align: center; vertical-align: middle;">{!! trans('system.action.label') !!}</th>
            </tr>
            </thead>
            <tbody>
                @if (count($timekeeping) > 0)
                    @foreach ($timekeeping as $key => $item)
                        <tr>
                            <td>{!! $key + 1 !!}</td>
                            <td class="company_id">{{ $item->company->shortened_name }}</td>
                            <td class="department_id text-left">{{ $item->department->name }}</td>
                            <td> {{ $item->month < 10 ? '0'.$item->month : $item->month }}/{{ $item->year }}</td>
                            <td>{{ $item->user_by->fullname }}</td>
                            
                            {{-- <td>
                                @if ($item->status == 'APPROVED')
                                    <span class="label label-success" style="font-size: 12px">Đã chốt</span>
                                @endif
                            </td> --}}
                            <td class="status">
                                @if ($item->status == 'APPROVED')
                                    <span class="label label-success" style="font-size: 14px">Đã chốt</span><br>
                                    {{ $item->date_approved ? date('m/d/Y H:i:s', strtotime($item->date_approved)) : ''}}

                                @else
                                    <span class="hiden-status" style="font-size: 14px">Chưa chốt</span><br>
                                @endif
                            </td>
                            <td>
                                @if ($item->status == 'APPROVED')
                                    {{ $item->userApproved->fullname }}
                                @endif
                            </td>
                            {{-- <td>
                                @if ($item->status == 'APPROVED')
                                    {{ $item->date_approved ? date('m/d/Y H:i:s', strtotime($item->date_approved)) : ''}}
                                @endif
                            </td> --}}
                            <td>
                                @if ($item->version == 1)
                                    <a href="{{ route('admin.timekeepings.detail', $item->id) }}" class="btn btn-info btn-xs">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                @else 
                                    <a href="{{ route('admin.timekeeping.detail', $item->id) }}" class="btn btn-info btn-xs">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                @endif
                                
                                @if ((Auth::user()->hasRole('TP') || Auth::user()->id == 1 || in_array("delete", $moreActions) ) && $item->status != 'APPROVED')
                                    <a href="javascript:void(0)"
                                        link="{{ route('admin.timekeeping.destroy', $item->id) }}"
                                        class="btn-confirm-del btn btn-default btn-xs">
                                        <i class="text-danger glyphicon glyphicon-remove"></i>
                                    </a>    
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>           
        </table>
    </div>
</div>