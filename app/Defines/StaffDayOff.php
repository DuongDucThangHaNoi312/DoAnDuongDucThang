<?php


namespace App\Defines;

use App\Models\Contract;
use Illuminate\Support\Facades\Auth;

class StaffDayOff
{
    const STAFF_DAY_OFF_TAKE="T";
    const STAFF_DAY_OFF_WEDDING="C";
    const STAFF_DAY_OFF_UNPAID="U";
    const STAFF_DAY_OFF_UNPAID_70="U_70";
    const STAFF_DAY_OFF_SICK="S";
    const STAFF_DAY_OFF_MATERNITY="M";


    const STATUS_ON =0;
    const STATUS_OFF =1;


    public static function getStatusForOption()
    {
        return
            [self::STAFF_DAY_OFF_TAKE => trans('staffs.leave.' . self::STAFF_DAY_OFF_TAKE),
            self::STAFF_DAY_OFF_WEDDING => trans('staffs.leave.' . self::STAFF_DAY_OFF_WEDDING),
            self::STAFF_DAY_OFF_UNPAID_70 => trans('staffs.leave.' . self::STAFF_DAY_OFF_UNPAID_70),
            self::STAFF_DAY_OFF_SICK => trans('staffs.leave.' . self::STAFF_DAY_OFF_SICK),
            self::STAFF_DAY_OFF_MATERNITY => trans('staffs.leave.' . self::STAFF_DAY_OFF_MATERNITY),
            ];
    }
    public static function getStatusOffForOption()
    {
        return
            [
                self::STATUS_ON => trans('staffs.statu.' . self::STATUS_ON),
                self::STATUS_OFF => trans('staffs.statu.' . self::STATUS_OFF),
            ];
    }

    public static function checkApproved($userId)
    {
        $contract = Contract::where('user_id', $userId)->where('type_status', 1)->first();

        if ($contract->qualification_id == 23 && Auth::user()->id == $userId) {
            return true;
        }

        if ($contract->position_id == 2 && Auth::user()->id == $userId) {
            return true;
        }

        return false;
    }
}