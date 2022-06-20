{{-- @dd($payrolls[0]) --}}
<div class="box">
    <div class="box-body no-padding" style="overflow-x:auto;">
        <table class="table table-striped table-bordered" style="width: 100%" id="tablePayrolls">
            <thead>
            <tr>
                <th style="text-align: center; vertical-align: middle;">{!! trans('system.no.') !!}</th>
                <th style="text-align: center; vertical-align: middle;" class="company_id">{!! trans('timekeeping.company') !!}</th>
                <th style="text-align: center; vertical-align: middle; width: 150px" class="department_id">{!! trans('timekeeping.department') !!}</th>
                <th style="text-align: center; vertical-align: middle; width: 100px">{!! trans('timekeeping.month') !!}</th>
                {{-- @if (Auth::user()->hasRole('TP') || Auth::user()->hasRole('system')) --}}
                <th style="text-align: center; vertical-align: middle; width: 100px;">{!! trans('payrolls.total_user') !!}</th>
                <th style="text-align: center; vertical-align: middle; width: 150px;">{!! trans('payrolls.total_salary') !!}</th>
                {{-- @endif --}}
                
                <th style="text-align: center; vertical-align: middle; width: 100px;">{{ trans('timekeeping.created_by') }}</th>
                <th style="text-align: center; vertical-align: middle; width: 100px;">Trạng thái <br> bảng công</th>
                <th style="text-align: center; vertical-align: middle; width: 120px;" class="status">Trạng thái <br> bảng lương</th>

                <th style="text-align: center; vertical-align: middle; width: 100px;">{{ trans('payrolls.user_approved') }}</th>
                <th style="text-align: center; vertical-align: middle; width: 100px">{!! trans('system.action.label') !!}</th>
            </tr>
            </thead>
            <tbody>
                @if (count($payrolls) > 0)
                    @foreach ($payrolls as $key => $item)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td class="company_id">{{ $item->company->shortened_name }}</td>
                            <td class="department_id">{{ $item->department->name }}</td>
                            <td>{{ $item->month < 10 ? '0'.$item->month : $item->month    }}/{{ $item->year }}</td>
                            {{-- @if (Auth::user()->hasRole('TP') || Auth::user()->hasRole('system')) --}}
                            <td>{{ count($item->userPayroll) }}</td>
                            <td style="text-align: right">{{ \App\Helper\HString::currencyFormatVn(intval($item->total), 0, ',', '.') }}</td>
                            {{-- @endif --}}
                          
                            <td>{{ $item->user_by->fullname }}</td>
                            <td>
                                <?php 
                                    $check = DB::table('timekeepings')->where('month', $item->month)->where('year', $item->year)->where('department_id', $item->department_id)->first()->status;    
                                ?>
                                @if ($check == 'APPROVED')
                                    <span class="label label-success" style="font-size: 14px;">Đã chốt</span>
                                @endif
                            </td>
                            <td class="status">
                                @if ($item->status == 'APPROVED')
                                    <span class="label label-success" style="font-size: 14px">Đã duyệt</span><br>
                                    {{ date('d/m/Y H:i:s', strtotime($item->date_approved)) }}
                                @else
                                    <span class="hiden-status" style="font-size: 14px">Chưa duyệt</span><br>
                                @endif
                            </td>
                            <td>{{ $item->status == 'APPROVED' ? $item->userApproved->fullname : '' }}</td>

                            <td style="float: left; border: none">
                                @permission('payrolls.read')
                                    @if ($item->version == 1)
                                        <a href="{{ route('admin.payroll.detail', $item->id) }}" class="btn btn-info btn-xs">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    @else    
                                        <a href="{{ route('admin.payrolls.detail', $item->id) }}" class="btn btn-info btn-xs">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    @endif
                                @endpermission
                                
                                @permission('payrolls.delete')
                                    @if ($item->status != 'APPROVED')
                                        <a href="javascript:void(0)" link="{!! route('admin.payrolls.destroy', $item->id) !!}" class="btn-confirm-del btn btn-default btn-xs"><i class="text-danger glyphicon glyphicon-remove"></i></a>
                                    @endif
                                @endpermission
                                
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>           
        </table>
        @if (count($payrolls) == 0)
        <div class="text-center error">
            <span class="text-size"><i class="fas fa-search"></i> {!! trans('timekeeping.no_data') !!}</span>
        </div>
        @endif
    </div>
</div>