<?php

namespace App\Define;

class Bank {

    const TYPE_INTERNAL = 0;
    const TYPE_EXTERNAL = 1;

    const USER_CUSTOMER = 0;
    const USER_MERCHANT = 1;
    const USER_PARTNER  = 2;

    public static function getTypes() {
        return [ self::TYPE_INTERNAL, self::TYPE_EXTERNAL ];
    }

    public static function getSelectTypes() {
        return [ self::TYPE_INTERNAL => trans('banks.types.' . self::TYPE_INTERNAL), self::TYPE_EXTERNAL => trans('banks.types.' . self::TYPE_EXTERNAL) ];
    }

    public static function getUsers() {
        return [ self::USER_CUSTOMER, self::USER_MERCHANT, self::USER_PARTNER ];
    }

    public static function getSelectUsers() {
        return [ self::USER_CUSTOMER => trans('banks.users.' . self::USER_CUSTOMER), self::USER_MERCHANT => trans('banks.users.' . self::USER_MERCHANT), self::USER_PARTNER => trans('banks.users.' . self::USER_PARTNER) ];
    }

    public static function listAmountUserMerchant() {
        return [ 200000, 500000, 1000000, 2000000, 5000000, 10000000 ];
    }

    public static function listAmountAtmUserMerchant() {
        return [ 100000, 200000, 500000, 1000000, 2000000, 5000000, 10000000 ];
    }
}
