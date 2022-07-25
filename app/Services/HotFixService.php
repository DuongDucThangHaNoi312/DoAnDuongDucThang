<?php


namespace App\Services;


use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class HotFixService
{
    // public function hotFix($pain, $param1)
    // {
    //     $r = 'OK!';
    //     try {
    //         //DB::beginTransaction();
    //         if ($pain == 'smallâsá') \App\Models\Contract::where('type_status', '<>', 1)->update(['status' => 0]);
    //         elseif ($pain == 'big') {
    //             \App\StaffDayOff::withTrashed()->where('code', "S")->update(['title' => "Nghỉ hưởng BHXH"]);
    //         }
    //         elseif ($pain == 'leave') {
    //             $a = \App\Models\Contract::whereNotNull('set_notvalid_on')
    //                 ->where('set_notvalid_on', '<=', now())
    //                 ->whereIn('type_status', [3, 7])
    //                 ->get();
    //             foreach ($a as $item) {
    //                 $b = \App\Models\Contract::where('type_status', 1)->where('user_id', $item->user_id)->first();
    //                 if ($b) continue;
    //                 if ($param1 && $item->type_status == 3) {
    //                     $item->user()->update([
    //                         'is_leave' => 1,
    //                         'active' => 0
    //                     ]);
    //                 } else {
    //                     $item->user()->update([
    //                         'is_leave' => 1
    //                     ]);
    //                 }
    //             }
    //         }
    //         elseif ($pain == 'rest') {
    //             $users = \App\User::whereNull('is_leave')->get();
    //             foreach ($users as $user) {
    //                 $staffStart = $user->staff_start;
    //                 if (!$staffStart) continue;
    //                 $originalRest = 0;
    //                 $dateTp = explode('-', $staffStart);
    //                 $y = $dateTp[0]; $m = $dateTp[1]; $d = $dateTp[2];
    //                 if ($y == now()->year) {
    //                     if (intval($d) == 1) $add = 1;
    //                     else $add = 0;
    //                     $originalRest = 12 - intval($m) + $add;
    //                 } else {
    //                     $date = \Carbon\Carbon::createFromDate(now()->year, 12, 31);
    //                     $seniority = round(($date->diff(\Carbon\Carbon::parse($staffStart))->days + 1) / 365, 1);
    //                     $originalRest = intval($seniority / 5) + 12;
    //                 }
    //                 $data = [];
    //                 if ($user->original_rest == $originalRest) continue;
    //                 //$t = \App\Models\Contract::getCountAddLeave($user->id) + 12;
    //                 $dayOffTotalL = \App\StaffDayOff::where('status', 1)
    //                     ->where('user_id', $user->id)
    //                     ->where('code', 'L')
    //                     ->where('start', '>=', '2021-12-26')
    //                     ->sum('total');
    //                 //if ($user->rest != $t - $dayOffTotalL) {
    //                 $data = [
    //                     'original_rest' => $originalRest,
    //                     'rest' => $originalRest - $dayOffTotalL,
    //                 ];
    //                 //}
    //                 if ($param1 == 'origin') $data = ['original_rest' => $t];
    //                 $user->update($data);
    //                 $a[$user->fullname] = $user->id;
    //             }
    //             dd($a);
    //         }
    //         elseif ($pain == 'delete-user') {
    //             if (empty($param1)) {
    //                 $userId = [539, 556, 580, 582, 540, 544, 538, 579, 571, 555, 574, 568];
    //             } else $userId = [$param1];

    //             $c = \App\Models\Contract::whereIn('user_id', $userId)->get();
    //             foreach ($c as $item) {
    //                 if (is_null($item)) continue;
    //                 $item->allowances()->delete();
    //                 $item->appendixAllowances3()->delete();
    //                 $item->contractFiles()->delete();
    //                 $item->contractFiles()->delete();
    //                 $item->delete();
    //             }
    //             \App\User::whereIn('id', $userId)->forceDelete();

    //         }
    //         elseif ($pain == 'dept-group') {
    //             $contracts = \App\Models\Contract::where('type_status', 1)->get();
    //             foreach ($contracts as $contract) {
    //                 $group = \App\Models\Department::getGroupOfDept($contract->department_id);
    //                 $contract->update([
    //                     'department_group_id' => $group
    //                 ]);
    //                 $contract->user()->update([
    //                     'dept_group_id' => $group
    //                 ]);
    //             }
    //         }
    //         elseif ($pain == 'dept-group-concurrent') {
    //             $contracts = \App\Models\ConcurrentContract::get();
    //             foreach ($contracts as $contract) {
    //                 $group = \App\Models\Department::getGroupOfDept($contract->department_id);
    //                 $contract->update([
    //                     'department_group_id' => $group
    //                 ]);
    //             }
    //             return $pain;
    //         } elseif ($pain == 'dept-dayoff-duplicate') {
    //             if ($param1) {
    //                 $tmp = explode(';', $param1);
    //                 $start = $tmp[0]; $deptId = $tmp[1];
    //                 $q = "start_date >= '{$start}'";
    //                 if ($deptId) $q .= " AND department_id = {$deptId}";
    //                 $d = \App\Models\CalendarDepartment::whereRaw($q)->orderBy('start_date', 'desc')->get()->groupBy([
    //                     'department_id',
    //                     'type',
    //                     function ($q) {
    //                         return date('w', strtotime($q->start_date));
    //                     }
    //                 ]);
    //                 //dd($d->toArray());
    //                 $data = [];
    //                 $deptName = \App\Models\Department::pluck('name', 'id')->toArray();
    //                 foreach ($d as $dept => $deptData) {
    //                     $key = $dept.'_'.$deptName[$dept];
    //                     foreach ($deptData as $type => $typeData) {
    //                         foreach ($typeData as $day => $items) {
    //                             if ($items) {
    //                                 $a = $items->toArray();
    //                                 $c = count($a);
    //                                 for ($i=0;$i<$c; $i++) {
    //                                     for ($j=$i+1;$j<$c;$j++) {
    //                                         if ($a[$j]['start_date'] <= $a[$i]['end_date'] && $a[$j]['end_date'] >= $a[$i]['start_date']) {
    //                                             $data[$key][$type][] = [$a[$i]['id'] => [$a[$i]['start_date'], $a[$i]['end_date']], $a[$j]['id'] => [$a[$j]['start_date'], $a[$j]['end_date']]];
    //                                         }
    //                                     }
    //                                 }
    //                             }
    //                         }
    //                     }
    //                 }
    //                 dd('Các cặp id trùng:', $data);
    //             }
    //         } elseif ($pain == 'check-dayoff') {
    //             if ($param1) {
    //                 $p = explode(',', $param1);
    //                 $comId = $p[0]; $deptId = $p[1]; $userId = $p[2]; $dateCheck = $p[3];
    //                 $workSchedule = \App\Models\WorkSchedule::where('company_id', $comId)->where('department_id', $deptId)
    //                     ->first();
    //                 $nghi_co_luong = \App\Define\Timekeeping::fullPayLeave();
    //                 $nghi_nua_luong = \App\Define\Timekeeping::halfSalaryLeave();
    //                 $y = now()->year; $m = intval(now()->month);
    //                 $start = date('Y-m-d', strtotime(now()->year . '-' . ((intval(now()->month) - 1)) . '-' . 26));
    //                 $end = date('Y-m-d', strtotime(now()->year . '-' . intval(now()->month) . '-' . 26));
    //                 $nghi_nhan_vien = \App\StaffDayOff::where('user_id', $userId)->where('start', '<=', $end)->where('end', '>=', $start)->get();
    //                 $nghi_phong_ban = \App\Models\CalendarDepartment::where('department_id', $deptId)->where('categories', 'holiday')->get();
    //                 $selectDayOff = \App\StaffDayOff::selectDayOff($workSchedule, $nghi_phong_ban, $nghi_nhan_vien, $userId, $dateCheck, $deptId, $m, $y);
    //                 $a = [
    //                     'nghi_nhan_vien' => $nghi_nhan_vien,
    //                     'nghi-pb' => $nghi_phong_ban,
    //                     'selectdayoff' => $selectDayOff,
    //                     'code-co-luong' => $nghi_co_luong,
    //                     'code-nua-luong' => $nghi_nua_luong,
    //                 ];
    //                 dd($a);
    //             }
    //         } elseif ($pain == 'fix-valid-to-contract') {
    //             $contracts = \App\Models\Contract::whereNotNull('valid_to')
    //                 ->where('type', '<>', \App\Defines\Contract::TYPE_UNLIMITED)
    //                 //->where('type_status', 1)
    //                 ->where('is_main', 2)
    //                 ->get();
    //             foreach ($contracts as $item) {
    //                 if ($item->valid_from && $item->valid_to) {
    //                     $data = [];
    //                     $validTo = \App\Models\Contract::setValidTo(date('d/m/Y', strtotime($item->valid_from)), $item->type);
    //                     $setNotValid = \Carbon\Carbon::parse($validTo)->addDay()->format('Y-m-d 00:00:00');
    //                     if ($item->valid_to->format('Y-m-d') != $validTo) $data['valid_to'] = $validTo;
    //                     if (is_null($item->set_notvalid_on) || $item->set_notvalid_on != $setNotValid && $item->type_status == 1) $data['set_notvalid_on'] = $setNotValid;
    //                     $b[$item->id] = ['old_v' => $item->valid_to->format('Y-m-d'), 'new_v' => $validTo,
    //                         'old_s' => $item->set_notvalid_on, 'new_s' => $setNotValid];
    //                     $c[$item->id] = $data;
    //                     if ($data) {
    //                         $item->update($data);
    //                     }
    //                 }

    //             }
    //             dd($c, $b);
    //         } elseif ($pain == 'check-leave') {
    //             $b = \App\User::with('contracts')->get();
    //             foreach ($b as $item) {
    //                 $contracts = $item->contracts;
    //                 $now = now();
    //                 $isLeave = true;
    //                 foreach ($contracts as $c) {
    //                     if (is_null($c->set_notvalid_on) || $c->set_notvalid_on > $now) {
    //                         $isLeave = false;
    //                         break;
    //                     }
    //                 }
    //                 //if ($item->id == 167 ) dd($isLeave, $contracts->toArray());
    //                 if ($isLeave && !$item->is_leave)
    //                     $a[$item->id.$item->fullname] = ['is_leave' => $item->is_leave, 'is_leave_contract' => 1];
    //             }
    //             dd($a);
    //         } elseif ($pain == 'check-active') {
    //             $users = \App\User::where('active', '<>', 1)->get();
    //             foreach ($users as $item) {
    //                 $c = \App\Models\Contract::whereIn('type_status', [1,7])->where('user_id', $item->user_id)->first();
    //                 if (!is_null($c)) {
    //                     $con[$item->id] = ['type_status' => $c->type_status, 'user' => $item->fullname];
    //                 }
    //             }
    //             dd($con);
    //         } elseif ($pain == 'timekeeping-sub') {
    //             $userHasSubCodes = User::whereNotNull('code_timekeeping_subs')
    //                 ->whereNotNull('code_timekeeping')
    //                 ->where('code_timekeeping_subs', '<>', '')
    //                 ->pluck('code_timekeeping_subs', 'code_timekeeping')
    //                 ->toArray();
    //             $r = [];
    //             $date = $param1 ?? date('Y').'-'.(intval(date('m'))-1).'-'.'25';
    //             $tz = strtotime($date.' 00:00:00');

    //             $arrSubCodes = [];
    //             foreach ($userHasSubCodes as $mainCode => $listSubs) {
    //                 $tmp = explode(',', $listSubs);
    //                 if (count($tmp) == 0) continue;
    //                 foreach ($tmp as $key) {
    //                     $arrSubCodes[intval($key)] = $mainCode;
    //                 }
    //             }
    //             $rows = \DB::table("jupiter-attendance.CHECKINO")
    //                 ->whereIn('primary_code', array_keys($arrSubCodes))
    //                 ->where('timeint', '>', $tz)
    //                 ->get()->toArray();
    //             dd($rows);
    //         } elseif ($pain == 'sync-machine-code') {
    //             User::syncAttendanceMachine();
    //         }
    //         //DB::commit();
    //     } catch (\Exception $e) {
    //         //DB::rollBack();
    //         $r = $e;
    //     } finally {
    //         return $r;
    //     }
    // }
}