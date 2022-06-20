<?php

namespace App\Defines;

class KPI
{
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
    const KPI_MIN = 0;
    const KPI_MAX = 200;

    public static function getKPI()
    {
        return [
            self::KPI_MIN=>self::KPI_MIN,
            self::KPI_MAX=>self::KPI_MAX,

        ];

    }
}