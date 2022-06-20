<?php
return [
        'label'=>'Hồ sơ ứng tuyển',
        'company_id'=>'Công ty ứng tuyển',
        'department_id'=>'Phòng ban ứng tuyển',
        'title_id'=>'Vị trí ứng tuyển',
        'name'=>'Tên ứng viên',
        'telephone'=>'Số điện thoại',
        'id_card_no'=>'Số CMND/CCCD',
        'dob'=>'Ngày sinh',
        'recruitment_address'=>'Địa chỉ phỏng vấn',
        'gender'=>[
            'label'=>'Giới tính',
            App\Define\Recruitment::GENDER_MALE => 'Nam',
            App\Define\Recruitment::GENDER_FEMALE => 'Nữ',
        ],
        'person_info'=>'Thông tin ứng viên',
        'company_info'=>'Thông tin công ty',
        'email'=>'Email',
        'permanent_residence'=>'Hộ khẩu thường trú',
        'education_level'=>[
            'label'=>'Trình độ học vấn',
            App\Define\Recruitment::UNIVERSITY=>'Đại Học',
            App\Define\Recruitment::COLLEGE=>'Cao Đẳng',
        ],
        'description'=>'Mô tả',
        'file'=>'File CV đính kèm',
];