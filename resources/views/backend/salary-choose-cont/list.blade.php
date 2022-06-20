<div class="box">
    <div class="box-body no-padding" style="overflow-x:auto;">
        <table class="table table-striped table-bordered" style="width: 100%" id="tablePayrolls">
            <thead>
            <tr>
                <th style="text-align: center; vertical-align: middle;">{!! trans('system.no.') !!}</th>
                <th style="text-align: center; vertical-align: middle; min-width: 100px" class="company_id">{!! trans('timekeeping.company') !!}</th>
                <th style="text-align: center; vertical-align: middle; min-width: 150px" class="department_id">{!! trans('timekeeping.department') !!}</th>
                <th style="text-align: center; vertical-align: middle; min-width: 100px">{!! trans('timekeeping.month') !!}</th>
                <th style="text-align: center; vertical-align: middle; min-width: 100px;">{!! trans('payrolls.total_user') !!}</th>
                <th style="text-align: center; vertical-align: middle; min-width: 150px;">{!! trans('payrolls.total_salary') !!}</th>
                <th style="text-align: center; vertical-align: middle; min-width: 100px;">{{ trans('timekeeping.created_by') }}</th>
                <th style="text-align: center; vertical-align: middle; min-width: 100px;" class="status">Trạng thái </th>
                <th style="text-align: center; vertical-align: middle; min-width: 100px">TP duyệt</th>
                <th style="text-align: center; vertical-align: middle; min-width: 100px">Ngày TP duyệt</th>
                <th style="text-align: center; vertical-align: middle; min-width: 100px">KT duyệt</th>
                <th style="text-align: center; vertical-align: middle; min-width: 100px">Ngày KT duyệt</th>
                <th style="text-align: center; vertical-align: middle; min-width: 100px">{!! trans('system.action.label') !!}</th>
            </tr>
            </thead>
            <tbody>
                @if (count($salaryChooseConts) > 0)
                    @foreach ($salaryChooseConts as $key => $item)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td class="company_id">{!! $item->company->shortened_name !!}</td>
                            <td class="department_id">{!! $item->deparment->name !!}</td>
                            <td>{!! $item->month !!}/{!! $item->year  !!}</td>
                            <td>{!! !is_null($item->total_user) ? ($item->total_user) : 0 !!}</td>
                            <td style="text-align: right">{{ \App\Helper\HString::currencyFormatVn(intval($item->total_money), 0, ',', '.') }}</td>
                            <td>{!! ($item->createdBy->fullname) !!}</td>
                            <td class="status">
                                @if (!is_null($item->kt_approved_date))
                                        <span class="label label-success" style="font-size: 14px">KT duyệt</span><br>
                                @else
                                    @if (!is_null($item->tp_approved_date))
                                        <span class="label label-primary" style="font-size: 14px">TP duyệt</span><br>
                                    @else
                                        <span class="label label-default" style="font-size: 14px">Khởi tạo</span><br>
                                    @endif
                                @endif
                            </td>
                            <td>{!! $item->tpApproved->fullname !!}</td>
                            <td>{!! strtotime($item->tp_approved_date) > 0 ? date('d/m/Y', strtotime($item->tp_approved_date)) : '' !!}</td>
                            <td>{!! $item->ktApproved->fullname !!}</td>
                            <td>{!! strtotime($item->kt_approved_date) > 0 ? date('d/m/Y', strtotime($item->kt_approved_date)) : '' !!}</td>
                            <td style="float: center; border: none">
                                <a href="{{ route('admin.salary-choose-containers.show', $item->id) }}" class="btn btn-info btn-xs">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if (($user->hasRole('TGD') || $user->hasRole('system') || $user->hasRole('TP') || in_array($user->qualification_id, \App\Defines\User::KT)) && (is_null($item->kt_approved_date)))
                                    <a href="javascript:void(0)" link="{!! route('admin.salary-choose-containers.destroy', $item->id) !!}" class="btn-confirm-del btn btn-default btn-xs"><i class="text-danger glyphicon glyphicon-remove"></i></a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>           
        </table>
        @if (count($salaryChooseConts) == 0)
        <div class="text-center error">
            <span class="text-size"><i class="fas fa-search"></i> {!! trans('timekeeping.no_data') !!}</span>
        </div>
        @endif
    </div>
</div>