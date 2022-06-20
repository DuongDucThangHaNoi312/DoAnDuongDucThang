<?php

return [
    'label' => 'Nhân Viên',
    'employees' => 'Employees Name',
    'href' => 'Liên kết',
    'name' => 'Tiêu đề',
    'code_placeholder' => 'Chỉ bao gồm chữ,số, dấu _-.',
    'fullname' => 'Họ tên',
    'active'=>'Đăng nhập',
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
    'email'=>'Email',
    'phone'=>'Số điện thoại',
    'password'=>'Mật khẩu',
    're_password'=>'Nhập lại mật khẩu',
    'code_timekeeping'=>'Mã chấm công',
    'roles'=>'Các vai trò',
    'image'=>'Ảnh',
    'card_no'=>'Chỉ bao gồm những số ',
    'tax_code' => 'Mã số thuế',
    'insurance_no'  => 'Số sổ BHXH',
    'bank_name'     => 'Tên Ngân hàng',
    'bank_account'  => 'Số tài khoản NH',
    'driver_license_no' => 'Số GPLX',
    'driver_license_expire'     => 'Thời hạn bằng lái xe ô tô',
    'driver_license_class'      => 'Hạng bằng lái xe ô tô',
    'family'    => 'Gia đình',
    'dob'       => 'Ngày sinh',
    'ethnicity' => 'Dân tộc',
    'marital_status'        => [
        'label' => 'Tình trạng kết hôn',
        \App\Defines\Staff::MARITAL_STATUS_SINGLE   => 'Độc thân',
        \App\Defines\Staff::MARITAL_STATUS_MARRIED  => 'Kết hôn',
    ],
    'qualifications'        => [
        'label' => 'Bằng cấp cao nhất',
        \App\Defines\Staff::QUALIFICATION_UNIVERSITY    => 'Đại học/Học viện',
        \App\Defines\Staff::QUALIFICATION_COLLEGE       => 'Cao đẳng',
        \App\Defines\Staff::QUALIFICATION_INTERMEDIATE  => 'Trung cấp',
        \App\Defines\Staff::QUALIFICATION_POST_GRADUATE => 'Sau đại học',
        \App\Defines\Staff::QUALIFICATION_HIGHSCHOOL    => 'Tốt nghiệp THPT',
        \App\Defines\Staff::QUALIFICATION_SECONDARY     => 'Tốt nghiệp THCS',
    ],
    'dependent'     => 'Phụ thuộc',
    'independent'   => 'Không phụ thuộc',
    'dependent_from'=> 'Phụ thuộc từ',
    'dependent_to'  => 'Phụ thuộc đến',
    'family_relationships'  => [
        'label' => 'Quan hệ',
        \App\Defines\Staff::FAMILY_RELATIONSHIP_WIFE    => 'Vợ',
        \App\Defines\Staff::FAMILY_RELATIONSHIP_HUSBAND => 'Chồng',
        \App\Defines\Staff::FAMILY_RELATIONSHIP_CHILDREN=> 'Con',
        \App\Defines\Staff::FAMILY_RELATIONSHIP_MOTHER  => 'Mẹ',
        \App\Defines\Staff::FAMILY_RELATIONSHIP_FATHER  => 'Cha',
        \App\Defines\Staff::FAMILY_RELATIONSHIP_BROTHER => 'Anh',
        \App\Defines\Staff::FAMILY_RELATIONSHIP_SISTER  => 'Chị',
        \App\Defines\Staff::FAMILY_RELATIONSHIP_YOUNGER => 'Em',

    ],
    'emergency_contact' => 'Liên hệ khẩn cấp',
    'emergency_phone'   => 'Điện thoại khẩn cấp',
    'genders' => [
        'label' => 'Giới tính',
        \App\Defines\Staff::GENDER_FEMALE => 'Nữ',
        \App\Defines\Staff::GENDER_MALE => 'Nam',
    ],
    'status' => [
        'label' => 'Tình trạng',
        \App\Defines\Staff::STATUS_QUIT => 'Nghỉ việc',
        \App\Defines\Staff::STATUS_PROBATIONARY => 'Thử việc',
        \App\Defines\Staff::STATUS_OFFICIAL => 'Chính thức',
    ],
    'leave' => [
        'label' => '',
        \App\Defines\StaffDayOff::STAFF_DAY_OFF_TAKE => '12 ngày phép',
        \App\Defines\StaffDayOff::STAFF_DAY_OFF_WEDDING => 'Nghỉ cưới',
        \App\Defines\StaffDayOff::STAFF_DAY_OFF_UNPAID_70 => 'Nghỉ hưởng 70 % lương',
        \App\Defines\StaffDayOff::STAFF_DAY_OFF_MATERNITY => 'Nghỉ thai sản',
        \App\Defines\StaffDayOff::STAFF_DAY_OFF_SICK => 'Nghỉ không lương',
    ],
    'user_info' => 'Thông tin nhân viên',
    'create_contract'=>'Đi đến tạo hợp đồng luôn cho nhân viên',
    'roles_user'=>'Thêm mới quyền cho nhân viên',
    'set_role_first'=>'Vui lòng cập nhật vai trò cho nhân viên trước',
    'staff_start' => 'Ngày vào công ty',
    'domicile'  => 'Nơi sinh'
];
