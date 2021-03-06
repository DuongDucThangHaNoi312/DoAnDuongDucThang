<?php
use App\Define\Staff;
return [
    'label' => 'Chức Danh',
    'href' => 'Liên kết',
    'name' => 'Tên chức danh ',
    'create_title'=>'Thêm mới chức danh ',
    'edit_title'=>'Sửa chức danh ',
    'code_placeholder' => 'Chỉ bao gồm chữ,số, dấu _-.',
    'code' => 'Mã chức danh',
    'created_at' => 'Ngày tạo',
    'weight'=>'Cấp bậc',
    'add'=>'Thành công',
    'unique_in_dept'=>'Xác định chức vụ',
    'is_system '=>'Đánh dấu chức vụ',
    'edit'=>'Cập nhật thành công',
    'start'=>'Từ ngày',
    'end'=>'Đến ngày',
    'reason'=>'Lý do',
    'status'=>'Trạng thái duyệt đơn',
    'total'=>'Số buổi nghỉ',
    'destroy'=>'Hủy đơn',
    'destroys'=>'Hủy đơn thành công',
    'delete_leave'=>'Hủy đơn xin nghỉ',
    'action'=>'Thao tác',
    'errors'=>'Trường kpi không được để trống',
    'sent'=>'Gửi yêu cầu xin hủy đơn',
    'wait'=>'Chờ duyệt yêu cầu hủy đơn',
    'confirm'=>'Xác nhận',
    'out of date'=>'Quá hạn',
    'day_off'=>'Số ngày nghỉ',
    'type'=>'Loại ngày nghỉ',
    'day_unpaidLeave'=>'Nghỉ không lương',

    'weights' => [
        'label' => 'Cấp bậc',
        \App\Defines\Staff::WEIGHT_NV => 'Nhân Viên',
        \App\Defines\Staff::WEIGHT_PP => 'Phó Phòng',
        \App\Defines\Staff::WEIGHT_TP => 'Trưởng phòng',
        \App\Defines\Staff::WEIGHT_KT => 'Kế toán',
        \App\Defines\Staff::WEIGHT_GD => 'Giám đốc',
        \App\Defines\Staff::WEIGHT_TGD=> 'Tổng Giám đốc',
    ],
    'statu'=>[
        \App\Defines\StaffDayOff::STATUS_ON =>'Chưa duyệt',
        \App\Defines\StaffDayOff::STATUS_OFF=>'Đã duyêt',
    ],
    'name_es'=>'Tên tiếng anh',
    'approved_by' => 'Người duyệt',
    'approved_date' => 'Ngày duyệt',
    'edit_day_of' => 'Sửa đơn'
];
