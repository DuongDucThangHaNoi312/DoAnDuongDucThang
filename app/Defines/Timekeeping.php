<?php

namespace App\Define;

use App\Models\WorkSchedule;
use Carbon\Carbon;

class Timekeeping
{
    const DAYOFF_MORNING = '0.5M';
    const DAYOFF_AFTERNOON = '0.5A';

    public static function getMonth()
    {
        $month = [];
        for ($i = 1; $i <= 12 ; $i++) { 
            $arr[] = $i;
        }
        foreach ($arr as $item) {
            $month[$item] = trans('timekeeping.month') . ' ' . $item;
        }

        return $month;
    }

    public static function getYear()
    {
        $year = [];
        for ($i = 2020; $i <= 2030 ; $i++) { 
            $arr[] = $i;
        }
        foreach ($arr as $item) {
            $year[$item] = trans('timekeeping.year') . ' ' . $item;
        }

        return $year;
    }

    public static function checkSaturday($date = '', $department, $userId)
    {
        $check_th = Carbon::parse(date('Y-m-d', $date))->format('l');
        if (strtoupper($check_th) == 'SATURDAY') {
            $workSchedule = WorkSchedule::where('department_id', $department->id)->first();
            $holiday = \App\StaffDayOff::checkDateHasEvent($userId, date('Y-m-d', $date));

            if ($holiday == 'T' && $workSchedule->type == 1 || $holiday == 'T/2' && $workSchedule->type == 1) {
                return 'T/2';
            }
            if ($holiday == 'L' && $workSchedule->type == 1 || $holiday == 'L/2' && $workSchedule->type == 1) {
                return 'L/2';
            }
            if ($holiday == 'BB' && $workSchedule->type == 1) {
                return 'BB';
            }
        } else {
            return null;
        }
    }

    public static function userApprovedTimekeeping()
    {
        return ['admin', 'JNR1'];
    }

    public static function codeDayOff()
    {
        return [
            'nghi_cong_tac'  => 'T',
            'nghi_phep' => 'L',
            'nghi_70_luong' => 'C',
            'nghi_om' => 'S',
            'nghi_cuoi' => 'W',
            'nghi_hieu' => 'D',
            'nghi_khong_luong' => 'O',
            'nghi_le' => 'H',
            'lam_tai_nha' => 'F',
        ];
    }

    public static function dayOffFull() //NV nghỉ full ko tính công
    {
        return ['H', 'L', 'S', 'W', 'D', 'O', 'C', 'T', 'BB', 'F', 'H/2 H/2', 'L/2 L/2', 'S/2 S/2', 'W/2 W/2', 'D/2 D/2', 'O/2 O/2', 'C/2 C/2', 'T/2 T/2', 'BB/2 BB/2', 'F/2 F/2', 'L/2 T/2', 'T/2 L/2'];
    }

    public static function fullPayLeave() //NV nghỉ full có 1 ngày ông
    {
        return ['H', 'L', 'W', 'D', 'T', 'F', 'H/2 H/2', 'L/2 L/2', 'W/2 W/2', 'D/2 D/2', 'T/2 T/2', 'L/2 T/2', 'T/2 L/2', 'W/2 T/2', 'T2 W2', 'D/2 T/2', 'T2 D2', 'F/2 F/2', 'F/2 L/2', 'L2 F2', 'F/2 T/2', 'T2 F2'];
    }

    public static function halfSalaryLeave() //NV nghỉ nửa ngày có 1/2 công
    {
        return ['H/2', 'L/2', 'W/2', 'D/2', 'T/2', 'F/2'];
    }

    public static function getYear1()
    {
        $year = [];
        for ($i = 2020; $i <= 2030 ; $i++) { 
            $arr[$i] = $i;
        }
        foreach ($arr as $item) {
            $year[$item] = $item;
        }

        return $year;
    }
}