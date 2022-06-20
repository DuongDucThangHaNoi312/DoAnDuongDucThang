<?php
return [
    'label' => 'Công ty',
    'name' => 'Tên công ty',
    'address' => 'Địa chỉ ',
    'telephone' => 'Số điện thoại ',
    'tax_code' => 'Mã số thuế ',
    'status' => [
        'label' => 'Trạng thái',
        App\Define\Company::COMPANY_ACTIVE => 'Đang hoạt động',
        App\Define\Company::COMPANY_INACTIVE => 'Không hoạt động',
    ],
    'company_active'=>'Công ty đang hoạt động không thể xóa !!!',
    'department_active'=>'Đang có phòng ban hoạt động không thể xóa !!!',
    'shortened_name'=>'Tên viết tắt',
    'name_es'=>'Tên tiếng anh',
    'address_es'=>'Địa chỉ tiếng anh',
    'user'=>'Người đại diện',
    'qualification'=>'Chức danh',
    'active'=>'Đang hoạt động',
    'fax' => 'Số Fax'
];
