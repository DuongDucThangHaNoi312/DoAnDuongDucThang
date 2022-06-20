<?php

namespace App\Models;

use App\Define\OverTime;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OverTimes extends Model
{
    protected $table = 'overtimes';
    protected $fillable = ['id', 'company_id', 'department_id', 'shifts', 'display_with_type', 'display_with_data', 'hidden_with_users', 'start_date', 'end_date', 'overtime_hours', 'status', 'created_by'];
    protected $dates = ['start_date', 'end_date'];

    public static function rules($id = 0)
    {
        return [
            'company_id' => 'required' . ($id == 0 ? '' : ',' . $id),
            'department_id' => 'required' . ($id == 0 ? '' : ',' . $id),
            'start_date' => 'required',
            'overtime_hours' => 'required',
        ];
    }

    public static function getOT($month = null, $year = null, $user_id = null, $currentDeptId = null)
    {
        $month == null ? $month = getdate()['mon'] : $month = $month;
        $year == null ? $year = getdate()['year'] : $year = $year;
        $user_id == null ? $user_id = Auth::id() : $user_id = $user_id;

        $startMonth = Carbon::create($year, $month - 1, 26);
        $endMonth = Carbon::create($year, $month, 26);
        $TangCa = [];
        $tangCa = [];
        if (empty($currentDeptId)) $departmentId = User::find($user_id)->department_id;
        else $departmentId = $currentDeptId;
        $overTime = OverTimes::where('department_id', $departmentId)->get();
        $dayOffCalendar = CalendarDepartment::where('department_id', $departmentId)->get();
        $holiday = [];
        $dayOff = [];

        foreach ($dayOffCalendar as $off) {
            if ($off->categories == 'holiday') {
                $off->type == 'one' ? array_push($holiday, $off->start_date) : '';
                if ($off->type == 'multiple') {
                    $startDate = Carbon::create($off->start_date);
                    $endDate = Carbon::create($off->end_date);
                    for ($dt = $startDate; $dt <= $endDate; $dt->addDays(1)) {
                        array_push($holiday, $dt->toDateString());
                    }
                }
            }
            if ($off->categories == 'normal') {
                if ($off->type == 'one') {
                    array_push($dayOff, $off->start_date);
                }
                if ($off->type == 'everyweek') {
                    $startDate = Carbon::create($off->start_date);
                    $endDate = Carbon::create($off->end_date);
                    for ($dt = $startDate; $dt <= $endDate; $dt->addDays(7)) {
                        array_push($dayOff, $dt->toDateString());
                    }
                }
                if ($off->type == 'multiple') {
                    $startDate = Carbon::create($off->start_date);
                    $startDate->addDay();
                    $endDate = Carbon::create($off->end_date);
                    $endDate->subDay();
                    for ($dt = $startDate; $dt <= $endDate; $dt->addDay()) {
                        array_push($dayOff, $dt->toDateString());
                    }
                }
            }

        }
        foreach ($overTime as $OT) {
            if ($OT->display_with_type == 1 && in_array($user_id, json_decode($OT->hidden_with_users)) == false || $OT->display_with_type == 2 && in_array($user_id, json_decode($OT->display_with_data))) {
                if ($OT->status == 0) {
                    if (in_array($OT->start_date->toDateString(), $holiday)) {
                        $type = OverTime::TYPE_HOLIDAY;
                    } elseif (in_array($OT->start_date->toDateString(), $dayOff)) {
                        $type = OverTime::TYPE_DAYOFF;
                    } else {
                        $type = OverTime::TYPE_NORMAL;
                    }
                    if ($OT->start_date >= $startMonth->toDateString() && $OT->start_date <= $endMonth->toDateString()) {
                        $ot = [
                            'type' => $type,
                            'date' => $OT->start_date->toDateString(),
                            'hours' => $OT->overtime_hours,
                            'shifts' => $OT->shifts
                        ];
                        array_push($tangCa, $ot);
                        if ($OT->shifts == null) {
                            $tc = [
                                'type' => $type,
                                'date' => $OT->start_date->toDateString(),
                                'hours' => $OT->overtime_hours,
                            ];
                            array_push($TangCa, $tc);
                        } else {
                            $tc = [
                                'type' => $type,
                                'date' => $OT->start_date->toDateString(),
                                'hours' => [
                                    'day' => $OT->shifts != 3 && $OT->shifts != null ? $OT->overtime_hours : null,
                                    'night' => $OT->shifts == 3 ? $OT->overtime_hours : null,
                                ],
                            ];
                            array_push($TangCa, $tc);
                        }


                    }

                }

                if ($OT->status == 1) {
                    if (in_array($OT->start_date->toDateString(), $holiday)) {
                        $type = OverTime::TYPE_HOLIDAY;
                    } elseif (in_array($OT->start_date->toDateString(), $dayOff)) {
                        $type = OverTime::TYPE_DAYOFF;
                    } else {
                        $type = OverTime::TYPE_NORMAL;
                    }
                    $startDate = Carbon::create(($OT->start_date)->toDateString());
                    $endDate = Carbon::create(($OT->end_date)->toDateString());
                    for ($dt = $startDate; $dt <= $endDate; $dt->addDays(7)) {
                        if ($dt >= $startMonth->toDateString() && $dt <= $endMonth->toDateString()) {
                            $ot = [
                                'type' => $type,
                                'date' => $dt->toDateString(),
                                'hours' => $OT->overtime_hours,
                                'shifts' => $OT->shifts
                            ];
                            array_push($tangCa, $ot);
                            if ($OT->shifts == null) {
                                $tc = [
                                    'type' => $type,
                                    'date' => $dt->toDateString(),
                                    'hours' => $OT->overtime_hours,
                                ];
                                array_push($TangCa, $tc);
                            } else {
                                $tc = [
                                    'type' => $type,
                                    'date' => $dt->toDateString(),
                                    'hours' => [
                                        'day' => $OT->shifts != 3 && $OT->shifts != null ? $OT->overtime_hours : null,
                                        'night' => $OT->shifts == 3 ? $OT->overtime_hours : null,
                                    ],
                                ];
                                array_push($TangCa, $tc);
                            }


                        }
                    }
                }
                if ($OT->status == 2) {
                    $startDate = Carbon::create(($OT->start_date)->toDateString());
                    $endDate = Carbon::create(($OT->end_date)->toDateString());
                    for ($dt = $startDate; $dt <= $endDate; $dt->addDay()) {
                        if (in_array($dt->toDateString(), $holiday)) {
                            $type = OverTime::TYPE_HOLIDAY;
                        } elseif (in_array($dt->toDateString(), $dayOff)) {
                            $type = OverTime::TYPE_DAYOFF;
                        } else {
                            $type = OverTime::TYPE_NORMAL;
                        }
                        if ($dt >= $startMonth->toDateString() && $dt <= $endMonth->toDateString()) {
                            $ot = [
                                'type' => $type,
                                'date' => $dt->toDateString(),
                                'hours' => $OT->overtime_hours,
                                'shifts' => $OT->shifts
                            ];
                            array_push($tangCa, $ot);
                            if ($OT->shifts == null) {
                                $tc = [
                                    'type' => $type,
                                    'date' => $dt->toDateString(),
                                    'hours' => $OT->overtime_hours,
                                ];
                                array_push($TangCa, $tc);
                            } else {
                                $tc = [
                                    'type' => $type,
                                    'date' => $dt->toDateString(),
                                    'hours' => [
                                        'day' => $OT->shifts != 3 && $OT->shifts != null ? $OT->overtime_hours : null,
                                        'night' => $OT->shifts == 3 ? $OT->overtime_hours : null,
                                    ],
                                ];
                                array_push($TangCa, $tc);
                            }
                        }
                    }

                }
            }
        }
        for ($i = 0; $i < count($tangCa); $i++) {
            for ($j = $i; $j < count($tangCa); $j++) {
                if ($i != $j) {
                    $dateDulicate = array_intersect_assoc($tangCa[$i], $tangCa[$j])['date'];
                    foreach ($TangCa as $key => $a) {
                        if ($a['date'] == $dateDulicate) {
                            unset($TangCa[$key]);
                        }
                    }
                    if ($dateDulicate) {
                        if ($tangCa[$i]['shifts'] == 3 && $tangCa[$j]['shifts'] != 3 && $tangCa[$j]['shifts'] != null) {
                            $d = [
                                'type' => $tangCa[$i]['type'],
                                'date' => $dateDulicate,
                                'hours' => ['day' => $tangCa[$j]['hours'], 'night' => $tangCa[$i]['hours']],
                            ];
                            array_push($TangCa, $d);
                        } else if ($tangCa[$i]['shifts'] != 3 && $tangCa[$j]['shifts'] == 3 && $tangCa[$i]['shifts'] != null) {
                            $d = [
                                'type' => $tangCa[$i]['type'],
                                'date' => $dateDulicate,
                                'hours' => ['day' => $tangCa[$i]['hours'], 'night' => $tangCa[$j]['hours']],
                            ];
                            array_push($TangCa, $d);
                        } else if ($tangCa[$i]['shifts'] == 3 && $tangCa[$j]['shifts'] == 3) {
                            $d = [
                                'type' => $tangCa[$i]['type'],
                                'date' => $dateDulicate,
                                'hours' => ['day' => null, 'night' => $tangCa[$j]['hours'] + $tangCa[$i]['hours']],
                            ];
                            array_push($TangCa, $d);
                        } else if ($tangCa[$j]['shifts'] != 3 && $tangCa[$i]['shifts'] != 3 && $tangCa[$j]['shifts'] != null && $tangCa[$i]['shifts'] != null) {
                            $d = [
                                'type' => $tangCa[$i]['type'],
                                'date' => $dateDulicate,
                                'hours' => ['day' => $tangCa[$j]['hours'] + $tangCa[$i]['hours'], 'night' => null],
                            ];
                            array_push($TangCa, $d);
                        } else if ($tangCa[$i]['shifts'] == null) {
                            $d = [
                                'type' => $tangCa[$i]['type'],
                                'date' => $dateDulicate,
                                'hours' => $tangCa[$j]['hours'] + $tangCa[$i]['hours'],
                            ];
                            array_push($TangCa, $d);
                        }
                    }
                }

            }
        }
        return $TangCa;
    }

    public static function totalWorkingInMonth($month, $year, $departmentId)
    {
        $startMonth = Carbon::create($year, $month - 1, 26);
        $endMonth = Carbon::create($year, $month, 25);
        $dayInMonth = $endMonth->diffInDays($startMonth) + 1;

        $departmentOff = DB::table('department_day_offs')->where('department_id', $departmentId)->whereNull('deleted_at')->where('categories', '<>', 'holiday')->get();
        $allDayOff = [];
        $halfDayOff = [];
        foreach ($departmentOff as $off) {
            if ($off->type == 'one') {
                if ($off->start_date >= $startMonth->toDateString() && $off->start_date <= $endMonth->toDateString()) {
                    $off->from_type == $off->to_type ? array_push($halfDayOff, $off->start_date) : array_push($allDayOff, $off->start_date);
                }
            }
            if ($off->type == 'multiple') {
                $start = Carbon::create($off->start_date);
                $start->addDay();
                $end = Carbon::create($off->end_date);
                $end->subDay();
                for ($dt = $start; $dt <= $end; $dt->addDay()) {
                    if ($dt->toDateString() >= $startMonth->toDateString() && $dt->toDateString() <= $endMonth->toDateString()) {
                        array_push($allDayOff, $dt->toDateString());
                    }
                }
                if ($off->start_date >= $startMonth->toDateString() && $off->start_date <= $endMonth->toDateString()) {
                    $off->from_type == 'MORNING' ? array_push($allDayOff, $off->start_date) : array_push($halfDayOff, $off->start_date);
                }
                if ($off->end_date >= $startMonth->toDateString() && $off->end_date <= $endMonth->toDateString()) {
                    $off->to_type == 'MORNING' ? array_push($halfDayOff, $off->start_date) : array_push($allDayOff, $off->start_date);
                }
            }
            if ($off->type == 'everyweek') {
                $start = Carbon::create($off->start_date);
                $end = Carbon::create($off->end_date);
                for ($dt = $start; $dt <= $end; $dt->addDays(7)) {
                    if ($dt->toDateString() >= $startMonth->toDateString() && $dt->toDateString() <= $endMonth->toDateString()) {

                        $off->from_type == $off->to_type ? array_push($halfDayOff, $dt->toDateString()) : array_push($allDayOff, $dt->toDateString());
                    }
                  
                }
            }

        }
        $totalInMonth = $dayInMonth - count($allDayOff) - 0.5 * count($halfDayOff);
        return $totalInMonth;

    }
}
