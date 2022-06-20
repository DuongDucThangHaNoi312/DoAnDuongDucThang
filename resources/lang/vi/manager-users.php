<?php

use App\Define\Staff;

return [
    'label' => 'Tài Khoản',
    'employees' => 'Employees Name',
    'password' =>'Mật khẩu',
    'confirm-password' =>'Xác nhận mật khẩu',
    'email' =>'Email',
    'href' => 'Liên kết',
    'name' => 'Tiêu đề',
    'code_placeholder' => 'Chỉ bao gồm chữ,số, dấu _-.',
    'fullname' => 'Họ tên',
    'code' => 'Mã nhân viên',
    'addresses' => 'Địa chỉ',
    'nationality' => 'Quốc tịch',
    'id_card_no' => 'Số CCCD',
    'issued_on' => 'Ngày cấp',
    'issued_at' => 'Nơi cấp',
    'date_of_birth' => 'Ngày sinh',
    'list'=>'Danh sách',
    'start_date'=>'Start Date',
    'end_date'=>'End Date',
    'type_of'=>'Leave Name',
    'weight'=>'Leave Name',
    'take-leave'=>'Danh sách đơn xin nghỉ',
    'staff_name'=>'Tên nhân viên',
    'manager_calendar'=>'Quản lý lịch ',
    'manager-leave'=>'Lịch xin nghỉ',
    'success'=>'Xác nhận thành công',
    'genders' => [
        'label' => 'Giới tính',
        \App\Defines\Staff::GENDER_FEMALE   => 'Nữ',
        \App\Defines\Staff::GENDER_MALE     => 'Nam',
        \App\Defines\Staff::GENDER_OTHER    => 'Khác',
    ],
    'status' => [
        'label' => 'Tình trạng',
        \App\Defines\Staff::STATUS_QUIT         => 'Nghỉ việc',
        \App\Defines\Staff::STATUS_PROBATIONARY => 'Thử việc',
        \App\Defines\Staff::STATUS_OFFICIAL     => 'Chính thức',
    ],
    'leave' => [
        'label' => '',
        \App\Defines\StaffDayOff::STAFF_DAY_OFF_TAKE        => '12 ngày phép',
        \App\Defines\StaffDayOff::STAFF_DAY_OFF_WEDDING     => 'Nghỉ cưới',
        \App\Defines\StaffDayOff::STAFF_DAY_OFF_UNPAID_70   => 'Nghỉ hưởng 70 % lương',
        \App\Defines\StaffDayOff::STAFF_DAY_OFF_MATERNITY   => 'Nghỉ thai sản',
        \App\Defines\StaffDayOff::STAFF_DAY_OFF_SICK        => 'Nghỉ không lương',
    ],

];
