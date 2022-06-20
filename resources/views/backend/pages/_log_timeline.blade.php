<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header" style="background-color: #3c8dbc; font-weight: bold; font-size: 16px;">
            <span>Chi tiết chỉnh sửa</span>
        </div>
        <div class="modal-body">
            <div style="max-height: 70vh; overflow-y: scroll;">
                <ul class="timeline" style="text-align: left;">
                    @if (count($data) > 0)
                        @foreach($data as $key => $value)
                            <li>
                                <i class="fa fa-bolt bg-blue"></i>
                                <div class="timeline-item">
                                    <div>
                                        <span class="time"><i class="fa fa-clock-o"></i></span>
                                        <span class="timeline-header" style="float: left">
                                        Trường thay đổi: <b> {!!  trans($lang.".".$key) !!} </b>
                                        </span>
                                    </div>
                                    @foreach($value as $key => $timeline)
                                        <div class="timeline-body" style="padding-left : 0px">
                                    <span>
                                        Dữ liệu mới: {!! $timeline['new_data']  !!}
                                    </span><br/>
                                            <span style="text-decoration: line-through;">
                                        Dữ liệu cũ: {!! $timeline['old_data']  !!}
                                    </span><br/>
                                    <span style="float: right ;color: #999; font-size: 12px;">
                                        {!! ($timeline['action_at']) !!}
                                    </span>
                                    </div>
                                    <div class="timeline-footer" style="padding-top: 0px">
                                        <span class="btn btn-warning btn-xs">{!! $users[$timeline['action_by']] ?? '-' !!}</span>
                                    </div>
                                    @endforeach
                                </div>
                            </li>
                        @endforeach
                    @else
                        <li>
                            <i class="fa fa-bolt bg-blue"></i>
                            <div class="timeline-item">
                                <h3 class="timeline-header" style="float: left">
                                    {!! trans('system.not_edited_yet') !!}
                                </h3>
                            </div>
                        </li>
                    @endif
                    <li>
                        <i class="fa fa-user bg-blue"></i>
                        <div class="timeline-item">
                            <span class="time"><i class="fa fa-clock-o"></i> {!! date('d/m/Y H:i:s', strtotime($record->created_at)) !!}</span>
                            <h3 class="timeline-header" style="float: left">
                                {!! trans('system.created_by') !!}
                                {!! $users[$record->created_by] ?? $users[$record['user_id']]  !!}
                            </h3>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-danger"
                    data-dismiss="modal">Đóng</button>
        </div>
    </div>
</div>


