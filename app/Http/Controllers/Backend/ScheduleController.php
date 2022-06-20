<?php

namespace App\Http\Controllers\Backend;

use App\Defines\Staff;
use App\Models\Company;
use App\Models\Contract;
use App\StaffFamily;
use App\User;
use Carbon\Carbon;
use App\StaffDayOff;
use App\Models\Shift;
use App\Defines\Schedule;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\CalendarDepartment;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ScheduleController extends Controller
{
    public function index()
    {
        $staff = Auth::user();
        $staffId = $staff->id;
        $departmentId = $staff->department_id;
        if (!$departmentId) {
            return view('backend.schedules.index', compact('departmentId'));
        }
        $deptData = DB::table('departments')
            ->where('id', $departmentId)
            ->first();
        if (is_null($deptData)) {
            Session::flash('message', 'Phòng ban không tồn tại');
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.home');
        }
        $typeDept = $deptData->type;

        if ($typeDept != \App\Define\Shift::OFFICE_TIME) {
            $workSchedules = Shift::getShift(Carbon::now()->copy()->startOfYear()->format('Y-m-d'), $staffId) ?? collect();
        }
        /*$this->handleStaffDayOffSameDeptDayOff(StaffDayOff::getStaffDayOff($staffId, 0, 1), CalendarDepartment::getDayOff($departmentId, 1), $staffId);*/
        $eventStaffs = StaffDayOff::getStaffDayOff($staffId);
        $dayOffDepartments = CalendarDepartment::getDayOff($departmentId);
        $eventStaffs = collect($eventStaffs);
        $dayOffDepartments = collect($dayOffDepartments);
        return view('backend.schedules.index', compact('eventStaffs', 'dayOffDepartments', 'typeDept', 'workSchedules', 'startWork', 'endWork', 'departmentId'));
    }

    public function store(Request $request)
    {
        $response = ['message' => trans('system.have_an_error'), 'data' => ""];
        $statusCode = 200;
        if ($request->ajax()) {
            try {
                $event = $request->input('event');
                $event['status'] = $event['status'] ?? 0;
                $event['staff_id'] = Auth::id();
                $event['user_id'] = Auth::id();
                $event['created_by'] = Auth::id();
                $event['color'] = trans('schedules.color_types.' . $event['code']);
                $event['title'] = trans('schedules.day-offs.' . $event['code']);
                $today = Carbon::today();
                $validator = \Validator::make($event, StaffDayOff::rules());
                $validator->setAttributeNames(trans('schedules'));
                if ($validator->fails()) {
                    $statusCode = 400;
                    $errors = $validator->errors()->all();
                    $response['message'] = $errors[0];
                    return response()->json($response, $statusCode);
                }
                //ko cho xin nghi tuong lai
                
                if (strtotime($today) > strtotime($event['start']) && $event['code'] != Schedule::DAY_OFF_BABE) {
                    $message = trans('schedules.error_past_day_off');
                    throw new \Exception($message, 1);
                }
				/*$currentYear = now()->year;
				if (Carbon::parse($event['start'])->year != $currentYear || Carbon::parse($event['end'])->year != $currentYear ) {
					$message =  trans('schedules.err_year');
					throw new \Exception($message, 1);
				}*/
                if ($event['total'] > StaffDayOff::countDayPendingCurrent(Auth::id()) && !in_array($event['code'], Schedule::dayOffNoLimit())) {
                    $message = trans('schedules.over_allow_day_offs');
                    throw new \Exception($message, 1);
                }
				/*if ($event['half_shift']) {
                    if (!Carbon::parse($event['start'])->isSaturday()) {
                        $message = trans('schedules.err_half_shift_2');
                        throw new \Exception($message, 1);
                    }
                    if ($event['start'] != $event['end']) {
                        $message = trans('schedules.err_half_shift');
                        throw new \Exception($message, 1);
                    }
                }*/
                $event = StaffDayOff::create($event);
                $response['message'] = trans('system.success');
                $response['data'] = $event;
                return response()->json($response, $statusCode);
            } catch (\Exception $e) {
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

    public function update(Request $request, $id)
    {
        $response = ['message' => trans('system.have_an_error'), 'data' => ""];
        $statusCode = 200;
        if ($request->ajax()) {
            try {
                $event = StaffDayOff::find(intval($id));
                if (is_null($event)) {
                    $message = trans('system.no_record_found');
                    throw new \Exception($message, 1);
                }
                $today = Carbon::today();
                if (strtotime($today) > strtotime($event->start) && $event['code'] != Schedule::DAY_OFF_BABE) {
                    $message = trans('schedules.error_past_day_off');
                    throw new \Exception($message, 1);
                }
                if ($event['status'] == 1) {
                    $message = trans('schedules.error_handle_day_off');
                    throw new \Exception($message, 1);
                }

                $eventNew = $request->input('event');
                $duration = $request->input('duration');
                if ($eventNew['total'] > StaffDayOff::countDayPendingCurrent(Auth::id(), $id) && !in_array($event['code'], Schedule::dayOffNoLimit())) {
                    $message = trans('schedules.over_allow_day_offs');
                    throw new \Exception($message, 1);
                }
                if ($duration) {
                    $eventNew = $event->toArray();
                    $eventNew['start'] = Carbon::createFromFormat('Y-m-d', $event['start'])->addDays($duration)->format('Y-m-d');
                    $eventNew['end'] = Carbon::createFromFormat('Y-m-d', $event['end'])->addDays($duration)->format('Y-m-d');
                }
                /*if ($eventNew['half_shift']) {
                    if (!Carbon::parse($event['start'])->isSaturday()) {
                        $message = trans('schedules.err_half_shift_2');
                        throw new \Exception($message, 1);
                    }
                    if ($eventNew['start'] != $event['end']) {
                        $message = trans('schedules.err_half_shift');
                        throw new \Exception($message, 1);
                    }
                }
                if (Carbon::parse($eventNew['start'])->year != Carbon::now()->year || Carbon::parse($eventNew['end'])->year != Carbon::now()->year ) {
                    $message =  trans('schedules.err_year');
                    throw new \Exception($message, 1);
                }*/
                $eventNew['status'] = 0;
                $eventNew['title'] = trans('schedules.day-offs.' . $eventNew['code']);

                $listFields = ['code', 'start', 'end', 'from_type', 'to_type', 'reason'];
                $logs = [];
                foreach ($listFields as $nameField) {
                    if ($event[$nameField] <> $eventNew[$nameField]) {
                        if ($nameField == 'code') {
                            $oldData = $event->title.($event->code);
                            $newData = Schedule::getDayOffTypeForOption()[$eventNew['code']].($eventNew['code']);
                        } elseif ($nameField == 'from_type' || $nameField == 'to_type') {
                            if ($event->half_shift == 1) {
                                $oldData = trans('schedules.time-shift-offs.'.$event[$nameField]);
                                $newData = trans('schedules.time-shift-offs.'.$eventNew[$nameField]);
                            } else {
                                $oldData = trans('schedules.time-offs.'.$event[$nameField]);
                                $newData = trans('schedules.time-offs.'.$eventNew[$nameField]);
                            }
                        } else {
                            $oldData = $event[$nameField];
                            $newData = $eventNew[$nameField];
                        }
                        $logs[] = [
                            'old_data' => $oldData,
                            'new_data' => $newData,
                            'field' => $nameField,
                            'action_by' => Auth::id(),
                            'action_at' => now(),
                            'key' => now()->timestamp
                        ];
                    }
                }
                DB::beginTransaction();
                if ($logs) {
                    $event->listLogs()->createMany($logs);
                }
                $event->update($eventNew);
                DB::commit();
                $response['message'] = trans('system.success');
                $response['data'] = $event;
            } catch (\Exception $e) {
                DB::rollBack();
                $statusCode = 400;
                $response['message'] = $e->getMessage();
            } finally {
                return response()->json($response, $statusCode);
            }
        } else {
            $statusCode = 500;
            return response()->json($response, $statusCode);
        }
    }

    public function destroy(Request $request, $id)
    {
        $response = ['message' => trans('system.have_an_error'), 'data' => ""];
        $statusCode = 200;
        if ($request->ajax()) {
            try {
                $query = StaffDayOff::where('id', $id);
                $dayOffEvent = $query->first();
                if (is_null($dayOffEvent)) {
                    $message = trans('system.no_record_found');
                    throw new \Exception($message, 1);
                }
                $today = Carbon::today();
                if (strtotime($today) > strtotime($dayOffEvent->start) && $dayOffEvent->code != Schedule::DAY_OFF_BABE) {
                    $message = trans('schedules.error_past_day_off');
                    throw new \Exception($message, 1);
                }
                if ($dayOffEvent->status) {
                    $message = trans('schedules.error_handle_day_off');
                    throw new \Exception($message, 1);
                }
                $query->forceDelete();
                $response['message'] = trans('system.success');
            } catch (\Exception $e) {
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

    public function getCountDayOff(Request $request)
    {
        $response = ['message' => trans('system.have_an_error')];
        $statusCode = 200;
        if ($request->ajax()) {
            $month = $request->input('month');
            $year = $request->input('year');
            $response['countAccept'] = StaffDayOff::countAcceptDayOffInMonth(Auth::id(), $month, $year);;
            $response['countPending'] = StaffDayOff::countPendingDayOffInMonth(Auth::id(), $month, $year);
            $response['message'] = trans('system.success');
            return response()->json($response, $statusCode);
        } else {
            $statusCode = 405;
            return response()->json($response, $statusCode);
        }
    }

    public function handleStaffDayOffSameDeptDayOff($staffDayOffs, $deptDayOffs, $staffId)
    {
        $user = User::find($staffId);
        foreach ($staffDayOffs as $staffDayOff) {
            if (!in_array($staffDayOff->code, Schedule::dayOffNoLimit())) {
                foreach ($deptDayOffs as $deptDayOff) {
                    if ($staffDayOff->start > $deptDayOff['end'] && $staffDayOff->end < $deptDayOff['start']) continue;
                    if ($staffDayOff->end > $deptDayOff['start'] && $staffDayOff->start < $deptDayOff['end']) {
                        if ($staffDayOff->status && $staffDayOff->code == Schedule::DAY_OFF_12) {
                            $user->update([
                                'rest' => $user->rest + $staffDayOff->total
                            ]);
                        }
                        StaffDayOff::find($staffDayOff->id)->forceDelete();
                        continue;
                    }
                    if ($staffDayOff->start == $deptDayOff['end']) {
                        if ($deptDayOff['to_type'] == Schedule::TIME_OFF_AFTERNOON) {
                            if ($staffDayOff->status && $staffDayOff->code == Schedule::DAY_OFF_12) {
                                $user->update([
                                    'rest' => $user->rest + $staffDayOff->total
                                ]);
                            }
                            StaffDayOff::find($staffDayOff->id)->forceDelete();
                            continue;
                        }
                        if ($deptDayOff['to_type'] == Schedule::TIME_OFF_MORNING) {
                            if ($staffDayOff->from_type == Schedule::TIME_OFF_MORNING) {
                                if ($staffDayOff->status && $staffDayOff->code == Schedule::DAY_OFF_12) {
                                    $user->update([
                                        'rest' => $user->rest + $staffDayOff->total
                                    ]);
                                }
                                StaffDayOff::find($staffDayOff->id)->forceDelete();
                                continue;
                            }
                        }
                    }
                    if ($staffDayOff->end == $deptDayOff['start']) {
                        if ($deptDayOff['from_type'] == Schedule::TIME_OFF_MORNING) {
                            if ($staffDayOff->status && $staffDayOff->code == Schedule::DAY_OFF_12) {
                                $user->update([
                                    'rest' => $user->rest + $staffDayOff->total
                                ]);
                            }
                            StaffDayOff::find($staffDayOff->id)->forceDelete();
                            continue;
                        }
                        if ($deptDayOff['from_type'] == Schedule::TIME_OFF_MORNING) {
                            if ($staffDayOff->to_type != Schedule::TIME_OFF_MORNING) {
                                if ($staffDayOff->status && $staffDayOff->code == Schedule::DAY_OFF_12) {
                                    $user->update([
                                        'rest' => $user->rest + $staffDayOff->total
                                    ]);
                                }
                                StaffDayOff::find($staffDayOff->id)->forceDelete();
                                continue;
                            }
                        }
                    }
                }
            }
        }
    }
}
