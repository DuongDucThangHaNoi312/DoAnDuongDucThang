<?php

namespace App\Models;

use App\Defines\Schedule;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Shift extends Model
{
    protected $table = 'department_working_day';
    protected $fillable = ['id', 'start_date', 'end_date', 'first_shift', 'second_shift', 'third_shift', 'first_shift_and_ot', 'second_shift_and_ot', 'department_id'];
    protected $dates = ['start_date', 'end_date'];

    public static function rules()
    {
        return [
            'start_date' => 'required',
            'end_date' => 'required|after:start_date;',
        ];
    }

    public static function getShift($day = null, $userId = null)
    {
        $day = $day ?? now()->format('Y-m-d');
        $day = Carbon::create($day)->toDateString();

        $userId = $userId ?? Auth::id();
        $shiftWork = DB::table('shifts')->where('user_id', $userId)->get();
        $shift_work = [];
        foreach ($shiftWork as $shifts) {
//            $isHalfShift = self::isHalfShift($shifts->shifts);
            if ($day >= $shifts->start && $day <= $shifts->end) {
                array_push($shift_work, [
                    'start_date' => $day,
                    'end_date' => $shifts->end,
                    'shift' => $shifts->shifts,
                ]);
            } else if ($day <= $shifts->start) {
                array_push($shift_work, [
                    'start_date' => $shifts->start,
                    'end_date' => $shifts->end,
                    'shift' => $shifts->shifts,
                ]);
            }
        }
        return $shift_work;
    }

    public static function getShiftEveryDay($year = null, $month = null, $userId = null)
    {
        $year == null ? $year = Carbon::now()->year : $year = $year;
        $month == null ? $month = Carbon::now()->month : $month = $month;
        $userId == null ? $userId = Auth::id() : $userId = $userId;

        $startMonth = Carbon::create($year, $month - 1, 26);

        $endMonth = Carbon::create($year, $month, 25);

        $shiftWork = DB::table('shifts')->where('user_id', $userId)->get();
        $shift_work = [];
        foreach ($shiftWork as $shifts) {
            if ($shifts->start == $shifts->end) {
                array_push($shift_work, [
                    'date' => $shifts->start,
                    'shift' => $shifts->shifts
                ]);
            } else {
                $start = Carbon::create($shifts->start);
                $end = Carbon::create($shifts->end);

                for ($dt = $start; $dt <= $end; $dt->addDay()) {
                    if ($dt->toDateString() >= $startMonth->toDateString() && $dt->toDateString() <= $endMonth->toDateString()) {
                        array_push($shift_work, [
                            'date' => $dt->toDateString(),
                            'shift' => $shifts->shifts
                        ]);
                    }
                }
            }

        }
        return $shift_work;
    }

    public static function getShiftEveryDayByUsers($year, $month, $userIds = [])
    {
        $startMonth = Carbon::create($year, $month - 1, Schedule::DATE_START_SALARY);
        $endMonth = Carbon::create($year, $month, Schedule::DATE_END_SALARY);
        $shiftWorks = DB::table('shifts')
            ->whereIn('user_id', $userIds)
            ->get()
            ->groupBy('user_id');
        $result = [];
        foreach ($shiftWorks as $userId => $shiftWork) {
            $result[$userId] = [];
            foreach ($shiftWork as $shifts) {
                if ($shifts->start == $shifts->end) {
                    array_push($result[$userId], [
                        'date' => $shifts->start,
                        'shift' => $shifts->shifts
                    ]);
                } else {
                    $start = Carbon::create($shifts->start);
                    $end = Carbon::create($shifts->end);
                    for ($dt = $start; $dt <= $end; $dt->addDay()) {
                        if ($dt->toDateString() >= $startMonth->toDateString() && $dt->toDateString() <= $endMonth->toDateString()) {
                            array_push($result[$userId], [
                                'date' => $dt->toDateString(),
                                'shift' => $shifts->shifts
                            ]);
                        }
                    }
                }

            }
        }
        return $result;
    }

    public static function getShiftInSpace($start, $end, $userId)
    {

        $start = Carbon::create($start);

        $end = Carbon::create($end);
        $shift_work = [];
        $shiftWorks = DB::table('shifts')->where('user_id', $userId)->get();
        $shiftIN = DB::table('shifts')->where('user_id', $userId)
            ->whereBetween('start', [$start, $end])->whereBetween('end', [$start, $end])->get();

        foreach ($shiftWorks as $shifts) {
            $start_date = Carbon::create($shifts->start);
            $end_date = Carbon::create($shifts->end);
            $checkStart = $start->between($start_date, $end_date);
            $checkEnd = $end->between($start_date, $end_date);
            if ($checkStart == true || $checkEnd == true) {
                array_push($shift_work, [
                    'start_date' => $shifts->start,
                    'end_date' => $shifts->end,
                    'shift' => $shifts->shifts,
                ]);
            }

        }
        if(count($shiftIN)){
            foreach ($shiftIN as $shiftin){
                array_push($shift_work,[
                    'start_date' => $shiftin->start,
                    'end_date' => $shiftin->end,
                    'shift' => $shiftin->shifts,
                ]);
            }
        }
        return $shift_work;
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public static function getShiftUser($day, $userId)
    {
        $items = Shift::getShift($day, $userId);
        foreach ($items as $key => $item) {
            if ($item['start_date'] == $day) {
                $category = CategoryShift::find($item['shift']);
                // switch ($category->type) {
                //     case 1:
                //         $type = 'N';
                //         break;
                //     case 2:
                //         $type = 'HC';
                //         break;
                //     case 3:
                //         $type = 'Đ';
                //         break;
                // }
                return $category->shortened_name;
            }
        }

        return '';
    }

    public static function isHalfShift($shift = '')
    {
//        if (empty($shift)) return '';
        $user = User::find(Auth::id());
        $shift_time = ShiftTime::where('department_id', $user->department_id)->where('category_shift_id', $shift)
            ->first();
        if (empty($shift_time)) return '';
        if (!is_null($shift_time->off_mid_shift) && !is_null($shift_time->start_mid_shift)) return 1;
        return '';
    }
}
