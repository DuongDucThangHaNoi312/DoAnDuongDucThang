<?php

namespace App\Define;

class Constant
{
    const PAGE_NUM_FRONTEND = 12;
    const PAGE_NUM_MOBILE   = 6;

	const PAGE_NUM           	= 10;
	const PAGE_NUM_5          	= 3;
    const PAGE_NUM_20           = 20;
    const PAGE_NUM_50           = 50;
    const PAGE_NUM_500          = 500;

    const POSITION_TPHCNS   = 10;
    const POSITION_TP       = 2;
    const POSITION_TGD      = 1;

    public static function roundNumber($number, $precision = 0)
    {
        return round($number, $precision);
    }

    public static function getDateFromDayMonthYear($year, $month, $day, $resultFormat = 'Y-m-d')
    {
        return date($resultFormat, strtotime($year.'-'.$month.'-'.$day));
    }
}
