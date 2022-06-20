<?php

namespace App\Http\Controllers\backend;

use App\Define\Shift;
use App\Models\Department;
use App\User;
use App\StaffDayOff;
use App\Defines\Schedule;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CalendarDepartment;
use App\Models\Contract;
use App\Models\UserTeam;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class ManagerLeaveTakeController extends Controller
{
    public function managerLeave()
    {
        // $infoPermission = \App\PermissionUserObject::getMorePermissions();
        // if (count($infoPermission) <= 0) {
        //     return redirect()->route('admin.home');
        // }

        $qualificationId = Contract::where('type_status', 1)->pluck('qualification_id', 'id')->toArray();
        $positionId = Contract::where('type_status', 1)->pluck('position_id', 'id')->toArray();
        Cache::forget('staff_permission_manager_leave');

        return view('backend.manager_leave.index', compact('qualificationId', 'positionId'));
    }

    public function getData(Request $request)
    {
        $infoPermission = Cache::get('staff_permission_manager_leave');
        if (!$infoPermission) {
            $infoPermission = \App\PermissionUserObject::getMorePermissions("", "manager.leave.read");
            Cache::put('staff_permission_manager_leave', $infoPermission);
        }

        if (isset($infoPermission['check']) && $infoPermission['check'] == 0) {
            if (count($infoPermission['teams']) > 0) {
                $user_ids = UserTeam::whereIn('team_id', $infoPermission['teams'])->pluck('user_id')->toArray();
                $manager_leaves = StaffDayOff::withTrashed()->whereIn('user_id', $user_ids)->orderBy('id', 'DESC');
            } else {
                dd('Bạn không có quyền truy cập');
            }
        } else {
            $user_ids = [];
            if (count($infoPermission['teams']) > 0) {
                $user_ids = UserTeam::whereIn('team_id', $infoPermission['teams'])->pluck('user_id')->toArray();
            }
            $manager_leaves = StaffDayOff::getLeaveFollowPermission($infoPermission, $user_ids);
        }
        $manager_leaves->with('user', 'approvedBy');

        return DataTables::of($manager_leaves)
            ->addIndexColumn()
            ->addColumn('fullname_code', function ($manager_leaves) {
                return $manager_leaves->user->fullname . "-" . $manager_leaves->user->code;
            })
            ->editColumn('approved_date', function ($manager_leaves) {
                return $manager_leaves->approved_date ? date('d/m/Y H:i:s', strtotime($manager_leaves->approved_date)) : "";
            })
            ->editColumn('start', function ($manager_leaves) {
                return $manager_leaves->start ? date('d/m/Y', strtotime($manager_leaves->start)) : "";
            })
            ->editColumn('end', function ($manager_leaves) {
                return $manager_leaves->end ? date('d/m/Y', strtotime($manager_leaves->end)) : "";
            })
            ->filter(function ($instance) use ($request) {
                if ($request->get('status') == '0' || $request->get('status') == '1') {
                    $instance->where('status', $request->get('status'));
                }
                if ($request->get('name')) {
                    $userId = User::where(DB::raw('CONCAT(fullname, "-", code)'), 'like', "%" . $request->get('name') . "%")->pluck('id')->toArray();
                    $instance->whereIn('user_id', $userId);
                }
                if ($request->get('title')) {
                    $instance->where('title', 'like', "%" . $request->get('title') . "%");
                }
                if ($request->get('reason')) {
                    $instance->where('reason', 'like', "%" . $request->get('reason') . "%");
                }
                if ($request->get('date_start')) {
                    $dateStart = str_replace('/', '-', $request->get('date_start'));
                    $dateStart = date('Y-m-d', strtotime($dateStart));
                    $instance->whereDate('start', '>=', $dateStart);
                }
                if ($request->get('date_end')) {
                    $dateEnd = str_replace('/', '-', $request->get('date_end'));
                    $dateEnd = date('Y-m-d', strtotime($dateEnd));
                    $instance->whereDate('end', '<=', $dateEnd);
                }
                if ($request->get('total')) {
                    $instance->where('total', $request->get('total'));
                }
            })
            ->make(true);
    }

    public function activeStatus($id) // khong thay dung o dau?
    {
        $active_leave = StaffDayOff::find(intval($id));
        $active_leave->status = 1;
        $user_id = $active_leave->user_id;
        $user_rest = User::where('id', $user_id)->get();
        foreach ($user_rest as $user)
            if ($active_leave['code'] == Schedule::DAY_OFF_12) {
                $rest = ((($user->rest) * 2) - $active_leave['total']) / 2;
                DB::table('users')->where('id', $user_id)->update([
                    'rest' => $rest
                ]);
            }
        $active_leave->save();
        return back();
    }

    public function deletes(Request $request)
    {
        $manager_delete = StaffDayOff::withTrashed()->find($request->id);
        if (is_null($manager_delete)) {
            return response()->json(['error' => trans('system.have_an_error')], 404);
        }
        if ($manager_delete->status == 1)
            $this->resetDayLeaveUser($manager_delete->user_id, $manager_delete->total, $manager_delete->code);
        $manager_delete->forceDelete();

        return response()->json(['success' => trans('staffs.success')], 200);
    }

    public function updateStatus(Request $request)
    {
        $response = ['message' => trans('system.have_an_error'), 'data' => ""];
        $statusCode = 200;
        if ($request->ajax()) {
            try {
                DB::beginTransaction();
                $dayOff = StaffDayOff::find($request->dayoff_id);
                if (is_null($dayOff)) throw new \Exception('Đơn xin nghỉ không tồn tại');
                if ($dayOff->status == 1) throw new \Exception('Đơn đã được duyệt');
                $dataSave = [
                    'status' => $request->status,
                    'approved_by' => Auth::id(),
                    'approved_date' => date('Y-m-d H:i:s')
                ];
                $userData = User::where('id', $dayOff->user_id)
                    ->where('active', 1)
                    ->first();
                if (is_null($userData)) throw new \Exception('Nhân viên không hoạt động hoặc không tồn tại');
                if ($dayOff['code'] == Schedule::DAY_OFF_12) {
                    $rest = (($userData->rest) - $dayOff['total']);
                    if ($rest < 0) throw new \Exception('Đã hết ngày nghỉ phép.');
                    $userData->update(['rest' => $rest]);
                }
                $dayOff->update($dataSave);
                DB::commit();
                $response['message'] = 'Duyệt đơn thành công';
            } catch (\Exception $e) {
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

    // Reset day leave
    protected function resetDayLeaveUser($user_id, $number_day, $code)
    {
        $user = User::find($user_id);
        if ($code == Schedule::DAY_OFF_12) {
            $user->rest = ($user->rest + $number_day);
            $user->updated_at = date('Y-m-d H:i:s');
            $user->save();
        }
    }

    public function editDayOffAdmin(Request $request)
    {
        $response = ['message' => trans('system.have_an_error')];
        $statusCode = 200;
        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $data = $request->all();
                if (!$data['start'] || !$data['end']) throw new \Exception('Ngày bắt đầu và Ngày kết thúc không để trống');
                $start = date('Y-m-d', strtotime(str_replace('/', '-', $data['start'])));
                $end = date('Y-m-d', strtotime(str_replace('/', '-', $data['end'])));
                $data['start'] = $start;
                $data['end'] = $end;

                if ($start > $end) throw new \Exception(__('schedules.error_end_date'));
                if (!$data['reason']) throw new \Exception('Chi tiết lý do không để trống');
                if ($data['total'] <= 0) throw new \Exception('Total error');

                $dayoff = StaffDayOff::find(intval($data['id']));
                if (is_null($dayoff)) throw new \Exception(__('system.record_not_found'));

                $limitDayOff = Schedule::getLimitDatOffByType($data['code']);
                if ($limitDayOff && $data['total'] > $limitDayOff)
                    throw new \Exception('Quá ngày nghỉ quy định cho '.Schedule::getDayOffTypeForOption()[$data['code']]);
                /*
                 * Không chọn vào ngày nhân viên đã xin nghỉ
                 * Không chọn vào ngày nghỉ phòng ban
                 * Đối với phòng ban ca chỉ nghỉ vào ngày đã có lịch ca
                 **/
                $_MORNING = Schedule::TIME_OFF_MORNING;
                $_AFTERNOON = Schedule::TIME_OFF_AFTERNOON;
                $_DEPT_TYPE_OFFICE = Shift::OFFICE_TIME;
                $lastYearDate = ((now()->year) - 1).'-01-01';

                $userData = User::where('id', $dayoff->user_id)
                    ->where('active', 1)
                    ->first();
                if (is_null($userData)) throw new \Exception('Nhân viên không tồn tại hoặc không hoạt động');
                $restDayOff = $userData->rest;
                if ($data['total'] != $dayoff->total && $dayoff->status == 1) {
                    $_DAY_OFF_12 = Schedule::DAY_OFF_12;
                    $restAfter = null;
                    if ($dayoff->code == $_DAY_OFF_12 && $data['code'] == $_DAY_OFF_12) {
                        $restAfter = $restDayOff + $dayoff->total - $data['total'];
                        if ($restAfter < 0) throw new \Exception('Vượt quá số ngày nghỉ phép tồn');
                    } elseif ($dayoff->code == $_DAY_OFF_12 && $data['code'] != $_DAY_OFF_12) {
                        $restAfter = $restDayOff + $dayoff->total;
                    } elseif ($dayoff->code != $_DAY_OFF_12 && $data['code'] == $_DAY_OFF_12) {
                        $restAfter = $restDayOff - $dayoff->total;
                        if ($restAfter < 0) throw new \Exception('Vượt quá số ngày nghỉ phép tồn');
                    }
                    if ($restAfter) $userData->update(['rest' => $restAfter]);
                }

                $deptData = Department::where('status', 1)
                    ->where('id', $userData->department_id)
                    ->first();
                if (is_null($deptData)) throw new \Exception('Phòng ban không tồn tại');
                $typeDept = $deptData->type;
                $staffDayOff = StaffDayOff::where('start', '<=', $end)
                    ->where('end', '>=', $start)
                    ->where('user_id', $dayoff->user_id)
                    ->where('id', '<>', $data['id'])
                    ->first();
                if ($staffDayOff) {
                    $isHas = StaffDayOff::isSameDayOff($staffDayOff, $data, $typeDept);
                    if ($isHas) throw new \Exception('Ngày chọn đã có đơn xin nghỉ');
                }
                $deptDayOffs = CalendarDepartment::getDayOffsByDate($userData->department_id, $lastYearDate, $data);
                if ($data['code'] != Schedule::DAY_OFF_BABE) {
                    foreach ($deptDayOffs as $item) {
                        $isHas = StaffDayOff::isSameDayOff($item, $data, $typeDept);
                        if ($isHas) {
                            throw new \Exception('Ngày chọn đã có lịch nghỉ phòng ban');
                        }
                    }
                }

                //Cẩn thận model Shift eo lấy bảng shifts
                if ($data['code'] != Schedule::DAY_OFF_BABE) {
                    if ($typeDept != $_DEPT_TYPE_OFFICE) {
                        $shift = DB::table('shifts')->where('user_id', $userData->id)
                            ->where('start', '<=', $start)
                            ->where('end', '>=', $end)
                            ->first();
                        if (is_null($shift)) throw new \Exception(__('schedules.error_no_work_schedule'));
                    }
                }

                $listFields = ['code', 'start', 'end', 'from_type', 'to_type', 'reason'];
                $logs = [];
                foreach ($listFields as $nameField) {
                    if ($dayoff[$nameField] <> $data[$nameField]) {
                        if ($nameField == 'code') {
                            $oldData = $dayoff->title.($dayoff->code);
                            $newData = Schedule::getDayOffTypeForOption()[$data['code']].($data['code']);
                        } elseif ($nameField == 'from_type' || $nameField == 'to_type') {
                            if ($dayoff->half_shift == 1) {
                                $oldData = trans('schedules.time-shift-offs.'.$dayoff[$nameField]);
                                $newData = trans('schedules.time-shift-offs.'.$data[$nameField]);
                            } else {
                                $oldData = trans('schedules.time-offs.'.$dayoff[$nameField]);
                                $newData = trans('schedules.time-offs.'.$data[$nameField]);
                            }
                        }
                        else {
                            $oldData = $dayoff[$nameField];
                            $newData = $data[$nameField];
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
                if ($logs) {
                    $dayoff->listLogs()->createMany($logs);
                }
                $dayoff->update([
                    'start' => $start,
                    'end' => $end,
                    'reason' => $data['reason'],
                    'code' => $data['code'],
                    'total' => $data['total'],
                    'from_type' => $data['from_type'],
                    'to_type' => $data['to_type'],
                    'title' => Schedule::getDayOffTypeForOption()[$data['code']],
                    'color' => Schedule::colorDayOffs()[$data['code']]
                ]);
                DB::commit();
                $response['message'] = 'Sửa đơn thành công';
                Session::flash('message', $response['message']);
                Session::flash('alert-class', 'success');
            } catch (\Exception $e) {
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
}
