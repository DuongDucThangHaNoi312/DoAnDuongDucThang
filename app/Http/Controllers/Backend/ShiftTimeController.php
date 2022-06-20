<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\CalendarDepartment;
use App\Models\Department;
use App\Models\DepartmentWorkingDayLogs;
use App\Models\Shift;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Response;

class ShiftTimeController extends Controller
{
    public function firstShift(Request $request)
    {
        $data = $request->all();
        if ($data['userId'] == null) {
            $users = DB::table('users')
                ->where('department_id', $data['departmentId'])
                ->pluck('fullname', 'id')->toArray();
        } else {
            $users = DB::table('users')
                ->where('department_id', $data['departmentId'])
                ->whereNotIn('id', $data['userId'])
                ->pluck('fullname', 'id')->toArray();

        }
        return $users;

    }

    public function secondShift(Request $request)
    {
        $data = $request->all();
        if ($data['userId1'] == null && $data['userId2'] == null) {
            $users = DB::table('users')
                ->where('department_id', $data['departmentId'])
                ->pluck('fullname', 'id')->toArray();
        } elseif ($data['userId1'] == null) {
            $users = DB::table('users')
                ->where('department_id', $data['departmentId'])
                ->whereNotIn('id', $data['userId2'])
                ->pluck('fullname', 'id')->toArray();
        } elseif ($data['userId2'] == null) {
            $users = DB::table('users')
                ->where('department_id', $data['departmentId'])
                ->whereNotIn('id', $data['userId1'])
                ->pluck('fullname', 'id')->toArray();
        } else {
            $users = DB::table('users')
                ->where('department_id', $data['departmentId'])
                ->whereNotIn('id', $data['userId1'])
                ->whereNotIn('id', $data['userId2'])
                ->pluck('fullname', 'id')->toArray();
        }
        return $users;
    }

    public function loadWorking(Request $request)
    {
        try {
            $data = DB::table('department_working_day')
                ->where('department_id', $request->get('departmentId'))
                ->select('id', 'start_date', 'end_date')->get();

            return $this->responseData(true, $data, trans('shifts.success'));
        } catch (\Exception $exception) {
            return $this->responseData(false, [$exception->getMessage()], trans('shifts.errors'));
        }
    }

    public function loadWorkShiftOneDay(Request $request)
    {
        try {
            $data = Shift::find($request->get('id'));
            return $this->responseData(true, $data, trans('shifts.success'));
        } catch (\Exception $exception) {
            return $this->responseData(false, [$exception->getMessage()], trans('shifts.errors'));
        }
    }

    public function checkingWorkingDay(Request $request)
    {
        $data = $request->all();

        $data['start_date'] = Carbon::createFromFormat('d/m/Y', $request->get('start_date'))->toDateString();
        $data['end_date'] = Carbon::createFromFormat('d/m/Y', $request->get('end_date'))->toDateString();

        $department = Department::find($data['department_id']);
        if ($department->type == 2) {
            $same1 = array_intersect($data['first_shift'], $data['second_shift']);
            $same2 = array_intersect($data['second_shift'], $data['third_shift']);
            $same3 = array_intersect($data['first_shift'], $data['third_shift']);
            if ($data['first_shift'] == '' && $data['second_shift'] == '' && $data['third_shift'] == '') {
                return $this->responseData(false, $data, trans('shifts.cannot_required_all'));
            } else if (count($same1) > 0) {
                return $this->responseData(false, $data, trans('shifts.must_one'));
            } else if (count($same2) > 0) {
                return $this->responseData(false, $data, trans('shifts.must_one'));
            } else if (count($same3) > 0) {
                return $this->responseData(false, $data, trans('shifts.must_one'));
            }

        }
        if ($department->type == 3) {
            $same = array_intersect($data['second_shift_and_ot'], $data['first_shift_and_ot']);
            if ($data['first_shift_and_ot'] == '' && $data['second_shift_and_ot'] == '') {
                return $this->responseData(false, $data, trans('shifts.cannot_required_all'));
            } else if (count($same) > 0) {
                return $this->responseData(false, $data, trans('shifts.must_one'));
            }
        }

        $off = CalendarDepartment::where('department_id', $data['department_id'])->get();
        $date = [];
        foreach ($off as $day) {
            if ($day->type == 'one') {
                array_push($date, $day->start_date);
            }
            if ($day->type == 'everyweek') {
                $start = Carbon::create($day->start_date);
                $end = Carbon::create($day->end_date);
                for ($dt = $start; $dt <= $end; $dt->addDays(7)) {
                    array_push($date, $dt->toDateString());
                }
            }
            if ($day->type == 'multiple') {
                $start = Carbon::create($day->start_date);
                $end = Carbon::create($day->end_date);
                for ($dt = $start; $dt <= $end; $dt->addDay()) {
                    array_push($date, $dt->toDateString());
                }
            }
        }

        $shifts = Shift::where('department_id', $data['department_id'])->get();
        $working = [];
        foreach ($shifts as $shift) {
            if ($shift->start_date->toDateString() == $shift->end_date->toDateString()) {
                array_push($working, $shift->start_date->toDateString());
            } else {
                $start = Carbon::create($shift->start_date->toDateString());
                $end = Carbon::create($shift->end_date->toDateString());
                for ($dt = $start; $dt <= $end; $dt->addDay()) {
                    array_push($working, $dt->toDateString());
                }
            }

        }
        if ($data['idShift']) {
            if ($data['start_date'] == Shift::find($data['idShift'])->start_date->toDateString() && $data['end_date'] == Shift::find($data['idShift'])->end_date->toDateString()) {
                return $this->responseData(true, $data, trans('shifts.success'));
            }
        }
        if ($data['start_date'] == $data['end_date']) {

            if (in_array($data['start_date'], $date)) {
                return $this->responseData(false, $data, trans('calendar_departments.have_a_day_off'));
            }
            if (in_array($data['start_date'], $working)) {
                return $this->responseData(false, $data, trans('calendar_departments.have_a_work_off'));
            }
        } else {
            $start = Carbon::create($data['start_date']);
            $end = Carbon::create($data['end_date']);
            if ($data['idShift']) {
                $startDate = Carbon::create(Shift::find($data['idShift'])->start_date->toDateString());
                $endDate = Carbon::create(Shift::find($data['idShift'])->end_date->toDateString());
                for ($dy = $startDate; $dy <= $endDate; $dy->addDay()) {
                    unset($working[array_search($dy->toDateString(), $working)]);
                }
            }
            for ($dt = $start; $dt <= $end; $dt->addDay()) {
                if (in_array($dt->toDateString(), $date)) {
                    return $this->responseData(false, $dt->toDateString(), trans('calendar_departments.have_a_day_off'));
                } elseif (in_array($dt->toDateString(), $working)) {
                    return $this->responseData(false, $dt->toDateString(), trans('calendar_departments.have_a_work_off'));
                }
            }

        }
        return $this->responseData(true, $data, trans('shifts.success'));
    }

    public function storeShift(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->all();
            $data['start_date'] ? $data['start_date'] = Carbon::createFromFormat('d/m/Y', $request->get('start_date'))->toDateString() : '';
            $data['end_date'] ? $data['end_date'] = Carbon::createFromFormat('d/m/Y', $request->get('end_date'))->toDateString() : '';
            $validator = Validator::make($data, Shift::rules());
            $validator->setAttributeNames(trans('shifts'));
            if ($validator->passes()) {
                $data['first_shift'] = json_encode($data['first_shift']);
                $data['second_shift'] = json_encode($data['second_shift']);
                $data['third_shift'] = json_encode($data['third_shift']);
                $data['first_shift_and_ot'] = json_encode($data['first_shift_and_ot']);
                $data['second_shift_and_ot'] = json_encode($data['second_shift_and_ot']);
                $shift = Shift::create($data);
                DB::commit();
                Session::flash('message', trans('system.success'));
                Session::flash('alert-class', 'success');
                return $this->responseData(true, $shift, trans('shifts.success_create'));
            }
            return $this->responseData(false, $validator->errors(), trans('system.have_an_error'));
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->responseData(false, [$exception->getMessage()], trans('shifts.error_create'));
        }
    }

    public function updateShift(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->all();
            $shift = Shift::find($data['idShift']);
            $data['first_shift'] = json_encode($data['first_shift']);
            $data['second_shift'] = json_encode($data['second_shift']);
            $data['third_shift'] = json_encode($data['third_shift']);
            $data['first_shift_and_ot'] = json_encode($data['first_shift_and_ot']);
            $data['second_shift_and_ot'] = json_encode($data['second_shift_and_ot']);
            $data['start_date'] ? $data['start_date'] = Carbon::createFromFormat('d/m/Y', $request->get('start_date'))->toDateString() : '';
            $data['end_date'] ? $data['end_date'] = Carbon::createFromFormat('d/m/Y', $request->get('end_date'))->toDateString() : '';
            unset($data['day_click_shift'], $data['idShift'], $data['_token']);

            foreach ($data as $key => $value) {

                if ($key == 'start_date' || $key == 'end_date' ? $shift->$key->toDateString() : $shift->$key != $value) {
                    DepartmentWorkingDayLogs::create([
                        'work_day_id' => $shift->id,
                        'field' => $key,
                        'old_data' => $shift->$key,
                        'new_data' => $value,
                        'note' => trans('shifts.note'),
                        'action_by' => $request->user()->id,
                        'action_at' => date("Y-m-d H:i:s"),
                    ]);
                    DB::commit();
                }
            }

            if ($data['start_date'] == $shift->start_date->toDateString() && $data['end_date'] != $shift->end_date->toDateString()) {
                $newStartTimeStamps = strtotime('+1 day', strtotime($data['end_date']));
                $newStartDate = date('Y-m-d', $newStartTimeStamps);
                Shift::create($data);
                $shift->update([
                    'start_date' => $newStartDate
                ]);
                DB::commit();
            } else if ($data['end_date'] == $shift->end_date->toDateString() && $data['start_date'] != $shift->start_date->toDateString()) {
                $newEndTimeStamps = strtotime('-1 day', strtotime($data['start_date']));
                $newEndDate = date('Y-m-d', $newEndTimeStamps);
                Shift::create($data);
                $shift->update([
                    'end_date' => $newEndDate
                ]);
                DB::commit();
            } else if ($data['end_date'] == $shift->end_date->toDateString() && $data['start_date'] == $shift->start_date->toDateString()) {
                $shift->update($data);
                DB::commit();
            } else if ($data['start_date'] >= $shift->start_date->toDateString() && $data['end_date'] <= $shift->end_date->toDateString()) {
                $newStartTimeStamps = strtotime('+1 day', strtotime($data['end_date']));
                $newStartDate = date('Y-m-d', $newStartTimeStamps);
                $newEndTimeStamps = strtotime('-1 day', strtotime($data['start_date']));
                $newEndDate = date('Y-m-d', $newEndTimeStamps);
                Shift::create($data);
                Shift::create([
                    'start_date' => $newStartDate,
                    'end_date' => $shift->end_date,
                    'first_shift' => $shift->first_shift,
                    'second_shift' => $shift->second_shift,
                    'third_shift' => $shift->third_shift,
                    'first_shift_and_ot' => $shift->first_shift_and_ot,
                    'second_shift_and_ot' => $shift->second_shift_and_ot,
                    'department_id' => $shift->department_id,
                ]);
                $shift->update([
                    'end_date' => $newEndDate
                ]);
                DB::commit();
            }
            return $this->responseData(true, [$shift], trans('shifts.success_update'));
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->responseData(false, [$exception->getMessage()], trans('shifts.error_update'));
        }
    }

    public function updateShiftAll(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->all();
            $shift = Shift::find($data['idShift']);
            $data['first_shift'] = json_encode($data['first_shift']);
            $data['second_shift'] = json_encode($data['second_shift']);
            $data['third_shift'] = json_encode($data['third_shift']);
            $data['first_shift_and_ot'] = json_encode($data['first_shift_and_ot']);
            $data['second_shift_and_ot'] = json_encode($data['second_shift_and_ot']);
            $data['start_date'] ? $data['start_date'] = Carbon::createFromFormat('d/m/Y', $request->get('start_date'))->toDateString() : '';
            $data['end_date'] ? $data['end_date'] = Carbon::createFromFormat('d/m/Y', $request->get('end_date'))->toDateString() : '';
            unset($data['day_click_shift'], $data['idShift'], $data['_token']);
            foreach ($data as $key => $value) {
                $old = $key == 'start_date' || $key == 'end_date' ? $shift->$key->toDateString() : $shift->$key;
                if ($old != $value) {
                    DepartmentWorkingDayLogs::create([
                        'work_day_id' => $shift->id,
                        'field' => $key,
                        'old_data' => $shift->$key,
                        'new_data' => $value,
                        'note' => trans('shifts.note'),
                        'action_by' => $request->user()->id,
                        'action_at' => date("Y-m-d H:i:s"),
                    ]);
                    DB::commit();
                }
            }
            $shift->update($data);
            DB::commit();
            return $this->responseData(true, [$shift], trans('shifts.success_update'));
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->responseData(false, [$exception->getMessage()], trans('shifts.error_update'));
        }
    }

    public function deleteAllShift(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->all();
            $shift = Shift::find($data['idShift']);
            $shift->delete();
            DB::commit();
            return $this->responseData(true, $shift, trans('shifts.success_delete'));
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->responseData(false, [$exception->getMessage()], trans('shifts.error_delete'));
        }
    }

    public function deleteOneShift(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->all();

            $shift = Shift::find($data['idShift']);
            $data['first_shift'] = json_encode($data['first_shift']);
            $data['second_shift'] = json_encode($data['second_shift']);
            $data['third_shift'] = json_encode($data['third_shift']);
            $data['first_shift_and_ot'] = json_encode($data['first_shift_and_ot']);
            $data['second_shift_and_ot'] = json_encode($data['second_shift_and_ot']);
            $data['start_date'] ? $data['start_date'] = Carbon::createFromFormat('d/m/Y', $request->get('start_date'))->toDateString() : '';
            $data['end_date'] ? $data['end_date'] = Carbon::createFromFormat('d/m/Y', $request->get('end_date'))->toDateString() : '';
            $data['day_click_shift'] = Carbon::createFromFormat('d/m/Y', $request->get('day_click_shift'))->toDateString();

            if ($data['day_click_shift'] == $shift->start_date->toDateString()) {
                $newStartTimeStamps = strtotime('+1 day', strtotime($data['day_click_shift']));
                $newStartDate = date('Y-m-d', $newStartTimeStamps);

                $shift->update([
                    'start_date' => $newStartDate
                ]);
                DB::commit();
            } elseif ($data['day_click_shift'] == $shift->end_date->toDateString()) {
                $newEndTimeStamps = strtotime('-1 day', strtotime($data['day_click_shift']));
                $newEndDate = date('Y-m-d', $newEndTimeStamps);
                $shift->update([
                    'end_date' => $newEndDate
                ]);
                DB::commit();
            } else {
                $newStartTimeStamps = strtotime('+1 day', strtotime($data['day_click_shift']));
                $newStartDate = date('Y-m-d', $newStartTimeStamps);
                $newEndTimeStamps = strtotime('-1 day', strtotime($data['day_click_shift']));
                $newEndDate = date('Y-m-d', $newEndTimeStamps);
                Shift::create([
                    'start_date' => $newStartDate,
                    'end_date' => $shift->end_date,
                    'first_shift' => $shift->first_shift,
                    'second_shift' => $shift->second_shift,
                    'third_shift' => $shift->third_shift,
                    'first_shift_and_ot' => $shift->first_shift_and_ot,
                    'second_shift_and_ot' => $shift->second_shift_and_ot,
                    'department_id' => $shift->department_id,
                ]);
                $shift->update([
                    'end_date' => $newEndDate
                ]);
                DB::commit();
            }
            return $this->responseData(true, [$shift], trans('shifts.success_delete'));
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->responseData(false, [$exception->getMessage()], trans('shifts.error_delete'));
        }
    }

    public function totalWorking(Request $request)
    {
        $year = $request->get('year');
        $shifts = Shift::where('department_id', $request->get('departmentId'))->get();
        $monthWork = [
            [], [], [], [], [], [], [], [], [], [], [], []
        ];
        foreach ($shifts as $shift) {
            for ($i = 0; $i < 12; $i++) {
                if ($shift->start_date == $shift->end_date) {
                    $monthStartDate = Carbon::create($shift->start_date->toDateString())->format('m');
                    $yearStartDate = Carbon::create($shift->start_date->toDateString())->format('Y');
                    if ($monthStartDate == $i + 1 && $year == $yearStartDate) {
                        array_push($monthWork[$i], $shift->start_date->toDateString());
                    }
                } else {
                    $start = Carbon::create($shift->start_date->toDateString());
                    $end = Carbon::create($shift->end_date->toDateString());
                    for ($dt = $start; $dt <= $end; $dt->addDay()) {
                        $monthDt = Carbon::create($dt->toDateString())->format('m');
                        $yearDt = Carbon::create($dt->toDateString())->format('Y');
                        if ($monthDt == $i + 1 && $year == $yearDt) {
                            array_push($monthWork[$i], $dt->toDateString());
                        }
                    }
                }
            }
        }
        $dayOffs = CalendarDepartment::where('department_id', $request->get('departmentId'))->get();
        $monthOffAll = [
            [], [], [], [], [], [], [], [], [], [], [], []
        ];
        $monthOffHalf = [
            [], [], [], [], [], [], [], [], [], [], [], []
        ];
        foreach ($dayOffs as $dayOff) {
            for ($i = 0; $i < 12; $i++) {
                if ($dayOff->type == 'one') {
                    $monthOffStartDate = Carbon::create($dayOff->start_date)->format('m');
                    $yearStartDate = Carbon::create($dayOff->start_date)->format('Y');
                    if ($monthOffStartDate == $i + 1 && $year == $yearStartDate) {
                        $dayOff->from_type == $dayOff->to_type ? array_push($monthOffHalf[$i], $dayOff->start_date) : array_push($monthOffAll[$i], $dayOff->start_date);
                    }
                }
                if ($dayOff->type == 'multiple') {
                    $monthOffStartDate = Carbon::create($dayOff->start_date)->format('m');
                    $yearStartDate = Carbon::create($dayOff->start_date)->format('Y');
                    if ($monthOffStartDate == $i + 1 && $year == $yearStartDate) {
                        $dayOff->from_type == 'MORNING' ? array_push($monthOffAll[$i], $dayOff->start_date) : array_push($monthOffHalf[$i], $dayOff->start_date);
                    }
                    $start = Carbon::create($dayOff->start_date);
                    $start->addDay();
                    $end = Carbon::create($dayOff->end_date);
                    $end->subDay();
                    for ($dt = $start; $dt <= $end; $dt->addDay()) {
                        $monthOffDt = Carbon::create($dt->toDateString())->format('m');
                        $yearOffDt = Carbon::create($dt->toDateString())->format('Y');
                        if ($monthOffDt == $i + 1 && $year == $yearOffDt) {
                            array_push($monthOffAll[$i], $dt->toDateString());
                        }
                    }
                    $monthOffEndDate = Carbon::create($dayOff->end_date)->format('m');
                    $yearEndDate = Carbon::create($dayOff->end_date)->format('Y');

                    if ($monthOffEndDate == $i + 1 && $year == $yearEndDate) {
                        $dayOff->to_type == 'MORNING' ? array_push($monthOffHalf[$i], $dayOff->end_date) : array_push($monthOffAll[$i], $dayOff->end_date);
                    }
                }
                if ($dayOff->type == 'everyweek') {
                    $start = Carbon::create($dayOff->start_date);
                    $end = Carbon::create($dayOff->end_date);
                    for ($dt = $start; $dt <= $end; $dt->addDays(7)) {
                        $monthOffDt = Carbon::create($dt->toDateString())->format('m');
                        $yearOffDt = Carbon::create($dt->toDateString())->format('Y');
                        if ($monthOffDt == $i + 1 && $year == $yearOffDt) {
                            $dayOff->from_type == $dayOff->to_type ? array_push($monthOffHalf[$i], $dt->toDateString()) : array_push($monthOffAll[$i], $dt->toDateString());
                        }
                    }
                }
            }
        }
        $totalInMonth = [
            [], [], [], [], [], [], [], [], [], [], [], []
        ];
        for ($i = 0; $i < 12; $i++) {
            $dayInMonth = Carbon::create($year, $i + 1)->daysInMonth;
            $totalInMonth[$i] = $dayInMonth - count($monthOffAll[$i]) - 0.5 * count($monthOffHalf[$i]);

        }
        return $totalInMonth;
    }

    public function responseData($status, $data, $message)
    {
        $status_code = ($status == true) ? 200 : 500;
        return response()->json([
            'status' => $status,
            'data' => $data,
            'message' => $message
        ], $status_code);
    }
}
