<div class="nav-tabs-custom">
    <ul class="nav nav-tabs" style="font-weight: 700; font-size: 14px;">
        <li class="active"><a href="#detail-leave-tab"
                              data-toggle="tab">Chi tiết</a></li>
        <li><a href="#leave-tab" data-toggle="tab">Tổng hợp</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="detail-leave-tab">
            <div class="table-responsive">
                @include('backend.reports.view.partition-leave._detail_leave_table')
            </div>
        </div>
        <div class="tab-pane" id="leave-tab">
            <div class="table-responsive">
                @include('backend.reports.view.partition-leave._leave_table')
            </div>
        </div>
    </div>
</div>
