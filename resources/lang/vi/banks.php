<?php

return [
    'label'                 => 'Ngân hàng/Thẻ',
    'name'                  => 'Tên ngân hàng/thẻ',
    'type'                  => 'Loại ngân hàng/thẻ',
    'user'                  => 'Người dùng',
    'fee_fixed'             => 'Phí cố định',
    'fee_percent'           => 'Phí phần trăm',
    'raw_fee_fixed'         => 'Phí cố định gốc',
    'raw_fee_percent'       => 'Phí phần trăm gốc',
    'code'                  => 'Mã ngân hàng/thẻ',
    'gateway'               => 'Cổng phát hành',
    'logo'                  => 'Logo',
    'online'    => 'Online',
    'qr_code'   => 'QR Code',
    'is_partner'=> 'Trả CTV',
    'types'           => [
        App\Define\Bank::TYPE_INTERNAL      => 'Thẻ nội địa',
        App\Define\Bank::TYPE_EXTERNAL      => 'Thẻ quốc tế',
    ],
    'users'           => [
        App\Define\Bank::USER_CUSTOMER      => 'Khách hàng thường',
        App\Define\Bank::USER_MERCHANT      => 'Đại lý',
        App\Define\Bank::USER_PARTNER       => 'Đối tác',
    ],
];

