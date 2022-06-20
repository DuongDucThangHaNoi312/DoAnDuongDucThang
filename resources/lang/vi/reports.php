<?php
return [
    'label'             => 'Báo cáo',
    'types'             => [
        'label' => 'Chọn một loại báo cáo',
        \App\Define\Report::STAFF_DEPENDENCY        => 'Báo cáo Phụ thuộc',
        \App\Define\Report::STAFF_SOCIAL_INSURANCE  => 'Báo cáo BHXH',
        \App\Define\Report::STAFF_LEAVE      => 'Báo cáo nghỉ phép',
        \App\Define\Report::STAFF_KPI      => 'Báo cáo KPI',
        \App\Define\Report::CONTRACT      => 'Báo cáo hợp đồng',
    ],
    'no_selected_type' => 'Bạn cần chọn 1 loại báo cáo.',
    'range_date' => 'Khoảng thời gian',
    'leave_types' => [
        'DETAIL' => 'Chi tiết',
        'GENERAL' => 'Tổng hợp phép'
    ]
];