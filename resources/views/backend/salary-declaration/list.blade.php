<div class="box">
    <div class="box-body no-padding" style="overflow-x:auto;">
        <table class="table table-striped table-bordered" style="width: 100%" id="tablePayrolls">
            <thead>
            <tr>
                <th style="text-align: center; vertical-align: middle;">{!! trans('system.no.') !!}</th>
                <th style="text-align: center; vertical-align: middle;" class="department_group">Nhóm phòng ban</th>
                <th style="text-align: center; vertical-align: middle; width: 150px" class="month_year">Tháng</th>
                <th style="text-align: center; vertical-align: middle; width: 100px">Số TK Chính</th>
                <th style="text-align: center; vertical-align: middle; width: 100px;">Số TK Nhánh</th>
                <th style="text-align: center; vertical-align: middle; width: 150px;">Số TK KH tự mở</th>
                <th style="text-align: center; vertical-align: middle; width: 100px;">Tổng điểm</th>
                <th style="text-align: center; vertical-align: middle; width: 120px;">Tổng tiền thưởng </th>
                <th style="text-align: center; vertical-align: middle; width: 100px;">Người tạo</th>
                <th style="text-align: center; vertical-align: middle; width: 100px" class="status">Trạng thái</th>
                <th style="text-align: center; vertical-align: middle; width: 100px">TP duyệt</th>
                <th style="text-align: center; vertical-align: middle; width: 100px">Ngày TP duyệt</th>
                <th style="text-align: center; vertical-align: middle; width: 100px">KT duyệt</th>
                <th style="text-align: center; vertical-align: middle; width: 100px">Ngày KT duyệt</th>
                <th style="text-align: center; vertical-align: middle; width: 100px">Thao tác</th>
            </tr>
            </thead>
            <tbody>
                @if (count($data) > 0)
                @php $i = 1; @endphp
                    @foreach ($data as $key1 => $item)
                        @foreach ($item as $key2 => $value)
                            <tr>
                                <td>{!! $i !!}</td>
                                <td class="department_group" style="min-width: 100px">{!! $value['department_group'] !!}</td>
                                <td style="min-width: 100px" class="month_year">{!!  $value['month_year'] !!}</td>
                                <td style="min-width: 100px" >{!! $value['declaration_main'] !!}</td>
                                <td style="min-width: 100px" >{!! $value['declaration_branch'] !!}</td>
                                <td style="min-width: 100px" >{!! $value['declaration_self'] !!}</td>
                                <td style="min-width: 100px" >{!! $value['point'] !!}</td>
                                <td style="min-width: 100px" >{!! $value['point'] > (count(array_merge(...$value['CREATED']))*100) ?  \App\Helper\HString::currencyFormat(($value['point'] - count(array_merge(...$value['CREATED']))*100)*40000) : 0 !!}    </td>
                                <td style="min-width: 100px" >{!! $value['created_by'] !!}</td>
                                <td style="min-width: 100px"  class="status">
                                    @if (!is_null($value['kt_approved_date']))
                                        <span class="label label-success" style="font-size: 14px">KT duyệt</span><br>
                                    @else
                                        @if (!is_null($value['tp_approved_date']))
                                            <span class="label label-primary" style="font-size: 14px">TP duyệt</span><br>
                                        @else
                                            <span class="label label-default" style="font-size: 14px">Khởi tạo</span><br>
                                        @endif
                                    @endif
                                </td>
                                <td style="min-width: 100px" >{!! $value['tp_approved_by'] !!}</td>
                                <td style="min-width: 100px" >{!! strtotime($value['tp_approved_date']) > 0 ? date('d/m/Y', strtotime($value['tp_approved_date'])) : '' !!}</td>
                                <td style="min-width: 100px" >{!! $value['kt_approved_by'] !!}</td>
                                <td style="min-width: 100px" >{!! strtotime($value['kt_approved_date']) > 0 ? date('d/m/Y', strtotime($value['kt_approved_date'])) : '' !!}</td>
                                <td style="border: none; min-width: 150px">
                                    <div class="row">
                                        @if ($user->hasRole('TGD') || $user->hasRole('system') || $user->hasRole('TP') || in_array($user->qualification_id, \App\Defines\User::KT))
                                            <a title="Xem" href="{{ route('admin.salary-declarations.detailDep') }}?month_year={!! $key1 !!}&department_group_id={!! $key2 !!}" class="btn btn-default btn-xs">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @endif
                                        
                                        
                                        @if ($user->hasRole('TGD') || $user->hasRole('system') || $user->hasRole('TP') || in_array($user->qualification_id, \App\Defines\User::KT))
                                            <a href="{{ route('admin.salary-declarations.detailCom') }}?month_year={!! $key1 !!}&department_group_id={!! $key2 !!}" class="btn btn-default btn-xs">
                                                <i class="fa fa-list"></i>
                                            </a>
                                        @endif    

                                        @if (($user->hasRole('TGD') && $value['kt_approved'] == false) || ($user->hasRole('system')  && $value['kt_approved'] == false) || ($user->hasRole('TP') && ($value['kt_approved']) == false && ($value['tp_approved']) == false) || (in_array($user->qualification_id, \App\Defines\User::KT) && ($value['tp_approved']) == true) )
                                            <a title="Duyệt" class="btn btn-default btn-xs" data-toggle="modal" data-target="#approved{!! $i !!}">
                                                <i class="fa fa-check"></i>
                                            </a>
                                        @endif   
                                        
                                        @if (($user->hasRole('TGD') || $user->hasRole('system') || $user->hasRole('TP') || in_array($user->qualification_id, \App\Defines\User::KT)) && ($value['kt_approved']) == false)
                                            <a title="Tính lại" class="btn btn-default btn-xs" data-toggle="modal" data-target="#restart{!! $i !!}">
                                                <i class="fa fa-fan"></i>
                                            </a>
                                            <a title="Xóa"  href="javascript:void(0)" link="{!! route('admin.salary-declarations.destroy', $key2) !!}?month_year={!! $key1 !!}" class="btn-confirm-del btn btn-default btn-xs">
                                                <i class="text-danger glyphicon glyphicon-remove"></i>
                                            </a>
                                        @endif    
                                            {{-- modal duyệt --}}
                                                <div class="modal fade" id="approved{!! $i !!}" tabindex="-1" role="dialog" aria-labelledby="approvedLabel"  aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                                    <div class="modal-dialog" role="document" style="text-align: left">
                                                        <form action="{!! route('admin.salary-declarations.approved') !!}" method="POST">
                                                            @csrf
                                                            <div class="modal-content">
                                                                <div class="modal-header" style="background-color: #3c8dbc; color: white; text-align: center">
                                                                    <h4 class="modal-title" id="approvedLabel">Duyệt lương thưởng tờ khai nhóm {!! $value['department_group'] !!}</h4>
                                                                    <h4>T{!! $value['month_year'] !!}</h4>
                                                                </div>
                                                                <div class="modal-body text-center">
                                                                    <h4 style="color: red">Lưu ý bảng lương đã được duyệt không thể thêm mới và tính lại</h4>
                                                                </div>
                                                                <input type="hidden" name="department_group_id" value = {!! $key2 !!}>
                                                                <input type="hidden" name="month_year" value = {!! $key1 !!}>
                                                                <div class="modal-footer" style="text-align: center">
                                                                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Đóng</button>
                                                                    <button type="submit" class="btn btn-primary btn-sm">Xác nhận</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                        
                                                    </div>
                                                </div>
                                            {{-- end modal duyêt --}}
                                        {{-- modal tính lại --}}
                                            <div class="modal fade" id="restart{!! $i !!}" tabindex="-1" role="dialog" aria-labelledby="restartLabel"  aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                                <div class="modal-dialog" role="document" style="text-align: left">
                                                    <form action="{!! route('admin.salary-declarations.restart') !!}" method="POST">
                                                        @csrf
                                                        <div class="modal-content">
                                                            <div class="modal-header" style="background-color: #3c8dbc; color: white; text-align: center">
                                                                <h4 class="modal-title" id="restartLabel">Bạn muộn tính lại lương thưởng tờ khai nhóm  {!! $value['department_group'] !!}</h4>
                                                                <h4>T{!! $value['month_year'] !!}</h3>
                                                            </div>
                                                            <input type="hidden" name="department_group_id" value = {!! $key2 !!}>
                                                            <input type="hidden" name="month_year" value = {!! $key1 !!}>
                                                            <div class="modal-footer" style="text-align: center">
                                                                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Đóng</button>
                                                                <button type="submit" class="btn btn-primary btn-sm">Xác nhận</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                    
                                                </div>
                                            </div>
                                        {{-- end modal tính lại --}}
                                    </div>
                                </td>
                            </tr>
                            @php $i++ ; @endphp
                        @endforeach         
                    @endforeach
                @endif
            </tbody>           
        </table>
        
       

        @if (count($data) == 0)
        <div class="text-center error">
            <span class="text-size"><i class="fas fa-search"></i> {!! trans('timekeeping.no_data') !!}</span>
        </div>
        @endif
    </div>
</div>