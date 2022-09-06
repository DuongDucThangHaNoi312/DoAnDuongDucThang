<div class="row">
    <div class="col-md-4 col-sm-6 col-xs-12">
        <div class="info-box">
            <a href="{!! route('admin.staffs.index') !!}">
                <span class="info-box-icon bg-green"><i class="fas fa-user"></i></span>
            </a>
            <div class="info-box-content">
                <span class="info-box-text">Tổng nhân viên</span>
                <span class="info-box-number">{!!  App\Models\User::countActives() !!}</span>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-sm-6 col-xs-12">
        <div class="info-box">
            <a href="{!! route('admin.meeting-rooms.index') !!}">
                <span class="info-box-icon bg-blue"><i class="far fa-building"></i></span>
            </a>
            <div class="info-box-content">
                <span class="info-box-text">Phòng họp </span>
                <span class="info-box-number">{!! \App\Models\MeetingRoom::countMeetingRoom() !!}</span>
            </div>
        </div>
    </div>
</div>
