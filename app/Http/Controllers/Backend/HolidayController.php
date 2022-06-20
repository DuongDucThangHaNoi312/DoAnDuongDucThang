<?php

namespace App\Http\Controllers\Backend;

use App\Holiday;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;


class HolidayController extends Controller
{
    public function INT($d)
    {
        return floor($d);
    }

    public function jdFromDate($dd, $mm, $yy)
    {
        $a = $this::INT((14 - $mm) / 12);
        $y = $yy + 4800 - $a;
        $m = $mm + 12 * $a - 3;
        $jd = $dd + $this::INT((153 * $m + 2) / 5) + 365 * $y + $this::INT($y / 4) - $this::INT($y /
                100) + $this::INT($y / 400) - 32045;
        if ($jd < 2299161) {
            $jd = $dd + $this::INT((153 * $m + 2) / 5) + 365 * $y + $this::INT($y / 4) -
                32083;
        }
        return $jd;
    }

    public function jdToDate($jd)
    {
        if ($jd > 2299160) { // After 5/10/1582, Gregorian calendar
            $a = $jd + 32044;
            $b = $this::INT((4 * $a + 3) / 146097);
            $c = $a - $this::INT(($b * 146097) / 4);
        } else {
            $b = 0;
            $c = $jd + 32082;
        }
        $d = $this::INT((4 * $c + 3) / 1461);
        $e = $c - $this::INT((1461 * $d) / 4);
        $m = $this::INT((5 * $e + 2) / 153);
        $day = $e - $this::INT((153 * $m + 2) / 5) + 1;
        $month = $m + 3 - 12 * $this::INT($m / 10);
        $year = $b * 100 + $d - 4800 + $this::INT($m / 10);
        //echo "day = $day, month = $month, year = $year\n";
        return array(
            $day,
            $month,
            $year
        );
    }

    public function getNewMoonDay($k, $timeZone)
    {
        $T = $k / 1236.85; // Time in Julian centuries from 1900 January 0.5
        $T2 = $T * $T;
        $T3 = $T2 * $T;
        $dr = M_PI / 180;
        $Jd1 = 2415020.75933 + 29.53058868 * $k + 0.0001178 * $T2 - 0.000000155 * $T3;
        $Jd1 = $Jd1 + 0.00033 * sin((166.56 + 132.87 * $T - 0.009173 * $T2) * $dr); // Mean new moon
        $M = 359.2242 + 29.10535608 * $k - 0.0000333 * $T2 - 0.00000347 * $T3; // Sun's mean anomaly
        $Mpr = 306.0253 + 385.81691806 * $k + 0.0107306 * $T2 + 0.00001236 * $T3; // Moon's mean anomaly
        $F = 21.2964 + 390.67050646 * $k - 0.0016528 * $T2 - 0.00000239 * $T3; // Moon's argument of latitude
        $C1 = (0.1734 - 0.000393 * $T) * sin($M * $dr) + 0.0021 * sin(2 * $dr * $M);
        $C1 = $C1 - 0.4068 * sin($Mpr * $dr) + 0.0161 * sin($dr * 2 * $Mpr);
        $C1 = $C1 - 0.0004 * sin($dr * 3 * $Mpr);
        $C1 = $C1 + 0.0104 * sin($dr * 2 * $F) - 0.0051 * sin($dr * ($M + $Mpr));
        $C1 = $C1 - 0.0074 * sin($dr * ($M - $Mpr)) + 0.0004 * sin($dr * (2 * $F + $M));
        $C1 = $C1 - 0.0004 * sin($dr * (2 * $F - $M)) - 0.0006 * sin($dr * (2 * $F + $Mpr));
        $C1 = $C1 + 0.0010 * sin($dr * (2 * $F - $Mpr)) + 0.0005 * sin($dr * (2 * $Mpr + $M));
        if ($T < -11) {
            $deltat = 0.001 + 0.000839 * $T + 0.0002261 * $T2 - 0.00000845 * $T3 - 0.000000081 * $T * $T3;
        } else {
            $deltat = -0.000278 + 0.000265 * $T + 0.000262 * $T2;
        }

        $JdNew = $Jd1 + $C1 - $deltat;
        return $this::INT($JdNew + 0.5 + $timeZone / 24);
    }

    public function getSunLongitude($jdn, $timeZone)
    {
        $T = ($jdn - 2451545.5 - $timeZone / 24) / 36525;
        $T2 = $T * $T;
        $dr = M_PI / 180;
        $M = 357.52910 + 35999.05030 * $T - 0.0001559 * $T2 - 0.00000048 * $T * $T2;
        $L0 = 280.46645 + 36000.76983 * $T + 0.0003032 * $T2;
        $DL = (1.914600 - 0.004817 * $T - 0.000014 * $T2) * sin($dr * $M);
        $DL = $DL + (0.019993 - 0.000101 * $T) * sin($dr * 2 * $M) + 0.000290 * sin($dr * 3 * $M);
        $L = $L0 + $DL;
        $omega = 125.04 - 1934.136 * $T;
        $L = $L - 0.00569 - 0.00478 * sin($omega * $dr);
        $L = $L * $dr;
        $L = $L - M_PI * 2 * ($this::INT($L / (M_PI * 2))); // Normalize to (0, 2*PI)
        return $this::INT($L / M_PI * 6);
    }

    public function getLunarMonth11($yy, $timeZone)
    {
        $off = $this->jdFromDate(31, 12, $yy) - 2415021;
        $k = $this::INT($off / 29.530588853);
        $nm = $this::getNewMoonDay($k, $timeZone);
        $sunLong = $this::getSunLongitude($nm, $timeZone);
        if ($sunLong >= 9) {
            $nm = $this::getNewMoonDay($k - 1, $timeZone);
        }
        return $nm;
    }

    public function getLeapMonthOffset($a11, $timeZone)
    {
        $k = $this::INT(($a11 - 2415021.076998695) / 29.530588853 + 0.5);
        $last = 0;
        $i = 1;
        $arc = $this::getSunLongitude($this::getNewMoonDay($k + $i, $timeZone), $timeZone);
        do {
            $last = $arc;
            $i = $i + 1;
            $arc = $this::getSunLongitude($this::getNewMoonDay($k + $i, $timeZone), $timeZone);
        } while ($arc != $last && $i < 14);
        return $i - 1;
    }


    public function convertSolar2Lunar($dd, $mm, $yy, $timeZone)
    {
        $dayNumber = $this::jdFromDate($dd, $mm, $yy);
        $k = $this::INT(($dayNumber - 2415021.076998695) / 29.530588853);
        $monthStart = $this::getNewMoonDay($k + 1, $timeZone);
        if ($monthStart > $dayNumber) {
            $monthStart = $this::getNewMoonDay($k, $timeZone);
        }
        $a11 = $this::getLunarMonth11($yy, $timeZone);
        $b11 = $a11;
        if ($a11 >= $monthStart) {
            $lunarYear = $yy;
            $a11 = $this::getLunarMonth11($yy - 1, $timeZone);
        } else {
            $lunarYear = $yy + 1;
            $b11 = $this::getLunarMonth11($yy + 1, $timeZone);
        }
        $lunarDay = $dayNumber - $monthStart + 1;
        $diff = $this::INT(($monthStart - $a11) / 29);
        $lunarLeap = 0;
        $lunarMonth = $diff + 11;
        if ($b11 - $a11 > 365) {
            $leapMonthDiff = $this::getLeapMonthOffset($a11, $timeZone);
            if ($diff >= $leapMonthDiff) {
                $lunarMonth = $diff + 10;
                if ($diff == $leapMonthDiff) {
                    $lunarLeap = 1;
                }
            }
        }
        if ($lunarMonth > 12) {
            $lunarMonth = $lunarMonth - 12;
        }
        if ($lunarMonth >= 11 && $diff < 4) {
            $lunarYear -= 1;
        }
        return array(
            $lunarDay,
            $lunarMonth,
            $lunarYear,
            $lunarLeap);
    }


    public function convertLunar2Solar($lunarDay, $lunarMonth, $lunarYear, $lunarLeap,
                                       $timeZone)
    {
        if ($lunarMonth < 11) {
            $a11 = $this::getLunarMonth11($lunarYear - 1, $timeZone);
            $b11 = $this::getLunarMonth11($lunarYear, $timeZone);
        } else {
            $a11 = $this::getLunarMonth11($lunarYear, $timeZone);
            $b11 = $this::getLunarMonth11($lunarYear + 1, $timeZone);
        }
        $k = $this::INT(0.5 + ($a11 - 2415021.076998695) / 29.530588853);
        $off = $lunarMonth - 11;
        if ($off < 0) {
            $off += 12;
        }
        if ($b11 - $a11 > 365) {
            $leapOff = $this::getLeapMonthOffset($a11, $timeZone);
            $leapMonth = $leapOff - 2;
            if ($leapMonth < 0) {
                $leapMonth += 12;
            }
            if ($lunarLeap != 0 && $lunarMonth != $leapMonth) {
                return array(
                    0,
                    0,
                    0);
            } else
                if ($lunarLeap != 0 || $off >= $leapOff) {
                    $off += 1;
                }
        }
        $monthStart = $this::getNewMoonDay($k + $off, $timeZone);
        return $this::jdToDate($monthStart + $lunarDay - 1);
    }

    public function getAnniversaryDay(Request $request)
    {
        $lunarYear = $request->get('year');
        $lunarMonth = 03;
        $lunarDay = 10;
        $leap = 0;
        $timezone = '7.0';
        $day = new HolidayController();
        $day = $day->convertLunar2Solar($lunarDay, $lunarMonth, $lunarYear, $leap, $timezone);
        $day[0] = str_pad($day[0], 2, '0', STR_PAD_LEFT);
        $day[1] = str_pad($day[1], 2, '0', STR_PAD_LEFT);
        return implode("/", $day);
    }

    public function getHoliday(Request $request)
    {
        $holidays = Holiday::all();
        return view('backend.holiday_form', compact('holidays'));
    }

    public function loadHoliday(Request $request)
    {
        $holidays = Holiday::all();
        return $holidays;
    }

    public function store(Request $request)
    {

        $data = $request->all();
        foreach ($data['new_year'] as $newYear) {

            $request->merge(['holidays' => trans('holidays.new_year')]);
            Holiday::create([
                'start_date' => Carbon::createFromFormat('d/m/Y', $newYear[0])->format('Y-m-d'),
                'end_date' => Carbon::createFromFormat('d/m/Y', $newYear[1])->format('Y-m-d'),
                'holidays' => $request->get('holidays'),
            ]);
        }
        foreach ($data['lunar_new_year'] as $lunarNewYear) {
            $request->merge(['holidays' => trans('holidays.lunar_new_year')]);
            Holiday::create([
                'start_date' => Carbon::createFromFormat('d/m/Y', $lunarNewYear[0])->format('Y-m-d'),
                'end_date' => Carbon::createFromFormat('d/m/Y', $lunarNewYear[1])->format('Y-m-d'),
                'holidays' => $request->get('holidays'),
            ]);
        }
        foreach ($data['anniversary_day'] as $anniversaryDay) {
            $request->merge(['holidays' => trans('holidays.anniversary_day')]);
            Holiday::create([
                'start_date' => Carbon::createFromFormat('d/m/Y', $anniversaryDay[0])->format('Y-m-d'),
                'end_date' => Carbon::createFromFormat('d/m/Y', $anniversaryDay[1])->format('Y-m-d'),
                'holidays' => $request->get('holidays'),
            ]);
        }
        foreach ($data['unified_day'] as $unifiedDay) {
            $request->merge(['holidays' => trans('holidays.unified_day')]);
            Holiday::create([
                'start_date' => Carbon::createFromFormat('d/m/Y', $unifiedDay[0])->format('Y-m-d'),
                'end_date' => Carbon::createFromFormat('d/m/Y', $unifiedDay[1])->format('Y-m-d'),
                'holidays' => $request->get('holidays'),
            ]);
        }
        foreach ($data['labor_day'] as $laborDay) {
            $request->merge(['holidays' => trans('holidays.labor_day')]);
            Holiday::create([
                'start_date' => Carbon::createFromFormat('d/m/Y', $laborDay[0])->format('Y-m-d'),
                'end_date' => Carbon::createFromFormat('d/m/Y', $laborDay[1])->format('Y-m-d'),
                'holidays' => $request->get('holidays'),
            ]);
        }
        foreach ($data['national_day'] as $nationalDay) {
            $request->merge(['holidays' => trans('holidays.national_day')]);
            Holiday::create([
                'start_date' => Carbon::createFromFormat('d/m/Y', $nationalDay[0])->format('Y-m-d'),
                'end_date' => Carbon::createFromFormat('d/m/Y', $nationalDay[1])->format('Y-m-d'),
                'holidays' => $request->get('holidays'),
            ]);
        }
        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');
        return back();
    }

    public function update(Request $request)
    {

        $data = $request->all();
        $holidays = Holiday::where('holidays', trans('holidays.new_year'))
            ->whereNotExists(function ($q) use ($data) {
                $q->where('start_date', Carbon::createFromFormat('d/m/Y', $data['new_year'][0][0])->format('Y-m-d'));
            });
        $holidays->update([
            'start_date' => Carbon::createFromFormat('d/m/Y', $data['new_year'][1][0])->format('Y-m-d'),
            'end_date' => Carbon::createFromFormat('d/m/Y', $data['new_year'][1][1])->format('Y-m-d'),
        ]);

        $holidays = Holiday::where('holidays', trans('holidays.lunar_new_year'));
        $holidays->update([
            'start_date' => Carbon::createFromFormat('d/m/Y', $data['lunar_new_year'][0][0])->format('Y-m-d'),
            'end_date' => Carbon::createFromFormat('d/m/Y', $data['lunar_new_year'][0][1])->format('Y-m-d'),
        ]);

        $holidays = Holiday::where('holidays', trans('holidays.anniversary_day'))
            ->whereNotExists(function ($q) use ($data) {
                $q->where('start_date', Carbon::createFromFormat('d/m/Y', $data['anniversary_day'][0][0])->format('Y-m-d'));
            });

        $holidays->update([
            'start_date' => Carbon::createFromFormat('d/m/Y', $data['anniversary_day'][1][0])->format('Y-m-d'),
            'end_date' => Carbon::createFromFormat('d/m/Y', $data['anniversary_day'][1][1])->format('Y-m-d'),
        ]);

        $holidays = Holiday::where('holidays', trans('holidays.labor_day'))
            ->whereNotExists(function ($q) use ($data) {
                $q->where('start_date', Carbon::createFromFormat('d/m/Y', $data['labor_day'][0][0])->format('Y-m-d'));
            });

        $holidays->update([
            'start_date' => Carbon::createFromFormat('d/m/Y', $data['labor_day'][1][0])->format('Y-m-d'),
            'end_date' => Carbon::createFromFormat('d/m/Y', $data['labor_day'][1][1])->format('Y-m-d'),
        ]);

        $holidays = Holiday::where('holidays', trans('holidays.national_day'))
            ->whereNotExists(function ($q) use ($data) {
                $q->where('start_date', Carbon::createFromFormat('d/m/Y', $data['national_day'][0][0])->format('Y-m-d'));
            });

        $holidays->update([
            'start_date' => Carbon::createFromFormat('d/m/Y', $data['national_day'][1][0])->format('Y-m-d'),
            'end_date' => Carbon::createFromFormat('d/m/Y', $data['national_day'][1][1])->format('Y-m-d'),
        ]);


        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');
        return back();
    }
}
