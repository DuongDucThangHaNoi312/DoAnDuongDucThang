<div class="row">
    <div class="col-md-4 col-sm-6 col-xs-12">
        <div class="info-box">
            <a href="{!! route('admin.staffs.index') !!}">
                <span class="info-box-icon bg-green"><i class="fas fa-user"></i></span>
            </a>
            <div class="info-box-content">
                <span class="info-box-text">Tổng nhân viên</span>
                <span class="info-box-number">{!! App\User::countActives() !!}</span>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-sm-6 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-blue"><i class="far fa-building"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">Phòng họp trống </span>
                <span class="info-box-number">{!! \App\Models\Department::countActiveDepts() !!}</span>
            </div>
        </div>
    </div>
   
    <div class="col-md-4 col-sm-6 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-blue"><i class="far fa-building"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">Phòng đã thuê </span>
                <span class="info-box-number"> 0 </span>
            </div>
        </div>
    </div>



</div>
