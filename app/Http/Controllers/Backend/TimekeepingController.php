<?php

namespace App\Http\Controllers\Backend;

use App\Define\OverTime;
use App\Define\Shift as DefineShift;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Contract;
use App\Models\Department;
use App\Models\Log;
use App\Models\OverTimes;
use App\Models\Shift;
use App\Models\TimeKeeping;
use App\Models\TimeKeepingDetail;
use App\Models\WorkSchedule;
use App\Models\CheckInO;
use App\StaffDayOff;
use App\Models\CalendarDepartment;
use App\Models\CategoryShift;
use App\Models\ConcurrentContract;
use App\Models\Newborn;
use App\Models\ShiftTime;
use App\Models\Team;
use App\Models\UserTeam;
use App\Permission;
use App\PermissionUserObject;
use App\User;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Mpdf\Tag\Strong;
use Mpdf\Tag\Time;
use PhpOffice\PhpSpreadsheet\Calculation\Category;
use Psy\VersionUpdater\Checker;

use function GuzzleHttp\json_decode;

class TimekeepingController extends Controller
{
    const NGHI_LAM = 0;
    const DU_NGAY_CONG = 1;
    const DI_MUON = 2;
    const VE_SOM = 3;
    const QUEN_QUET = 4;
    const DIMUON_VESOM = 5;
    const EXPORT = 1;
    const NUA_CONG = 10;
    const HOLIDAY_NUA_CONG = 15;
    const HOLIDAY = 11;

    public function teamQuery($route = '')
    {
        $user_ids = $timekeeping_detail = [];
        $query = PermissionUserObject::getTeamQueryPermission(Auth::user()->id, $route);
        if ($query !== '') $teams = Team::whereRaw($query)->get();
        if (!empty($teams)) $teams->load('users');

        foreach ($teams as $key => $item) {
            $user_ids[$item->id] = array_column($item->users->toArray(), 'user_id');
            array_push($user_ids[$item->id], $item->user_id);
        }

        return [
            'teams' => $teams,
            'user_ids' => $user_ids
        ];
    }

    public function getDateByMonth($month, $year)
    {
        $return = [];
        // for ($i = 1; $i <= 31; $i++) {
        //     $time = mktime(12, 0, 0, $month, $i, $year);
        //     $return[] = date('Y-m-d', $time);
        // }
        $start = $year . '-' . ($month - 1) . '-' . 26;
        $end = $year . '-' . $month . '-' . 26;

        $period = new DatePeriod(
            new DateTime($start),
            new DateInterval('P1D'),
            new DateTime($end)
        );
        foreach ($period as $key => $value) {
            $return[] = $value->format('Y-m-d');
        }

        return $return;
    }

    public function index(Request $request)
    {
        // $query = '1=1';
        // $company_id = $request->input('company_id');
        // $department_id = $request->input('department_id');
        // $month = $request->input('month');
        // $year = $request->input('year');

        // if ($company_id) $query .= " AND company_id = {$company_id}";
        // if ($department_id) $query .= " AND department_id = {$department_id}";
        // if ($month) $query .= " AND month = {$month}";
        // if ($year) $query .= " AND year = {$year}";

        // if (Auth::user()->hasRole('TP') && !Auth::user()->hasRole('TGD') || Auth::user()->hasRole('TPNS') && !Auth::user()->hasRole('TGD')) {
        //     $department_group = Department::departmentsRole();
        //     $departmentID = Auth::user()->department_id;
        //     if (count($department_group) > 1) {
        //         $str = implode(", ", $department_group);
        //         $query .= " AND department_id IN  ({$str})";
        //     } else {
        //         $query .= " AND department_id =  '{$departmentID}'";
        //     }
        //     $companysOption = Company::companysOption();
        //     $departmentOption = Department::departmentsOption();
        // }

        $query = "1=1";
        $infoPermission = PermissionUserObject::getMorePermissions(Auth::id());
        if ($infoPermission['departments']) {
            // if ($infoPermission['companies']) $query .= " AND company_id IN(" . implode(',', $infoPermission['companies']) . ")";
            if (!in_array(Auth::user()->department_id, $infoPermission['departments'])) array_push($infoPermission['departments'], Auth::user()->department_id);
            $query .= " AND department_id IN(" . implode(',', $infoPermission['departments']) . ")";
        } else {
            $user = User::find(Auth::user()->id, ['department_id']);
            if (!empty($user->department_id)) $query .= " AND department_id IN(" . $user->department_id . ")";
        }
        //$query = PermissionUserObject::getQueryPermission(Auth::id());
        //dd($query);
        $timekeeping = TimeKeeping::whereRaw($query)->orderBy('year', 'DESC')->orderBy('month', 'DESC')->get();
        $timekeeping->load('company', 'department');

        $morePermissions = DB::table('permission_user')->where('user_id', auth()->id())->pluck('permission_id')->toArray();
        $moreActions = Permission::whereIn('id', $morePermissions)->where('module', 'timekeeping')->pluck('action')->toArray();
        
        return view('backend.timekeeping.index', compact('timekeeping', 'companysOption', 'department_group', 'departmentOption', 'teams', 'user_ids', 'moreActions'));
    }

    public function staffDayOf($code, $item, $data)
    {
        $date = [];
        $staff_id = User::where('code_timekeeping', $code)->pluck('id')->first();
        $end = date('Y-m-d', strtotime($data['year'] . '-' . ($data['month']) . '-' . '25'));

        $staffDayOffs = StaffDayOff::where('user_id', $staff_id)
            ->whereIn(DB::RAW('month(start)'), [$data['month'] - 1, $data['month']])
            ->where('start', '<', $end)
            ->get();
        foreach ($staffDayOffs as $key => $value) {
            $period = new DatePeriod(
                new DateTime($value->start),
                new DateInterval('P1D'),
                new DateTime(date('Y-m-d', strtotime('+1 day', strtotime($value->end))))
            );
            foreach ($period as $p => $i) {
                $start_end[] = strtotime($i->format('Y-m-d'));
            }
            if (
                $value->from_type == \App\Defines\Schedule::TIME_OFF_MORNING
                && $value->to_type == \App\Defines\Schedule::TIME_OFF_AFTERNOON
            ) {
                $date = [
                    $value->user->code_timekeeping => $start_end
                ];
            } else {
                if ($value->from_type == \App\Defines\Schedule::TIME_OFF_AFTERNOON) {
                    $arr = [\App\Defines\Schedule::TIME_OFF_MORNING];

                    array_splice($item[$start_end[0]], 1, 0, $arr);
                    $keys = array_keys($item[$start_end[0]]);
                    $keys[array_search(1, $keys, true)] = 'work';
                    $item[$start_end[0]] = array_combine($keys, $item[$start_end[0]]);

                    unset($start_end[0]);
                };
                if ($value->to_type == \App\Defines\Schedule::TIME_OFF_MORNING) {
                    $arr = [
                        'work' => \App\Defines\Schedule::TIME_OFF_AFTERNOON
                    ];
                    array_splice($item[end($start_end)], 1, 0, $arr);
                    $keys = array_keys($item[end($start_end)]);
                    $keys[array_search(1, $keys, true)] = 'work';
                    $item[end($start_end)] = array_combine($keys, $item[end($start_end)]);

                    array_pop($start_end);
                };
                $start_end = array_values($start_end);
                $date = [
                    $value->user->code_timekeeping => $start_end
                ];
            }
        }
        foreach ($date as $index => $value) {
            if ($index == $code) {
                foreach ($value as $i1 => $v1) {
                    if (array_key_exists($v1, $item)) {
                        unset($item[$v1]);
                    } else {
                        $results[$code][$v1]['status'] = 10;
                    }
                }
            }
        }

        return $item;
    }

    public function checkInOut($timeKeeping, $data, $checkInOut, $workSchedule, $department, $staffs, $contracts, $startDate = null)
    {
        $return = $results = $timeKeepingDetail = $shifts = $insert = [];
        foreach ($checkInOut as $item) {
            $timedate = date('Y-m-d', $item->timeint);

            $results[$item->primary_code][strtotime($timedate)][] = json_decode(json_encode($item), true);
        }
        $user_ids = $staffs->pluck('id')->toArray();
        if (count($results) > 0) {

            foreach ($results as $key => $item) {
                $staff_id = User::where('code_timekeeping', $key)->where('active', 1)->pluck('id')->first();
                $contract = $contracts->where('user_id', $staff_id)->first();
                if (is_null($contract)) continue;
                if (strtotime($contract->set_notvalid_on) < strtotime($startDate) && $contract->type_status == 2) {
                    if (in_array($contract->user_id, $user_ids)) {
                        $k = array_search($contract->user_id, $user_ids);
                        unset($user_ids[$k]);
                    }
                    continue;
                }

                if (in_array($staff_id, $user_ids)) {
                    $k_user = array_search($staff_id, $user_ids);
                    unset($user_ids[$k_user]);
                }
                // $overTimes1 = [];
                $overTimes = OverTimes::getOT($data['month'], $data['year'], $staff_id);
                // $item = $this->staffDayOf($key, $item, $data);
                $shift_users = Shift::getShiftEveryDay($data['year'], $data['month'], $staff_id);
                $shift_users = collect($shift_users);


                $dayoffs1 = CalendarDepartment::getDayOff($department->id);
                $dayoffs1 = collect($dayoffs1);
                foreach ($this->getDateByMonth($data['month'], $data['year']) as $k1 => $v1) {
                    $timedate = strtotime($item[$key]);
                    $dateByMonth = strtotime($v1);

                    $timeCheckIn  = $timeCheckOut = $totalHours = $foulHours = $status = $ot = $total = $shift = $total_tv = $total_hd = $type_ot = 0;
                    $color = "red";
                    $return = [
                        'time_check_out'    => $timeCheckOut,
                        'time_check_in'     => $timeCheckIn,
                        'total_hours'       => $totalHours,
                        'status'            => $status,
                        'foul_hours'        => $foulHours,
                        'ot'                => $ot,
                        'color'             => $color,
                        'total_tv'          => $total_tv,
                        'total_hd'          => $total_hd,
                        'type_ot'           => $type_ot
                    ];
                    $data_contracts = Contract::getContractsInAMonth($staff_id, $data['month'], $data['year']);
                    // $contract = $contracts->where('user_id', $staff_id)->first();
                    $dayoffs = $dayoffs1->where('start', date('Y-m-d', $dateByMonth))->first();

                    if (array_key_exists($dateByMonth, $item)) {
                        $total = $total_tv = $total_hd = 1;

                        if (
                            $department->type == \App\Define\Department::HOURS
                            || $department->type == \App\Define\Department::FUNCTIONAL_OFFICE
                        ) {
                            $arr = [
                                current($item[$dateByMonth]),
                                end($item[$dateByMonth])
                            ];
                            unset($results[$key][$dateByMonth]);
                            $item[$dateByMonth] = $arr;

                            $timeCheckIn = date('H:i:s', $item[$dateByMonth][1]['timeint']);
                            $timeCheckOut = date('H:i:s', $item[$dateByMonth][0]['timeint']);

                            $fromAm = date_format(date_create($workSchedule->from_morning), "H:i:s");
                            $toPm = date_format(date_create($workSchedule->to_afternoon), "H:i:s");
                            $to_morning = date_format(date_create($workSchedule->to_morning), "H:i:s");
                            $from_afternoon = date_format(date_create($workSchedule->from_afternoon), "H:i:s");

                            $from_sa_morning = date_format(date_create($workSchedule->from_sa_morning), "H:i:s");
                            $to_sa_morning = date_format(date_create($workSchedule->to_sa_morning), "H:i:s");
                            $from_sa_afternoon = date_format(date_create($workSchedule->from_sa_afternoon), "H:i:s");
                            $to_sa_afternoon = date_format(date_create($workSchedule->to_sa_afternoon), "H:i:s");

                            $over_time = date_format(date_create($workSchedule->ot), "H:i:s"); //thời gian tính ot
                            $check_in = new Carbon($timeCheckIn);
                            $check_out = new Carbon($timeCheckOut);

                            $intTimeCheckOut = new Carbon($timeCheckOut);
                            $intTimeCheckIn = new Carbon($timeCheckIn);
                            $totalHours = $intTimeCheckOut->diff($intTimeCheckIn)->format('%H:%I:%S');

                            $department_day_of = CalendarDepartment::where('start_date', '<=', date('Y-m-d', $dateByMonth))
                                ->where('end_date', '>=', date('Y-m-d', $dateByMonth))
                                ->where('department_id', $data['department_id'])
                                ->first();


                            if ($department->type == \App\Define\Department::HOURS) {
                                $total_hours = $check_in->diff($check_out)->format('%H:%I:%S');
                                $return['time_check_out'] = $timeCheckOut;
                                $return['time_check_in'] = $timeCheckIn;

                                if ($total_hours >= "09:00:00") {
                                    $return['status'] = self::DU_NGAY_CONG;
                                    $return['color'] = "white";
                                    $return['total'] = 1;
                                } else {
                                    $return['status'] = self::NUA_CONG;
                                    $return['color'] = 'silver';
                                    $return['total'] = 0.5;
                                }

                                if (strtoupper($check_th) == 'SATURDAY' && !empty($department_day_of)) {
                                    if ($total_hours >= "04:00:00") {
                                        $return['status'] = self::NUA_CONG;
                                        $return['color'] = 'silver';
                                        $return['total'] = 0.5;
                                    }
                                }
                                if (strtoupper($check_th) == 'SUNDAY' && !empty($department_day_of)) {
                                    $return = [
                                        'time_check_out'    => $timeCheckOut,
                                        'time_check_in'     => $timeCheckIn,
                                        'total_hours'       => $totalHours,
                                        'status'            => $status,
                                        'foul_hours'        => $foulHours,
                                        'ot'                => $ot,
                                        'color'             => $color,
                                        'total_tv'          => $total_tv,
                                        'total_hd'          => $total_hd,
                                        'type_ot'           => $type_ot
                                    ];
                                }

                                $checkHoliday = StaffDayOff::checkDateHasEvent($staff_id, date('Y-m-d', $dateByMonth));
                                if ($checkHoliday == 'H/2' || $checkHoliday == 'H') {
                                    $return['time_check_in'] = $timeCheckIn;
                                    $return['time_check_out'] = $timeCheckOut;
                                    $return['color'] = 'red';
                                    $return['status'] = self::NGHI_LAM;
                                    if ($checkHoliday == 'H/2') $return['total'] = $return['total'] + 0.5;
                                    if ($checkHoliday == 'H') $return['total'] = 1;
                                }
                                if (
                                    $checkHoliday == 'T' || $checkHoliday == 'T/2'
                                    && $dateByMonth <= strtotime(date('Y-m-d'))
                                ) {
                                    $return['time_check_in'] = $timeCheckIn;
                                    $return['time_check_out'] = $timeCheckOut;
                                    $return['color'] = 'red';
                                    $return['status'] = self::NGHI_LAM;
                                    if ($checkHoliday == 'T/2') $return['total'] = $return['total'] + 0.5;
                                    if ($checkHoliday == 'T') $return['total'] = 1;
                                }

                                $return1 = $return;
                                $total_tv = $total_hd = $return1['total'];
                                $so_cong = $this->getWorkingByContract($data_contracts, $total_tv, $total_hd, $dateByMonth, $contract);
                                if ($so_cong == 0) {
                                    if ($contract->is_main == 1) $return1['total_tv'] = $total;
                                    if ($contract->is_main == 2) $return1['total_hd'] = $total;
                                } else {
                                    $return1['total_tv'] = $so_cong['total_tv'];
                                    $return1['total_hd'] = $so_cong['total_hd'];
                                }

                                if ($return1['status'] != self::QUEN_QUET) {
                                    $return1 = $this->getOT($data_contracts, $return1, $overTimes, $item, $check_out, $over_time, $dateByMonth, $contract, $dayoffs, $workSchedule);
                                }
                            }
                            if ($department->type == \App\Define\Department::FUNCTIONAL_OFFICE) {
                                if ($contract->type_status == 2) {
                                    $set_notvalid_on = strtotime($contract->set_notvalid_on);
                                    if ($set_notvalid_on > $dateByMonth) {
                                        $insert[$key][$dateByMonth] = $return;
                                        continue;
                                    }
                                }

                                if ($contract->type_status == 1) {
                                    $valid_from = strtotime($contract->valid_from);
                                    if ($dateByMonth < $valid_from) {
                                        $insert[$key][$dateByMonth] = $return;
                                        continue;
                                    }
                                }

                                $checkHoliday = StaffDayOff::checkDateHasEvent($staff_id, date('Y-m-d', $dateByMonth));

                                $dateDayOff = StaffDayOff::checkDateHasEvent1($staff_id, date('Y-m-d', $dateByMonth));

                                if (!is_null($dayoffs)) {
                                    if ($dayoffs['from_type'] == 1 && $dayoffs['to_type'] == 2) {
                                        $return1 = $return;
                                        $return1['time_check_in'] = $timeCheckIn;
                                        $return1['time_check_out'] = $timeCheckOut;
                                    } else {

                                        if ($dayoffs['from_type'] == 1 && $dayoffs['to_type'] == 1) {
                                            $return1 = $this->handlingTime($return, $timeCheckOut, $timeCheckIn, $from_afternoon, $toPm);
                                        }
                                        if ($dayoffs['from_type'] == 2 && $dayoffs['to_type'] == 2) {
                                            $return1 = $this->handlingTime($return, $timeCheckOut, $timeCheckIn, $from_sa_morning, $to_sa_morning);
                                        }
                                        $return1['status'] = self::NUA_CONG;
                                        $return1['color'] = 'silver';
                                        $return1['total'] = 0.5;
                                    }
                                    if (
                                        $dayoffs['from_type'] == 2 && $dayoffs['to_type'] == 2 && $dateDayOff == '0.5M'
                                        || $dayoffs['from_type'] == 1 && $dayoffs['to_type'] == 1 && $dateDayOff == '0.5A'

                                    ) {
                                        $return1 = $return;
                                    }

                                    if (
                                        $checkHoliday == 'T' || $checkHoliday == 'T/2'
                                        && $dateByMonth <= strtotime(date('Y-m-d'))
                                    ) {
                                        $return1['color'] = 'red';
                                        $return1['status'] = self::NGHI_LAM;
                                        $return1['total'] = 0.5;
                                    }
                                } else {
                                    $return1 = $this->handlingTime($return, $timeCheckOut, $timeCheckIn, $fromAm, $toPm);
                                    $newborn = Newborn::where('user_id', $staff_id)->where('start', '<=',  date('Y-m-d', $dateByMonth))->where('end', '>=', date('Y-m-d', $dateByMonth))
                                        ->whereNull('deleted_at')
                                        ->first();

                                    if (!is_null($newborn)) {
                                        $check_to = new DateTime($to_morning);
                                        $check_af = new DateTime($from_afternoon);
                                        $gio_nghi_trua = $check_to->diff($check_af)->format('%H:%I:%S');

                                        $t_h = new DateTime($return1['foulHours']);
                                        $gio_nghi_trua1 = new DateTime($gio_nghi_trua);

                                        $so_gio_thuc_te = $gio_nghi_trua1->diff($t_h)->format('%H');
                                        $so_gio_thuc_te_i = $gio_nghi_trua1->diff($t_h)->format('%I');

                                        if (intval($so_gio_thuc_te_i) >= 30) {
                                            $so_gio_thuc_te = intval($so_gio_thuc_te) + 0.5;
                                        }

                                        if ($so_gio_thuc_te < $newborn->time) {
                                            $return1['status'] = self::NUA_CONG;
                                            $return1['color'] = 'silver';
                                            $return1['total'] = 0.5;
                                        } else {
                                            $return1['status'] = self::DU_NGAY_CONG;
                                            $return1['color'] = 'white';
                                            $return1['total'] = 1;
                                        }
                                    }


                                    if ($dateDayOff == '1') $return1 = $return; // nghỉ cả ngày

                                    if ($dateDayOff == '0.5A') {
                                        //nghỉ chiều làm sáng
                                        $return1 = $this->handlingTime($return, $timeCheckOut, $timeCheckIn, $fromAm, $to_morning);
                                        $return1['status'] = self::NUA_CONG;
                                        $return1['color'] = "silver";
                                        $return1['total'] = 0.5;
                                    }
                                    if ($dateDayOff == '0.5M') {
                                        //nghỉ sáng làm chiểu
                                        $return1 = $this->handlingTime($return, $timeCheckOut, $timeCheckIn, $from_afternoon, $from_afternoon);
                                        $return1['status'] = self::NUA_CONG;
                                        $return1['color'] = 'silver';
                                        $return1['total'] = 0.5;
                                    }


                                    if (
                                        in_array($checkHoliday, ['T', 'T/2', 'T/2 T/2'])
                                        && $dateByMonth <= strtotime(date('Y-m-d'))
                                    ) {
                                        if ($return1['total'] == 0.5 && $checkHoliday == 'T/2') {
                                            if ($checkHoliday == 'T/2') $return1['total'] = $return1['total'] + 0.5;
                                        } else if ($checkHoliday == 'T/2 T/2') {
                                            $return1['color'] = 'red';
                                            $return1['status'] = self::NGHI_LAM;
                                            $return1['total'] = 1;
                                        } else {
                                            $return1['time_check_in'] = $timeCheckIn;
                                            $return1['time_check_out'] = $timeCheckOut;

                                            $return1['color'] = 'red';
                                            $return1['status'] = self::NGHI_LAM;
                                            if ($checkHoliday == 'T') $return1['total'] = 1;
                                            if ($checkHoliday == 'T/2') $return1['total'] = 0.5;
                                        }
                                    }
                                }


                                if ($checkHoliday == 'H/2' || $checkHoliday == 'H') {
                                    $return1['time_check_in'] = $timeCheckIn;
                                    $return1['time_check_out'] = $timeCheckOut;
                                    $return1['color'] = 'red';
                                    $return1['status'] = self::NGHI_LAM;
                                    if ($checkHoliday == 'H/2') $return1['total'] = $return1['total'] + 0.5;
                                    if ($checkHoliday == 'H') $return1['total'] = 1;
                                }
                                if ($checkHoliday == 'BB' || $checkHoliday == 'BB/2') {
                                    $return1['time_check_in'] = $timeCheckIn;
                                    $return1['time_check_out'] = $timeCheckOut;
                                    $return1['color'] = 'red';
                                    $return1['status'] = self::NGHI_LAM;
                                    if ($checkHoliday == 'BB/2') $return1['total'] = $return1['total'] + 0.5;
                                    if ($checkHoliday == 'BB') $return1['total'] = 0;
                                }

                                if (in_array($checkHoliday, ['L/2 T/2'])) {
                                    $return['total'] = 0.5;
                                    $return1['color'] = 'red';
                                    $return1['status'] = self::NGHI_LAM;
                                }

                                $total_tv = $total_hd = $return1['total'];
                                $so_cong = $this->getWorkingByContract($data_contracts, $total_tv, $total_hd, $dateByMonth, $contract);
                                if ($so_cong == 0) {
                                    if ($contract->is_main == 1) $return1['total_tv'] = $total;
                                    if ($contract->is_main == 2) $return1['total_hd'] = $total;
                                } else {
                                    $return1['total_tv'] = $so_cong['total_tv'];
                                    $return1['total_hd'] = $so_cong['total_hd'];
                                }

                                if ($return1['status'] != self::QUEN_QUET) {
                                    $return1 = $this->getOT($data_contracts, $return1, $overTimes, $item, $check_out, $over_time, $dateByMonth, $contract, $dayoffs, $workSchedule);
                                }


                                // $check_th = Carbon::parse(date('Y-m-d', 1631293200))->format('l');

                            }
                        } else if ($department->type == \App\Define\Department::DECLARATION_OFFICE) {
                            if ($contract->type_status == 2) {
                                $set_notvalid_on = strtotime($contract->set_notvalid_on);
                                if ($set_notvalid_on < $dateByMonth) {
                                    $insert[$key][$dateByMonth] = $return;
                                    continue;
                                }
                            }

                            if ($contract->type_status == 1) {
                                $boNhiem = Contract::where('type_status', 5)->where('user_id', $contract->user_id)
                                    ->where('set_notvalid_on', '>', $startDate)
                                    ->orderBy('id', 'DESC')
                                    ->first();
                                $valid_from = strtotime($contract->valid_from);

                                if ($dateByMonth < $valid_from && is_null($boNhiem)) {
                                    $insert[$key][$dateByMonth] = $return;
                                    continue;
                                }
                            }

                            $shift = $shift_users->where('date', date('Y-m-d', $dateByMonth))->first()['shift'];

                            if (is_null($shift)) $shift = 100;

                            $return['shift'] = $shift;

                            $shift_times = ShiftTime::where('department_id', $department->id)->get();
                            $shift_time = $shift_times->where('category_shift_id', $shift)->first();

                            $limit_timein = date('Y-m-d ' . $shift_time->limit_time_in, $dateByMonth);
                            // if ($shift_time->category->type == 2 ||  $shift_time->category->type == 1) {
                            //     $limit_timeout = date('Y-m-d ' . $shift_time->limit_time_out, $dateByMonth);
                            // } else if ($shift_time->category->type == 3) {
                            //     $dd = date('Y-m-d', $dateByMonth);
                            //     $limit_timeout = date('Y-m-d ' . $shift_time->limit_time_out, strtotime($dd . ' +1 day'));

                            // }
                            if ($shift_time->limit_time_out < $shift_time->limit_time_in) {
                                $dd = date('Y-m-d', $dateByMonth);
                                $limit_timeout = date('Y-m-d ' . $shift_time->limit_time_out, strtotime($dd . ' +1 day'));
                            } else {
                                $limit_timeout = date('Y-m-d ' . $shift_time->limit_time_out, $dateByMonth);
                            }

                            $check_time = $checkInOut->where('primary_code', $key)->where('timeint', '>=', strtotime($limit_timein))->where('timeint', '<=', strtotime($limit_timeout));
                            $check_time = json_decode(json_encode($check_time), true);

                            if (count($check_time) == 0) {
                                if (is_null($item[$dateByMonth])) {
                                    $timeCheckIn = $timeCheckOut = 0;
                                } else if (!is_null($dayoffs)) {
                                    $current_out = current($item[$dateByMonth]);
                                    $end_in = end($item[$dateByMonth]);

                                    if ($current_out) $timeCheckIn = date('Y-m-d H:i:s', $end_in['timeint']);
                                    if ($end_in) $timeCheckOut = date('Y-m-d H:i:s', $current_out['timeint']);
                                }
                            } else {
                                $current_out = current($check_time);
                                $end_in = end($check_time);
                                if ($check_time) $timeCheckIn = date('Y-m-d H:i:s', $end_in['timeint']);
                                if ($check_time) $timeCheckOut = date('Y-m-d H:i:s', $current_out['timeint']);
                            }

                            if ($return['shift'] == 100 && $timeCheckIn == $timeCheckOut) {
                                $insert[$key][$dateByMonth] = $return;
                                continue;
                            }

                            $checkHoliday = StaffDayOff::checkDateHasEvent($staff_id, date('Y-m-d', $dateByMonth));

                            if ($timeCheckIn != 0 && $timeCheckOut != 0) {
                                $dateDayOff = StaffDayOff::checkDateHasEvent1($staff_id, date('Y-m-d', $dateByMonth));

                                if (!is_null($dayoffs)) {
                                    if ($dayoffs['from_type'] == 1 && $dayoffs['to_type'] == 2) {
                                        $return1 = $return;
                                        if ($shift == 100) {
                                            $return['time_check_in'] = $timeCheckIn;
                                            $return['time_check_out'] = $timeCheckOut;

                                            $return1 = $return;
                                        }
                                        $return1 = $this->getOtShift($data_contracts, $return1, $overTimes, $dateByMonth, $contract, $department, $shift_time, $type = '', $dayoffs = '');
                                    } else if ($dayoffs['from_type'] == 1 && $dayoffs['to_type'] == 1) {

                                        if ($shift != 100) {
                                            $return1 = $this->handlingTime($return, $timeCheckOut, $timeCheckIn, $shift_time->start_mid_shift, $shift_time->time_out);
                                            if ($return1['status'] == self::DU_NGAY_CONG) {
                                                $return1['status'] = self::NUA_CONG;
                                                $return1['color'] = 'silver';
                                            }

                                            $return1['total'] = 0.5;
                                            // $return1['color'] = 'silver';
                                            if ($dateDayOff == '0.5A' && in_array($checkHoliday, ['T', 'T/2', 'L', 'L/2', 'O', 'O/2', 'S', 'S/2', 'W', 'W/2', 'D', 'D/2', 'C', 'C/2'])) {
                                                //phòng ban làm chiều, nghỉ buổi chiều
                                                $return1['status'] = self::NGHI_LAM;
                                                $return1['color'] = 'red';
                                                $return1['total'] = 0;
                                            }

                                            if ($return1['status'] != self::QUEN_QUET) {
                                                $return1 = $this->getOtShift($data_contracts, $return1, $overTimes, $dateByMonth, $contract, $department, $shift_time, $type = 'lam_chieu');
                                            }
                                        }
                                    } else if ($dayoffs['from_type'] == 2 && $dayoffs['to_type'] == 2) {
                                        if ($shift != 100) {
                                            $return1 = $this->handlingTime($return, $timeCheckOut, $timeCheckIn, $shift_time->time_in, $shift_time->off_mid_shift);
                                            if ($return1['status'] == self::DU_NGAY_CONG) {
                                                $return1['status'] = self::NUA_CONG;
                                                $return1['color'] = 'silver';
                                            }
                                            $return1['total'] = 0.5;
                                            // $return1['color'] = 'silver';

                                            if ($dateDayOff == '0.5M' && in_array($checkHoliday, ['T', 'T/2', 'L', 'L/2', 'O', 'O/2', 'S', 'S/2', 'W', 'W/2', 'D', 'D/2', 'C', 'C/2'])) {
                                                //nghỉ SÁNG làm CHIỀU
                                                $return1['status'] = self::NGHI_LAM;
                                                $return1['color'] = 'red';
                                                $return1['total'] = 0;
                                            }

                                            if ($return1['status'] != self::QUEN_QUET) {
                                                $return1 = $this->getOtShift($data_contracts, $return1, $overTimes, $dateByMonth, $contract, $department, $shift_time, $type = 'lam_sang');
                                            }
                                        } else {
                                            $return['time_check_in'] = $timeCheckIn;
                                            $return['time_check_out'] = $timeCheckOut;

                                            $return1 = $return;
                                        }
                                    }
                                } else {

                                    if ($shift == 100) {
                                        $out = current($item[$dateByMonth]);
                                        $in = end($item[$dateByMonth]);

                                        if ($in) $timeCheckIn = date('H:i:s', $in['timeint']);
                                        if ($out) $timeCheckOut = date('H:i:s', $out['timeint']);

                                        $return['time_check_in'] = $timeCheckIn;
                                        $return['time_check_out'] = $timeCheckOut;

                                        $return1 = $return;
                                    } else {
                                        $return1 = $this->handlingTime($return, $timeCheckOut, $timeCheckIn, $shift_time->time_in, $shift_time->time_out);


                                        if (in_array($checkHoliday, ['L', 'O', 'D', 'W', 'S'])) {
                                            $return1['color'] = 'red';
                                            $return1['total'] = 0;
                                            $return1['status'] = self::NGHI_LAM;
                                        }

                                        $newborn = DB::table('newborns')->where('user_id', $staff_id)->where('start', '<=',  date('Y-m-d', $dateByMonth))->where('end', '>=', date('Y-m-d', $dateByMonth))
                                            ->whereNull('deleted_at')
                                            ->first();


                                        if (!is_null($newborn)) {
                                            $check_to = new DateTime($shift_time->off_mid_shift);
                                            $check_af = new DateTime($shift_time->start_mid_shift);
                                            $gio_nghi_trua = $check_to->diff($check_af)->format('%H:%I:%S');

                                            $t_h = new DateTime($return1['foulHours']);
                                            $gio_nghi_trua1 = new DateTime($gio_nghi_trua);

                                            $so_gio_thuc_te = $gio_nghi_trua1->diff($t_h)->format('%H');
                                            $so_gio_thuc_te_i = $gio_nghi_trua1->diff($t_h)->format('%I');


                                            if (intval($so_gio_thuc_te_i) >= 30) {
                                                $so_gio_thuc_te = intval($so_gio_thuc_te) + 0.5;
                                            }

                                            if ($so_gio_thuc_te < $newborn->time) {
                                                $return1['status'] = self::NUA_CONG;
                                                $return1['color'] = 'silver';
                                                $return1['total'] = 0.5;
                                            } else {
                                                $return1['status'] = self::DU_NGAY_CONG;
                                                $return1['color'] = 'white';
                                                $return1['total'] = 1;
                                            }
                                        }
                                    }
                                    if (
                                        $checkHoliday == 'T' || $checkHoliday == 'T/2'
                                        && $dateByMonth <= strtotime(date('Y-m-d'))
                                    ) {
                                        if ($return1['total'] == 0.5 && $checkHoliday == 'T/2') {
                                            if ($checkHoliday == 'T/2') {
                                                $return1['total'] = $return1['total'] + 0.5;
                                                $return1['an_chinh'] = 1;
                                            }
                                        } else {
                                            $return1['time_check_in'] = $timeCheckIn;
                                            $return1['time_check_out'] = $timeCheckOut;

                                            $return1['color'] = 'red';
                                            $return1['status'] = self::NGHI_LAM;
                                            if ($checkHoliday == 'T') {
                                                $return1['total'] = 1;
                                                $return1['an_chinh'] += 1;
                                            }
                                            if ($checkHoliday == 'T/2') $return1['total'] = 0.5;
                                        }
                                    }


                                    if (($dateDayOff == '0.5M' || $dateDayOff == '0.5A') && !in_array($checkHoliday, ['T', 'T/2'])) {
                                        if ($return1['total'] == 1) {
                                            $return1['color'] = 'silver';
                                            $return1['total'] = '0.5';
                                            $return1['status'] = self::NUA_CONG;
                                        }
                                    }


                                    if ($return1['status'] != self::QUEN_QUET) {
                                        $return1 = $this->getOtShift($data_contracts, $return1, $overTimes, $dateByMonth, $contract, $department, $shift_time);
                                    }
                                }
                            } else {

                                $return1 = $return;
                            }

                            if ($checkHoliday == 'H/2' || $checkHoliday == 'H') {
                                $return1['color'] = 'red';
                                $return1['status'] = self::NGHI_LAM;
                                if ($checkHoliday == 'H/2') $return1['total'] = $return1['total'] + 0.5;
                                if ($checkHoliday == 'H') {
                                    $return1['an_chinh'] += 1;
                                    $return1['total'] = 1;
                                }
                            }

                            if ($checkHoliday == 'BB' || $checkHoliday == 'BB/2') {

                                $return1['color'] = 'red';
                                $return1['status'] = self::NGHI_LAM;
                                if ($checkHoliday == 'BB/2') $return1['total'] = $return1['total'] + 0.5;
                                if ($checkHoliday == 'BB') {
                                    $return1['an_chinh'] += 1;
                                    $return1['total'] = 1;
                                };
                            }

                            if ($return1['status'] == self::DU_NGAY_CONG) {
                                $return1['an_chinh'] += 1;
                            }

                            if ($checkHoliday == 'L') {
                                $return1['an_chinh'] += 1;
                            }

                            if (in_array($checkHoliday, ['L/2', 'D/2', 'W/2', 'H/2', 'T/2']) && $return1['total'] == 0.5) {
                                $return1['an_chinh'] += 1;
                            }



                            $total_tv = $total_hd = $return1['total'];

                            $so_cong = $this->getWorkingByContract($data_contracts, $total_tv, $total_hd, $dateByMonth, $contract);

                            if ($so_cong == 1 || $so_cong == 0) {
                                if ($contract->is_main == 1) $return1['total_tv'] = $total_tv;
                                if ($contract->is_main == 2) $return1['total_hd'] = $total_hd;
                            } else {

                                $return1['total_tv'] = $so_cong['total_tv'];
                                $return1['total_hd'] = $so_cong['total_hd'];
                            }
                        }


                        $insert[$key][$dateByMonth] = $return1;
                    } else {
                        if ($contract->type_status == 2) {
                            $set_notvalid_on = strtotime($contract->set_notvalid_on);
                            if ($set_notvalid_on < $dateByMonth) {
                                $insert[$key][$dateByMonth] = $return;
                                continue;
                            }
                        }

                        if ($contract->type_status == 1) {
                            $boNhiem = Contract::where('type_status', 5)->where('user_id', $contract->user_id)
                                ->where('set_notvalid_on', '>', $startDate)
                                ->orderBy('id', 'DESC')
                                ->first();
                            $valid_from = strtotime($contract->valid_from);
                            if ($dateByMonth < $valid_from && is_null($boNhiem)) {
                                $insert[$key][$dateByMonth] = $return;
                                continue;
                            }
                        }

                        $insert[$key][$dateByMonth] = $return;

                        $checkHoliday = StaffDayOff::checkDateHasEvent($staff_id, date('Y-m-d', $dateByMonth));
                        if (array_key_exists($dateByMonth, $dayoffs)) {
                            if ($dayoffs['from_type'] == 1 && $dayoffs['to_type'] == 2) {
                                if ($checkHoliday == 'H/2' || $checkHoliday == 'H') {
                                    if ($checkHoliday == 'H/2') $return['total'] = 0.5;
                                    if ($checkHoliday == 'H') $return['total'] = 1;
                                }
                            } else {
                                if ($checkHoliday == 'T/2' || $checkHoliday == 'T') $return['total'] = 0.5;
                            }
                        } else {
                            if ($checkHoliday == 'H/2' || $checkHoliday == 'H') {
                                if ($checkHoliday == 'H/2') $return['total'] = 0.5;
                                if ($checkHoliday == 'H') $return['total'] = 1;
                            }
                            if (
                                in_array($checkHoliday, ['T', 'T/2', 'T/2 T/2'])
                                && $dateByMonth <= strtotime(date('Y-m-d'))
                            ) {
                                if ($checkHoliday == 'T/2') $return['total'] = 0.5;
                                if ($checkHoliday == 'T') $return['total'] = 1;
                            }
                            if (strpos($checkHoliday, 'T/2')) $return['total'] = 0.5;
                            if ($checkHoliday == 'T/2 T/2') $return['total'] = 1;
                        }

                        if ($workSchedule->type == 1) {

                            $check_th = Carbon::parse(date('Y-m-d', $dateByMonth))->format('l');
                            if (strtoupper($check_th) == 'SATURDAY' && $dateByMonth <= strtotime(date('Y-m-d'))) {
                                if ($contract->type_status == 1) {
                                    $valid_from = strtotime($contract->valid_from);
                                    if ($dateByMonth < $valid_from) {
                                        $insert[$key][$dateByMonth] = $return;
                                        continue;
                                    } else {
                                        $return['total'] = 0.5;
                                        $return['status'] = self::NUA_CONG;
                                        $return['color'] = 'silver';
                                    }
                                }

                                if (!is_null($dayoffs)) {
                                    if ($dayoffs['from_type'] <> $dayoffs['to_type']) {
                                        $return['time_check_in'] = $timeCheckIn;
                                        $return['time_check_out'] = $timeCheckOut;
                                        $return['color'] = 'red';
                                        $return['status'] = self::NGHI_LAM;
                                        $return['total'] = 0;
                                    } else {
                                        $return['total'] = 0.5;
                                        $return['status'] = self::NUA_CONG;
                                        $return['color'] = 'silver';

                                        if ($checkHoliday == 'T/2') {
                                            $return['status'] = self::NGHI_LAM;
                                        }
                                    }
                                } else {
                                    $return['total'] = 1;
                                    $return['status'] = self::DU_NGAY_CONG;
                                    $return['color'] = 'white';
                                }

                                if ($checkHoliday == 'T/2 T/2' || $checkHoliday == 'T') {
                                    $return['status'] = self::NGHI_LAM;
                                    $return['color'] = 'red';
                                    $return['total'] = 1;
                                }

                                if (in_array($checkHoliday, ['L', 'L/2', 'O', 'O/2', 'S', 'S/2', 'W', 'W/2', 'D', 'D/2', 'C', 'C/2'])) {
                                    $return['status'] = self::NGHI_LAM;
                                    $return['total'] = 0;
                                    $return['color'] = 'red';
                                }

                                if ($checkHoliday == 'H/2') {
                                    $return['status'] = self::HOLIDAY_NUA_CONG;
                                    $return['color'] = 'silver';
                                    $return['total'] = 0.5;
                                }
                                if ($checkHoliday == 'H') {
                                    $return['status'] = self::HOLIDAY;
                                    $return['color'] = 'red';
                                    $return['total'] = 1;
                                }

                                if ($checkHoliday == 'BB' || $checkHoliday == 'BB/2') {
                                    $return['time_check_in'] = $timeCheckIn;
                                    $return['time_check_out'] = $timeCheckOut;
                                    $return['color'] = 'red';
                                    $return['status'] = self::NGHI_LAM;
                                    $return['total'] = 0;
                                }
                            }
                        }

                        if ($department->type == \App\Define\Department::DECLARATION_OFFICE) {
                            if ($checkHoliday == 'L') {
                                $return['an_chinh'] += 1;
                            }
                        }

                        $total_tv = $total_hd = $return['total'];
                        $so_cong = $this->getWorkingByContract($data_contracts, $total_tv, $total_hd, $dateByMonth, $contract);
                        if ($so_cong == 0) {
                            if ($contract->is_main == 1) $return['total_tv'] = $total;
                            if ($contract->is_main == 2) $return['total_hd'] = $total;
                        } else {
                            $return['total_tv'] = $so_cong['total_tv'];
                            $return['total_hd'] = $so_cong['total_hd'];
                        }

                        $insert[$key][$dateByMonth] = $return;
                    }

                    ksort($insert[$key]);
                }

                $total_tv = array_sum(array_column($insert[$key], 'total_tv'));
                $total_hd = array_sum(array_column($insert[$key], 'total_hd'));
                $timeKeepingDetail[] = [
                    'timekeeping_id' => $timeKeeping->id,
                    'code'           => $key,
                    'detail'         => json_encode($insert[$key]),
                    'created_by'     => $timeKeeping->created_by,
                    'total'          => $total_hd + $total_tv,
                    'staff_id'       => $staff_id
                ];
            }
            $date = [];
            if (count($user_ids) > 0) {
                foreach ($user_ids as $key => $user) {
                    $contract = $contracts->where('user_id', $user)->first();

                    if (is_null($contract)) continue;
                    if (strtotime($contract->set_notvalid_on) < strtotime($startDate) && $contract->type_status == 2) {
                        continue;
                    }

                    foreach ($this->getDateByMonth($data['month'], $data['year']) as $k1 => $v1) {
                        $dateByMonth = strtotime($v1);

                        $timeCheckIn  = $timeCheckOut = $totalHours = $foulHours = $status = $ot = $total = $shift = $total_tv = $total_hd = $type_ot = 0;
                        $color = "red";
                        $return = [
                            'time_check_out'    => $timeCheckOut,
                            'time_check_in'     => $timeCheckIn,
                            'total_hours'       => $totalHours,
                            'status'            => $status,
                            'foul_hours'        => $foulHours,
                            'ot'                => $ot,
                            'color'             => $color,
                            'total_tv'          => $total_tv,
                            'total_hd'          => $total_hd,
                            'type_ot'           => $type_ot
                        ];

                        $checkHoliday = StaffDayOff::checkDateHasEvent($user, date('Y-m-d', $dateByMonth));
                        if (array_key_exists($dateByMonth, $dayoffs)) {
                            if ($dayoffs['from_type'] == 1 && $dayoffs['to_type'] == 2) {
                                if ($checkHoliday == 'H/2' || $checkHoliday == 'H') {
                                    if ($checkHoliday == 'H/2') $return['total'] = 0.5;
                                    if ($checkHoliday == 'H') $return['total'] = 1;
                                }
                            } else {
                                if ($checkHoliday == 'T/2' || $checkHoliday == 'T') $return['total'] = 0.5;
                            }
                        } else {
                            if ($checkHoliday == 'H/2' || $checkHoliday == 'H') {
                                if ($checkHoliday == 'H/2') $return['total'] = 0.5;
                                if ($checkHoliday == 'H') $return['total'] = 1;
                            }
                            if (
                                $checkHoliday == 'T' || $checkHoliday == 'T/2'
                                && $dateByMonth <= strtotime(date('Y-m-d'))
                            ) {
                                if ($checkHoliday == 'T/2') $return['total'] = 0.5;
                                if ($checkHoliday == 'T') $return['total'] = 1;
                            }
                            if (strpos($checkHoliday, 'T/2')) $return['total'] = 0.5;
                        }

                        $total_tv = $total_hd = $return['total'];
                        $so_cong = $this->getWorkingByContract($data_contracts, $total_tv, $total_hd, $dateByMonth, $contract);
                        if ($so_cong == 0) {
                            if ($contract->is_main == 1) $return['total_tv'] = $total;
                            if ($contract->is_main == 2) $return['total_hd'] = $total;
                        } else {
                            $return['total_tv'] = $so_cong['total_tv'];
                            $return['total_hd'] = $so_cong['total_hd'];
                        }

                        $date[$dateByMonth] = $return;
                    }


                    $timeKeepingDetail[] = [
                        'timekeeping_id' => $timeKeeping->id,
                        'code'           => 0,
                        'detail'         => json_encode($date),
                        'created_by'     => $timeKeeping->created_by,
                        'total'          => 0,
                        'staff_id'       => $user
                    ];
                }
            }
        } else {
            $date = [];
            $user_ids = User::where('department_id', $data['department_id'])->pluck('id')->toArray();
            foreach ($user_ids as $key => $user) {
                $contract = $contracts->where('user_id', $user)->first();
                if (is_null($contract)) continue;
                if (strtotime($contract->set_notvalid_on) < strtotime($startDate) && $contract->type_status == 2) {
                    if (in_array($contract->user_id, $user_ids)) {
                        $k = array_search($contract->user_id, $user_ids);
                        unset($user_ids[$k]);
                    }
                    continue;
                }

                foreach ($this->getDateByMonth($data['month'], $data['year']) as $k1 => $v1) {
                    $dateByMonth = strtotime($v1);

                    $timeCheckIn  = $timeCheckOut = $totalHours = $foulHours = $status = $ot = $total = $shift = $total_tv = $total_hd = $type_ot = 0;
                    $color = "red";
                    $return = [
                        'time_check_out'    => $timeCheckOut,
                        'time_check_in'     => $timeCheckIn,
                        'total_hours'       => $totalHours,
                        'status'            => $status,
                        'foul_hours'        => $foulHours,
                        'ot'                => $ot,
                        'color'             => $color,
                        'total_tv'          => $total_tv,
                        'total_hd'          => $total_hd,
                        'type_ot'           => $type_ot
                    ];

                    $date[$dateByMonth] = $return;
                }

                $timeKeepingDetail[] = [
                    'timekeeping_id' => $timeKeeping->id,
                    'code'           => 0,
                    'detail'         => json_encode($date),
                    'created_by'     => $timeKeeping->created_by,
                    'total'          => 0,
                    'staff_id'       => $user
                ];
            }
        }

        if (DB::table('timekeeping_detail')->insert($timeKeepingDetail)) {
            return true;
        }


        return false;
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $data['created_by'] = $request->user()->id;
        $validator = \Validator::make($data, TimeKeeping::rules());
        $validator->setAttributeNames(trans('time_keeping'));
        $startDate = date('Y-m-d 00:00:00', strtotime($data['year'] . '-' . (($data['month'] - 1)) . '-' . 26));
        $endDate = date('Y-m-d 23:59:00', strtotime($data['year'] . '-' . $data['month'] . '-' . 26));
        $department = Department::find($data['department_id']);

        if ($validator->passes()) {
            $department = Department::find($data['department_id']);
            if ($department->type == \App\Define\Department::FUNCTIONAL_OFFICE) {
                $workSchedule = WorkSchedule::where('company_id', $data['company_id'])->where('department_id', $data['department_id'])
                    ->first();
            } else if ($department->type == \App\Define\Department::DECLARATION_OFFICE) {
                $workSchedule = ShiftTime::where('company_id', $data['company_id'])->where('department_id', $data['department_id'])
                    ->first();
            }
            if (empty($workSchedule) && $department->type != \App\Define\Department::HOURS) {
                return \Response::json([
                    'status' => 'FAIL',
                    'message' => trans('timekeeping.error_workschedule')
                ]);
            }

            $check = TimeKeeping::where('company_id', $data['company_id'])->where('department_id', $data['department_id'])
                ->where('month', $data['month'])->where('year', $data['year'])->first();


            if ($check) {
                return \Response::json([
                    'status' => 'FAIL',
                    'message' => trans('timekeeping.exist')
                ]);
            }
            User::syncAttendanceMachine();
            $contracts = Contract::where('department_id', $data['department_id'])->whereIn('type_status', [1, 2, 7])->get();

            $staffs = User::whereIn('id', $contracts->pluck('user_id')->toArray())
                ->where('active', 1)
                ->get();
            //$staffs->pluck('code_timekeeping')->toArray()
            $checkInOut = DB::connection('mysql2')->table('CHECKINO')->whereIn('primary_code', $staffs->pluck('code_timekeeping')->toArray())
                ->where('timeint', '>=', strtotime($startDate))
                ->where('timeint', '<=', strtotime($endDate))
                ->orderBy('timeint', 'DESC')
                ->get();

            // $concurrent_contract = ConcurrentContract::where('status', 1)->where('company_id', $data['company_id'])
            //         ->where('department_id', $data['department_id'])
            //         ->pluck('user_id');

            // if (count($checkInOut) == 0 && count($concurrent_contract) == 0) {
            //     return \Response::json([
            //         'status' => 'FAIL',
            //         'message' => trans('timekeeping.error_timekeeping')
            //     ]);
            // }

            DB::beginTransaction();
            try {
                $timeKeeping = TimeKeeping::create($data);
                // $timeKeeping = 1;
                $this->checkInOut($timeKeeping, $data, $checkInOut, $workSchedule, $department, $staffs, $contracts, $startDate);

                DB::commit();
                return \Response::json([
                    'status' => 'SUCCESS',
                    'message' => trans('timekeeping.success'),
                    'link' => route('admin.timekeeping.detail', $timeKeeping->id)
                ]);
            } catch (Exception $e) {
                DB::rollBack();
                return \Response::json([
                    'status' => 'FAIL',
                    'message' => $e->getMessage()
                ]);
            }
        }

        return \Response::json(['errors' => $validator->errors()]);
    }

    public function detail(Request $request, $id)
    {
        $search = $request->input('fullname');
        $getDays = $getDates = [];
        $totalDayOf = 0;
        $detail = TimeKeeping::find($id);
        if (empty($detail)) {
            return redirect()->route('admin.timekeeping.index');
        }
        $getDateByMonth = $this->getDateByMonth($detail->month, $detail->year);
        // $start = $detail->year . '-' . ($detail->month - 1) . '-' . 26;
        // $end = $detail->year . '-' . $detail->month . '-' . 25;

        $start = date('Y-m-d', strtotime($detail->year . '-' . (($detail->month - 1)) . '-' . 26));
        $end = date('Y-m-d', strtotime($detail->year . '-' . $detail->month . '-' . 25));

        foreach ($getDateByMonth as $key => $item) {
            $getDays[] = substr(Carbon::parse($item)->format('l'), 0, 3);
            $getDates[] = Carbon::parse($item)->format('d');
            $getDateByMonth[$key] = strtotime($item);
        }
        $totalDay = count($getDays);
        $arr = array_count_values($getDays);

        $total_day_request = OverTimes::totalWorkingInMonth($detail->month, $detail->year, $detail->department_id);
        $user_ids = ConcurrentContract::where('company_id', $detail->company_id)->where('department_id', $detail->department_id)->pluck('user_id');

        $concurrent_contract = ConcurrentContract::where('status', 1)->where('company_id', $detail->company_id)
            ->where('department_id', $detail->department_id)
            ->pluck('user_id');

        $timekeeping_ids = TimeKeepingDetail::whereHas('timekeeping', function ($q) use ($detail) {
            $q->where('month', $detail->month)->where('year', $detail->year);
        })->whereIn('staff_id', $concurrent_contract)->pluck('id');

        if (is_null($timekeeping_ids)) $timekeeping_ids = [];

        if (!empty($search)) {
            $items = TimeKeepingDetail::where('timekeeping_id', $id)->whereHas('staff', function ($query) use ($search) {
                $query->where('fullname', 'like', '%' . $search . '%');
            })->orWhereIn('id', $timekeeping_ids)->get();
        } else {
            $infoPermission = PermissionUserObject::getMorePermissions(Auth::user()->id, 'timekeeping.read');

            if (Auth::user()->hasRole('NV') && count($infoPermission['departments']) == 0) {
                $items = TimeKeepingDetail::with('staff', 'timekeeping')->where('timekeeping_id', $id)->where('staff_id', Auth::user()->id)->get();
            } else {
                $items = TimeKeepingDetail::with('staff', 'timekeeping')->where('timekeeping_id', $id)->orWhereIn('id', $timekeeping_ids)->get();
            }
        }

        $dayoffs = CalendarDepartment::getDayOff($detail->department_id);
        $dayoffs = collect($dayoffs);

        foreach ($items as $key => $item) {
            $concurrent_contract = ConcurrentContract::where('status', 1)
                ->where('department_id', $detail->department_id)
                ->where('user_id', $item->staff_id)->first();
            if (!is_null($concurrent_contract)) {
                $items[$key]['concurrent_contract'] = 1;
            }
            $items[$key]['detail'] = json_decode($item->detail, true);
            // $staffDayOffs = StaffDayOff::where('user_id', $item->staff_id)
            //     ->where('start', '<=', $end)
            //     ->where('end', '>=', $start)
            //     ->get();
            // foreach ($staffDayOffs as $index => $staffDayOff) {
            //     $totalDayOf = $staffDayOff->total;
            //     if (strtotime($staffDayOff->start) >= strtotime($start) && strtotime($staffDayOff->end) <= strtotime($end)) {
            //         foreach (\App\Defines\Schedule::getDayOffType() as $k => $v) {
            //             if ($staffDayOff->code == $v && $staffDayOff->user_id == $item->staff_id) {
            //                 $items[$key][$k] = $totalDayOf++;
            //             }
            //         }
            //     } else {
            //         $betweenDay = date('d', strtotime($staffDayOff->start));
            //         foreach (\App\Defines\Schedule::getDayOffType() as $k => $v) {
            //             if ($staffDayOff->code == $v) {
            //                 $items[$key][$k] = $totalDayOf++ - 26 - $betweenDay;
            //             }
            //         }
            //     }
            // }
            // $items[$key]['total_ot'] = array_sum(array_column($item->detail, 'ot_tv')) + array_sum(array_column($item->detail, 'ot_hd')) + array_sum(array_column($item->detail, 'hours_night_day_ot_tv)')) + array_sum(array_column($item->detail, 'hours_night_day_ot_hd'));
            $nghi_k_xin = 0;
            $nghi_k_xin_tt = 0;

            if ($item->staff->department_id == $detail->department_id) {
                foreach ($item->detail as $dateByMonth => $i) {
                    if (in_array($i['total'], [0, null])) {
                        $dayoff = $dayoffs->where('start', date('Y-m-d', $dateByMonth))->first();
                        $checkHoliday = StaffDayOff::checkDateHasEvent($item->staff_id, date('Y-m-d', $dateByMonth));
                        if (is_null($dayoff) && $checkHoliday == ' ') $nghi_k_xin++;
                        if (!is_null($dayoff) && $checkHoliday == ' ') {
                            if ($dayoff['to_type'] == $dayoff['from_type']) $nghi_k_xin_tt += 0.5;
                        }
                        if (is_null($dayoff) && in_array($checkHoliday, ['L/2', 'T/2', 'W/2', 'D/2', 'C/2', 'O/2'])) $nghi_k_xin_tt += 0.5;
                    }
                }
            }
            $items[$key]['nghi_k_xin'] = $nghi_k_xin + $nghi_k_xin_tt;


            $total_work = StaffDayOff::countTotalInMonthForTimeKeeping($item->staff_id, $detail->month, $detail->year, $detail->department_id);


            $total_hd = array_sum(array_column($item->detail, 'total_hd'));
            if ($total_hd >= $total_work['T']) {
                $total_hd = $total_hd - $total_work['T'];
            }
            $total_tv = array_sum(array_column($item->detail, 'total_tv'));

            if ($total_tv >= $total_work['T']) {
                $total_tv = $total_tv - $total_work['T'];
            }

            $items[$key]['total_tv'] = $total_tv;
            $shifts = array_count_values(array_column($item->detail, 'shift'));
            $cong = collect($item->detail);

            foreach ($shifts as $s => $shift) {
                $category = CategoryShift::find($s);
                if ($category->type == 1) {
                    $items[$key]['shift_day'] += $cong->where('shift', $s)->sum('total');
                } else if ($category->type == 2) {
                    $items[$key]['shift_hc'] += $cong->where('shift', $s)->sum('total');
                } else if ($category->type == 3) {
                    $items[$key]['shift_night'] += $cong->where('shift', $s)->sum('total');
                }
            }


            if (!empty($total_work)) {
                $items[$key]['total'] = $total_hd + $total_tv + $total_work['L'] + $total_work['D'] + $total_work['W'] + $total_work['T'];
            }
            $items[$key]['total_hd'] = $total_hd;

            // if ($total_work['H'] > 0) $total_h = $total_work['H'];
        }
        // if ($total_h > 0) $total_day_request = $total_day_request + $total_h;

        $start = Carbon::createFromDate($detail->year, $detail->month - 1, 26)->format('Y-m-d');
        $end = Carbon::createFromDate($detail->year, $detail->month)->format('Y-m-d');

        $getShift = DefineShift::getShift();
        $nghi_phong_ban = CalendarDepartment::where('department_id', $detail->department_id)->where('categories', 'holiday')->get();
        $nghi_nhan_vien = StaffDayOff::whereIn('user_id', $items->pluck('staff_id')->toArray())->where('start', '<=', $end)->where('end', '>=', $start)->get();
        $workSchedule = WorkSchedule::where('department_id', $detail->department_id)->first();

        if ($request->export == self::EXPORT) {
            return [
                'items' => $items,
                'detail' => $detail,
                'getDays' => $getDays,
                'getDates' => $getDates,
                'total_day_request' => $total_day_request,
                'nghi_phong_ban' => $nghi_phong_ban,
                'nghi_nhan_vien' => $nghi_nhan_vien,
                'workSchedule' => $workSchedule,
                'getShift' => $getShift
            ];
        }

        return view('backend.timekeeping.detail', compact('items', 'detail', 'getDays', 'getDates', 'total_day_request', 'getShift', 'nghi_phong_ban', 'nghi_nhan_vien', 'workSchedule'));
    }

    public function exportExcel(Request $request, $id)
    {
        $timekeeping = TimeKeeping::find($id);
        if (empty($timekeeping)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.timekeeping.index');
        }
        $request->export = self::EXPORT;
        $data = $this->detail($request, $id);
        if (empty($data)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.timekeeping.index');
        }

        return \Excel::download(new \App\Exports\TimekeepingExport($data), 'Bang-cong' . '_' . date('H.m_d-m-Y') . '.xlsx');
    }

    public function sign(Request $request, $id)
    {
        $timekeeping = TimeKeeping::find($id);
        if (empty($timekeeping)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.timekeeping.index');
        }
        $request->export = self::EXPORT;
        $data = $this->detail($request, $id);
        if (empty($data)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.timekeeping.index');
        }

        return \Excel::download(new \App\Exports\TimekeepingSign($data), 'cong' . '_' . date('H.m_d-m-Y') . '.xlsx');
    }

    public function updateTimekeeping(Request $request, $id)
    {
        $total_tv = $total_hd = 1;
        $data = $request->all();

        $return = \Response::json([
            'status' => 'FAIL',
            'message' => 'Lỗi'
        ]);

        $timekeepingDetail = TimeKeepingDetail::find($id);
        if (empty($timekeepingDetail)) {
            return $return;
        }

        $detail = json_decode($timekeepingDetail->detail, true);
        // if ($detail[$data['key']]['total'] == $data['status']) {
        //     return \Response::json([
        //         'status' => 'FAIL',
        //         'message' => 'Không thay đổi dữ liệu'
        //     ]);
        // }

        $department = Department::where('id', $timekeepingDetail->timekeeping->department_id)->first();
        $data_contracts = Contract::getContractsInAMonth($timekeepingDetail->staff_id, $timekeepingDetail->timekeeping->month, $timekeepingDetail->timekeeping->year);
        $contract = Contract::where('user_id', $timekeepingDetail->staff_id)->whereIn('type_status', [1, 7])->first();
        $so_cong = $this->getWorkingByContract($data_contracts, $total_tv, $total_hd, $data['key'], $contract);

        if (array_key_exists($data['key'], $detail)) {
            $data_old = [
                $data['key'] => $detail[$data['key']]
            ];
            if ($department->type == \App\Define\Department::FUNCTIONAL_OFFICE && in_array($data['status'], [1, 10])) {
                $data['status'] == 1 ? $total = 1 : $total = 0.5;
                unset($data['shift']);

                $detail[$data['key']]['status'] = $data['status'];
                $detail[$data['key']]['color'] = $data['status'] == 1 ? 'white' : 'silver';

                if ($so_cong == 1 || $so_cong == 0) {
                    if ($contract->is_main == 1) $detail[$data['key']]['total_tv'] = $total;
                    if ($contract->is_main == 2) $detail[$data['key']]['total_hd'] = $total;
                } else {
                    $detail[$data['key']]['total_tv'] = $so_cong['total_tv'] == 1 ? $total : 0;
                    $detail[$data['key']]['total_hd'] = $so_cong['total_hd'] == 1 ? $total : 0;
                }
            } else {
                unset($data['status']);
                if ($department->type == \App\Define\Department::BORDER_OFFICE) {
                    unset($data['shift']);
                    $data['shift'] = $data['type-shift'];
                    unset($data['type-shift']);
                }
                if ($department->type == \App\Define\Department::DECLARATION_OFFICE) {
                    unset($data['type-shift']);
                }
                $ca_nua = $data['shift'];
                $data['shift'] = str_replace('_', '', $data['shift']);

                if (preg_match("/_/i", $ca_nua)) {
                    $ca_nua = 10;
                }

                if (in_array($data['shift'], [1, 2, 3, 4, 5, 6, 7, 8, 9, 10])) {
                    !is_null($data['shift']) ? $total = 1 : '';
                    $detail[$data['key']]['shift'] = $data['shift'];
                    $detail[$data['key']]['status'] = 1;
                    $detail[$data['key']]['color'] = 'white';
                    $detail[$data['key']]['an_chinh'] = 1;

                    if ($so_cong == 1 || $so_cong == 0) {
                        if ($contract->is_main == 1) $detail[$data['key']]['total_tv'] = $total_tv;
                        if ($contract->is_main == 2) $detail[$data['key']]['total_hd'] = $total_hd;
                    } else {
                        $detail[$data['key']]['total_tv'] = $so_cong['total_tv'];
                        $detail[$data['key']]['total_hd'] = $so_cong['total_hd'];
                    }

                    if ($ca_nua == 10) {
                        if ($detail[$data['key']]['total_tv'] == 1) $detail[$data['key']]['total_tv'] = 0.5;
                        if ($detail[$data['key']]['total_hd'] == 1) $detail[$data['key']]['total_hd'] = 0.5;

                        $detail[$data['key']]['color'] = 'silver';
                        $detail[$data['key']]['status'] = self::NUA_CONG;
                        $detail[$data['key']]['an_chinh'] = 0;
                    }
                }
            }
            $detail[$data['key']]['total'] = $total;
            $update_total = $timekeepingDetail->total - $data_old[$data['key']]['total'] + $total;
            $data_new = [
                $data['key'] => $detail[$data['key']]
            ];
        }

        $check = $timekeepingDetail->update([
            'detail' => json_encode($detail),
            'total' => $update_total
        ]);
        if ($check) {
            $response = [
                'data_new' => json_encode($data_new),
                'data_old' => json_encode($data_old),
                'action_by' => $request->user()->id,
                'action_at' => date('Y-m-d H:i:s'),
                'field' => '',
                'note' => $data['note'],
                'log_type' => get_class($timekeepingDetail),
                'log_id' => $id,
            ];

            if (Log::create($response)) {
                return \Response::json([
                    'status' => 'SUCCESS',
                    'message' => 'Cập nhập thành công',
                    'cong' => $data['status'],
                    'key' => $data['key'],
                    'id' => $id,
                    'total' => $data['status'] == 10 ? 0.5 : $data['status']
                ]);
            }
            return \Response::json([
                'status' => 'SUCCESS',
                'message' => 'Cập nhập thành công',
                'cong' => $data['status'],
                'key' => $data['key'],
                'id' => $id,
                'total' => $data['status'] == 10 ? 0.5 : $data['status']
            ]);
        }

        return $return;
    }

    public function getLog($id)
    {
        $data = [];
        $content = '';
        $logs = TimeKeepingDetail::find($id)->logs;
        if (count($logs) == 0) {
            return \Response::json([
                'status' => 'FAIL',
                'message' => 'Không có lịch sử cập nhật'
            ]);
        }

        foreach ($logs as $key => $log) {
            $data_new = json_decode($log->data_new, true);
            $data_old = json_decode($log->data_old, true);

            $detail_old = reset($data_old);
            $content_old = 'Ngày ' . date('d/m/Y', array_key_first($data_old)) . ' - ' . ($detail_old['status'] == 0 ? 'Không đi làm' : 'Đi làm nửa buổi');

            $detail = reset($data_new);
            if (in_array($detail['shift'], [1, 2, 3, 4, 5])) {
                $content = 'Ngày ' . date('d/m/Y', array_key_first($data_new)) . ' - ' . (in_array($detail['shift'], [1, 2, 3]) ? ('Đi làm ca ' . $detail['shift']) : ('Làm hành chính'));
            } else {
                $content = 'Ngày ' . date('d/m/Y', array_key_first($data_new)) . ' - ' . ($detail['status'] == 1 ? 'Đi làm cả ngày' : 'Đi làm nửa buổi');
            }
            $data[] = [
                'id'            => $log->id,
                'content_old'   => $content_old,
                'content'       => $content,
                'note'          => is_null($log->note) ? '' : $log->note,
                'user'          => $log->user->fullname,
                'action_at'     => date('d/m/Y H:i:s', strtotime($log->action_at))
            ];
        }
        array_multisort(array_column($data, 'id'), SORT_DESC, $data);

        return \Response::json([
            'status' => 'SUCCESS',
            'message' => 'Thành công',
            'data' => $data
        ]);
    }

    public function handlingTime($return, $timeCheckOut, $timeCheckIn, $startTime, $endTime, $department = '')
    {
        if ($return['shift'] == 100) {
            return $return;
        }
        $return['time_check_out'] = $timeCheckOut;
        $return['time_check_in'] = $timeCheckIn;

        $timeCheckIn = date('H:i:s', strtotime($timeCheckIn));
        $timeCheckOut = date('H:i:s', strtotime($timeCheckOut));

        $check_in = new Carbon($timeCheckIn);
        $check_out = new Carbon($timeCheckOut);


        $return['foulHours'] = $check_in->diff($check_out)->format('%H:%I:%S');


        if ($timeCheckIn == $timeCheckOut && $timeCheckIn == "00:00:00" && $timeCheckOut = "00:00:00") {
            $return['shift'] = 0;
        } else if (
            $timeCheckIn == $timeCheckOut
            || $timeCheckIn == "00:00:00" && $timeCheckOut != "00:00:00"
            || $timeCheckOut == "00:00:00" && $timeCheckIn != "00:00:00"
            || $timeCheckOut == "00:00:00"
        ) {
            if ($timeCheckIn == $timeCheckOut && is_null($return['shift'])) {
                if ($timeCheckIn <= "12:00:00")  $return['time_check_out'] = "0";
                if ($timeCheckOut > "12:00:00")  $return['time_check_in'] = "0";
            }

            $return['status'] = self::QUEN_QUET;
            $return['color'] = "olive";
            $return['total'] = 0.5;
        } else if ($timeCheckIn <= $startTime && $timeCheckOut >= $endTime) {
            $return['status'] = self::DU_NGAY_CONG;
            $return['color'] = "white";
            $return['total'] = 1;
        } else if ($timeCheckIn > $startTime && $timeCheckOut < $endTime) {
            $return['status'] = self::DIMUON_VESOM;
            $return['color'] = "#F433FF";
            $return['total'] = 0.5;
        } else if ($timeCheckIn > $startTime && $timeCheckOut >= $endTime) {
            $return['status'] = self::DI_MUON;
            $return['color'] = "lime";
            $from_pm = new Carbon($startTime);
            $return['total'] = 0.5;
        } else if ($timeCheckIn <= $startTime && $timeCheckOut < $endTime) {
            $return['status'] = self::VE_SOM;
            $return['color'] = "yellow";
            $to_pm = new Carbon($endTime);
            $check_out1 = new Carbon($timeCheckOut);
            $return['total'] = 0.5;


            $ve_som_h = $check_out1->diff($to_pm)->format('%H');
            $ve_som_i = $check_out1->diff($to_pm)->format('%I');

            if ($ve_som_i <= 29) $check_vao_i = 0;
            if ($ve_som_i >= 30) $check_vao_i = 0.5;
            if ($ve_som_i >= 59) $check_vao_i = 1;

            $return['ve_som'] = $ve_som_h + $check_vao_i;
        }
        if ($department->type == \App\Define\Department::FUNCTIONAL_OFFICE) {
            if (
                $timeCheckIn <= "12:00:00" && $timeCheckOut <= "12:00:00"
                || $timeCheckIn > "12:00:00" && $timeCheckIn > "12:00:00"
            ) {
                $return['status'] = self::QUEN_QUET;
                $return['color'] = "olive";
            }
        }

        return $return;
    }

    public function getWorkingByContract($data_contracts, $total_tv, $total_hd, $dateByMonth, $contract)
    {
        $total = 0;
        $return  = [
            'total_tv' => 0,
            'total_hd' => 0
        ];
        if (count($data_contracts) >= 2) {
            foreach ($data_contracts as $key => $data_contract) {
                $intStart = strtotime($data_contract['start']);
                $intEnd = strtotime($data_contract['end']);

                if ($intStart <= $dateByMonth && $dateByMonth <= $intEnd) {
                    if ($data_contract['is_main'] == \App\Defines\Staff::STATUS_PROBATIONARY) $return['total_tv'] = $total_tv;
                    if ($data_contract['is_main'] == \App\Defines\Staff::STATUS_OFFICIAL) $return['total_hd'] = $total_hd;
                }
            }
        } else {
            if ($contract->is_main == \App\Defines\Staff::STATUS_PROBATIONARY) $return['total_tv'] = $total_tv;
            if ($contract->is_main == \App\Defines\Staff::STATUS_OFFICIAL) $return['total_hd'] = $total_hd;
        }

        return $return;
    }

    public function getOT($data_contracts, $return1, $overTimes, $item, $check_out, $over_time, $dateByMonth, $contract, $dayoffs, $workSchedule)
    {
        if ($return1['status'] == self::QUEN_QUET) {
            return $return1;
        }
        $checkHoliday = StaffDayOff::checkDateHasEvent($contract->user_id, date('Y-m-d', $dateByMonth));
        if (in_array($checkHoliday, ['T', 'T/2'])) {
            return $return1;
        }


        $check_th = Carbon::parse(date('Y-m-d', $dateByMonth))->format('l');

        foreach ($overTimes as $index => $overTime) {
            if (array_key_exists(strtotime($overTime['date']), $item)) {
                $item[strtotime($overTime['date'])]['type_ot'] = $overTime['type'];
                $item[strtotime($overTime['date'])]['hours'] = $overTime['hours'];
            }
        }

        if (!is_null($item[$dateByMonth]['type_ot'])) {
            $return1['type_ot'] = $item[$dateByMonth]['type_ot'];
            $to_over_time = new Carbon($over_time);
            $checkin = new Carbon($return1['time_check_in']);

            if ($check_out >= $to_over_time) {
                $check_ot = $to_over_time->diff($check_out)->format('%H:%I:%S');
            } else {
                $check_ot = "00:00:00";
            }

            if ($item[$dateByMonth]['type_ot'] == \App\Define\OverTime::TYPE_HOLIDAY) {
                $check_out = new Carbon($return1['time_check_out']);
                $check_in = new Carbon($return1['time_check_in']);

                $check_ot = $check_in->diff($check_out)->format('%H:%I:%S');
            }


            if (strtoupper($check_th) == 'SATURDAY') {

                if (!is_null($dayoffs)) {
                    if ($dayoffs['from_type'] == 2 && $dayoffs['to_type'] == 2) {
                        if ($return1['time_check_in'] < "09:00:00") {
                            $to_over_time_in_sa = new Carbon('13:30');
                            $to_over_time_out_sa = new Carbon('17:00');
                            if ($return1['time_check_out'] >= "19:00:00") {
                                $check_ot1 = $to_over_time_in_sa->diff($to_over_time_out_sa)->format('%H:%I:%S');
                                $check_ot2 = $to_over_time->diff($check_out)->format('%H:%I:%S');
                                $check_ot = strtotime($check_ot1) + strtotime($check_ot2) - strtotime("00:00:00");
                                $check_ot = date('H:i:s', $check_ot);
                            } else if ($return1['time_check_out'] <= "18:59:00" && $return1['time_check_out'] >= "13:30:00") {
                                $check_ot = $to_over_time_in_sa->diff($check_out)->format('%H:%I:%S');
                            } else {
                                $check_ot = "00:00:00";
                            }
                        } else {
                            $to_over_time_sa = new Carbon('14:30');
                            if ($check_out >= $to_over_time_sa) {
                                $check_ot = $to_over_time_sa->diff($check_out)->format('%H:%I:%S');
                            } else {
                                $check_ot = "00:00:00";
                            }
                        }
                    } else if ($dayoffs['from_type'] == 1 && $dayoffs['to_type'] == 2) {
                        $to_morning = new Carbon($workSchedule->to_morning);
                        $from_afternoon = new Carbon($workSchedule->from_afternoon);

                        $nghi_trua = $to_morning->diff($from_afternoon)->format('%H:%I:%S');

                        $nghi_trua_h = intval(date_format(date_create($nghi_trua), "H"));
                        $nghi_trua_i = intval(date_format(date_create($nghi_trua), "i"));

                        if ($nghi_trua_i <= 29) $nghi_trua_i = 0;
                        if ($nghi_trua_i >= 30) $nghi_trua_i = 0.5;
                        if ($nghi_trua_i >= 59) $nghi_trua_i = 1;

                        $nghi_trua = $nghi_trua_h + $nghi_trua_i;
                        // $to_sa_morning = new Carbon($workSchedule->to_sa_morning);


                        // if ($return1['time_check_out'] > $workSchedule->from_sa_afternoon && $return1['time_check_out'] > $workSchedule->to_sa_afternoon) {
                        //     $to_sa_afternoon = new Carbon($workSchedule->to_sa_afternoon);
                        //     $check_ot1 = $from_sa_afternoon->diff($to_sa_afternoon)->format('%H:%I:%S');

                        // } else if ($return1['time_check_out'] > $workSchedule->from_sa_afternoon 
                        //     && $return1['time_check_out'] < $workSchedule->to_sa_afternoon) {
                        //     $check_ot1 = $from_sa_afternoon->diff($check_out)->format('%H:%I:%S');
                        // }

                        // if ($return1['time_check_in'] >= $workSchedule->from_sa_morning) {
                        //     $check_ot2 = $to_sa_morning->diff($check_in)->format('%H:%I:%S');

                        // } else if ($return1['time_check_in'] < $workSchedule->from_sa_morning) {
                        //     $check_ot2 = $to_sa_morning->diff($from_sa_morning)->format('%H:%I:%S');

                        // }
                        // $check_ot = strtotime($check_ot1) + strtotime($check_ot2) - strtotime("00:00:00");
                        // $check_ot = date('H:i:s', $check_ot);


                        $check_ot = $checkin->diff($check_out)->format('%H:%I:%S');
                    }
                }
            }

            $covert_check_ot_h = intval(date_format(date_create($check_ot), "H"));
            $covert_check_ot_i = intval(date_format(date_create($check_ot), "i"));
            if ($covert_check_ot_i <= 29) $check_i = 0;
            if ($covert_check_ot_i >= 30) $check_i = 0.5;
            if ($covert_check_ot_i >= 59) $check_i = 1;

            $covert_check_ot = $covert_check_ot_h + $check_i;

            if ($nghi_trua > 0 && $return1['time_check_out'] > $workSchedule->from_afternoon) {
                $covert_check_ot = $covert_check_ot - $nghi_trua;
            }

            if (count($data_contracts) >= 2) {
                foreach ($data_contracts as $kk => $data_contract) {
                    $intStart = strtotime($data_contract['start']);
                    $intEnd = strtotime($data_contract['end']);

                    if ($intStart <= $dateByMonth && $dateByMonth <= $intEnd) {
                        if ($contract['is_main'] == \App\Defines\Staff::STATUS_PROBATIONARY) {
                            if ($covert_check_ot >= $item[$dateByMonth]['hours']) {
                                if (!is_null($item[$dateByMonth]['hours'])) $return1['ot_tv'] = $item[$dateByMonth]['hours'];
                                if (!is_null($item[$dateByMonth]['hours_night_not_day'])) $return1['hours_night_not_day_tv'] = $item[$dateByMonth]['hours_night_not_day'];
                                if (!is_null($item[$dateByMonth]['hours_night_have_day'])) $return1['hours_night_have_day_tv'] = $item[$dateByMonth]['hours_night_have_day'];
                            } else {
                                if (!is_null($item[$dateByMonth]['hours'])) $return1['ot_tv'] = $covert_check_ot;
                            }

                            if ($covert_check_ot >= $item[$dateByMonth]['night']) {
                                if (!is_null($item[$dateByMonth]['night'])) $return1['night_tv'] = $item[$dateByMonth]['night'];
                            } else {
                                if (!is_null($item[$dateByMonth]['night'])) $return1['night_tv'] = $covert_check_ot;
                            }
                        }
                        if ($contract['is_main'] == \App\Defines\Staff::STATUS_OFFICIAL) {
                            if ($covert_check_ot >= $item[$dateByMonth]['hours']) {
                                if (!is_null($item[$dateByMonth]['hours'])) $return1['ot_hd'] = $item[$dateByMonth]['hours'];
                                if (!is_null($item[$dateByMonth]['hours_night_not_day'])) $return1['hours_night_not_day_hd'] = $item[$dateByMonth]['hours_night_not_day'];
                                if (!is_null($item[$dateByMonth]['hours_night_have_day'])) $return1['hours_night_have_day_hd'] = $item[$dateByMonth]['hours_night_have_day'];
                            } else {
                                if (!is_null($item[$dateByMonth]['hours'])) $return1['ot_hd'] = $covert_check_ot;
                            }
                            if ($return1['time_check_out'] == "0" || $check_ot == "00:00:00") {
                                $return1['ot_hd'] = 0;
                                $return1['type_ot'] = 0;
                            }

                            if ($covert_check_ot >= $item[$dateByMonth]['night']) {
                                if (!is_null($item[$dateByMonth]['night'])) $return1['night_hd'] = $item[$dateByMonth]['night'];
                            } else {
                                if (!is_null($item[$dateByMonth]['night'])) $return1['night_hd'] = $covert_check_ot;
                            }
                        };
                    }
                }
            } else {
                if ($contract['is_main'] == \App\Defines\Staff::STATUS_PROBATIONARY) {
                    if ($covert_check_ot >= $item[$dateByMonth]['hours']) {
                        if (!is_null($item[$dateByMonth]['hours'])) $return1['ot_tv'] = $item[$dateByMonth]['hours'];
                    } else {
                        if (!is_null($item[$dateByMonth]['hours'])) $return1['ot_tv'] = $covert_check_ot;
                    }
                }
                if ($contract['is_main'] == \App\Defines\Staff::STATUS_OFFICIAL) {
                    if ($covert_check_ot >= $item[$dateByMonth]['hours']) {
                        if (!is_null($item[$dateByMonth]['hours'])) $return1['ot_hd'] = $item[$dateByMonth]['hours'];
                    } else {
                        if (!is_null($item[$dateByMonth]['hours'])) $return1['ot_hd'] = $covert_check_ot;
                    }
                };
            }
            $return1['ot'] = $return1['ot_tv'] + $return1['ot_hd'];

            if ($contract->phuCapAn[0]['pivot']['expense'] == 25000) {
                $return1['ot'] = $return1['ot_tv'] + $return1['ot_hd'];

                if ($return1['type_ot'] == 1) {
                    if ($return1['ot'] >= 6) $return1['an_chinh'] = 1;
                    if ($return1['ot'] == self::NUA_CONG && $return1['ot'] > 0) $return1['an_chinh'] = 1;
                    if ($return1['ot'] >= 3 && $return1['ot'] < 6) $return1['an_phu'] = 1;
                    if ($return1['ot'] >= 7) $return1['an_phu'] = 1;
                }

                if ($return1['type_ot'] == 2 || $return1['type_ot'] == 3) {
                    if ($return1['ot'] > 4) $return1['an_chinh'] = 1;
                    if ($return1['ot'] >= 11) $return1['an_phu'] = 1;
                }
            }
            // $thu = ['MONDAY', 'TUESDAY', 'WEDNESDAY', 'THURSDAY', 'FRIDAY'];
            // if (in_array(strtoupper($check_th), $thu)) {
            //     if ($return1['ot'] > 3) $return1['an_phu'] = 1;
            // }
            // if (strtoupper($check_th) == 'SATURDAY' || strtoupper($check_th) == 'SUNDAY') {
            //     if ($return1['ot'] > 4) $return1['an_chinh'] = 1;
            //     if ($return1['ot'] > 11) {
            //         $return1['an_chinh'] = 1;
            //         $return1['an_phu'] = 1;
            //     } 
            // }

            if (
                $item[$dateByMonth]['type_ot'] == \App\Define\OverTime::TYPE_DAYOFF
                && strtoupper($check_th) != 'SATURDAY'
            ) {
                $return1['status'] = 0;
                $return1['color'] = 'white';
                $return1['total_tv'] = 0;
                $return1['total_hd'] = 0;
                $return1['status'] = 0;
                $return1['total'] = 0;
            }

            if ($item[$dateByMonth]['type_ot'] == \App\Define\OverTime::TYPE_HOLIDAY) $return1['color'] = 'red';
        }


        return $return1;
    }

    public function listOt(Request $request)
    {
        $query = PermissionUserObject::getQueryPermission(Auth::id(), 'ot.read');
        $timekeeping = TimeKeeping::whereRaw($query)->orderBy('year', 'DESC')->orderBy('month', 'DESC')->get();
        $timekeeping->load('company', 'department');

        return view('backend.timekeeping.list-ot', compact('timekeeping'));
    }

    public function otDetail(Request $request, $id)
    {
        $search = $request->input('fullname');
        $getDays = $getDates = [];

        $data = Timekeeping::find($id);
        if (empty($data)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.ot.index');
        }
        $data->load('company', 'department');

        $getDateByMonth = $this->getDateByMonth($data->month, $data->year);

        foreach ($getDateByMonth as $key => $item) {
            $getDays[] = substr(Carbon::parse($item)->format('l'), 0, 3);
            $getDates[] = Carbon::parse($item)->format('d');
            $getDateByMonth[$key] = strtotime($item);
        }

        if (!empty($search)) {
            $items = TimeKeepingDetail::where('timekeeping_id', $id)->whereHas('staff', function ($query) use ($search) {
                $query->where('fullname', 'like', '%' . $search . '%');
            })->get();
        } else {
            $infoPermission = PermissionUserObject::getMorePermissions(Auth::user()->id, 'timekeeping.read');

            if (Auth::user()->hasRole('NV') && count($infoPermission['departments']) == 0) {
                $items = TimeKeepingDetail::where('timekeeping_id', $id)->where('staff_id', Auth::user()->id)->get();
            } else {
                $items = TimeKeepingDetail::where('timekeeping_id', $id)->get();
            }
        }
        $startDate = date('Y-m-d 00:00:00', strtotime($data->year . '-' . (($data->month - 1)) . '-' . 26));


        $items->load('staff');
        if (empty($items)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.ot.index');
        }
        foreach ($items as $key => $item) {
            $total_type_normal_ot = 0;
            $hours_night_have_day = 0;
            $hours_night_not_day = 0;
            $night_dayoff = 0;
            $night_holiday = 0;

            $full_cong = 0;
            $nghiHieuHi = 0;
            $nghiXinThem = 0;
            $nghiLinhTinh = 0;

            $items[$key]['detail'] = json_decode($item->detail, true);
            foreach ($item->detail as $k => $ot) {
                $checkHoliday = StaffDayOff::checkDateHasEvent($item->staff_id, date('Y-m-d', $k));


                $total_ot = $ot['ot_tv'] + $ot['ot_hd'] + $ot['night_hd'] + $ot['night_tv'] + $ot['hours_night_have_day_hd'] + $ot['hours_night_not_day_hd'] + $ot['hours_night_not_day_tv'] + $ot['hours_night_have_day_tv'];

                if ($ot['type_ot'] == \App\Define\OverTime::TYPE_NORMAL) {
                    $total_type_normal_ot += $ot['ot'];
                    $items[$key]['total_type_normal'] = $total_type_normal_ot;

                    if (!is_null($ot['hours_night_have_day_hd']) || !is_null($ot['hours_night_have_day_tv'])) {
                        $hours_night_have_day += $ot['hours_night_have_day_hd'] + $ot['hours_night_have_day_tv'];
                        $items[$key]['hours_night_have_day'] = $hours_night_have_day;
                    }
                    if (!is_null($ot['hours_night_not_day_hd']) || !is_null($ot['hours_night_not_day_tv'])) {
                        $hours_night_not_day += $ot['hours_night_not_day_hd'] + $ot['hours_night_not_day_tv'];
                        $items[$key]['hours_night_not_day'] = $hours_night_not_day;
                    }
                }
                if ($ot['type_ot'] == \App\Define\OverTime::TYPE_DAYOFF) {
                    $items[$key]['total_type_dayoff'] += $ot['ot'];
                    if (!is_null($ot['night_hd']) || !is_null($ot['night_tv'])) {
                        $night_dayoff += $ot['night_hd'] + $ot['night_tv'];
                        $items[$key]['night_dayoff'] = $night_dayoff;
                    }
                }
                if ($ot['type_ot'] == \App\Define\OverTime::TYPE_HOLIDAY) {
                    $items[$key]['total_type_holiday'] += $ot['ot'];
                    if (!is_null($ot['night_hd']) || !is_null($ot['night_tv'])) {
                        $night_holiday += $ot['night_hd'] + $ot['night_tv'];
                        $items[$key]['night_holiday'] = $night_holiday;
                    }
                }

                if (($ot['status'] == self::DU_NGAY_CONG)
                    // || ($ot['total'] == 1 && $ot['status'] != self::DU_NGAY_CONG && $ot['an_chinh'] >= 1)
                    // || ($ot['total'] == 0 && $ot['status'] == self::NGHI_LAM && $ot['an_chinh'] >= 1 )

                    || ($ot['total'] == 0.5 && in_array($checkHoliday, ['L/2', 'D/2', 'W/2', 'H/2']))
                    || ($ot['total'] == 1 && in_array($checkHoliday, ['T/2']))
                    || ($ot['status'] == self::NGHI_LAM && $total_ot == 0 && $ot['an_chinh'] >= 1)
                    || in_array($checkHoliday, ['D', 'W'])
                ) {
                    $full_cong++;
                }

                if (in_array($checkHoliday, ['D', 'W'])) {
                    $nghiHieuHi++;
                }

                $dieuChuyen = Contract::where('user_id', $item->staff_id)->where('department_id', $data->department_id)
                    ->where('type_status', 2)
                    ->orderBy('id', 'DESC')
                    ->where('set_notvalid_on', '>', $startDate)
                    ->first();
                if (!is_null($dieuChuyen)) {
                    $set_notvalid_on = strtotime($dieuChuyen->set_notvalid_on);
                    if ($k < $set_notvalid_on) {
                        if (in_array($checkHoliday, ['L', 'T', 'L/2 L/2', 'T/2 T/2'])) {
                            if ($ot['an_chinh'] == 0 || is_null($ot['an_chinh']) || !isset($ot['an_chinh'])) $nghiXinThem++;
                        }
                        if (($ot['total'] == 1 && in_array($checkHoliday, ['L/2', 'D/2', 'W/2', 'H/2', 'T/2']))) {
                            if ($ot['an_chinh'] == 0 || is_null($ot['an_chinh']) || !isset($ot['an_chinh'])) $nghiLinhTinh++;
                        }
                    }
                } else {
                    if (in_array($checkHoliday, ['L', 'T', 'L/2 L/2', 'T/2 T/2', 'H'])) {
                        if ($ot['an_chinh'] == 0 || is_null($ot['an_chinh']) || !isset($ot['an_chinh'])) $nghiXinThem++;
                    }
                    if (($ot['total'] == 1 && in_array($checkHoliday, ['L/2', 'D/2', 'W/2', 'H/2', 'T/2']))
                        || (($ot['total_hd'] == 0.5 || $ot['total_tv'] == 0.5) && $total_ot > 0)
                    ) {
                        if ($ot['an_chinh'] == 0 || is_null($ot['an_chinh']) || !isset($ot['an_chinh'])) $nghiLinhTinh++;
                    }
                }
            }
            $items[$key]['an_phu'] = array_sum(array_column($item->detail, 'an_phu'));
            $items[$key]['an_chinh'] = array_sum(array_column($item->detail, 'an_chinh')) + $nghiHieuHi + $nghiXinThem + $nghiLinhTinh;
            $items[$key]['full_cong'] = $full_cong + $nghiXinThem;

            // $items[$key]['total_ot_hd'] = intval(array_sum(array_column($item->detail, 'ot_hd'))) + intval(array_sum(array_column($item->detail, 'hours_night_day_ot_hd')));
        }


        if ($request->export == self::EXPORT) {
            return [
                'getDays' => $getDays,
                'getDates' => $getDates,
                'items' => $items,
                'data' => $data
            ];
        }

        return view('backend.timekeeping.detail-ot', compact('getDays', 'getDates', 'items', 'data'));
    }

    public function warning(Request $request, $id)
    {
        $data = $request->all();
        $check = 0;
        $content = '';

        $logs = TimeKeepingDetail::find($id)->logs;
        foreach ($logs as $index => $log) {
            $data_old = json_decode($log->data_old, true);
            if ($data_old[$data['key']]) {
                $check++;
            }
        }
        if ($check >= 3) {
            $content = 'Cảnh báo đã chỉnh sửa ' . $check . ' lần';
            return \Response::json([
                'status' => 'SUCCESS',
                'content' => $content
            ]);
        }

        return '';
    }

    public function destroy($id)
    {
        $timekeeping = TimeKeeping::find($id);
        if (is_null($timekeeping)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.timekeeping.index');
        }
        try {
            DB::beginTransaction();
            $timekeeping->timeKeepingDetail()->delete();
            $timekeeping->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors($e)->withInput();
        }
        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');
        return redirect()->route('admin.timekeeping.index');
    }

    public function recalculate($id)
    {
        $timekeeping = TimeKeeping::find($id);
        if (empty($timekeeping)) {
            return \Response::json([
                'status' => 'FAIL',
                'message' => trans('system.error')
            ]);
        }
        $data = [
            'month' => $timekeeping->month,
            'year' => $timekeeping->year,
            'department_id' => $timekeeping->department_id,
            'company_id' => $timekeeping->company_id,
        ];
        $startDate = date('Y-m-d 00:00:00', strtotime($timekeeping->year . '-' . (($timekeeping->month - 1)) . '-' . 26));
        $endDate = date('Y-m-d 23:59:00', strtotime($timekeeping->year . '-' . $timekeeping->month . '-' . 26));
        $department = Department::find($timekeeping->department_id);

        if ($department->type == \App\Define\Department::FUNCTIONAL_OFFICE) {
            $workSchedule = WorkSchedule::where('company_id', $department->company->id)->where('department_id', $department->id)
                ->first();
        } else if ($department->type == \App\Define\Department::DECLARATION_OFFICE) {
            $workSchedule = ShiftTime::where('company_id', $department->company->id)->where('department_id', $department->id)
                ->first();
        }

        if (empty($workSchedule) && $department->type == \App\Define\Department::FUNCTIONAL_OFFICE) {
            return \Response::json([
                'status' => 'FAIL',
                'message' => trans('timekeeping.error_workschedule')
            ]);
        }

        User::syncAttendanceMachine();

        // $concurrent_contract = ConcurrentContract::where('status', 1)->where('company_id', $timekeeping->company_id)
        //     ->where('department_id', $timekeeping->department_id)
        //     ->pluck('user_id');

        $contracts = Contract::where('department_id', $data['department_id'])->whereIn('type_status', [1, 2, 7])->get();

        // $staffs = User::whereIn('id', $contracts->pluck('user_id')->toArray())
        //     ->where('active', 1)
        //     ->pluck('code_timekeeping');

        $staffs = User::whereIn('id', $contracts->pluck('user_id')->toArray())
            ->where('active', 1)
            ->get();

        // if (count($staffs) == 0 && count($concurrent_contract) > 0) {
        //     return \Response::json([
        //         'status' => 'FAIL',
        //         'message' => 'Bảng công kiêm nhiệm không được đổ lại'
        //     ]);
        // }

        $checkInOut = CheckInO::whereIn('primary_code', $staffs->pluck('code_timekeeping')->toArray())
            ->where('timeint', '>=', strtotime($startDate))
            ->where('timeint', '<=', strtotime($endDate))
            ->orderBy('timeint', 'DESC')
            ->get();


        // if (count($checkInOut) == 0) {
        //     return \Response::json([
        //         'status' => 'FAIL',
        //         'message' => 'Không có dữ liệu chấm công'
        //     ]);
        // }

        DB::beginTransaction();

        try {
            $timekeeping->updated_at = date('Y-m-d H:i:s');
            $timekeeping->user = Auth::user()->id;
            $timekeeping->save();

            if ($timekeeping->timeKeepingDetail()->delete()) {


                $this->checkInOut($timekeeping, $data, $checkInOut, $workSchedule, $department, $staffs, $contracts, $startDate);
            } else {

                $this->checkInOut($timekeeping, $data, $checkInOut, $workSchedule, $department, $staffs, $contracts, $startDate);
            }

            DB::commit();
            return \Response::json([
                'status' => 'SUCCESS',
                'message' => 'Tính lại thành công'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return \Response::json([
                'status' => 'FAIL',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getOtShift($data_contracts, $return1, $overTimes, $dateByMonth, $contract, $department, $shift_time, $type = '', $dayoffs = '')
    {

        if (empty($overTimes) || ($return1['time_check_out'] == 0 && $return1['time_check_in'] == 0)) {
            return $return1;
        }
        if ($return1['time_check_out'] == $return1['time_check_in']) {
            return $return1;
        }

        $tru = 0;
        if ($return1['status'] == self::QUEN_QUET) {
            return $return1;
        }
        $checkHoliday = StaffDayOff::checkDateHasEvent($contract->user_id, date('Y-m-d', $dateByMonth));
        if (in_array($checkHoliday, ['T'])) {
            return $return1;
        }

        if ($return1['status'] == self::DIMUON_VESOM) {
            return $return1;
        }

        $overTimes = collect($overTimes);
        $overTime = $overTimes->where('date', date('Y-m-d', $dateByMonth))->first();

        if (is_null($overTime)) {
            return $return1;
        }

        $return1['type_ot'] = $overTime['type'];

        if (!is_null($overTime['hours']['day'])) {
            $return1['hours'] = $overTime['hours']['day'];
        }

        if (!is_null($overTime['hours']['night'])) {
            $return1['night'] = $overTime['hours']['night'];
        }

        if (
            !is_null($overTime['hours']['night']) && is_null($overTime['hours']['day'])
            && $overTime['type'] == 1
        ) {

            $return1['hours_night_not_day'] = $overTime['hours']['night'];
            unset($return1['night']);
        }

        if (
            !is_null($overTime['hours']['night']) && !is_null($overTime['hours']['day'])
            && $overTime['type'] == 1
        ) {

            $return1['hours_night_have_day'] = $overTime['hours']['night'];
            $return1['hours'] = $overTime['hours']['day'];
            unset($return1['night']);
        }

        if ((!is_null($overTime['hours']['night']) || !is_null($overTime['hours']['day']))
            && $overTime['type'] != 1
        ) {

            $return1['hours'] = $overTime['hours']['day'];
            $return1['night'] = $overTime['hours']['night'];
        }

        $shift_night = ShiftTime::where('department_id', $department->id)->whereHas('category', function ($q) {
            $q->where('type', 3);
        })->first();


        $checkin = new Carbon($return1['time_check_in']);
        $checkout = new Carbon($return1['time_check_out']);

        $h_in = date('Y-m-d', strtotime($return1['time_check_in']));
        $gio_vao = $h_in . ' ' . $shift_time->time_in;
        $gio_vao_toi = $h_in . ' ' . $shift_night->time_in;


        $h_out = date('Y-m-d', strtotime($return1['time_check_out']));
        $gio_ra = $h_out . ' ' . $shift_time->time_out;
        $gio_ra_toi = $h_out . ' ' . $shift_night->time_out;

        $off_mid_shift_toi = $h_out . ' ' . $shift_night->off_mid_shift;
        $off_mid_shift_ot = new Carbon($off_mid_shift_toi);


        $gio_tinh_ot_vao = new Carbon($gio_vao);
        $gio_tinh_ot_ra = new Carbon($gio_ra);

        $gio_tinh_ot_vao_toi = new Carbon($gio_vao_toi);
        $gio_tinh_ot_ra_toi = new Carbon($gio_ra_toi);

        $gio_vao_chieu = $h_in . ' ' . $shift_time->start_mid_shift;

        $gio_ra_ca_sang = $h_in . ' ' . $shift_time->off_mid_shift;
        $tinh_gio_ra_sang = new Carbon($gio_ra_ca_sang);

        if ($return1['type_ot'] == OverTime::TYPE_NORMAL) {

            if ($shift_time->category->type == 3) {
                // làm đêm thường chỉ có ot ngày
                if (!is_null($return1['hours'])) {
                    // $date_gio_tinh_ot_ra = $gio_tinh_ot_ra->addDays(1);
                    // $checkout = $checkout->addDays(1);
                    $duoc_ot_vao_h = $duoc_ot_vao_i = $duoc_ot_ra_h = $duoc_ot_ra_i = 0;
                    if (strtotime($return1['time_check_in']) < strtotime($gio_vao_toi)) {
                        $duoc_ot_vao_h = $checkin->diff($gio_tinh_ot_vao_toi)->format('%H');
                        $duoc_ot_vao_i = $checkin->diff($gio_tinh_ot_vao_toi)->format('%I');
                    }

                    if (strtotime($return1['time_check_out']) > $gio_ra_toi && date('Y-m-d', strtotime($gio_ra_toi)) > date('Y-m-d', strtotime($gio_vao_toi))) {
                        $duoc_ot_ra_h = $gio_tinh_ot_ra_toi->diff($checkout)->format('%H');
                        $duoc_ot_ra_i = $gio_tinh_ot_ra_toi->diff($checkout)->format('%I');
                    }

                    if ($duoc_ot_vao_i <= 29) $check_vao_i = 0;
                    if ($duoc_ot_vao_i >= 30) $check_vao_i = 0.5;
                    if ($duoc_ot_vao_i >= 59) $check_vao_i = 1;

                    if ($duoc_ot_ra_i <= 29) $check_ra_i = 0;
                    if ($duoc_ot_ra_i >= 30) $check_ra_i = 0.5;
                    if ($duoc_ot_ra_i >= 59) $check_ra_i = 1;

                    $check_ot = intval($duoc_ot_ra_h) + intval($duoc_ot_vao_h) + $check_vao_i + $check_ra_i;

                    // $check_ot = strtotime($duoc_ot_vao) + strtotime($duoc_ot_ra) - strtotime("00:00:00");
                    // $check_ot = date('H:i:s', $check_ot);
                }

                $return1['hours_night_not_day'] = 0;
                $return1['hours_night_have_day'] = 0;
            } else {
                // làm ngày thưởng ot, ngày và đêm

                //làm ngày thường ot ngày
                if (!is_null($return1['hours'])) {

                    if (strtotime($return1['time_check_in']) < strtotime($gio_vao)) {
                        $duoc_ot_vao_h = $checkin->diff($gio_tinh_ot_vao)->format('%H');
                        $duoc_ot_vao_i = $checkin->diff($gio_tinh_ot_vao)->format('%I');
                    } else {
                        $duoc_ot_vao_h = 0;
                        $duoc_ot_vao_i = 0;
                    }

                    if (strtotime($return1['time_check_out']) > strtotime($gio_ra)) {
                        // if (strtotime($return1['time_check_out']) > strtotime($gio_tinh_ot_vao_toi) && strtotime($gio_tinh_ot_vao_toi) > strtotime($gio_ra)) { // thời gian out rơi vào ca tối, lấy mốc in đêm
                        //     $duoc_ot_ra_h = $gio_tinh_ot_vao_toi->diff($checkout)->format('%H');
                        //     $duoc_ot_ra_i = $gio_tinh_ot_vao_toi->diff($checkout)->format('%I');

                        // } else {
                        //     $duoc_ot_ra_h = $gio_tinh_ot_ra->diff($checkout)->format('%H');
                        //     $duoc_ot_ra_i = $gio_tinh_ot_ra->diff($checkout)->format('%I');

                        // }

                        if (strtotime($return1['time_check_out']) > strtotime($gio_tinh_ot_vao_toi) && !is_null($shift_night)) {
                            $duoc_ot_ra_h = $gio_tinh_ot_ra->diff($gio_tinh_ot_vao_toi)->format('%H');
                            $duoc_ot_ra_i = $gio_tinh_ot_ra->diff($gio_tinh_ot_vao_toi)->format('%I');
                        } else {
                            $duoc_ot_ra_h = $gio_tinh_ot_ra->diff($checkout)->format('%H');
                            $duoc_ot_ra_i = $gio_tinh_ot_ra->diff($checkout)->format('%I');
                        }
                    } else {
                        $return1['hours_night_not_day'] = 0;
                        $return1['hours_night_have_day'] = 0;
                        $duoc_ot_ra_h = 0;
                    }

                    if ($duoc_ot_vao_i <= 29) $check_vao_i = 0;
                    if ($duoc_ot_vao_i >= 30) $check_vao_i = 0.5;
                    if ($duoc_ot_vao_i >= 59) $check_vao_i = 1;

                    if ($duoc_ot_ra_i <= 29) $check_ra_i = 0;
                    if ($duoc_ot_ra_i >= 30) $check_ra_i = 0.5;
                    if ($duoc_ot_ra_i >= 59) $check_ra_i = 1;

                    $check_ot = intval($duoc_ot_ra_h) + intval($duoc_ot_vao_h) + $check_vao_i + $check_ra_i;
                    // $check_ot = strtotime($duoc_ot_vao) + strtotime($duoc_ot_ra) - strtotime("00:00:00");
                    // $check_ot = date('H:i:s', $check_ot);
                }

                //làm ngày thường ot đêm
                if (!is_null($return1['hours_night_have_day']) || !is_null($return1['hours_night_not_day'])) {

                    if (!is_null($shift_night)) {
                        //tính ot với thời gian checkout với thời gian vào làm ca đêm
                        if (
                            strtotime($return1['time_check_out']) > strtotime($gio_tinh_ot_vao_toi)
                            && strtotime($gio_tinh_ot_vao_toi) > strtotime($gio_ra)
                        ) {
                            // $shift_night_time_in = new Carbon($shift_night->time_in);

                            $check_ot1 = $gio_tinh_ot_vao_toi->diff($checkout)->format('%H:%I:%S');

                            // dd($checkout);

                            $covert_check_ot_h1 = intval(date_format(date_create($check_ot1), "H"));
                            $covert_check_ot_i1 = intval(date_format(date_create($check_ot1), "i"));
                            if ($covert_check_ot_i1 <= 29) $check_i = 0;
                            if ($covert_check_ot_i1 >= 30) $check_i = 0.5;
                            if ($covert_check_ot_i1 >= 59) $check_i = 1;

                            $ot = $covert_check_ot_h1 + $check_i;


                            if ($ot > 0) {
                                if ($check_ot > 0) { // ot đêm có ot ngày
                                    if ($ot < $return1['hours_night_have_day']) {
                                        $return1['hours_night_have_day'] = $ot;
                                    } else {
                                        $return1['hours_night_have_day'] = $return1['hours_night_have_day'];
                                    }
                                    $return1['hours_night_not_day'] = 0;
                                } else { // ot đêm không ot ngày
                                    if ($ot < $return1['hours_night_not_day']) {
                                        $return1['hours_night_not_day'] = $ot;
                                    } else {
                                        $return1['hours_night_not_day'] = $ot;
                                        $return1['hours_night_have_day'] = 0;
                                    }
                                }
                            } else {
                                $return1['hours_night_not_day'] = 0;
                                $return1['hours_night_have_day'] = 0;
                            }
                        }
                        // tính ot với thời gian checkin với giờ ra ca đêm
                        else if (strtotime($return1['time_check_in']) < strtotime($gio_tinh_ot_ra_toi)) {

                            $check_ot1 = $gio_tinh_ot_ra_toi->diff($checkin)->format('%H:%I:%S');

                            $covert_check_ot_h1 = intval(date_format(date_create($check_ot1), "H"));
                            $covert_check_ot_i1 = intval(date_format(date_create($check_ot1), "i"));
                            if ($covert_check_ot_i1 <= 29) $check_i = 0;
                            if ($covert_check_ot_i1 >= 30) $check_i = 0.5;
                            if ($covert_check_ot_i1 >= 59) $check_i = 1;

                            $ot = $covert_check_ot_h1 + $check_i;

                            if ($ot > 0) {
                                if ($check_ot > 0) { // ot đêm có ot ngày
                                    if ($ot < $return1['hours_night_have_day']) {
                                        $return1['hours_night_have_day'] = $ot;
                                    } else {
                                        $return1['hours_night_have_day'] = $return1['hours_night_have_day'];
                                    }
                                } else { // ot đêm không ot ngày
                                    if ($ot < $return1['hours_night_not_day']) {
                                        $return1['hours_night_not_day'] = $ot;
                                    } else {
                                        $return1['hours_night_not_day'] = $return1['hours_night_not_day'];
                                    }
                                }
                            } else {
                                $return1['hours_night_not_day'] = 0;
                                $return1['hours_night_have_day'] = 0;
                            }
                        } else {
                            $return1['hours_night_not_day'] = 0;
                            $return1['hours_night_have_day'] = 0;
                        }
                    } else {
                        $return1['hours_night_not_day'] = 0;
                        $return1['hours_night_have_day'] = 0;
                    }
                }
            }
        } else if ($return1['type_ot'] == OverTime::TYPE_DAYOFF || $return1['type_ot'] == OverTime::TYPE_HOLIDAY) {

            if (!is_null($return1['hours'])) {

                if ($type == 'lam_sang') {

                    if ($return1['shift'] == 3) {
                        if (strtotime($return1['time_check_in']) < strtotime($gio_vao_toi)) {
                            $check_ot = $gio_tinh_ot_vao_toi->diff($checkin)->format('%H:%I:%S');
                        } else {
                            $check_ot = 0;
                        }
                    } else {
                        if (strtotime($return1['time_check_out']) > strtotime($gio_vao_chieu)) {
                            $start_mid_shift = new Carbon($gio_vao_chieu);

                            if (strtotime($return1['time_check_out']) > strtotime($gio_tinh_ot_vao_toi) && !is_null($shift_night)) {
                                $check_ot = $start_mid_shift->diff($gio_tinh_ot_vao_toi)->format('%H:%I:%S');
                            } else {

                                $check_ot = $start_mid_shift->diff($checkout)->format('%H:%I:%S');
                            }
                        } else {
                            $check_ot = 0;
                        }
                    }
                } else if ($type == 'lam_chieu') {

                    if ($return1['shift'] == 3) {
                        if (strtotime($return1['time_check_out']) > strtotime($gio_ra_toi)) {
                            $check_ot = $gio_tinh_ot_ra->diff($checkout)->format('%H:%I:%S');
                        }
                    } else {

                        if ($return1['time_check_in'] < $gio_ra_ca_sang) {
                            $check_ot = $tinh_gio_ra_sang->diff($checkin)->format('%H:%I:%S');
                        } else {
                            $check_ot = 0;
                        }
                    }
                } else {
                    $check_ot = $checkin->diff($checkout)->format('%H:%I:%S');
                }

                $covert_check_ot_h = intval(date_format(date_create($check_ot), "H"));
                $covert_check_ot_i = intval(date_format(date_create($check_ot), "i"));

                if ($covert_check_ot_i <= 29) $check_i = 0;
                if ($covert_check_ot_i >= 30) $check_i = 0.5;
                if ($covert_check_ot_i >= 59) $check_i = 1;

                $check_ot = $covert_check_ot_h + $check_i;
            }

            if (!is_null($return1['night']) && !is_null($shift_night)) {

                if (strtotime($return1['time_check_out']) > strtotime($gio_tinh_ot_vao_toi)) {
                    // $shift_night_time_out = new Carbon($shift_time->time_out);
                    // $shift_night_time_out = $shift_night_time_out->addDays(1);

                    //làm sáng tính ot chiều
                    if ($return1['shift'] == 3) {
                        if ($type == 'lam_sang') {

                            if (strtotime($return1['time_check_out']) > strtotime($gio_vao_chieu)) {

                                $start_mid_shift = new Carbon($gio_vao_chieu);
                                $check_ot1 = $checkout->diff($start_mid_shift)->format('%H:%I:%S');
                                // $check_ot1 = $checkout->diff($gio_tinh_ot_vao_toi)->format('%H:%I:%S');
                            } else {
                                $check_ot1 = 0;
                            }
                        } else if ($type == 'lam_chieu') { // làm chiều tính ot sáng
                            if (strtotime($return1['time_check_in']) < strtotime($gio_vao_toi)) {

                                $check_ot1 = $checkin->diff($gio_tinh_ot_vao_toi)->format('%H:%I:%S');
                                // $check_ot1 = $checkout->diff($gio_tinh_ot_vao_toi)->format('%H:%I:%S');
                            } else {
                                $check_ot1 = 0;
                            }
                        } else { //tính ot cả ngày
                            $check_ot1 = $checkin->diff($checkout)->format('%H:%I:%S');
                        }
                    } else {
                        if (strtotime($return1['time_check_out']) > strtotime($gio_vao_toi)) {
                            $check_ot1 = $checkout->diff($gio_tinh_ot_vao_toi)->format('%H:%I:%S');
                        } else {
                            $check_ot1 = 0;
                        }
                    }

                    $covert_check_ot_h1 = intval(date_format(date_create($check_ot1), "H"));
                    $covert_check_ot_i1 = intval(date_format(date_create($check_ot1), "i"));
                    if ($covert_check_ot_i1 <= 29) $check_i = 0;
                    if ($covert_check_ot_i1 >= 30) $check_i = 0.5;
                    if ($covert_check_ot_i1 >= 59) $check_i = 1;

                    $ot = $covert_check_ot_h1 + $check_i;

                    if ($ot < $return1['night']) {
                        $return1['night'] = $ot;
                    }
                } else {
                    $return1['night'] = 0;
                }
            } else {
                $return1['night'] = 0;
            }

            if (!is_null($dayoffs) && $return1['status'] == self::NGHI_LAM) {
                if ($check_ot >= 5) $check_ot = $check_ot - 1;
            }
        }

        if ($return1['status'] == self::DI_MUON) {
            $tru = $checkin->diff($gio_tinh_ot_vao)->format('%H:%I:%S');

            $tru_h = intval(date_format(date_create($tru), "H"));
            $tru_i = intval(date_format(date_create($tru), "i"));

            if ($tru_i <= 29) $tru_i = 0;
            if ($tru_i >= 30) $tru_i = 0.5;
            if ($tru_i >= 59) $tru_i = 1;

            $tru = $tru_h + $tru_i;
            $check_ot = $check_ot - $tru;

            if ($check_ot < 0) $check_ot = 0;
        }


        if ($return1['status'] == self::VE_SOM) {
            if ($return1['ve_som'] > $check_ot) {
                $check_ot = 0;
            } else {
                $check_ot = $check_ot - $return1['ve_som'];
            }
            if ($check_ot < 0) $check_ot = 0;
        }

        $covert_check_ot = $check_ot;

        if (count($data_contracts) >= 2) {
            foreach ($data_contracts as $kk => $data_contract) {
                $intStart = strtotime($data_contract['start']);
                $intEnd = strtotime($data_contract['end']);

                if ($intStart <= $dateByMonth && $dateByMonth <= $intEnd) {
                    if ($data_contract['is_main'] == \App\Defines\Staff::STATUS_PROBATIONARY) {

                        if ($covert_check_ot >= $return1['hours']) {
                            if (!is_null($return1['hours'])) $return1['ot_tv'] = $return1['hours'];
                        } else {
                            if (!is_null($return1['hours'])) $return1['ot_tv'] = $covert_check_ot;
                        }

                        if ($return1['night'] > 0) {
                            if (!is_null($return1['night'])) $return1['night_tv'] = $return1['night'];
                        } else {
                            if (!is_null($return1['night'])) $return1['night_tv'] = $covert_check_ot;
                        }

                        if (!is_null($return1['hours_night_not_day'])) $return1['hours_night_not_day_tv'] = $return1['hours_night_not_day'];
                        if (!is_null($return1['hours_night_have_day'])) $return1['hours_night_have_day_tv'] = $return1['hours_night_have_day'];
                    }

                    if ($data_contract['is_main'] == \App\Defines\Staff::STATUS_OFFICIAL) {

                        if ($covert_check_ot >= $return1['hours']) {
                            if (!is_null($return1['hours'])) $return1['ot_hd'] = $return1['hours'];
                        } else {
                            if (!is_null($return1['hours'])) $return1['ot_hd'] = $covert_check_ot;
                        }

                        if ($return1['night'] > 0) {
                            $return1['night_hd'] = $return1['night'];
                        }

                        if (!is_null($return1['hours_night_not_day'])) $return1['hours_night_not_day_hd'] = $return1['hours_night_not_day'];
                        if (!is_null($return1['hours_night_have_day'])) $return1['hours_night_have_day_hd'] = $return1['hours_night_have_day'];
                    }
                }
            }
        } else {
            if ($contract['is_main'] == \App\Defines\Staff::STATUS_PROBATIONARY) {
                if ($covert_check_ot >= $return1['hours']) {
                    if (!is_null($return1['hours'])) $return1['ot_tv'] = $return1['hours'];
                } else {
                    if (!is_null($return1['hours'])) $return1['ot_tv'] = $covert_check_ot;
                }

                if ($return1['night'] > 0) {
                    if (!is_null($return1['night'])) $return1['night_tv'] = $return1['night'];
                } else {
                    if (!is_null($return1['night'])) $return1['night_tv'] = $covert_check_ot;
                }

                if (!is_null($return1['hours_night_not_day'])) $return1['hours_night_not_day_tv'] = $return1['hours_night_not_day'];
                if (!is_null($return1['hours_night_have_day'])) $return1['hours_night_have_day_tv'] = $return1['hours_night_have_day'];
            }
            if ($contract['is_main'] == \App\Defines\Staff::STATUS_OFFICIAL) {

                if ($covert_check_ot >= $return1['hours']) {
                    if (!is_null($return1['hours'])) $return1['ot_hd'] = $return1['hours'];
                } else {
                    if (!is_null($return1['hours'])) $return1['ot_hd'] = $covert_check_ot;
                }

                if ($return1['night'] > 0) {
                    $return1['night_hd'] = $return1['night'];
                }

                if (!is_null($return1['hours_night_not_day'])) $return1['hours_night_not_day_hd'] = $return1['hours_night_not_day'];
                if (!is_null($return1['hours_night_have_day'])) $return1['hours_night_have_day_hd'] = $return1['hours_night_have_day'];
            };
        }

        // }
        $return1['ot'] = $return1['ot_tv'] + $return1['ot_hd'];

        if ($contract->phuCapAn[0]['pivot']['expense'] == 25000) {
            $total_ol = $return1['ot'] + $return1['night_hd'] + $return1['night_tv'] + $return1['hours_night_have_day_hd'] + $return1['hours_night_not_day_hd'] + $return1['hours_night_not_day_tv'] + $return1['hours_night_have_day_tv'];

            if ($return1['status'] == self::NUA_CONG && $total_ol > 0) $return1['an_chinh'] = 1;

            if ($return1['type_ot'] == 1) {
                if ($total_ol >= 6) $return1['an_chinh'] += 1; // ngày thường ot >= 6 ăn chính

                if ($total_ol >= 3 && $total_ol < 6) $return1['an_phu'] = 1; // ngày thường 0t >= 3, ot < 6, ăn phụ
                if ($total_ol >= 7 && $return1['total'] == 0.5) $return1['an_phu'] = 1;
            }

            if ($return1['type_ot'] == 2 || $return1['type_ot'] == 3) {
                if ($return1['total'] == 0.5 && $total_ol > 0) $return1['an_chinh'] = 1; //ngày đi làm 1/2v , ot > 0 ăn chính
                if ($total_ol > 4) $return1['an_chinh'] = 1; // ngày nghỉ ot > 4 ăn chính

                if ($total_ol >= 7 && $return1['total'] == 0.5) $return1['an_phu'] = 1; // ngày đi làm , ot >= 7, ăn phụ
                if ($total_ol >= 11) $return1['an_phu'] = 1; // ngày nghỉ ot > 11, ăn phụ 
            }
        }

        unset($return1['hours']);
        unset($return1['night']);

        return $return1;
    }

    public function updateOt(Request $request, $id)
    {
        $data = $request->all();
        $timekeeping_detail = TimeKeepingDetail::find(intval($id));
        $detail = json_decode($timekeeping_detail->detail, true);

        if (empty($timekeeping_detail)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.ot.index');
        }

        if ($request->ajax()) {
            if ($data['ot'] < 0) {
                return \Response::json([
                    'status' => 'FAIL',
                    'message' => 'Số giờ OT phải là số dương'
                ]);
            }

            $contract = Contract::where('user_id', $timekeeping_detail->staff_id)->whereIn('type_status', [1, 7])->first();

            $key = $data['key'];
            $type = $data['type'];

            $overTimes = OverTimes::getOT($timekeeping_detail->timekeeping->month, $timekeeping_detail->timekeeping->year, $timekeeping_detail->staff_id);
            $overTimes = collect($overTimes);
            $date_over_time = $overTimes->where('date', date('Y-m-d', $key))->first();

            if (is_null($date_over_time)) {
                return \Response::json([
                    'status' => 'FAIL',
                    'message' => 'Lỗi không có lịch OT'
                ]);
            }
            if (count($date_over_time['hours']) > 1) {
                $toi_da_ot = $date_over_time['hours'][$type];
            } else {
                $toi_da_ot = $date_over_time['hours'];
            }

            if (is_null($toi_da_ot)) {
                return \Response::json([
                    'status' => 'FAIL',
                    'message' => 'Lỗi không có lịch OT'
                ]);
            }

            $detail[$key]['type_ot'] = $date_over_time['type'];

            if ($data['ot'] > $toi_da_ot) $data['ot'] = $toi_da_ot;
            $data_old = $detail[$key];


            if ($detail[$key]['type_ot'] == 1) {
                if ($type == 'night' && $detail[$key]['ot_hd'] > 0) {
                    if ($contract->is_main == 2) {
                        $detail[$key]['hours_night_have_day_hd'] = $data['ot'];
                    }

                    if ($contract->is_main == 1) {
                        $detail[$key]['hours_night_have_day_tv'] = $data['ot'];
                    }
                    $detail[$key]['ot_change'] = 'night';
                    $detail[$key]['note_night'] = $data['note'];
                } else  if ($type == 'night' && $detail[$key]['ot_hd'] == 0) {
                    $detail[$key]['hours_night_have_day'] = 0;

                    if ($contract->is_main == 2) {
                        $detail[$key]['hours_night_not_day_hd'] = $data['ot'];
                        $detail[$key]['hours_night_have_day_hd'] = 0;
                    }

                    if ($contract->is_main == 1) {
                        $detail[$key]['hours_night_not_day_tv'] = $data['ot'];
                        $detail[$key]['hours_night_have_day_tv'] = 0;
                    }
                    $detail[$key]['ot_change'] = 'night';
                    $detail[$key]['note_night'] = $data['note'];
                }

                if ($type == 'day') {

                    if ($contract->is_main == 1 || ($detail[$key]['total_tv'] == 1 || $detail[$key]['total_tv'] == 0.5)) {

                        $detail[$key]['ot_tv'] = $data['ot'];

                        if ($detail[$key]['hours_night_not_day_tv'] > 0) {
                            $detail[$key]['hours_night_have_day_tv'] = $detail[$key]['hours_night_not_day_tv'];
                            $detail['hours_night_not_day_tv'] = 0;
                        }
                    } else if ($contract->is_main == 2) {
                        $detail[$key]['ot_hd'] = $data['ot'];
                        $detail[$key]['ot'] = $data['ot'];

                        if ($detail[$key]['hours_night_not_day_hd'] > 0) {
                            $detail[$key]['hours_night_have_day_hd'] = $detail[$key]['hours_night_not_day_hd'];
                            $detail[$key]['hours_night_not_day_hd'] = 0;
                        }
                    }


                    $detail[$key]['ot_change'] = 'day';
                    $detail[$key]['note_day'] = $data['note'];
                }
            }
            if ($detail[$key]['type_ot'] == 2 || $detail[$key]['type_ot'] == 3) {
                if ($type == 'night') {
                    if ($contract->is_main == 2) {
                        $detail[$key]['night_hd'] = $data['ot'];
                    }

                    if ($contract->is_main == 1) {
                        $detail[$key]['night_tv'] = $data['ot'];
                    }
                    $detail[$key]['ot_change'] = 'night';
                    $detail[$key]['note_night'] = $data['note'];
                }

                if ($type == 'day') {
                    if ($contract->is_main == 2) {
                        $detail[$key]['ot_hd'] = $data['ot'];
                    }

                    if ($contract->is_main == 1) {
                        $detail[$key]['ot_hd'] = $data['ot'];
                    }
                    $detail[$key]['ot_change'] = 'day';
                    $detail[$key]['note_day'] = $data['note'];
                }
            }


            if ($contract->phuCapAn[0]->pivot->expense == 25000) {
                $return = [];
                $return =  $detail[$key];
                $checkHoliday = StaffDayOff::checkDateHasEvent($timekeeping_detail->staff_id, date('Y-m-d', $key));

                $total_ot = $return['ot_tv'] + $return['ot_hd'] + $return['night_hd'] + $return['night_tv'] + $return['hours_night_have_day_hd'] + $return['hours_night_not_day_hd'] + $return['hours_night_not_day_tv'] + $return['hours_night_have_day_tv'];

                if (in_array($checkHoliday, ['L', 'W', 'D', 'T', 'H', 'L/2 L/2', 'W/2 W/2', 'D/2 D/2', 'T/2 T/2', 'H/2 H/2'])) {
                    $return['an_chinh'] = 1;
                } else if ($return['total'] == 0 && $return['time_check_out'] == 0 && $return['time_check_in'] == 0) {
                    $return['an_chinh'] = 0;
                    $return['an_phu'] = 0;
                } else if ($return['total'] == 0.5 && $total_ot < 2 && !in_array($checkHoliday, ['T/2', 'L/2', 'W/2', 'D/2', 'H/2'])) {
                    $return['an_phu'] = 0;
                    $return['an_chinh'] = 0;
                }

                if ($return['total_hd'] == 1 || $return['total_tv'] == 1) {
                    $return['an_chinh'] = 1;
                }

                if (($return['total_hd'] == 1 || $return['total_tv'] == 1) || (in_array($checkHoliday, ['T/2', 'L/2', 'W/2', 'D/2', 'H/2']) && $return['total'] == 0.5)) {
                    $return['an_chinh'] = 1;
                }

                if ($return['type_ot'] == 1) {
                    if ($total_ot >= 6 && $return['an_chinh'] == 1) {
                        $return['an_chinh'] = 2;
                    }

                    if ($total_ot >= 3 && $total_ot < 6) {
                        $return['an_phu'] = 1;
                    }

                    if ($total_ot >= 7 && $return['status'] == self::NUA_CONG) {
                        $return['an_phu'] = 1;
                    }

                    if ($total_ot < 3) {
                        $return['an_phu'] = 0;
                    }
                }


                if ($return['type_ot'] == 2 || $return['type_ot'] == 3) {
                    if ($total_ot > 4) $return['an_chinh'] = 1;
                    if ($total_ot >= 11) $return['an_phu'] = 1;
                }

                if ($total_ot > 0 && $return['total'] == 0.5 && $return['type_ot'] == 2) {
                    $return['an_chinh'] = 1;
                }

                if ($total_ot >= 7 && $return['total'] == 0.5 && $return['type_ot'] == 2) {
                    $return['an_phu'] = 1;
                }


                if ($total_ot == 0 && $return['total'] == 0.5 && $return['type_ot'] == 2) {
                    $return['an_chinh'] = 0;
                }

                if ($total_ot < 7 && $return['total'] == 0.5 && $return['type_ot'] == 2) {
                    $return['an_phu'] = 0;
                }

                // $return['ot'] = $return['ot_tv'] + $return['ot_hd'];

                // $total_ol = $return['ot_tv'] + $return['ot_hd'] + $return['night_hd'] + $return['night_tv'] + $return['hours_night_have_day_hd'] + $return['hours_night_not_day_hd'] + $return['hours_night_not_day_tv'] + $return['hours_night_have_day_tv'];


                // if ($return['type_ot'] == 1) {

                //     if ($total_ol >= 6 && $return['an_chinh'] < 2 ) $return['an_chinh'] += 1;
                //     if ($return['status'] == self::NUA_CONG && $total_ol > 0 && $return['an_chinh'] < 2 ) $return['an_chinh'] += 1;
                //     if ($total_ol >= 3 && $total_ol < 6) $return['an_phu'] = 1;

                //     if ($total_ol >= 7 && $return['status'] == self::NUA_CONG) $return['an_phu'] = 1;  

                //     if ($total_ol < 6 && $return['an_chinh'] >= 1 ) $return['an_chinh'] -= 1;
                //     if ($total_ol < 3 && $return['an_phu'] > 0 ) $return['an_phu'] -= 1;
                // }

                // if ($return['type_ot'] == 2 || $return['type_ot'] == 3) {
                //     if ($total_ol > 4 && $return['an_chinh'] < 2) $return['an_chinh'] += 1;
                //     if ($total_ol >= 11) $return['an_phu'] = 1;

                //     if ($total_ol < 4 && $return['an_chinh'] >= 1) $return['an_chinh'] -= 1;
                //     if ($total_ol < 11 && $return['an_phu'] > 0) $return['an_phu'] -= 1;

                // }
                $detail[$key] = $return;
            }

            $detail[$key]['ot'] = $detail[$key]['ot_hd'] +  $detail[$key]['ot_tv'];

            try {
                $timekeeping_detail->update([
                    'detail' => json_encode($detail),
                ]);

                $response = [
                    'data_new' => json_encode($return),
                    'data_old' => json_encode($data_old),
                    'action_by' => $request->user()->id,
                    'action_at' => date('Y-m-d H:i:s'),
                    'field' => 'ot',
                    'note' => $data['note'],
                    'log_type' => get_class($timekeeping_detail),
                    'log_id' => $timekeeping_detail->id,
                ];

                DB::table('logs')->insert($response);

                return \Response::json([
                    'status' => 'SUCCESS',
                    'message' => 'Cập nhật thành công'
                ]);
            } catch (Exception $e) {
                return \Response::json([
                    'status' => 'SUCCESS',
                    'message' => 'Cập nhật thành thất bại'
                ]);
            }
        }
    }

    public function teamDetail(Request $request, $teamId, $timekeepingId)
    {
        // $data = $this->teamQuery('timekeeping.read');
        // $team = $data['user_ids'][$teamId];
        // if ($team) {
        //     $timekeeping_detail = TimeKeepingDetail::teamTimeKeeping($team);
        // }
        // dd($timekeeping_detail[$timekeepingId]->toArray());
        return $this->detail($request, $timekeepingId);
    }

    public function approved($id)
    {

        $timekeeping = TimeKeeping::find(intval($id));

        if (is_null($timekeeping)) {
            // Session::flash('message', trans('system.have_an_error'));
            // Session::flash('alert-class', 'danger');
            // return redirect()->route('admin.timekeeping.index');

            return response()->json(['status' => 'FAIL', 'message' => trans('system.have_an_error')]);
        }

        try {
            $timekeeping->update([
                'status' => 'APPROVED',
                'user_approved' => Auth::user()->id,
                'date_approved' => date('Y-m-d H:i:s')
            ]);

            return response()->json(['status' => 'SUCCESS', 'fullname' => Auth::user()->fullname, 'message' => 'Thành công']);


            // Session::flash('message', trans('system.success'));
            // Session::flash('alert-class', 'success');
            // return redirect()->route('admin.timekeeping.detail', $id);

        } catch (Exception $e) {
            return response()->json(['status' => 'FAIL', 'message' => trans('system.have_an_error')]);
            // dd($e->getMessage());
            // Session::flash('message', trans('system.have_an_error'));
            // Session::flash('alert-class', 'danger');
            // return redirect()->route('admin.timekeeping.detail', $id);

        }
    }

    public function reset(Request $request, $id)
    {
        if ($request->ajax()) {
            $timekeeping_detail = TimeKeepingDetail::find(intval($id));
            if (is_null($timekeeping_detail)) {
                return response()->json(['status' => 'FAIL', 'message' => 'Có lỗi xảy ra']);
            }

            $logs = Log::where('log_id', intval($id))->get();

            if (count($logs) == 0) {
                return response()->json(['status' => 'FAIL', 'message' => 'Không có cập nhật']);
            }

            $detail = json_decode($timekeeping_detail->detail, true);

            foreach ($logs as $key => $log) {
                $data_old = json_decode($log->data_old, true);

                foreach ($data_old as $k => $old) {
                    if (array_key_exists($k, $detail)) {
                        $detail[$k] = $old;
                    }
                }
            }

            try {
                $timekeeping_detail->update([
                    'detail' => json_encode($detail)
                ]);
                Log::destroy($logs->pluck('id')->toArray());

                return response()->json(['status' => 'SUCCESS', 'message' => 'Cập nhập thành công']);
            } catch (Exception $e) {
                return response()->json(['status' => 'FAIL', 'message' => 'Có lỗi xảy ra']);
            }
        }
    }

    public function tinhSuatAn(Request $request, $id)
    {
        if ($request->ajax()) {
            $timekeeping_detail = TimeKeepingDetail::find(intval($id));
            if (is_null($timekeeping_detail)) {
                return response()->json(['status' => 'FAIL', 'message' => 'Có lỗi xảy ra']);
            }

            $detail = json_decode($timekeeping_detail->detail, true);

            foreach ($detail as $key => $item) {
                $checkHoliday = StaffDayOff::checkDateHasEvent($timekeeping_detail->staff_id, date('Y-m-d', $key));
                $total_ot = $item['ot_tv'] + $item['ot_hd'] + $item['night_hd'] + $item['night_tv'] + $item['hours_night_have_day_hd'] + $item['hours_night_not_day_hd'] + $item['hours_night_not_day_tv'] + $item['hours_night_have_day_tv'];

                if (in_array($checkHoliday, ['L', 'W', 'D', 'T', 'H', 'L/2 L/2', 'W/2 W/2', 'D/2 D/2', 'T/2 T/2', 'H/2 H/2'])) {
                    $detail[$key]['an_chinh'] = 1;
                } else if ($item['total'] == 0 && $item['time_check_out'] == 0 && $item['time_check_in'] == 0) {
                    $detail[$key]['an_chinh'] = 0;
                    $detail[$key]['an_phu'] = 0;
                } else if ($item['total'] == 0.5 && $total_ot < 2 && !in_array($checkHoliday, ['T/2', 'L/2', 'W/2', 'D/2', 'H/2'])) {
                    $detail[$key]['an_phu'] = 0;
                    $detail[$key]['an_chinh'] = 0;
                }



                if ($item['total_hd'] == 1 || $item['total_tv'] == 1) {
                    $detail[$key]['an_chinh'] = 1;
                }

                if (($item['total_hd'] == 1 || $item['total_tv'] == 1) || (in_array($checkHoliday, ['T/2', 'L/2', 'W/2', 'D/2', 'H/2']) && $item['total'] == 0.5)) {
                    $detail[$key]['an_chinh'] = 1;
                }

                if ($item['type_ot'] == 1) {
                    if ($total_ot >= 6 && $item['an_chinh'] == 1) {
                        $detail[$key]['an_chinh'] = 2;
                    }

                    if ($total_ot >= 3 && $total_ot < 6) {
                        $detail[$key]['an_phu'] = 1;
                    }

                    if ($total_ot >= 7 && $item['status'] == self::NUA_CONG) {
                        $detail[$key]['an_phu'] = 1;
                    }

                    if ($total_ot < 3) {
                        $detail[$key]['an_phu'] = 0;
                    }
                }


                if ($item['type_ot'] == 2 || $item['type_ot'] == 3) {
                    if ($total_ot > 4) $detail[$key]['an_chinh'] = 1;
                    if ($total_ot >= 11) $detail[$key]['an_phu'] = 1;
                }

                if ($total_ot > 0 && $item['total'] == 0.5 && $item['type_ot'] == 2) {
                    $detail[$key]['an_chinh'] = 1;
                }

                if ($total_ot >= 7 && $item['total'] == 0.5 && $item['type_ot'] == 2) {
                    $detail[$key]['an_phu'] = 1;
                }


                if ($total_ot == 0 && $item['total'] == 0.5 && $item['type_ot'] == 2) {
                    $detail[$key]['an_chinh'] = 0;
                }

                if ($total_ot < 7 && $item['total'] == 0.5 && $item['type_ot'] == 2) {
                    $detail[$key]['an_phu'] = 0;
                }

                if ($item['total'] == 0.5 && $total_ot == 0 && in_array($checkHoliday, ['T/2', 'L/2', 'W/2', 'D/2', 'H/2'])) {
                    $detail[$key]['an_phu'] = 0;
                    $detail[$key]['an_chinh'] = 0;
                }
            }

            try {
                $timekeeping_detail->update([
                    'detail' => json_encode($detail)
                ]);

                return response()->json(['status' => 'SUCCESS', 'message' => 'Cập nhập thành công']);
            } catch (Exception $e) {
                return response()->json(['status' => 'FAIL', 'message' => 'Có lỗi xảy ra']);
            }
        }
    }

    public function exportExcelOt(Request $request, $id)
    {
        $timekeeping = TimeKeeping::find($id);
        if (empty($timekeeping)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.ot.index');
        }
        $request->export = self::EXPORT;
        $data = $this->otDetail($request, $id);
        if (empty($data)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.ot.index');
        }

        return \Excel::download(new \App\Exports\TimekeepingExportOt($data), 'Bang-ot-' . $timekeeping->department->name . '.xlsx');
    }
}
