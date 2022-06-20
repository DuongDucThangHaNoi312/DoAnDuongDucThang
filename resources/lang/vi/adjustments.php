<?php
return[
    'label' => 'Danh mục khoản điều chỉnh',
    'code' => 'Mã điều chỉnh',
    'adjustment_name'=>'Tên khoản điều chỉnh',
    'status'=>'Tình trạng',
    'action'=>'Thao tác',
    'list'=>'Danh sách',
    'adjustment_type'=>'Loại điều chỉnh',
    'create'=>'Thêm mới',
    'increase_adjustment'=>'Điều chỉnh tăng',
    'reduce_adjustment'=>'Điều chỉnh giảm',
    'tax_exemption'=>'Miễn thuế',
    'taxable'=>'Chịu thuế',
    'amount_money'=>'Số tiền',
    'error'=>'Có lỗi xảy ra',
    'active'=>'Hoạt động',
    'adjustment_types' => [
        'label' => 'Loại Điều Chỉnh',
        \App\Defines\Adjustment::INCREASE_ADJUSTMENT => 'Điều chỉnh tăng',
        \App\Defines\Adjustment::REDUCE_ADJUSTMENT => 'Điều chỉnh giảm',
    ],
    'tax_status' => [
        'label' => 'Tình trạng thuế',
        \App\Defines\Adjustment::TAXABLE => 'Chịu thuế',
        \App\Defines\Adjustment::TAX_EXEMPTION => 'Miễn thuế',
    ],
    
];