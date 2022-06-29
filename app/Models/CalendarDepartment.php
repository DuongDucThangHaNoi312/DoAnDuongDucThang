<?php

namespace App\Models;

use App\Define\CalendarDepartments;
use App\Defines\Schedule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CalendarDepartment extends Model
{
    protected $table = 'department_day_offs';
    protected $fillable = ['id', 'categories', 'type', 'start_date', 'start_timestamps', 'from_type', 'end_date', 'end_timestamps', 'to_type', 'reason','status', 'department_id', 'created_by', 'deleted_by', 'deleted_at'];
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    public static function getDayOff($departmentId, $isFuture = null)
    {
		$now = \Carbon\Carbon::now()->format('Y-m-d');
        $day = $isFuture ? CalendarDepartment::where('department_id', $departmentId)->where('start_date', '>', $now)->get() : CalendarDepartment::where('department_id', $departmentId)->get();
        $dayOff = [];
        foreach ($day as $value) {
            if ($value->type == 'one' || $value->type == 'multiple'){
                $dayOffMultiple = [
                    'type'=> $value->categories == 'holiday' ? CalendarDepartments::HOLIDAY : CalendarDepartments::NORMAL,
                    'start' => $value->start_date,
                    'from_type' => $value->from_type == 'MORNING' ? Schedule::TIME_OFF_MORNING : Schedule::TIME_OFF_AFTERNOON,
                    'end' => $value->end_date.'T23:59:59',
                    'to_type' => $value->to_type == 'MORNING' ? Schedule::TIME_OFF_MORNING : Schedule::TIME_OFF_AFTERNOON,
                    'title' =>"Ngày nghỉ phòng ban: ". $value->reason,
                    'color'=> $value->categories == 'holiday' ? 'red': '#B4B4B4'
                ];
                array_push($dayOff, $dayOffMultiple);

            }
            if ($value->type == 'everyweek' ){
                $start_date = Carbon::create($value->start_date);
                $end_date = Carbon::create($value->end_date);
                for ($day = $start_date; $day <= $end_date; $day->addDays(7)) {
                    $dayOffMultiple = [
                        'type'=> CalendarDepartments::NORMAL,
                        'start' => $day->toDateString(),
                        'from_type' => $value->from_type == 'MORNING' ? Schedule::TIME_OFF_MORNING : Schedule::TIME_OFF_AFTERNOON,
                        'end' => $day->toDateString().'T23:59:59',
                        'to_type' => $value->to_type == 'MORNING' ? Schedule::TIME_OFF_MORNING : Schedule::TIME_OFF_AFTERNOON,
                        'title' =>"Ngày nghỉ phòng ban: ". $value->reason,
                        'color'=>'#B4B4B4'

                    ];
                    array_push($dayOff, $dayOffMultiple);
                }

            }
        }
        return $dayOff;
    }

    public static function getDayOffsByDate($departmentId, $fromDate = null, $dayOffCreat = null)
    {
        //$dayOffCreat dùng để lấy những ngày nghỉ gần với ngày của đơn nghỉ muốn tạo (cho trường hợp check đơn nghỉ có trùng lịch nghỉ phòng ban k)

        $_MORNING = Schedule::TIME_OFF_MORNING;
        $_AFTERNOON = Schedule::TIME_OFF_AFTERNOON;
        $day = $fromDate ? CalendarDepartment::where('department_id', $departmentId)->where('start_date', '>=', $fromDate)->get() : CalendarDepartment::where('department_id', $departmentId)->get();
        $dayOff = [];
        foreach ($day as $value) {
            $fromType = $value->from_type == 'MORNING' ? $_MORNING : $_AFTERNOON;
            $toType = $value->to_type == 'MORNING' ? $_MORNING : $_AFTERNOON;
            if ($dayOffCreat && ($value->start_date > $dayOffCreat['end'] || $value->end_date < $dayOffCreat['start'])) continue;
            if ($value->type == 'one' || $value->type == 'multiple'){
                $dayOffMultiple = [
                    'start' => $value->start_date,
                    'from_type' => $fromType,
                    'end' => $value->end_date,
                    'to_type' => $toType,
                ];
                array_push($dayOff, $dayOffMultiple);
            } elseif ($value->type == 'everyweek' ){
                $start_date = Carbon::create($value->start_date);
                $end_date = Carbon::create($value->end_date);
                for ($day = $start_date; $day <= $end_date; $day->addDays(7)) {
                    $daySave = $day->toDateString();
                    if ($dayOffCreat && ($daySave > $dayOffCreat['end'] || $daySave < $dayOffCreat['start'])) continue;
                    $dayOffMultiple = [
                        'start' => $daySave,
                        'from_type' => $fromType,
                        'end' => $daySave,
                        'to_type' => $toType,
                    ];
                    array_push($dayOff, $dayOffMultiple);
                }
            }
        }
        return $dayOff;
    }

    public static function countHolidays($departmentId, $beforeDate, $afterDate)
    {
        $m = 'MORNING';
        $a = 'AFTERNOON';
        $countH = 0;
        $holidays = CalendarDepartment::where('department_id', $departmentId)
            ->where('categories', 'holiday')
            ->where('start_date', '<=', $afterDate)
            ->where('end_date', '>=', $beforeDate)
            ->get();
        if (!count($holidays)) return $countH;
        foreach ($holidays as $holiday) {
            if ($holiday->start_date >= $beforeDate && $holiday->end_date <= $afterDate) {
               $countIn = self::countDayOffInRange($holiday->start_date, $holiday->end_date, $holiday->from_type, $holiday->to_type, $m, $a);
               $countH += $countIn;
            } else {
                if ($holiday->start_date < $beforeDate) {
                    $countIn = self::countDayOffInRange($beforeDate, $holiday->end_date, $m, $holiday->to_type, $m, $a);
                    $countH += $countIn;
                }
                if ($holiday->end_date > $afterDate) {
                    $countIn = self::countDayOffInRange($holiday->start_date, $afterDate, $holiday->from_type, $a, $m, $a);
                    $countH += $countIn;
                }
            }
        }
        return $countH;
    }

    public static function countDayOffInRange($start, $end, $fromType, $toType, $morning, $afternoon) //dem so ngay nghi ma start va end deu nam trong khoang
    {
        $temp = Carbon::parse($end)->diffInDays($start);
        if ($fromType == $morning && $toType == $afternoon) $temp++;
        if ($fromType == $morning && $toType == $morning || $fromType == $afternoon && $toType == $afternoon) $temp += 0.5;
        return $temp;
    }

}
