<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\CalendarDepartment;
use App\Models\Department;
use App\Models\DepartmentDayOffLogs;
use App\Models\Shift;
use App\Models\Team;
use App\Models\UserTeam;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Response;
use App\User;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Support\Facades\Auth;

class CalendarDepartmentController extends Controller
{
    public function index(Request $request, $id)
    {

            $dayOfAMonth = [];
            $firstDayOfMonth = [];
            $data = [];
            $year = intval($request->input('year', getdate()['year']));
            for ($i = 1; $i < 13; $i++) {
                $month = date($i);
                $daysInMonth = Carbon::create($year, $month)->daysInMonth;
                $date = (Carbon::create($year, $month)->format('l'));
                array_push($dayOfAMonth, $daysInMonth);
                array_push($firstDayOfMonth, $date);
            }
            $dayOfWeek = trans('calendar_departments.week');
            $monthOfYear = trans('calendar_departments.month');
            for ($i = 0; $i < 12; $i++) {
                array_push($data, [$monthOfYear[$i] => [$dayOfAMonth[$i], $firstDayOfMonth[$i]]]);
            }
            $department = Department::whereId($id)->with('company')->first();
            if (is_null($department)) {
                Session::flash('message', trans('system.have_an_error'));
                Session::flash('alert-class', 'danger');
                return redirect()->route('admin.departments.index');
            }
            $departmentGroups = DB::table('department_relationships')->get();
            return view('backend.department.calendar', compact('year', 'data', 'dayOfAMonth', 'firstDayOfMonth', 'dayOfWeek', 'monthOfYear', 'department', 'departmentGroups'));

    }

    public function loadDataOneDay(Request $request)
    {
        $dayOff = CalendarDepartment::find($request->get('id'));
        //dd($request->get('id'), $dayOff);
        return $dayOff;
    }

    public function loadDataDepartments(Request $request)
    {
        $dayOff = CalendarDepartment::where('department_id', ($request->get('departmentId')))->get();
        return $dayOff;
    }

    public function checkIsDayOff(Request $request)
    {
        $checking = CalendarDepartment::where('department_id', $request->get('department_id'))->get();
        $data = $request->all();

        $data['start_date'] = Carbon::createFromFormat('d/m/Y', $request->get('start_date'))->toDateString();
        $data['end_date'] = Carbon::createFromFormat('d/m/Y', $request->get('end_date'))->toDateString();
        $date = [];
        $working = [];
        $works = Shift::where('department_id', $request->get('department_id'))->get();
        foreach ($checking as $day) {
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

        foreach ($works as $work) {
            if ($work->start_date == $work->end_date) {
                array_push($working, $work->start_date);
            } else {

                $start = Carbon::create($work->start_date->toDateString());
                $end = Carbon::create($work->end_date->toDateString());
                for ($dt = $start; $dt <= $end; $dt->addDay()) {
                    array_push($working, $dt->toDateString());
                }
            }
        }

        if ($data['type'] == 'everyweek') {
            unset($date[array_search($data['start_date'], $date)]);
            $start = Carbon::create($data['start_date']);
            $end = Carbon::create($data['end_date']);
            for ($dt = $start; $dt <= $end; $dt->addDays(7)) {

                if (in_array($dt->toDateString(), $date)) {
                    return Response::json(['errors' => trans('calendar_departments.have_a_day_off')]);
                }
                if (in_array($dt->toDateString(), $working)) {
                    return Response::json(['errors' => trans('calendar_departments.have_a_work_off')]);
                }
            }
        }
        if ($data['type'] == 'multiple') {
            unset($date[array_search($data['start_date'], $date)]);
            $start = Carbon::create($data['start_date']);
            $end = Carbon::create($data['end_date']);
            for ($dt = $start; $dt <= $end; $dt->addDay()) {
                if (in_array($dt->toDateString(), $date)) {
                    return Response::json(['errors' => trans('calendar_departments.have_a_day_off')]);
                }
                if (in_array($dt->toDateString(), $working)) {
                    return Response::json(['errors' => trans('calendar_departments.have_a_work_off')]);
                }
            }
        }


        return Response::json(['success' => '1']);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->merge(['status' => intval($request->input('status', 0))]);
            $data = $request->all();
            $data['from_type'] ?? $data['from_type'] = 'MORNING';
            $data['to_type'] ?? $data['to_type'] = 'AFTERNOON';
            unset($data['day_click'], $data['id']);
            $validator = Validator::make($data, [
                'type' => 'required',
                'reason' => 'required',
            ]);
            if ($request->get('type') == 'multiple' || $request->get('type') == 'holiday') {
                $data['start_date'] = Carbon::createFromFormat('d/m/Y', $request->get('start_date'));
                $data['end_date'] != null ? $data['end_date'] = Carbon::createFromFormat('d/m/Y', $request->get('end_date')) : '';
                $validator = Validator::make($data, [
                    'end_date' => 'required',
                    'type' => 'required',
                    'reason' => 'required',
                ]);
            }
            $validator->setAttributeNames(trans('calendar_departments'));
            if ($validator->passes()) {
                if ($request->get('status') == 0) {
                    $data['start_date'] = Carbon::createFromFormat('d/m/Y', $request->get('start_date'));
                    $data['end_date'] = Carbon::createFromFormat('d/m/Y', $request->get('end_date'));
                    $datas = array_merge($data, [
                        'start_timestamps' => strtotime($data['start_date']),
                        'end_timestamps' => strtotime($data['end_date']),
                        'created_by' => $request->user()->id
                    ]);
                    CalendarDepartment::create($datas);
                    DB::commit();
                }
                if ($request->get('status') == 1) {
                    $groupsId = DB::table('department_relationships')->where('department_id', $request->get('department_id'))->first()->group_id;
                    $department = DB::table('department_relationships')->where('group_id', $groupsId)->get();
                    foreach ($department as $departmentName) {
                        $data['start_date'] = Carbon::createFromFormat('d/m/Y', $request->get('start_date'));
                        $data['end_date'] != null ? $data['end_date'] = Carbon::createFromFormat('d/m/Y', $request->get('end_date')) : '';
                        $datas = array_merge($data, [
                            'start_timestamps' => strtotime($data['start_date']),
                            'end_timestamps' => strtotime($data['end_date']),
                            'department_id' => $departmentName->department_id,
                            'created_by' => $request->user()->id
                        ]);
                        CalendarDepartment::create($datas);
                        DB::commit();
                    }
                }
                Session::flash('message', trans('system.success'));
                Session::flash('alert-class', 'success');
                return Response::json(['success' => '1']);
            }

            return Response::json(['errors' => $validator->errors()]);
        } catch (\Exception $exception) {
            DB::rollBack();
            return Response::json(['errors' => trans('system.have_an_error')]);
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $request->merge(['status' => intval($request->input('status', 0))]);
            $data = $request->all();
            $old = CalendarDepartment::find($data['id']);
            unset($data['day_click'], $data['id'], $data['_token']);
            $validator = Validator::make($data, [
                'type' => 'required',
                'reason' => 'required',
            ]);
            if ($request->get('type') == 'multiple' || $request->get('type') == 'holiday') {
                $data['start_date'] = Carbon::createFromFormat('d/m/Y', $request->get('start_date'));
                $data['end_date'] = $data['end_date'] != null ?  Carbon::createFromFormat('d/m/Y', $request->get('end_date')) : '';
                $validator = Validator::make($data, [
                    'end_date' => 'required',
                    'type' => 'required',
                    'reason' => 'required',
                ]);
            }
            $validator->setAttributeNames(trans('calendar_departments'));
            if ($validator->passes()) {
                //status = 0 là k auto lưu sang phòng ban cừng tên (giờ chỉ dùng có này)
                if ($request->get('status') == 0) {
                    $data['start_date'] = Carbon::createFromFormat('d/m/Y', $request->get('start_date'))->toDateString();
                    $data['end_date'] = Carbon::createFromFormat('d/m/Y', $request->get('end_date'))->toDateString();
                    $dayOff = CalendarDepartment::find($id);
                    if (is_null($dayOff)) throw new \Exception('Dữ liệu bị thay đổi, f5!');

                    $dayEveryweek = [];
                    if ($dayOff->type == 'everyweek') {
                        $start = Carbon::create($dayOff->start_date);
                        $end = Carbon::create($dayOff->end_date);
                        for ($dt = $start; $dt <= $end; $dt->addDays(7)) {
                            array_push($dayEveryweek, strtotime($dt->toDateString()));
                        }
                    }
                    $datas = array_merge($data, [
                        'start_timestamps' => strtotime($data['start_date']),
                        'end_timestamps' => strtotime($data['end_date']),
                        'created_by' => $request->user()->id
                    ]);
                    foreach ($data as $key => $value) {
                        if ($old->$key != $value) {
                            DepartmentDayOffLogs::create([
                                'day_off_id' => $old->id,
                                'field' => $key,
                                'old_data' => $old->$key,
                                'new_data' => $value,
                                'note' => trans('calendar_departments.note'),
                                'action_by' => $request->user()->id,
                                'action_at' => date("Y-m-d H:i:s"),
                            ]);
                            //DB::commit();
                        }
                    }
                    CalendarDepartment::create($datas);
                    //DB::commit();
                    if ($datas['start_date'] == $dayOff->start_date && $datas['end_date'] != $dayOff->end_date) {
                        if ($dayOff->type == 'everyweek' && $datas['type'] == 'everyweek') {
                            $date = [];
                            foreach ($dayEveryweek as $days) {
                                if ($days > $datas['end_timestamps']) {
                                    array_push($date, $days);
                                }
                            }
                            $newStartTimeStamps = min($date);
                            $newStartDate = date('Y-m-d', $newStartTimeStamps);
                        } else {
                            $newStartTimeStamps = strtotime('+1 day', $datas['end_timestamps']);
                            $newStartDate = date('Y-m-d', $newStartTimeStamps);
                        }
                        if ($newStartDate == $dayOff->end_date) {
                            $dayOff->update([
                                'type' => 'one',
                                'start_date' => $dayOff->end_date,
                                'start_timestamps' => $dayOff->end_timestamps,
                                'from_type' => $dayOff->to_type == 'AFTERNOON' && $dayOff->type == 'multiple' ? 'MORNING' : $dayOff->to_type,
                            ]);
                            //DB::commit();
                        } else {
                            $dayOff->update([
                                'start_date' => $newStartDate,
                                'start_timestamps' => $newStartTimeStamps,
                                'from_type' => $dayOff->to_type == 'AFTERNOON' && $dayOff->type == 'multiple' ? 'MORNING' : $dayOff->to_type,

                            ]);
                            //DB::commit();
                        }
                    } else if ($datas['end_date'] == $dayOff->end_date && $datas['start_date'] != $dayOff->start_date) {
                        if ($dayOff->type == 'everyweek' && $datas['type'] == 'everyweek') {
                            $newEndTimeStamps = strtotime('-7 day', $datas['start_timestamps']);
                            $newEndDate = date('Y-m-d', $newEndTimeStamps);
                        } else {
                            $newEndTimeStamps = strtotime('-1 day', $datas['start_timestamps']);
                            $newEndDate = date('Y-m-d', $newEndTimeStamps);
                        }
                        if ($newEndDate == $dayOff->start_date) {
                            $dayOff->update([
                                'type' => 'one',
                                'end_date' => $dayOff->start_date,
                                'end_timestamps' => $dayOff->start_timestamps,
                                'to_type' => $dayOff->from_type == 'MORNING' && $dayOff->type == 'multiple' ? 'AFTERNOON' : $dayOff->from_type,
                            ]);
                            //DB::commit();
                        } else {
                            $dayOff->update([
                                'end_date' => $newEndDate,
                                'end_timestamps' => $newEndTimeStamps,
                                'to_type' => $dayOff->from_type == 'MORNING' && $dayOff->type == 'multiple' ? 'AFTERNOON' : $dayOff->from_type,

                            ]);
                            //DB::commit();
                        }
                    } else if ($datas['end_date'] == $dayOff->end_date && $datas['start_date'] == $dayOff->start_date) {
                        $dayOff->forceDelete();
                        //DB::commit();
                    } else {
                        if ($dayOff->type == 'everyweek' && $datas['type'] == 'everyweek') {
                            $date = [];
                            foreach ($dayEveryweek as $days) {
                                if ($days > $datas['end_timestamps']) {
                                    array_push($date, $days);
                                }
                            }
                            $newStartTimeStamps = min($date);
                            $newStartDate = date('Y-m-d', $newStartTimeStamps);
                            $newEndTimeStamps = strtotime('-7 day', $datas['start_timestamps']);
                            $newEndDate = date('Y-m-d', $newEndTimeStamps);
                        } elseif ($dayOff->type == 'everyweek' && $datas['type'] == 'one') {
                            $date = [];
                            foreach ($dayEveryweek as $days) {
                                if ($days > $datas['end_timestamps']) {
                                    array_push($date, $days);
                                }
                            }
                            $newStartTimeStamps = min($date);
                            $newStartDate = date('Y-m-d', $newStartTimeStamps);
                            $newEndTimeStamps = strtotime('-7 day', $datas['start_timestamps']);
                            $newEndDate = date('Y-m-d', $newEndTimeStamps);
                        }
                        else {
                            $newStartTimeStamps = strtotime('+1 day', $datas['end_timestamps']);
                            $newStartDate = date('Y-m-d', $newStartTimeStamps);
                            $newEndTimeStamps = strtotime('-1 day', $datas['start_timestamps']);
                            $newEndDate = date('Y-m-d', $newEndTimeStamps);
                        }
                        CalendarDepartment::create([
                            'categories' => $dayOff->categories,
                            'type' => $newStartDate == $dayOff->end_date ? 'one' : $dayOff->type,
                            'start_date' => $newStartDate,
                            'start_timestamps' => $newStartTimeStamps,
                            'from_type' => $dayOff->from_type,
                            'end_date' => $dayOff->end_date,
                            'end_timestamps' => $dayOff->end_timestamps,
                            'to_type' => $dayOff->to_type,
                            'reason' => $dayOff->reason,
                            'status' => $dayOff->status,
                            'department_id' => $dayOff->department_id,
                            'created_by' => $dayOff->created_by,
                        ]);
                        $dayOff->update([
                            'type' => $newEndDate == $dayOff->start_date ? 'one' : $dayOff->type,
                            'end_date' => $newEndDate,
                            'end_timestamps' => $newEndTimeStamps,
                            'to_type' => $dayOff->type == 'multiple' ? 'AFTERNOON' : $dayOff->to_type
                        ]);
                        //DB::commit();
                    }
                }
                if ($request->get('status') == 1) {
                    $date = CalendarDepartment::find($id);
                    $data['start_date'] = Carbon::createFromFormat('d/m/Y', $request->get('start_date'))->toDateString();
                    $data['end_date'] = Carbon::createFromFormat('d/m/Y', $request->get('end_date'))->toDateString();
                    $dayEveryweek = [];
                    if ($date->type == 'everyweek') {
                        $start = Carbon::create($date->start_date);
                        $end = Carbon::create($date->end_date);
                        for ($dt = $start; $dt <= $end; $dt->addDays(7)) {
                            array_push($dayEveryweek, strtotime($dt->toDateString()));
                        }
                    }
                    $dates = [];
                    foreach ($dayEveryweek as $days) {
                        if ($days > strtotime($data['end_date'])) {
                            array_push($dates, $days);
                        }
                    }
                    $groupsId = DB::table('department_relationships')->where('department_id', $request->get('department_id'))->first()->group_id;
                    $department = DB::table('department_relationships')->where('group_id', $groupsId)->get();

                    foreach ($department as $departmentName) {

                        $day = CalendarDepartment::where('department_id', $departmentName->department_id)
                            ->where('start_date', $date->start_date)
                            ->get();
                        if (count($day) == 0) {
                            $datas = array_merge($data, [
                                'start_timestamps' => strtotime($data['start_date']),
                                'end_timestamps' => strtotime($data['end_date']),
                                'department_id' => $departmentName->department_id,
                                'created_by' => $request->user()->id
                            ]);
                            CalendarDepartment::create($datas);
                            DB::commit();
                        } else {
                            foreach ($day as $dayOff) {

                                $oldd = CalendarDepartment::find($dayOff->id);

                                $datas = array_merge($data, [
                                    'start_timestamps' => strtotime($data['start_date']),
                                    'end_timestamps' => strtotime($data['end_date']),
                                    'created_by' => $request->user()->id,
                                    'department_id' => $departmentName->department_id
                                ]);
                                CalendarDepartment::create($datas);
                                DB::commit();
                                foreach ($datas as $key => $value) {
                                    if ($oldd->$key != $value) {
                                        DepartmentDayOffLogs::create([
                                            'day_off_id' => $oldd->id,
                                            'field' => $key,
                                            'old_data' => $oldd->$key,
                                            'new_data' => $value,
                                            'note' => trans('calendar_departments.note'),
                                            'action_by' => $request->user()->id,
                                            'action_at' => date("Y-m-d H:i:s"),
                                        ]);
                                        DB::commit();
                                    }
                                }
                                if ($datas['start_date'] == $dayOff->start_date && $datas['end_date'] != $dayOff->end_date) {

                                    if ($dayOff->type == 'everyweek' && $datas['type'] == 'everyweek') {
                                        $newStartTimeStamps =min($dates);
                                        $newStartDate = date('Y-m-d', $newStartTimeStamps);
                                    }
                                    else {
                                        $newStartTimeStamps = strtotime('+1 day', $datas['end_timestamps']);
                                        $newStartDate = date('Y-m-d', $newStartTimeStamps);
                                    }
                                    if ($newStartDate == $dayOff->end_date) {
                                        $dayOff->update([
                                            'type' => 'one',
                                            'start_date' => $dayOff->end_date,
                                            'start_timestamps' => $dayOff->end_timestamps,
                                            'from_type' => $dayOff->to_type == 'AFTERNOON' && $dayOff->type == 'multiple' ? 'MORNING' : $dayOff->to_type,
                                        ]);
                                        DB::commit();
                                    }
                                    else {
                                        $dayOff->update([
                                            'start_date' => $newStartDate,
                                            'start_timestamps' => $newStartTimeStamps,
                                            'from_type' => $dayOff->to_type == 'AFTERNOON' && $dayOff->type == 'multiple' ? 'MORNING' : $dayOff->to_type,

                                        ]);
                                        DB::commit();

                                    }
                                }
                                else if ($datas['end_date'] == $dayOff->end_date && $datas['start_date'] != $dayOff->start_date) {
                                    if ($dayOff->type == 'everyweek' && $datas['type'] == 'everyweek') {
                                        $newEndTimeStamps = strtotime('-7 day', $datas['start_timestamps']);
                                        $newEndDate = date('Y-m-d', $newEndTimeStamps);
                                    } else {
                                        $newEndTimeStamps = strtotime('-1 day', $datas['start_timestamps']);
                                        $newEndDate = date('Y-m-d', $newEndTimeStamps);
                                    }
                                    if ($newEndDate == $dayOff->start_date) {
                                        $dayOff->update([
                                            'type' => 'one',
                                            'end_date' => $dayOff->start_date,
                                            'end_timestamps' => $dayOff->start_timestamps,
                                            'to_type' => $dayOff->from_type == 'MORNING' && $dayOff->type == 'multiple' ? 'AFTERNOON' : $dayOff->from_type,
                                        ]);
                                        DB::commit();
                                    } else {
                                        $dayOff->update([
                                            'end_date' => $newEndDate,
                                            'end_timestamps' => $newEndTimeStamps,
                                            'to_type' => $dayOff->from_type == 'MORNING' && $dayOff->type == 'multiple' ? 'AFTERNOON' : $dayOff->from_type,

                                        ]);
                                        DB::commit();
                                    }
                                }
                                else if ($datas['end_date'] == $dayOff->end_date && $datas['start_date'] == $dayOff->start_date) {
                                    $dayOff->forceDelete();
                                    DB::commit();
                                }
                                else {
                                    if ($dayOff->type == 'everyweek' && $datas['type'] == 'everyweek') {
                                        $date = [];
                                        foreach ($dayEveryweek as $days) {
                                            if ($days > $datas['end_timestamps']) {
                                                array_push($date, $days);
                                            }
                                        }
                                        $newStartTimeStamps = min($date);
                                        $newStartDate = date('Y-m-d', $newStartTimeStamps);
                                        $newEndTimeStamps = strtotime('-7 day', $datas['start_timestamps']);
                                        $newEndDate = date('Y-m-d', $newEndTimeStamps);
                                    } else {
                                        $newStartTimeStamps = strtotime('+1 day', $datas['end_timestamps']);
                                        $newStartDate = date('Y-m-d', $newStartTimeStamps);
                                        $newEndTimeStamps = strtotime('-1 day', $datas['start_timestamps']);
                                        $newEndDate = date('Y-m-d', $newEndTimeStamps);
                                    }
                                    CalendarDepartment::create([
                                        'categories' => $dayOff->categories,
                                        'type' => $newStartDate == $dayOff->end_date ? 'one' : $dayOff->type,
                                        'start_date' => $newStartDate,
                                        'start_timestamps' => $newStartTimeStamps,
                                        'from_type' => 'MORNING',
                                        'end_date' => $dayOff->end_date,
                                        'end_timestamps' => $dayOff->end_timestamps,
                                        'to_type' => $dayOff->to_type,
                                        'reason' => $dayOff->reason,
                                        'status' => $dayOff->status,
                                        'department_id' => $dayOff->department_id,
                                        'created_by' => $dayOff->created_by,
                                    ]);
                                    $dayOff->update([
                                        'type' => $dayOff->start_date == $newEndDate ? 'one' : $dayOff->type,
                                        'end_date' => $newEndDate,
                                        'end_timestamps' => $newEndTimeStamps,
                                        'to_type' => 'AFTERNOON'
                                    ]);
                                    DB::commit();
                                }
                            }
                        }
                    }
                }
                DB::commit();
                Session::flash('message', trans('system.success'));
                Session::flash('alert-class', 'success');
                return Response::json(['success' => '1']);
            }
            return Response::json(['errors' => $validator->errors()]);
        } catch (\Exception $exception) {
            DB::rollBack();
            return Response::json(['errors' => trans('system.have_an_error')]);
        }
    }

    public function updateAll(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $request->merge(['status' => intval($request->input('status', 0))]);
            $data = $request->all();
            $old = CalendarDepartment::find($data['id']);
            unset($data['day_click'], $data['id'], $data['_token']);
            $validator = Validator::make($data, [
                'type' => 'required',
                'reason' => 'required',
            ]);
            if ($request->get('type') == 'multiple' || $request->get('type') == 'holiday') {
                $data['start_date'] = Carbon::createFromFormat('d/m/Y', $request->get('start_date'));
                $data['end_date'] != null ? $data['end_date'] = Carbon::createFromFormat('d/m/Y', $request->get('end_date')) : '';
                $validator = Validator::make($data, [
                    'end_date' => 'required',
                    'type' => 'required',
                    'reason' => 'required',
                ]);
            }
            $validator->setAttributeNames(trans('calendar_departments'));
            if ($validator->passes()) {
                if ($request->get('status') == 0) {
                    $data['start_date'] = Carbon::createFromFormat('d/m/Y', $request->get('start_date'))->toDateString();
                    $data['end_date'] = Carbon::createFromFormat('d/m/Y', $request->get('end_date'))->toDateString();
                    $dayOff = CalendarDepartment::find($id);
                    $datas = array_merge($data, [
                        'start_timestamps' => strtotime($data['start_date']),
                        'end_timestamps' => strtotime($data['end_date']),
                        'created_by' => $request->user()->id
                    ]);
                    foreach ($data as $key => $value) {
                        if ($old->$key != $value) {
                            DepartmentDayOffLogs::create([
                                'day_off_id' => $old->id,
                                'field' => $key,
                                'old_data' => $old->$key,
                                'new_data' => $value,
                                'note' => trans('calendar_departments.note.changeall'),
                                'action_by' => $request->user()->id,
                                'action_at' => date("Y-m-d H:i:s"),
                            ]);
                            DB::commit();
                        }
                    }
                    $dayOff->update($datas);
                    DB::commit();
                }
                if ($request->get('status') == 1) {
                    $date = CalendarDepartment::find($id);
                    $groupsId = DB::table('department_relationships')->where('department_id', $request->get('department_id'))->first()->group_id;
                    $department = DB::table('department_relationships')->where('group_id', $groupsId)->get();
                    foreach ($department as $departmentName) {
                        $day = CalendarDepartment::where('department_id', $departmentName->department_id)
                            ->where('start_date', $date->start_date)
                            ->get();
                        if (count($day) == 0) {
                            $datas = array_merge($data, [
                                'start_timestamps' => strtotime($data['start_date']),
                                'end_timestamps' => strtotime($data['end_date']),
                                'department_id' => $departmentName->department_id,
                                'created_by' => $request->user()->id
                            ]);
                            CalendarDepartment::create($datas);
                            DB::commit();
                        } else {
                            foreach ($day as $dayOff) {
                                $old = CalendarDepartment::find($dayOff->id);
                                $data['start_date'] = Carbon::createFromFormat('d/m/Y', $request->get('start_date'))->toDateString();
                                $data['end_date'] = Carbon::createFromFormat('d/m/Y', $request->get('end_date'))->toDateString();
                                $datas = array_merge($data, [
                                    'start_timestamps' => strtotime($data['start_date']),
                                    'end_timestamps' => strtotime($data['end_date']),
                                    'created_by' => $request->user()->id,
                                    'department_id' => $dayOff->department_id
                                ]);
                                $dayOff->update($datas);
                                DB::commit();
                                foreach ($data as $key => $value) {
                                    if ($old->$key != $value) {
                                        DepartmentDayOffLogs::create([
                                            'day_off_id' => $old->id,
                                            'field' => $key,
                                            'old_data' => $old->$key,
                                            'new_data' => $value,
                                            'note' => trans('calendar_departments.note.changeall'),
                                            'action_by' => $request->user()->id,
                                            'action_at' => date("Y-m-d H:i:s"),
                                        ]);
                                        DB::commit();
                                    }
                                }
                            }
                        }
                    }
                }
                Session::flash('message', trans('system.success'));
                Session::flash('alert-class', 'success');
                return Response::json(['success' => '1']);
            }
            return Response::json(['errors' => $validator->errors()]);
        } catch (\Exception $exception) {
            DB::rollBack();
//            throw new \Exception($exception->getMessage());
            return Response::json(['errors' => trans('system.have_an_error')]);
        }
    }

    public function delete(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->all();
            unset($data['id']);
            $data['start_date'] = Carbon::createFromFormat('d/m/Y', $request->get('start_date'))->toDateString();
            $dayOff = CalendarDepartment::where('start_date', $data['start_date'])
                ->where('department_id', $data['department_id'])
                ->first();

            $dayOff->update([
                'deleted_by' => $request->user()->id
            ]);
            $dayOff->delete();
            DB::commit();
            Session::flash('message', trans('system.success'));
            Session::flash('alert-class', 'success');
            return Response::json(['success' => '1']);
        } catch (\Exception $exception) {
            DB::rollback();
            return Response::json(['errors' => trans('system.have_an_error')]);
        }
    }

    public function deleteMulti(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->all();
            unset($data['id']);
            $data['start_date'] = Carbon::createFromFormat('d/m/Y', $request->get('start_date'))->toDateString();
            $groupsId = DB::table('department_relationships')->where('department_id', $request->get('department_id'))->first()->group_id;
            $department = DB::table('department_relationships')->where('group_id', $groupsId)->get();
            foreach ($department as $departmentName) {
                $day = CalendarDepartment::where('start_date', $data['start_date'])
                    ->where('department_id', $departmentName->department_id)
                    ->get();
                foreach ($day as $dayOff) {
                    $dayOff->deleted_by = $request->user()->id;
                    $dayOff->save();
                    $dayOff->delete();
                    DB::commit();

                }
            }
            Session::flash('message', trans('system.success'));
            Session::flash('alert-class', 'success');
            return Response::json(['success' => '1']);
        } catch (\Exception $exception) {
            DB::rollback();
            return Response::json(['errors' => trans('system.have_an_error')]);
        }
    }

    public function deleteOne(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $data = $request->all();
            unset($data['id']);


            $data['start_date'] = Carbon::createFromFormat('d/m/Y', $request->get('start_date'))->toDateString();
            $data['end_date'] = Carbon::createFromFormat('d/m/Y', $request->get('end_date'))->toDateString();
            $data['day_click'] = Carbon::createFromFormat('d/m/Y', $request->get('day_click'))->toDateString();
            $dayOff = CalendarDepartment::find($id);

            $datas = array_merge($data, [
                'start_timestamps' => strtotime($data['start_date']),
                'end_timestamps' => strtotime($data['end_date']),
                'created_by' => $request->user()->id
            ]);

            if ($datas['day_click'] == $dayOff->start_date) {
                if ($datas['type'] == 'everyweek') {
                    $newStartTimeStamps = strtotime('+7 day', strtotime($datas['day_click']));
                    $newStartDate = date('Y-m-d', $newStartTimeStamps);
                } else {
                    $newStartTimeStamps = strtotime('+1 day', strtotime($datas['day_click']));
                    $newStartDate = date('Y-m-d', $newStartTimeStamps);
                }
                if ($newStartDate == $dayOff->end_date) {
                    $dayOff->update([
                        'type' => 'one',
                        'start_date' => $dayOff->end_date,
                        'start_timestamps' => $dayOff->end_timestamps,
                        'from_type' => $dayOff->to_type == 'AFTERNOON' && $datas['type'] == 'multiple' ? 'MORNING' : $dayOff->to_type,
                    ]);
                    DB::commit();
                } else {
                    $dayOff->update([
                        'start_date' => $newStartDate,
                        'start_timestamps' => $newStartTimeStamps,
                        'from_type' => $dayOff->to_type == 'AFTERNOON' && $datas['type'] == 'multiple' ? 'MORNING' : $dayOff->to_type,

                    ]);
                    DB::commit();
                }
            } else if ($datas['day_click'] == $dayOff->end_date) {
                if ($datas['type'] == 'everyweek') {
                    $newEndTimeStamps = strtotime('-7 day', strtotime($datas['day_click']));
                    $newEndDate = date('Y-m-d', $newEndTimeStamps);
                } else {
                    $newEndTimeStamps = strtotime('-1 day', strtotime($datas['day_click']));
                    $newEndDate = date('Y-m-d', $newEndTimeStamps);
                }
                if ($newEndDate == $dayOff->start_date) {
                    $dayOff->update([
                        'type' => 'one',
                        'end_date' => $dayOff->start_date,
                        'end_timestamps' => $dayOff->start_timestamps,
                        'to_type' => $dayOff->from_type == 'MORNING' && $datas['type'] == 'multiple' ? 'AFTERNOON' : $dayOff->from_type,
                    ]);
                    DB::commit();
                } else {
                    $dayOff->update([
                        'end_date' => $newEndDate,
                        'end_timestamps' => $newEndTimeStamps,
                        'to_type' => $dayOff->from_type == 'MORNING' && $datas['type'] == 'multiple' ? 'AFTERNOON' : $dayOff->from_type,

                    ]);
                    DB::commit();
                }
            } else {
                if ($datas['type'] == 'everyweek') {
                    $newStartTimeStamps = strtotime('+7 day', strtotime($datas['day_click']));
                    $newStartDate = date('Y-m-d', $newStartTimeStamps);
                    $newEndTimeStamps = strtotime('-7 day', strtotime($datas['day_click']));
                    $newEndDate = date('Y-m-d', $newEndTimeStamps);
                } else {
                    $newStartTimeStamps = strtotime('+1 day', strtotime($datas['day_click']));
                    $newStartDate = date('Y-m-d', $newStartTimeStamps);
                    $newEndTimeStamps = strtotime('-1 day', strtotime($datas['day_click']));
                    $newEndDate = date('Y-m-d', $newEndTimeStamps);
                }

                CalendarDepartment::create([
                    'categories' => $dayOff->categories,
                    'type' => $newStartDate == $dayOff->end_date ? 'one' : $dayOff->type,
                    'start_date' => $newStartDate,
                    'start_timestamps' => $newStartTimeStamps,
                    'from_type' => 'MORNING',
                    'end_date' => $dayOff->end_date,
                    'end_timestamps' => $dayOff->end_timestamps,
                    'to_type' => $dayOff->to_type,
                    'reason' => $dayOff->reason,
                    'status' => $dayOff->status,
                    'department_id' => $dayOff->department_id,
                    'created_by' => $dayOff->created_by,
                ]);
                $dayOff->update([
                    'type' => $dayOff->start_date == $newEndDate ? 'one' : $dayOff->type,
                    'end_date' => $newEndDate,
                    'end_timestamps' => $newEndTimeStamps,
                    'to_type' => $datas['type'] == 'multiple' ? 'AFTERNOON' : $dayOff->to_type
                ]);
                DB::commit();
            }
            Session::flash('message', trans('system.success'));
            Session::flash('alert-class', 'success');
            return Response::json(['success' => '1']);
        } catch (\Exception $exception) {
            DB::rollBack();
            return Response::json(['errors' => trans('system.have_an_error')]);
        }
    }

    public function deleteOneMulti(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $data = $request->all();
            $date = CalendarDepartment::find($id);

            $groupsId = DB::table('department_relationships')->where('department_id', $request->get('department_id'))->first()->group_id;

            $department = DB::table('department_relationships')->where('group_id', $groupsId)->get();
            foreach ($department as $departmentName) {
                $day = CalendarDepartment::where('department_id', $departmentName->department_id)
                    ->where('start_date', $date->start_date)
                    ->get();
                foreach ($day as $dayOff) {
                    $data['start_date'] = Carbon::createFromFormat('d/m/Y', $request->get('start_date'))->toDateString();
                    $data['end_date'] = Carbon::createFromFormat('d/m/Y', $request->get('end_date'))->toDateString();
                    $data['day_click'] = Carbon::createFromFormat('d/m/Y', $request->get('day_click'))->toDateString();
                    $datas = array_merge($data, [
                        'start_timestamps' => strtotime($data['start_date']),
                        'end_timestamps' => strtotime($data['end_date']),
                        'created_by' => $request->user()->id,
                        'department_id' => $departmentName->id
                    ]);
                    if ($datas['day_click'] == $dayOff->start_date) {
                        if ($datas['type'] == 'everyweek') {
                            $newStartTimeStamps = strtotime('+7 day', strtotime($datas['day_click']));
                            $newStartDate = date('Y-m-d', $newStartTimeStamps);
                        } else {
                            $newStartTimeStamps = strtotime('+1 day', strtotime($datas['day_click']));
                            $newStartDate = date('Y-m-d', $newStartTimeStamps);
                        }
                        if ($newStartDate == $dayOff->end_date) {
                            $dayOff->update([
                                'type' => 'one',
                                'start_date' => $dayOff->end_date,
                                'start_timestamps' => $dayOff->end_timestamps,
                                'from_type' => $dayOff->to_type == 'AFTERNOON' && $datas['type'] == 'multiple' ? 'MORNING' : $dayOff->to_type,
                            ]);
                            DB::commit();
                        } else {
                            $dayOff->update([
                                'start_date' => $newStartDate,
                                'start_timestamps' => $newStartTimeStamps,
                                'from_type' => $dayOff->to_type == 'AFTERNOON' && $datas['type'] == 'multiple' ? 'MORNING' : $dayOff->to_type,

                            ]);
                            DB::commit();
                        }
                    } else if ($datas['day_click'] == $dayOff->end_date) {
                        if ($datas['type'] == 'everyweek') {
                            $newEndTimeStamps = strtotime('-7 day', strtotime($datas['day_click']));
                            $newEndDate = date('Y-m-d', $newEndTimeStamps);
                        } else {
                            $newEndTimeStamps = strtotime('-1 day', strtotime($datas['day_click']));
                            $newEndDate = date('Y-m-d', $newEndTimeStamps);
                        }
                        if ($newEndDate == $dayOff->start_date) {
                            $dayOff->update([
                                'type' => 'one',
                                'end_date' => $dayOff->start_date,
                                'end_timestamps' => $dayOff->start_timestamps,
                                'to_type' => $dayOff->from_type == 'MORNING' && $datas['type'] == 'multiple' ? 'AFTERNOON' : $dayOff->from_type,
                            ]);
                            DB::commit();
                        } else {
                            $dayOff->update([
                                'end_date' => $newEndDate,
                                'end_timestamps' => $newEndTimeStamps,
                                'to_type' => $dayOff->from_type == 'MORNING' && $datas['type'] == 'multiple' ? 'AFTERNOON' : $dayOff->from_type,

                            ]);
                            DB::commit();
                        }
                    } else {
                        if ($datas['type'] == 'everyweek') {
                            $newStartTimeStamps = strtotime('+7 day', strtotime($datas['day_click']));
                            $newStartDate = date('Y-m-d', $newStartTimeStamps);
                            $newEndTimeStamps = strtotime('-7 day', strtotime($datas['day_click']));
                            $newEndDate = date('Y-m-d', $newEndTimeStamps);
                        } else {
                            $newStartTimeStamps = strtotime('+1 day', strtotime($datas['day_click']));
                            $newStartDate = date('Y-m-d', $newStartTimeStamps);
                            $newEndTimeStamps = strtotime('-1 day', strtotime($datas['day_click']));
                            $newEndDate = date('Y-m-d', $newEndTimeStamps);
                        }

                        CalendarDepartment::create([
                            'categories' => $dayOff->categories,
                            'type' => $newStartDate == $dayOff->end_date ? 'one' : $dayOff->type,
                            'start_date' => $newStartDate,
                            'start_timestamps' => $newStartTimeStamps,
                            'from_type' => 'MORNING',
                            'end_date' => $dayOff->end_date,
                            'end_timestamps' => $dayOff->end_timestamps,
                            'to_type' => $dayOff->to_type,
                            'reason' => $dayOff->reason,
                            'status' => $dayOff->status,
                            'department_id' => $dayOff->department_id,
                            'created_by' => $dayOff->created_by,
                        ]);
                        $dayOff->update([
                            'type' => $dayOff->start_date == $newEndDate ? 'one' : $dayOff->type,
                            'end_date' => $newEndDate,
                            'end_timestamps' => $newEndTimeStamps,
                            'to_type' => $datas['type'] == 'multiple' ? 'AFTERNOON' : $dayOff->to_type
                        ]);
                        DB::commit();
                    }
                }
            }
            Session::flash('message', trans('system.success'));
            Session::flash('alert-class', 'success');
            return Response::json(['success' => '1']);
        } catch (\Exception $exception) {
            DB::rollBack();
            return Response::json(['errors' => trans('system.have_an_error')]);
        }
    }

    public function copy(Request $request){
        $response = ['message' => trans('system.have_an_error'), 'data' => ""];
        $statusCode = 200;
        if ($request->ajax()) {
            try {
                DB::beginTransaction();
                $request->merge(['copy_group' => $request->input('copy_group', 0)]);
                $data = $request->all();
                if (!$data['department_id'] && !$data['copy_group']) {
                    $message = "Chưa chọn phòng ban.";
                    throw new \Exception($message, 1);
                }
                $deptCopyTo = [];
                if ($data['copy_group']) {
                    $deptOfGroups = Department::getDeptOffGroup($data['department_id_current']);
                    $deptCopyTo = $deptOfGroups;
                }
                if ($data['department_id'] && !in_array($data['department_id'], $deptCopyTo)) array_push($deptCopyTo, $data['department_id']);
                $departmentCurrent = CalendarDepartment::where('department_id',$data['department_id_current'])->whereYear('start_date',$data['year'])->whereYear('end_date',$data['year'])->get();
                CalendarDepartment::whereIn('department_id', $deptCopyTo)->whereYear('start_date',$data['year'])->whereYear('end_date',$data['year'])->forceDelete();
                if (count($departmentCurrent) > 0) {
                    foreach ($departmentCurrent as $dayOff){
                        foreach ($deptCopyTo as $deptId) {
                            $duplicate = $dayOff->replicate();
                            $duplicate->department_id = $deptId;
                            $duplicate->save();
                        }
                    }
                } else {
                    $message = "Phòng ban hiện tại chưa có lịch nghỉ.";
                    throw new \Exception($message, 1);
                }
                $response['message'] = trans('system.success');
                DB::commit();
            }
            catch (\Exception $e){
                DB::rollBack();
                if ($statusCode == 200) $statusCode = 500;
                $response['message'] = $e->getMessage();
            } finally {
                return response()->json($response, $statusCode);
            }
        } else {
            $statusCode = 405;
            return response()->json($response, $statusCode);
        }
    }

    public function getShiftByMonth(Request $request, $departmentId, $month, $year)
    {
        $search = $request->input('fullname');
        $getDateByMonth = $date = [];
        $department = Department::find($departmentId);

        for ($d = 1; $d <= 31; $d++) {
            $time=mktime(12, 0, 0, $month, $d, date('Y'));          
            if (date('m', $time) == $month)       
                $getDateByMonth[] = date('Y-m-d', $time);
        }
        foreach ($getDateByMonth as $key => $item) {
            $getDays[] = substr(Carbon::parse($item)->format('l'), 0, 3);
            $getDates[] = Carbon::parse($item)->format('d');
            $date[] = $item;
        }
        if (!empty($search)) {
            $users = User::where('department_id', $departmentId)->where('fullname', 'like', '%' . $search . '%')->get(['fullname', 'id', 'code']);
        } else {
            $users = User::where('department_id', $departmentId)->get(['fullname', 'id', 'code']);
        }
        $data = [
            'month' => $month,
            'year' => $year,
        ];
        
        if ($request->ajax()) {
            $output = '';
            $input = $request->all();
            if ($input['team_id'] == '') {
                return Response(1);
            }
            $team = Team::find(intval($input['team_id']));
            $ids = Team::where('id', intval($input['team_id']))->with('usersDetail')->get()->pluck('usersDetail.*.id')->toArray();
            array_push($ids[0], $team->user_id);
            $users = User::whereIn('id', $ids[0])->get(['fullname', 'id', 'code']);

            if ($users) {
                foreach ($users as $key => $user) {
                   
                    $output .= '<tr class="hover">';
                    $output .= '
                        <td>' . ($key + 1) . '</td>
                        <td>' . $user->fullname . '</td>
                        <td>' . $user->code . '</td>
                    ';
                    foreach ($date as $i => $item1) {
                        $output .= '
                        <td>' . Shift::getShiftUser($item1, $user->id) . '</td>
                        ';
                    }
                    $output .= '</tr>';
                }
            }

            return Response($output);
        }

        return view('backend.department.list-user-shift', compact('getDays', 'getDates', 'users', 'date', 'data', 'department'));
    }

    public function listTeam($departmentId)
    {
        $department = Department::find($departmentId);
        $teams = Team::where('department_id', $departmentId)->get();
        $teams->load('users', 'user');

        return view('backend.department.list-team', compact('user', 'teams', 'department'));
    }

    public function createTeam($departmentId)
    {
        $department = Department::find($departmentId);
        $user = Auth::user();

        $users = User::where('department_id', $departmentId)->whereNotIn('id', [$user->id])->get(['id', 'fullname', 'code', 'department_id']);
        $users->load('team');
        foreach ($users as $key => $item) {
            if ($item->hasRole('TP') || $item->hasRole('TPNS')) unset($users[$key]);
            $team = Team::where('user_id', $item->id)->first();
            if ($team) unset($users[$key]);
            if (!empty($item->team)) unset($users[$key]);
        }

        return view('backend.department.create-team', compact('users', 'department'));
    }

    public function storeTeam(Request $request)
    {
        $data = $request->all();
        $insert = [];
        if (empty($data['user_id'])) {
            Session::flash('message', 'Không có thành viên trong nhóm');
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.departments.create-team', $data['department_id']);
        }
        array_push($data['user_id'], $data['lead_user_id']);
        try {
			DB::beginTransaction();
			$team = Team::create([
                'name'        => $data['name'],
                'description' => $data['description'],
                'user_id'     => $data['lead_user_id'],
                'created_by'  => Auth::user()->id,
                'department_id' => $data['department_id']
            ]);
            foreach ($data['user_id'] as $key => $item) {
                $insert[] = [
                    'team_id' => $team->id,
                    'user_id' => $item,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
            }
            UserTeam::insert($insert);
			DB::commit();
		} catch (\Exception $e) {
			DB::rollBack();
			return back()->withErrors($e)->withInput();
		}

        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');
        return redirect()->route('admin.departments.list-team', $data['department_id']);
    }

    public function getAllUserTeam($id)
    {
        $team = Team::find($id);
        $team->load('user');
        if (empty($team)) {
            return \Response::json([
                'status' => 'FAIL',
                'message' => 'Không tìm thấy bản ghi'
            ]);
        }
        $team->load('usersDetail');

        return \Response::json([
            'status' => 'SUCCESS',
            'data'   => $team
        ]);
    }

    public function editTeam($id)
    {
        $user_ids = [];
        $team = Team::find($id);
        if (empty($team)) {
            Session::flash('message', trans('system.error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.departments.list-team');
        }
        $team->load('usersDetail');
        // dd($team->toArray());
        // $user = User::find(Auth::user()->id);
        $users = User::where('department_id', $team->department_id)->whereNotIn('id', [Auth::user()->id])->get(['id', 'fullname', 'code', 'department_id']);
        $users->load('teamOfLead', 'team');
        foreach ($users as $key => $item) {
            if (!empty($item->teamOfLead) && $item->teamOfLead->id != $team->id) unset($users[$key]);
            if (!empty($item->team) && $item->team->team_id != $team->id) unset($users[$key]);
        }
        foreach ($team->usersDetail as $key => $item) {
            $users_ids[] = $item->id; 
        }

        return view('backend.department.edit-team', compact('users', 'team', 'users_ids'));
    }

    public function saveEditTeam(Request $request, $id)
    {
        $data = $request->all();
        $insert = [];
        $team = Team::find($id);
        array_push($data['user_id'], $data['lead_user_id']);

        try {
			DB::beginTransaction();
			$team->update([
                'name'        => $data['name'],
                'description' => $data['description'],
                'user_id'     => $data['lead_user_id'],
                'created_by'  => Auth::user()->id
            ]);
            $team->users()->delete();

            foreach ($data['user_id'] as $key => $item) {
                $insert[] = [
                    'team_id' => $team->id,
                    'user_id' => $item,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
            }
            UserTeam::insert($insert);
			DB::commit();
		} catch (\Exception $e) {
			DB::rollBack();
			return back()->withErrors($e)->withInput();
		}

        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');
        return redirect()->route('admin.departments.list-team', $team->department_id);
    }

    public function deleteTeam($id)
    {
        $team = Team::find($id);
        $departmentId = $team->department_id;
        try {
			DB::beginTransaction();
            $team->users()->delete();
			$team->delete();

			DB::commit();
		} catch (\Exception $e) {
			DB::rollBack();
			return back()->withErrors($e)->withInput();
		}

        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');
        return redirect()->route('admin.departments.list-team', $departmentId);
    }

    public function searchUser(Request $request, $departmentId)
    {
        if ($request->ajax()) {
            if (!empty($request->q)) {
                $users = User::where('active', 1)->where('fullname', 'LIKE', '%' . $request->q . '%')->whereNotIn('department_id', [$departmentId])->whereNotIn('id', [1, 2, 3])->selectRaw('id, CONCAT(fullname, " - " ,code) as text')
                                    ->paginate(10)->toArray();
            } else {
                $users = User::where('active', 1)->whereNotIn('department_id', [$departmentId])->whereNotIn('id', [1, 2, 3])->selectRaw('id, CONCAT(fullname, " - " ,code) as text')
                                    ->paginate(10)->toArray();
            }
            
            if (count($users) > 0) return response()->json($users, 200);

            return response()->json(['error' => 'error', 'status' => 404]);
        }
    }
}
