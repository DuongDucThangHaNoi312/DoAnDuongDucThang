<?php

namespace App;

use App\Define\Shift;
use App\Models\ListLog;
use Carbon\Carbon;
use App\Defines\Schedule;
use App\Models\Department;
use App\Models\CalendarDepartment;
use App\Models\Contract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class StaffDayOff extends Model
{
    protected $table = 'staff_day_offs';
    protected $guarded = [];
    use SoftDeletes;

    public static function rules($id = 0)
    {
        return [
            'title' => 'required',
            'start' => 'required',
            'end' => 'required|gte:start',
            'from_type' => 'required',
            'to_type' => 'required',
            'total' => 'required',
            'code' => 'required',
            'user_id' => 'required',
            'reason'  => 'required'
        ];
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    public static function getRestLeave($staffId)
    {
        return User::find($staffId)->rest;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function listLogs()
    {
        return $this->morphMany(ListLog::class, 'object');
    }

    public static function countAcceptDayOffInMonth($staffId, $month, $year)
    {
        $dayOffs = StaffDayOff::where('user_id', $staffId)->whereMonth('start', $month)->whereYear('start', $year)->where('status', 1)->get();
        return count($dayOffs);
    }

    public static function countPendingDayOffInMonth($staffId, $month, $year)
    {
        $dayOffs = StaffDayOff::where('user_id', $staffId)->whereMonth('start', $month)
            ->whereYear('start', $year)
            ->where('status', 0)
            ->whereNotIn('code', Schedule::LEAVE_NOT_NEED_APPROVAL)
            ->get();
        return count($dayOffs);
    }

    public static function getStaffDayOff($userId, $isFormat = 1, $isFuture = null)
    {
        $now = Carbon::now()->format('Y-m-d');
        $dayOffs = $isFuture ? StaffDayOff::where('user_id', $userId)->where('start', '>', $now)->get() : StaffDayOff::where('user_id', $userId)->get();
        if ($dayOffs && $isFormat) {
            foreach ($dayOffs as &$eventStaff) {
                $eventStaff->end = $eventStaff->end . "T23:59:00";
            }
        }
        return $dayOffs;
    }

    public static function countDayOffs($userId, $month = null, $year = null, $type = '', $department_id = null)
    {
        $contract = Contract::where('user_id', $userId)->where('department_id', $department_id)->whereIn('type_status', [1, 2])->first();
        $beforeDate = Carbon::createFromDate($year, $month - 1, 26)->format('Y-m-d');
        $afterDate = Carbon::createFromDate($year, $month, 25)->format('Y-m-d');
        $m = Schedule::TIME_OFF_MORNING;
        $a = Schedule::TIME_OFF_AFTERNOON;
        $boNhiem = Contract::where('type_status', 5)->where('user_id', $contract->user_id)
                ->where('set_notvalid_on', '>', $beforeDate)
                ->orderBy('id', 'DESC')
                ->first();

        if (!is_null($contract) && is_null($boNhiem)) {
            if ($contract->type_status == 2) {
                $afterDate = date('Y-m-d', strtotime($contract->set_notvalid_on));
            }

            if ($contract->type_status == 1) {
                if (strtotime($contract->valid_from) > strtotime($beforeDate)) {
                    $beforeDate = date('Y-m-d', strtotime($contract->valid_from));
                }
            }
        }


        if ($type == Schedule::DAY_OFF_NO_SALARY) {
            $countO = 0;
            $dayOffs = StaffDayOff::where('user_id', $userId)
                ->where('start', '<=', $afterDate)
                ->where('end', '>=', $beforeDate)
                ->where('code', 'O')
                ->where('status', 1)
                ->get();

            if (!count($dayOffs)) return 0;

            foreach ($dayOffs as $dayOff) {
                $count = 0;
                if ($dayOff->start >= $beforeDate && $dayOff->end <= $afterDate) {
                    $count += $dayOff->total;
                } else {
                    if ($dayOff->start < $beforeDate) {
                        $countIn = CalendarDepartment::countDayOffInRange($beforeDate, $dayOff->end, $m, $dayOff->to_type, $m, $a);
                        $count += $countIn;
                    }
                    if ($dayOff->end > $afterDate) {
                        $countIn = CalendarDepartment::countDayOffInRange($dayOff->start, $afterDate, $dayOff->from_type, $a, $m, $a);
                        $count += $countIn;
                    }
                }
          
                if ($dayOff->code == Schedule::DAY_OFF_NO_SALARY) $countO += $count;
            }

            return $countO;
        }

        $temp = StaffDayOff::where('user_id', $userId)
            ->where('start', '<=', $afterDate)
            ->where('end', '>=', $beforeDate)
            ->where('status', 1)
            ->get();
        if (!count($temp)) return 0;
        $dayOffs = StaffDayOff::where('user_id', $userId)
            ->where('start', '<=', $afterDate)
            ->where('end', '>=', $beforeDate)
            ->whereIn('code', ['L', 'D', 'W', 'T', 'C', 'S'])
            ->where('status', 1)
            ->get();
        $countL = $countD = $countW = $countT = $countC = $countO = $countS = 0;

        foreach ($dayOffs as $dayOff) {
            $count = 0;
            if ($dayOff->start >= $beforeDate && $dayOff->end <= $afterDate) {
                $count += $dayOff->total;
            } else {
                if ($dayOff->start < $beforeDate) {
                    $countIn = CalendarDepartment::countDayOffInRange($beforeDate, $dayOff->end, $m, $dayOff->to_type, $m, $a);
                    $count += $countIn;
                }
                if ($dayOff->end > $afterDate) {
                    $countIn = CalendarDepartment::countDayOffInRange($dayOff->start, $afterDate, $dayOff->from_type, $a, $m, $a);
                    $count += $countIn;
                }
            }
            if ($dayOff->code == Schedule::DAY_OFF_12) $countL += $count;
            if ($dayOff->code == Schedule::DAY_OFF_WEDDING) $countW += $count;
            if ($dayOff->code == Schedule::DAY_OFF_FUNERAL) $countD += $count;
            if ($dayOff->code == Schedule::DAY_OFF_MISSION) $countT += $count;
            if ($dayOff->code == Schedule::DAY_OFF_70_SALARY) $countC += $count;
            if ($dayOff->code == Schedule::DAY_OFF_NO_SALARY) $countO += $count;
            if ($dayOff->code == Schedule::DAY_OFF_SICK) $countS += $count;
        }
        if ($type == 'T') return $countT;
        if ($type == 'L') return $countL;
        if ($type == 'C') return $countC;
        if ($type == 'S') return $countS;
        if ($type == 'W') return $countW;
        if ($type == 'D') return $countD;
        return $countL + $countW + $countD;
    }

    public static function countDayOffsInMonth($userId, $month = null, $year = null)
    {
        $beforeDate = Carbon::createFromDate($year, $month - 1, 26)->format('Y-m-d');
        $afterDate = Carbon::createFromDate($year, $month, 25)->format('Y-m-d');
        $dayOffs = StaffDayOff::where('user_id', $userId)->where('start', '>=', $beforeDate)->where('end', '<=', $afterDate)->get();
        return array_sum(array_column($dayOffs->toArray(), 'total'));
    }

    public static function checkDateHasEvent($userId, $date = null) //format date: y-m-d
    {
        $date = Carbon::createFromDate($date)->format('Y-m-d');
        $user = User::find($userId);
        $isHoliday = CalendarDepartment::where('department_id', $user->department_id)
            ->where('categories', 'holiday')
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->first();
        $m = 'MORNING';
        $a = 'AFTERNOON';
        $result = '';
        $resultH = '';
        $space = ' ';
        if (count($isHoliday)) {
            if ($isHoliday->start_date < $date && $isHoliday->end_date > $date) $resultH = 'H';
            elseif ($isHoliday->type == 'one') {
                if ($isHoliday->from_type == $m && $isHoliday->to_type == $a) $resultH = 'H';
                else $resultH = 'H/2';
            } elseif (($isHoliday->type == 'multiple')) {
                if ($isHoliday->start_date = $date) {
                    if ($isHoliday->from_type == $m) $resultH = 'H';
                    else $resultH = 'H/2';
                } else {
                    if ($isHoliday->to_type == $m) $resultH = 'H/2';
                    else $resultH = 'H';
                }
            }
        }
        // if ($result == 'H') return $result;
        $result = $result . $space;
        $dayOffs = StaffDayOff::where('user_id', $userId)->where('start', '<=', $date)->where('end', '>=', $date)->where('status', 1)->get();
        // if (!count($dayOffs)) return $result;
        foreach ($dayOffs as $dayOff) {
            $code = $dayOff->code . $space;
            $code2 = $dayOff->code . '/2' . $space;
            if ($dayOff->start < $date && $date < $dayOff->end) $result .= $dayOff->code . $space;
            elseif ($dayOff->total < 1) $result .= $code2;
            elseif ($date == $dayOff->start) $result .= $dayOff->from_type == Schedule::TIME_OFF_AFTERNOON ? $code2 : $code;
            elseif ($date == $dayOff->end) $result .= $dayOff->to_type == Schedule::TIME_OFF_MORNING ? $code2 : $code;
        }

        if ($resultH == 'H' && trim($result) == 'BB') {
            return $result;
        } else if ($resultH == 'H') {
            return $resultH;
        }
        if (!count($dayOffs)) return $result;

        return trim($result);
    }

    public static function countDayOffStaff($userId, $status, $date = null)
    {
        $date = $date ?? Carbon::now()->format('Y-m-d');
        $checkNull = StaffDayOff::where('user_id', $userId)->first();
        return $checkNull ? StaffDayOff::where('user_id', $userId)
            ->whereNotIn('code', Schedule::LEAVE_NOT_NEED_APPROVAL)
            ->where('start', '>', $date)
            ->where('status', $status)
            ->count() : 0;
    }

    public static function countDayOffDept($departmentId, $status, $date = null)
    {
        $date = $date ?? Carbon::now()->format('Y-m-d');
        $department = Department::find($departmentId);
        $dayOff = $department->dayOffs;
        return $dayOff ? $dayOff->whereNotIn('code', Schedule::LEAVE_NOT_NEED_APPROVAL)
            ->where('start', '>', $date)
            ->where('status', $status)
            ->count() : 0;
    }

    public static function countDayPendingCurrent($userId, $id = null)
    {
        $date = Carbon::now()->format('Y-m-d');
        $dayOffs = StaffDayOff::where('user_id', $userId)
            ->where('id', '<>', $id)
            ->whereIn('code', ['S', 'D', 'L'])
            ->where('start', '>', $date)
            ->where('status', 0)
            ->sum('total');
        return self::getRestLeave($userId) - $dayOffs;
    }

    public static function countTotalInMonthForTimeKeeping($userId, $month = null, $year = null, $department_id = null)
    {
        $contract = Contract::where('user_id', $userId)->where('department_id', $department_id)->whereIn('type_status', [1, 2])->first();

        $beforeDate = Carbon::createFromDate($year, $month - 1, 26)->format('Y-m-d');
        $afterDate = Carbon::createFromDate($year, $month, 25)->format('Y-m-d');
        $boNhiem = Contract::where('type_status', 5)->where('user_id', $contract->user_id)
                                                        ->where('set_notvalid_on', '>', $beforeDate)
                                                        ->orderBy('id', 'DESC')
                                                        ->first();
                                                        
        if (!is_null($contract) && is_null($boNhiem)) {
            if ($contract->type_status == 2) {
                $afterDate = date('Y-m-d', strtotime($contract->set_notvalid_on));
            }

            if ($contract->type_status == 1) {
                if (strtotime($contract->valid_from) > strtotime($beforeDate)) {
                    $beforeDate = date('Y-m-d', strtotime($contract->valid_from));
                }
            }
        }
        

        $departmentId = User::find($userId)->department_id;
        $holidays = CalendarDepartment::countHolidays($departmentId, $beforeDate, $afterDate);
        $temp = StaffDayOff::where('user_id', $userId)
            ->where('start', '<=', $afterDate)
            ->where('end', '>=', $beforeDate)
            ->where('status', 1)
            ->get();
        if (!count($temp)) return ['L' => 0, 'D' => 0, 'W' => 0, 'H' => $holidays];
        $dayOffs = StaffDayOff::where('user_id', $userId)
            ->where('start', '<=', $afterDate)
            ->where('end', '>=', $beforeDate)
            ->whereIn('code', ['L', 'D', 'W', 'T', 'S', 'C'])
            ->where('status', 1)
            ->get();
        $countL = $countD = $countW = $countT = $countS = $countC = 0;
        $m = Schedule::TIME_OFF_MORNING;
        $a = Schedule::TIME_OFF_AFTERNOON;
        foreach ($dayOffs as $dayOff) {
            $count = 0;
            if ($dayOff->start >= $beforeDate && $dayOff->end <= $afterDate) {
                $count += $dayOff->total;
            } else {
                if ($dayOff->start < $beforeDate) {
                    $countIn = CalendarDepartment::countDayOffInRange($beforeDate, $dayOff->end, $m, $dayOff->to_type, $m, $a);
                    $count += $countIn;
                }
                if ($dayOff->end > $afterDate) {
                    $countIn = CalendarDepartment::countDayOffInRange($dayOff->start, $afterDate, $dayOff->from_type, $a, $m, $a);
                    $count += $countIn;
                }
            }
            if ($dayOff->code == Schedule::DAY_OFF_12) $countL += $count;
            if ($dayOff->code == Schedule::DAY_OFF_WEDDING) $countW += $count;
            if ($dayOff->code == Schedule::DAY_OFF_FUNERAL) $countD += $count;
            if ($dayOff->code == Schedule::DAY_OFF_MISSION) $countT += $count;
            if ($dayOff->code == Schedule::DAY_OFF_SICK) $countS += $count;
            if ($dayOff->code == Schedule::DAY_OFF_70_SALARY) $countC += $count;
        }
        return ['L' => $countL, 'D' => $countD, 'W' => $countW, 'H' => $holidays, 'T' => $countT, 'C' => $countC, 'S' => $countS];
    }

    public static function checkDateIsMission($userId, $date)
    {
        $dayOff = StaffDayOff::where('user_id', $userId)
            ->where('code', Schedule::DAY_OFF_MISSION)
            ->where('start', '<=', $date)
            ->where('end', '>=', $date)
            ->first(['code', 'start', 'end', 'from_type', 'to_type']);
        if (!count($dayOff)) return '';
        return $dayOff;
    }

    public static function checkHalfShift($userId, $date)
    {
        if (!Carbon::parse($date)->isSaturday()) return false;
        $dayOff = StaffDayOff::where('user_id', $userId)
            ->whereNotNull('half_shift')
            ->where('start', '<=', $date)
            ->where('end', '>=', $date)
            ->first(['code', 'start', 'end', 'from_type', 'to_type']);
        if (!count($dayOff)) return false;
        return $dayOff;
    }

    public static function countTotalPerDayOff($userId, $fromMonth, $toMonth, $year)
    {
        $result = array_fill_keys(Schedule::arrTypeLeave(), 0);
        $beforeDate = Carbon::createFromDate($year, $fromMonth - 1, Schedule::DATE_START_SALARY)->format('Y-m-d');
        $afterDate = Carbon::createFromDate($year, $toMonth, Schedule::DATE_START_SALARY)->format('Y-m-d');
        $dayOffs = StaffDayOff::where('user_id', $userId)
            ->where('code', '<>', Schedule::DAY_OFF_MISSION)
            ->where('start', '<=', $afterDate)
            ->where('end', '>=', $beforeDate)
            ->get();
        if (!count($dayOffs)) return $result;
        $m = Schedule::TIME_OFF_MORNING;
        $a = Schedule::TIME_OFF_AFTERNOON;
        foreach ($dayOffs as $dayOff) {
            $count = 0;
            if ($dayOff->start >= $beforeDate && $dayOff->end <= $afterDate) {
                $count += $dayOff->total;
            } else {
                if ($dayOff->start < $beforeDate) {
                    $countIn = CalendarDepartment::countDayOffInRange($beforeDate, $dayOff->end, $m, $dayOff->to_type, $m, $a);
                    $count += $countIn;
                }
                if ($dayOff->end > $afterDate) {
                    $countIn = CalendarDepartment::countDayOffInRange($dayOff->start, $afterDate, $dayOff->from_type, $a, $m, $a);
                    $count += $countIn;
                }
            }
            $dayOff->status ? $result[$dayOff->code] += $count : $result[Schedule::DAY_OFF_NO_SALARY] += $count;
        }
        return $result;
    }

    public static function countTotal12Leave($userId, $fromMonth, $toMonth, $year)
    {
        $result = 0;
        $beforeDate = Carbon::createFromDate($year, $fromMonth - 1, Schedule::DATE_START_SALARY)->format('Y-m-d');
        $afterDate = Carbon::createFromDate($year, $toMonth, Schedule::DATE_START_SALARY)->format('Y-m-d');
        $dayOffs = StaffDayOff::where('user_id', $userId)
            ->whereCode(Schedule::DAY_OFF_12)
            ->where('start', '<=', $afterDate)
            ->where('end', '>=', $beforeDate)
            ->where('status', 1)
            ->get();
        if (!count($dayOffs)) return $result;
        $m = Schedule::TIME_OFF_MORNING;
        $a = Schedule::TIME_OFF_AFTERNOON;
        foreach ($dayOffs as $dayOff) {
            $count = 0;
            if ($dayOff->start >= $beforeDate && $dayOff->end <= $afterDate) {
                $count += $dayOff->total;
            } else {
                if ($dayOff->start < $beforeDate) {
                    $countIn = CalendarDepartment::countDayOffInRange($beforeDate, $dayOff->end, $m, $dayOff->to_type, $m, $a);
                    $count += $countIn;
                }
                if ($dayOff->end > $afterDate) {
                    $countIn = CalendarDepartment::countDayOffInRange($dayOff->start, $afterDate, $dayOff->from_type, $a, $m, $a);
                    $count += $countIn;
                }
            }
            $result += $count;
        }
        return $result;
    }

    public static function countTotalPerDayOffForUsers(array $usersId, $fromMonth, $toMonth, $year)
    {
//        $result = array_fill_keys(Schedule::arrTypeLeave(), 0);
        $result = [];
        $fromMonth = intval($fromMonth);
        $toMonth = intval($toMonth);
        $start = Carbon::createFromDate($year, $fromMonth - 1, Schedule::DATE_START_SALARY)->format('Y-m-d');
        $end = Carbon::createFromDate($year, $toMonth, Schedule::DATE_END_SALARY)->format('Y-m-d');
        $dayOffs = StaffDayOff::where('start', '<=', $end)
            ->where('status', 1)
            ->where('end', '>=', $start)
            ->whereIn('user_id', $usersId)
            ->get();
        if (!count($dayOffs)) return [];
        $m = Schedule::TIME_OFF_MORNING;
        $a = Schedule::TIME_OFF_AFTERNOON;
        for ($i = $fromMonth; $i <= $toMonth; $i++) {
            $beforeDate = Carbon::createFromDate($year, $i - 1, Schedule::DATE_START_SALARY)->format('Y-m-d');
            $afterDate = Carbon::createFromDate($year, $i, Schedule::DATE_END_SALARY)->format('Y-m-d');
            foreach ($dayOffs as $dayOff) {
                $count = 0;
                if ($dayOff->start > $afterDate || $dayOff->end < $beforeDate) continue;
                elseif ($dayOff->start >= $beforeDate && $dayOff->end <= $afterDate) {
                    $count += $dayOff->total;
                } else {
                    if ($dayOff->start <= $beforeDate) {
                        $countIn = CalendarDepartment::countDayOffInRange($beforeDate, $dayOff->end, $m, $dayOff->to_type, $m, $a);
                        $count += $countIn;
                    }
                    if ($dayOff->end >= $afterDate) {
                        $countIn = CalendarDepartment::countDayOffInRange($dayOff->start, $afterDate, $dayOff->from_type, $a, $m, $a);
                        $count += $countIn;
                    }
                }
                /*if ($dayOff->status) {*/
                    $result[$dayOff->user_id][$dayOff->code][$i] += $count;
                    $result[$dayOff->user_id][$dayOff->code]['total'] += $count;
                /*} else {
                    $result[$dayOff->user_id][Schedule::DAY_OFF_NO_SALARY][$i] += $count;
                    $result[$dayOff->user_id][Schedule::DAY_OFF_NO_SALARY]['total'] += $count;
                }*/
            }
        }
        return $result;
    }

    public static function getLeaveFollowPermission($infoPermission, $user_ids = null)
    {
        $query = "1=1";
        $manager_leaves = [];
        if ($infoPermission) {
            $depts = array_unique($infoPermission['departments']);
            $manager_leaves = $depts ? StaffDayOff::withTrashed()->orderBy('id','DESC')->whereRaw($query)->whereHas('user', function ($query) use ($depts, $user_ids) {
                $query->whereIn('department_id', $depts)->orWhereIn('user_id', $user_ids);
            })->with('user') : StaffDayOff::withTrashed()->orderBy('id','DESC')->where('user_id', \Auth::id());
        } else $manager_leaves = StaffDayOff::withTrashed()->orderBy('id','DESC');
        return $manager_leaves;
    }

    public static function getLeaveFollowManager($infoPermission)
    {
        $date = now()->format('Y-m-d');
        $manager_leaves = [];
        if ($infoPermission) {
            $depts = array_unique($infoPermission['departments']);
            $manager_leaves = $depts ? StaffDayOff::orderBy('id','DESC')->whereHas('user', function ($query) use ($depts) {
                $query->whereIn('department_id', $depts);
            })->with('user')->whereDate('created_at', $date)->get() : [];
        }
        return $manager_leaves;
    }

    public static function countLeavePendingFollowPer($infoPermission)
    {
        $date = now()->format('Y-m-d');
        $leaves = [];
        if ($infoPermission) {
            $depts = array_unique($infoPermission['departments']);
            if ($depts) {
                $leaves = StaffDayOff::orderBy('id','DESC')
                    ->where('code', '<>', Schedule::DAY_OFF_NO_SALARY)
                    ->where('start', '>=', $date)
                    ->whereHas('user', function ($query) use ($depts) {
                    $query->whereIn('department_id', $depts);
                })->get();
            }
        }
        $result = [0, 0];
        if ($leaves) {
            foreach ($leaves as $item) {
                if ($item->status == 1) $result[1] += 1;
                else $result[0] += 1;
            }
        }
        return $result;
    }

    public static function getLeaveFromPrevMonth($userId)
    {
        $d = now()->subDays(30)->format('Y-m-d');
        $dayOffs = StaffDayOff::where('user_id', $userId)->where('start', '>', $d)->get();
        if ($dayOffs) {
            foreach ($dayOffs as &$eventStaff) {
                $eventStaff->end = $eventStaff->end . "T23:59:00";
            }
        }
        return $dayOffs;
    }

    public static function countLeavePendingStaff($userId)
    {
        $date = now()->format('Y-m-d');
        $leaves = StaffDayOff::where('user_id', $userId)
            ->where('code', '<>', Schedule::DAY_OFF_NO_SALARY)
            ->where('start', '>=', $date)
            ->get();
        $result = [0, 0];
        if ($leaves) {
            foreach ($leaves as $item) {
                if ($item->status == 1) $result[1] += 1;
                else $result[0] += 1;
            }
        }
        return $result;
    }

    public static function checkIn6MonthBaby($userId, $date = null)
    {
        $date = empty($date) ? now()->format('d/m/Y') : $date;
        $dayOffBaby = StaffDayOff::where('user_id', $userId)->latest('id')->first();
        if (count($dayOffBaby) == 0) return null;
        $after6Month = Carbon::parse($dayOffBaby->end)->addMonths(6)->format('Y-m-d');
        if ($dayOffBaby->end < $date && $date <= $after6Month) return 1;
        return null;
    }

    public static function checkDateHasEvent1($userId, $date = null) //format date: y-m-d
    {
        // $date = Carbon::createFromDate($date)->format('Y-m-d');
        // $user = User::find($userId);
     
        
        // $m = 'MORNING';
        // $a = 'AFTERNOON';
        
        // $dayOff = StaffDayOff::where('user_id', $userId)->where('start', '<=', $date)->where('end', '>=', $date)->first();
        
        // if (!count($dayOff)) return '';

        // if ($dayOff->total == 1) {
        //     return 1;
        // } else {
        //     if ($dayOff->total < 1 && $date == $dayOff->start) {
        //         if ($date == '2021-09-20' ) {
        //             dd($dayOff->toArray());
        //         }
        //         if ($dayOff->from_type == $dayOff->to_type && $dayOff->from_type == Schedule::TIME_OFF_MORNING) return '0.5M';
        //         if ($dayOff->from_type == $dayOff->to_type && $dayOff->from_type == Schedule::TIME_OFF_AFTERNOON) return '0.5A';
        //     }
        // }

        $date = Carbon::createFromDate($date)->format('Y-m-d');
       
        $dayOff = StaffDayOff::where('user_id', $userId)->where('start', '<=', $date)->where('end', '>=', $date)->where('status', 1)->first();
        if (!count($dayOff)) return '';
        $code = 1;
        if ($dayOff->start < $date && $date < $dayOff->end) return $code;
        elseif ($dayOff->total < 1) return $dayOff->from_type == Schedule::TIME_OFF_AFTERNOON ? '0.5A' : '0.5M';
        elseif ($date == $dayOff->start) return $dayOff->from_type == Schedule::TIME_OFF_AFTERNOON ? '0.5A' : $code;
        elseif ($date == $dayOff->end) return $dayOff->to_type == Schedule::TIME_OFF_MORNING ? '0.5M' : $code;
        return '';
    }

    public static function dayOffDepartment($departmentId, $date)
    {
        $dayOffs = CalendarDepartment::getDayOff($departmentId);
        foreach ($dayOffs as $key => $item) {
            if ($date == strtotime($item['start'])
                && $item['from_type'] == 1 && $item['to_type'] == 1
            || $date == strtotime($item['start']) && $item['from_type'] == 2 && $item['to_type'] == 2
            ) {
                return '/2';
            }
        }

        return null;
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by', 'id');
    }

    public static function nghiKhongLuong($userId, $date = null) //format date: y-m-d
    {
        $date = Carbon::createFromDate($date)->format('Y-m-d');
        
        $result = '';
        $dayOffs = StaffDayOff::where('user_id', $userId)->where('start', '<=', $date)->where('end', '>=', $date)
                ->where('code', 'O')->get();
        if (count($dayOffs) == 0) return ''; 
        foreach ($dayOffs as $dayOff) {
            $code = $dayOff->code;
            $code2 = $dayOff->code . '/2';
            if ($dayOff->start < $date && $date < $dayOff->end) $result .= $dayOff->code;
            elseif ($dayOff->total < 1) $result .= $code2;
            elseif ($date == $dayOff->start) $result .= $dayOff->from_type == Schedule::TIME_OFF_AFTERNOON ? $code2 : $code;
            elseif ($date == $dayOff->end) $result .= $dayOff->to_type == Schedule::TIME_OFF_MORNING ? $code2 : $code;
        }
        
        return trim($result);
    }

    public static function selectDayOff($workSchedule, $nghi_phong_ban, $nghi_nhan_vien, $userId, $date = null, $department_id = null, $month = null, $year = null)
    {
        $date = Carbon::createFromDate($date)->format('Y-m-d');
        $contract = Contract::where('user_id', $userId)->where('department_id', $department_id)->whereIn('type_status', \App\Defines\Contract::getTypeStatusForSelectDayOffTimeKeeping())->first();

        $startDate = date('Y-m-d 00:00:00', strtotime($year . '-' . (($month - 1)) . '-' . 26));

        $boNhiem = Contract::where('type_status', \App\Defines\Contract::APPOINT)->where('user_id', $contract->user_id)
                                                        ->where('set_notvalid_on', '>', $startDate)
                                                        ->orderBy('id', 'DESC')
                                                        ->first();

        if (!is_null($contract) && is_null($boNhiem)) {
            if (strtotime($contract->set_notvalid_on) <= strtotime($date) && $contract->type_status == 7) {
                return '';
            }
            if (strtotime($contract->set_notvalid_on) <= strtotime($date) && ($contract->type_status == 2)) {
                return '';
            }
            if (strtotime($date) < strtotime($contract->valid_from) && $contract->type_status == 1) {
                $beforeContract = Contract::where('user_id', $userId)
                    ->where('id', '<>', $contract->id)
                    ->where('department_id', $department_id)
                    ->orderBy('set_notvalid_on', 'desc')
                    ->first();
                if (is_null($beforeContract) || !(!is_null($beforeContract) &&
                    strtotime($date) >= strtotime($beforeContract->valid_from) &&
                    strtotime($date) <= strtotime($beforeContract->set_notvalid_on)))
                return '';
            }
        }

        if ($department_id == $nghi_phong_ban->first()->department_id) {
            $isHoliday = $nghi_phong_ban->where('start_date', '<=', $date)->where('end_date', '>=', $date)->first();

        } else {
            $nghi_phong_ban = CalendarDepartment::where('department_id', $department_id)->where('categories', 'holiday')->get();
            $isHoliday = $nghi_phong_ban->where('start_date', '<=', $date)->where('end_date', '>=', $date)->first();
        }

        $m = 'MORNING';
        $a = 'AFTERNOON';
        $result = '';
        $resultH = '';
        $space = ' ';

        if (count($isHoliday)) {
            if ($isHoliday->start_date < $date && $isHoliday->end_date > $date) $resultH = 'H';
            elseif ($isHoliday->type == 'one') {
                if ($isHoliday->from_type == $m && $isHoliday->to_type == $a) $resultH = 'H';
                else $resultH = 'H/2';
            } elseif (($isHoliday->type == 'multiple')) {
                if ($isHoliday->start_date = $date) {
                    if ($isHoliday->from_type == $m) $resultH = 'H';
                    else $resultH = 'H/2';
                } else {
                    if ($isHoliday->to_type == $m) $resultH = 'H/2';
                    else $resultH = 'H';
                }
            }
        }

        $result = $result . $space;
        $dayOffs = $nghi_nhan_vien->where('user_id', $userId)->where('start', '<=', $date)->where('end', '>=', $date)->where('status', 1);
        // if (!count($dayOffs)) return $result;
        foreach ($dayOffs as $dayOff) {
            $code = $dayOff->code . $space;
            $code2 = $dayOff->code . '/2' . $space;
            if ($dayOff->start < $date && $date < $dayOff->end) $result .= $dayOff->code . $space;
            elseif ($dayOff->total < 1) $result .= $code2;
            elseif ($date == $dayOff->start) $result .= $dayOff->from_type == Schedule::TIME_OFF_AFTERNOON ? $code2 : $code;
            elseif ($date == $dayOff->end) $result .= $dayOff->to_type == Schedule::TIME_OFF_MORNING ? $code2 : $code;
        }

        if ($resultH == 'H' && trim($result) == 'BB') {
            return $result;
        } else if ($resultH == 'H') {
            return $resultH;
        }
        if (!count($dayOffs)) return $result;

        $check_th = Carbon::parse(date('Y-m-d', $date))->format('l');
        if (strtoupper($check_th) == 'SATURDAY') {
            if (($result == 'T' && $workSchedule->type == 1) || ($result == 'T/2' && $workSchedule->type == 1)) {
                return 'T/2';
            }
            if (($result == 'L' && $workSchedule->type == 1) || ($result == 'L/2' && $workSchedule->type == 1)) {
                return 'L/2';
            }
            if ($result == 'BB' && $workSchedule->type == 1) {
                return 'BB';
            }
        } else {
            return trim($result);
        }
        
    }

    public static function getDayOffByDateTimeKeeping($workSchedule, $nghi_phong_ban, $nghi_nhan_vien, $date, $contracts)
    {
        foreach ($contracts as $item) {
            $userId = $item->user_id;
            $department_id = $item->department_id;
            if (is_null($item->set_notvalid_on)) {
                if (date('Y-m-d', strtotime($item->valid_from)) > $date) $isDateBelongToContract = false;
                else {
                    $isDateBelongToContract = true;
                    break;
                }
            } else {
                if (date('Y-m-d', strtotime($item->valid_from)) <= $date &&
                    date('Y-m-d', strtotime($item->set_notvalid_on)) > $date) {
                    $isDateBelongToContract = true;
                    break;
                } else $isDateBelongToContract = false;
            }
        }
        if (!$isDateBelongToContract) return  '';
        
        if ($department_id == $nghi_phong_ban->first()->department_id) {
            $isHoliday = $nghi_phong_ban->where('start_date', '<=', $date)->where('end_date', '>=', $date)->first();
        } else {
            $nghi_phong_ban = CalendarDepartment::where('department_id', $department_id)->where('categories', 'holiday')->get();
            $isHoliday = $nghi_phong_ban->where('start_date', '<=', $date)->where('end_date', '>=', $date)->first();
        }
        $m = 'MORNING';
        $a = 'AFTERNOON';
        $result = '';
        $resultH = '';
        $space = ' ';

        if (count($isHoliday)) {
            if ($isHoliday->start_date < $date && $isHoliday->end_date > $date) $resultH = 'H';
            elseif ($isHoliday->type == 'one') {
                if ($isHoliday->from_type == $m && $isHoliday->to_type == $a) $resultH = 'H';
                else $resultH = 'H/2';
            } elseif (($isHoliday->type == 'multiple')) {
                if ($isHoliday->start_date = $date) {
                    if ($isHoliday->from_type == $m) $resultH = 'H';
                    else $resultH = 'H/2';
                } else {
                    if ($isHoliday->to_type == $m) $resultH = 'H/2';
                    else $resultH = 'H';
                }
            }
        }

        $result = $result . $space;
        $dayOffs = $nghi_nhan_vien->where('user_id', $userId)->where('start', '<=', $date)->where('end', '>=', $date)->where('status', 1);
        foreach ($dayOffs as $dayOff) {
            $code = $dayOff->code . $space;
            $code2 = $dayOff->code . '/2' . $space;
            if ($dayOff->start < $date && $date < $dayOff->end) $result .= $dayOff->code . $space;
            elseif ($dayOff->total < 1) $result .= $code2;
            elseif ($date == $dayOff->start) $result .= $dayOff->from_type == Schedule::TIME_OFF_AFTERNOON ? $code2 : $code;
            elseif ($date == $dayOff->end) $result .= $dayOff->to_type == Schedule::TIME_OFF_MORNING ? $code2 : $code;
        }

        if ($resultH == 'H' && trim($result) == 'BB') {
            return $result;
        } else if ($resultH == 'H') {
            return $resultH;
        }
        if (!count($dayOffs)) return $result;

        $check_th = Carbon::parse(date('Y-m-d', $date))->format('l');
        if (strtoupper($check_th) == 'SATURDAY') {
            if (($result == 'T' && $workSchedule->type == 1) || ($result == 'T/2' && $workSchedule->type == 1)) {
                return 'T/2';
            }
            if (($result == 'L' && $workSchedule->type == 1) || ($result == 'L/2' && $workSchedule->type == 1)) {
                return 'L/2';
            }
            if ($result == 'BB' && $workSchedule->type == 1) {
                return 'BB';
            }
        } else {
            return trim($result);
        }

    }


    public static function isSameDayOff($oldDayOff, $newDayOff, $typeDept)
    {
        $isHas = false;
        $_MORNING = Schedule::TIME_OFF_MORNING;
        $_AFTERNOON = Schedule::TIME_OFF_AFTERNOON;
        $_DEPT_TYPE_OFFICE = Shift::OFFICE_TIME;
        $oldStart = $oldDayOff['start'];
        $oldEnd = $oldDayOff['end'];
        $oldFromType = $oldDayOff['from_type'];
        $oldToType = $oldDayOff['to_type'];
        if (($newDayOff['start'] > $oldStart && $newDayOff['start'] < $oldEnd) || ($newDayOff['end'] > $oldStart && $newDayOff['end'] < $oldEnd) || $oldFromType == $_MORNING && $oldToType == $_AFTERNOON) {
            $isHas = true;
        } elseif ($newDayOff['start'] == $oldEnd) {
            if ($typeDept == $_DEPT_TYPE_OFFICE && $newDayOff['start'] == $newDayOff['end']) {
                $isHas = false;
            } elseif ($oldToType == $_AFTERNOON) {
                $isHas = !($newDayOff['start'] == $newDayOff['end']) || $newDayOff['to_type'] == $_AFTERNOON;
            } elseif ($oldToType == $_MORNING) $isHas = $newDayOff['from_type'] == $_MORNING;
        } elseif ($newDayOff['end'] == $oldStart) {
            if ($typeDept == $_DEPT_TYPE_OFFICE && $newDayOff['start'] == $newDayOff['end']) {
                $isHas = false;
            } elseif ($oldFromType == $_MORNING) {
                $isHas = !($newDayOff['start'] == $newDayOff['end']) || $newDayOff['from_type'] == $_AFTERNOON;
            } elseif ($oldFromType == $_AFTERNOON) $isHas = $newDayOff['to_type'] == $_AFTERNOON;
        }
        return $isHas;
    }
}
