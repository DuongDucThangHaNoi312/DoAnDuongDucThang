<?php

namespace App\Http\Controllers\Backend;

use App\Define\Department as DefineDepartment;
use App\Define\OverTime;
use App\Define\Shift;
use App\Define\Timekeeping as DefineTimekeeping;
use App\Defines\Schedule;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CalendarDepartment;
use App\Models\CategoryShift;
use App\Models\ConcurrentContract;
use App\Models\Contract;
use App\Models\Department;
use App\Models\Log;
use App\Models\Newborn;
use App\Models\OverTimes;
use App\Models\Shift as ModelsShift;
use App\Models\ShiftTime;
use App\Models\TimeKeeping;
use App\Models\TimeKeepingDetail;
use App\Models\WorkSchedule;
use App\PermissionUserObject;
use App\StaffDayOff;
use App\User;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class TimeKeepingController1 extends Controller
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
    const KHONG_TINH_OT = 'KHONG';
    const NGAY = 'NGAY';
    const HANH_CHINH = 'HANH_CHINH';
    const DEM = 'DEM';
    const NGAY_THUONG = 'NGAY_THUONG';
    const NGAY_NGHI = 'NGAY_NGHI';
    const NGAY_LE = 'NGAY_LE';
    const LAM_SANG = 'LAM_SANG';
    const LAM_CHIEU = 'LAM_CHIEU';
    const CA_NGAY = 'CA_NGAY';

    public function getDateByMonth($month, $year)
    {
        $return = [];
        $start = date('Y-m-d', strtotime($year . '-' . (($month - 1)) . '-' . 26));
        $end = date('Y-m-d', strtotime($year . '-' . $month . '-' . 26));
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
    
    public function store(Request $request)
    {
        $data = $request->all();
        $data['created_by'] = $request->user()->id;
        $validator = \Validator::make($data, TimeKeeping::rules());
        $validator->setAttributeNames(trans('time_keeping'));

        if ($data['month'] == 1)  $year = $data['year'] - 1;
        else $year = $data['year'];

        $startDate = date('Y-m-d 00:00:00', strtotime($year . '-' . (($data['month'] - 1)) . '-' . 26));
        $endDate = date('Y-m-d 23:59:00', strtotime($data['year'] . '-' . $data['month'] . '-' . 26));

        if ($validator->passes()) {
            DB::beginTransaction();
            try {
                $department = Department::find($data['department_id']);

                if ($department->type == \App\Define\Department::FUNCTIONAL_OFFICE) {
                    $workSchedule = WorkSchedule::where('company_id', $data['company_id'])->where('department_id', $data['department_id'])
                    ->first();
                } else if ($department->type == \App\Define\Department::DECLARATION_OFFICE) {
                    $workSchedule = ShiftTime::where('department_id', $data['department_id'])
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
                $contracts = Contract::where('department_id', $data['department_id'])->whereIn('type_status', [1, 2, 7])->orderBy('id', 'DESC')->get();
            
                $staffs = User::whereIn('id', $contracts->pluck('user_id')->toArray())
                        ->where('active', 1)
                        ->get();
                        // $staffs->pluck('code_timekeeping')
                $checkInOut = DB::connection('mysql2')->table('CHECKINO')->whereIn('primary_code', $staffs->pluck('code_timekeeping'))
                    ->where('timeint', '>=', strtotime($startDate))
                    ->where('timeint', '<=', strtotime($endDate))
                    ->orderBy('timeint', 'DESC')
                    ->get();
                    
                $data['version'] = 1;
                $timeKeeping = TimeKeeping::create($data);
                // $timeKeeping = 1;
                $this->checkInOut($timeKeeping, $data, $checkInOut, $workSchedule, $department, $staffs, $contracts); 

                DB::commit();

                return \Response::json([
                    'status' => 'SUCCESS',
                    'message' => trans('timekeeping.success'),
                    'link' => route('admin.timekeepings.detail', $timeKeeping->id)
                ]);

            } catch (Exception $e) {
                DB::rollBack();
                return \Response::json([
                    'status' => 'SUCCESS',
                    'message' => $e->getMessage()
                ]);
            }
        }

        return \Response::json(['errors' => $validator->errors()]);
    }

    public function checkInOut($timeKeeping, $data, $checkInOut, $workSchedule, $department, $staffs, $contracts)
    {
        foreach ($checkInOut as $item) {
            $timedate = date('Y-m-d', $item->timeint);
            $results[$item->primary_code][strtotime($timedate)][] = json_decode(json_encode($item), true);
        }
        if ($department->type == \App\Define\Department::FUNCTIONAL_OFFICE) {
            return $this->tinhCongHanhChinh($results, $timeKeeping, $data, $checkInOut, $workSchedule, $department, $staffs, $contracts);
        } else if ($department->type == \App\Define\Department::DECLARATION_OFFICE) {
            return $this->tinhCongTheoCa($results, $timeKeeping, $data, $checkInOut, $workSchedule, $department, $staffs, $contracts);
        }

        return false;
    }

    public function tinhCongHanhChinh($results, $timeKeeping, $data, $checkInOut, $workSchedule, $department, $staffs, $contracts)
    {
        $insert = $timeKeepingDetail = [];
        $loai_ngay_nghi = ['H', 'L', 'S', 'W', 'D', 'O', 'C', 'T', 'BB', 'H/2 H/2', 'L/2 L/2', 'S/2 S/2', 'W/2 W/2', 'D/2 D/2', 'O/2 O/2', 'C/2 C/2', 'T/2 T/2', 'BB/2 BB/2'];
        $nghi_co_luong = ['H', 'L', 'W', 'D', 'T', 'H/2 H/2', 'L/2 L/2', 'W/2 W/2', 'D/2 D/2', 'T/2 T/2', 'L/2 T/2', 'W/2 T/2', 'D/2 T/2'];
        $nghi_nua_luong = ['H/2', 'L/2', 'W/2', 'D/2', 'T/2'];

        $dayoffs = CalendarDepartment::getDayOff($department->id);
        $dayoffs = collect($dayoffs);

        $start = date('Y-m-d', strtotime($data['year'] . '-' . (($data['month'] - 1)) . '-' . 26));
        $end = date('Y-m-d', strtotime($data['year'] . '-' . $data['month'] . '-' . 26));
        
        // $nghi_nhan_vien = StaffDayOff::whereIn('user_id', $staffs->pluck('id')->toArray())->where('start', '<=', $end)->where('end', '>=', $start)->get();
        // $nghi_phong_ban = CalendarDepartment::where('department_id', $data['department_id'])->where('categories', 'holiday')->get();
        $newborns = Newborn::whereIn('user_id', $staffs->pluck('id')->toArray())->whereNull('deleted_at')->get();

        $from_morning = date_format(date_create($workSchedule->from_morning), "H:i:s");
        $to_morning = date_format(date_create($workSchedule->to_morning), "H:i:s");

        $from_afternoon = date_format(date_create($workSchedule->from_afternoon), "H:i:s");
        $to_afternoon = date_format(date_create($workSchedule->to_afternoon), "H:i:s");

        $from_sa_morning = date_format(date_create($workSchedule->from_sa_morning), "H:i:s");
        $to_sa_morning = date_format(date_create($workSchedule->to_sa_morning), "H:i:s");

        $from_sa_afternoon = date_format(date_create($workSchedule->from_sa_afternoon), "H:i:s");
        $to_sa_afternoon = date_format(date_create($workSchedule->to_sa_afternoon), "H:i:s");
        $user_ids = $staffs->pluck('id')->toArray();
        
        if (count($results) > 0) {
            foreach ($results as $key => $item) {
                $staff_id = $staffs->where('code_timekeeping', $key)->pluck('id')->first();
                $data_contracts = Contract::getContractsInAMonth($staff_id, $data['month'], $data['year']);
                $contract = $contracts->where('user_id', $staff_id)->first();
                if (is_null($contract)) continue;
                if (strtotime($contract->set_notvalid_on) < strtotime($start) && $contract->type_status == 2) {
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

                $status_contract = '';
                if ($contract->type_status == 1) {
                    $status_contract = 'DANG_HOAT_DONG';
                } else if ($contract->type_status == 2) {
                    $status_contract = 'DIEU_CHUYEN';
                } else if ($contract->type_status == 7) {
                    $status_contract = 'CHO_NGHI_VIEC';
                }
                $nghi_nhan_vien = StaffDayOff::where('user_id', $staff_id)->where('start', '<=', $end)->where('end', '>=', $start)->get();
                $nghi_phong_ban = CalendarDepartment::where('department_id', $data['department_id'])->where('categories', 'holiday')->get();

                foreach ($this->getDateByMonth($data['month'], $data['year']) as $k1 => $v1) {
                    $dateByMonth = strtotime($v1);
                    
                    $timeCheckIn  = $timeCheckOut = $totalHours = $status = $total = $ve_som = $di_muon = 0;
                    $contract_type = '';
    
                    $return = [
                        'time_check_out'    => $timeCheckOut,
                        'time_check_in'     => $timeCheckIn,
                        'total_hours'       => $totalHours,
                        'status'            => $status,
                        'total'             => $total,
                        'contract_type'     => $contract_type,
                        've_som'            => $ve_som,
                        'di_muon'           => $di_muon,
                        'time'              => 'NGHI_LAM',
                        'status_contract'   => $status_contract,
                        'day_off'           => '',
                        'edit'              => 0,
                        'total_work'        => 0,
                    ];
                    
                    $selectDayOff = StaffDayOff::selectDayOff($workSchedule, $nghi_phong_ban, $nghi_nhan_vien, $staff_id, date('Y-m-d', $dateByMonth), $data['department_id'], $data['month'], $data['year']);
                    $return['day_off'] = $selectDayOff;

                    if (in_array($selectDayOff, $loai_ngay_nghi)) { // nghỉ cả ngày không tính toán gì cả
                        if (count($data_contracts) == 1) { // kiểm tra hợp đồng trong 1 tháng
                            $return['contract_type'] = $contract->is_main == 2 ? 'HOP_DONG' : 'THU_VIEC';
                        } else {
                            foreach ($data_contracts as $p => $v) {
                                if ($dateByMonth <= strtotime($v['end'])) {
                                    $return['contract_type'] = $v['is_main'] == 2 ? 'HOP_DONG' : 'THU_VIEC';
                                    break;
                                }
                            }
                        }
    
                        $return['time_check_out'] = $timeCheckOut;
                        $return['time_check_in'] = $timeCheckIn;
                        if (in_array($selectDayOff, $nghi_co_luong)) {
                            $return['total'] = 1;
                        }
                        $insert[$key][$dateByMonth] = $return;
                       
                    } else {
                        $dayoff = $dayoffs->where('start', date('Y-m-d', $dateByMonth))->first();
                        $check_th = Carbon::parse(date('Y-m-d', $dateByMonth))->format('l');
                        
                        if (array_key_exists($dateByMonth, $item)) { //xử lý chấm vân tau
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

                            $arr = [
                                current($item[$dateByMonth]),
                                end($item[$dateByMonth])
                            ];
                            unset($results[$key][$dateByMonth]);
                            $item[$dateByMonth] = $arr;
                            
                            $timeCheckIn = !is_null($item[$dateByMonth][1]['timeint']) ? date('H:i:s', $item[$dateByMonth][1]['timeint']) : 0;
                            $timeCheckOut = !is_null($item[$dateByMonth][0]['timeint']) ? date('H:i:s', $item[$dateByMonth][0]['timeint']) : 0;
                            
                            if (!is_null($dayoff)) {
                                $dateDayOff = $this->nghiNuaNgay($staff_id, date('Y-m-d', $dateByMonth), $nghi_nhan_vien); //nhân viên xin nghỉ
                                
                                switch (strtoupper($check_th)) {
                                    case "SATURDAY":
                                        if ($dayoff['from_type'] == 1 && $dayoff['to_type'] == 1 && $dateDayOff != '0.5A') {
                                            //nghỉ sáng thứ 7, làm chiều
                                            if ($workSchedule->type == 1) {
                                                $return['total'] = 0.5;
                                            } else {
                                                $return = $this->handlingTime($return, $timeCheckOut, $timeCheckIn, $from_sa_afternoon, $to_sa_afternoon, $workSchedule);
                                            }
                                            $return['status'] = self::NUA_CONG;
                                            $return['total'] = 0.5;
                                            $return['time'] = self::LAM_CHIEU;
    
                                        } else if ($dayoff['from_type'] == 2 && $dayoff['to_type'] == 2 && $dateDayOff != '0.5M') {
                                            //nghỉ chiều làm, sáng
                                            if ($workSchedule->type == 1) {
                                                $return['total'] = 0.5;
                                            } else {
                                                $return = $this->handlingTime($return, $timeCheckOut, $timeCheckIn, $from_sa_morning, $to_sa_morning, $workSchedule);
                                            }

                                            $return['status'] = self::NUA_CONG;
                                            $return['total'] = 0.5;
                                            $return['time'] = self::LAM_SANG;

                                        } else {
                                            if (count($data_contracts) == 1) { // kiểm tra hợp đồng trong 1 tháng
                                                $return['contract_type'] = $contract->is_main == 2 ? 'HOP_DONG' : 'THU_VIEC';
                                            } else {
                                                foreach ($data_contracts as $p => $v) {
                                                    if ($dateByMonth <= strtotime($v['end'])) {
                                                        $return['contract_type'] = $v['is_main'] == 2 ? 'HOP_DONG' : 'THU_VIEC';
                                                        break;
                                                    }
                                                }
                                            }
                                        }

                                        if (in_array($selectDayOff, $nghi_nua_luong)) {
                                            $return['total'] = 0.5;
                                        }
    
                                        break;
                                    default:
                                        if ($dayoff['from_type'] == 1 && $dayoff['to_type'] == 1) {
                                            //nghỉ sáng t2-t6, làm chiều
                                            $return = $this->handlingTime($return, $timeCheckOut, $timeCheckIn, $from_afternoon, $to_afternoon, $workSchedule);
            
                                        } else if ($dayoff['from_type'] == 2 && $dayoff['to_type'] == 2) {
                                            //nghỉ chiều t2-t6, làm sáng
                                            $return = $this->handlingTime($return, $timeCheckOut, $timeCheckIn, $from_morning, $to_morning, $workSchedule);
                                        }
                                        if (in_array($selectDayOff, $nghi_nua_luong)) {
                                            $return['total'] = 0.5;
                                        }
                                }
                            
                                if (count($data_contracts) == 1) { // kiểm tra hợp đồng trong 1 tháng
                                    $return['contract_type'] = $contract->is_main == 2 ? 'HOP_DONG' : 'THU_VIEC';
                                } else {
                                    foreach ($data_contracts as $p => $v) {
                                        if ($dateByMonth <= strtotime($v['end'])) {
                                            $return['contract_type'] = $v['is_main'] == 2 ? 'HOP_DONG' : 'THU_VIEC';
                                            break;
                                        }
                                    }
                                }  
                                $return = $this->tinhOtHanhChinh($return, $data, $workSchedule, $department, $dateByMonth, $dayoff, $staff_id);

                                $insert[$key][$dateByMonth] = $return;
    
                            } else {
                                $newborn = $newborns->where('user_id', $staff_id)->where('start', '<=',  date('Y-m-d', $dateByMonth))->where('end', '>=', date('Y-m-d', $dateByMonth))
                                                            ->first();
    
                                if (!is_null($newborn)) {
                                    $check_to = new DateTime($to_morning);
                                    $check_af = new DateTime($from_afternoon);
                                    $gio_nghi_trua = $check_to->diff($check_af)->format('%H:%I:%S');
    
                                    $t_h = new DateTime($return['total_hours']);
                                    $gio_nghi_trua1 = new DateTime($gio_nghi_trua);
    
                                    $so_gio_thuc_te = $gio_nghi_trua1->diff($t_h)->format('%H');
                                    $so_gio_thuc_te_i = $gio_nghi_trua1->diff($t_h)->format('%I');
    
                                    if (intval($so_gio_thuc_te_i) >= 30) {
                                        $so_gio_thuc_te = intval($so_gio_thuc_te) + 0.5;
                                    }
                                    
                                    if ($so_gio_thuc_te < $newborn->time) {
                                        $return['status'] = self::NUA_CONG;
                                        $return['color'] = 'silver';
                                        $return['total'] = 0.5;
                                    } else {
                                        $return['status'] = self::DU_NGAY_CONG;
                                        $return['color'] = 'white';
                                        $return['total'] = 1;
                                    }
                                    
                                } else {
                                    $dateDayOff = $this->nghiNuaNgay($staff_id, date('Y-m-d', $dateByMonth), $nghi_nhan_vien); //nhân viên xin nghỉ
                                    
                                    switch ($dateDayOff) {
                                        case '0.5A': //nghỉ chiều làm sáng
                                            $return = $this->handlingTime($return, $timeCheckOut, $timeCheckIn, $from_morning, $to_morning);
                                            if ($return['status'] == self::DU_NGAY_CONG) $return['status'] = self::NUA_CONG;
                                            $return['total'] = $return['total_work'] = 0.5;
                                            $return['time'] = self::LAM_SANG;
                                            if (in_array($selectDayOff, $nghi_nua_luong)) {
                                                $return['total'] = 1;
                                            }
                                            break;
                                        case '0.5M': // nghỉ sáng làm chiều
                                            $return = $this->handlingTime($return, $timeCheckOut, $timeCheckIn, $from_afternoon, $to_afternoon);
                                            if ($return['status'] == self::DU_NGAY_CONG) $return['status'] = self::NUA_CONG;
                                            $return['total'] = $return['total_work'] = 0.5;
                                            $return['time'] = self::LAM_CHIEU;
                                            if (in_array($selectDayOff, $nghi_nua_luong)) {
                                                $return['total'] = 1;
                                            }
                                            break;
                                        default:  
                                            $return = $this->handlingTime($return, $timeCheckOut, $timeCheckIn, $from_morning, $to_afternoon, $workSchedule);
                                            $return['time'] = self::CA_NGAY;
                                            $return['total_work'] = $return['total'];
                                    }
                                }
    
                                if (count($data_contracts) == 1) { // kiểm tra hợp đồng trong 1 tháng
                                    $return['contract_type'] = $contract->is_main == 2 ? 'HOP_DONG' : 'THU_VIEC';
                                } else {
                                    foreach ($data_contracts as $p => $v) {
                                        if ($dateByMonth <= strtotime($v['end'])) {
                                            $return['contract_type'] = $v['is_main'] == 2 ? 'HOP_DONG' : 'THU_VIEC';
                                            break;
                                        }
                                    }
                                }
                                $return = $this->tinhOtHanhChinh($return, $data, $workSchedule, $department, $dateByMonth, $dayoff, $staff_id);
                               
                                $insert[$key][$dateByMonth] = $return;
                            }
                        } else { // không chấm vân tay
                            if ($workSchedule->type == 1 && strtoupper($check_th) == 'SATURDAY') { // check hành chính tích thứ 7 làm tại nhà
                                $dateDayOff = $this->nghiNuaNgay($staff_id, date('Y-m-d', $dateByMonth), $nghi_nhan_vien); //nhân viên xin nghỉ
                                
                                if (!is_null($dayoff)) {
                                    if ($dayoff['from_type'] == 1 && $dayoff['to_type'] == 1 && $dateDayOff == '0.5A') { // pb nghỉ sáng làm chiều, nv xin nghỉ chiều
                                        $return['total'] = 0;
                                        $return['status'] = self::NGHI_LAM;
                                    } else if ($dayoff['from_type'] == 2 && $dayoff['to_type'] == 2 && $dateDayOff == '0.5M') { //pb nghỉ chiều làm sáng, nv xin nghỉ sáng
                                        $return['total'] = 0;
                                        $return['status'] = self::NGHI_LAM;
                                    } else {
                                        $return['total'] = $return['total_work'] = 0.5;
                                        $return['status'] = self::NUA_CONG;
                                    }
                                } else {
                                    if ($dayoff['from_type'] == 1 && $dayoff['to_type'] == 1 && $dayoff != 1) { // ko có đơn xin nghỉ, phòng ban làm t7 cả ngày
                                        $return['total'] = 1;
                                        $return['status'] = self::DU_NGAY_CONG; 
                                    } else if ($dateDayOff == '0.5A' || $dateDayOff == '0.5M') { // có đơn xin nghỉ nửa ngày
                                        $return['total'] = 0.5;
                                        $return['status'] = self::NUA_CONG; 
                                    }
                                }
                            } else {
                                if (in_array($selectDayOff, $nghi_nua_luong)) {
                                    $return['total'] = 0.5;
                                }
                                if (in_array($selectDayOff, $nghi_co_luong)) {
                                    $return['total'] = 1;
                                }
                            }
    
                            if (count($data_contracts) == 1) { // kiểm tra hợp đồng trong 1 tháng
                                $return['contract_type'] = $contract->is_main == 2 ? 'HOP_DONG' : 'THU_VIEC';
                            } else {
                                foreach ($data_contracts as $p => $v) {
                                    if ($dateByMonth <= strtotime($v['end'])) {
                                        $return['contract_type'] = $v['is_main'] == 2 ? 'HOP_DONG' : 'THU_VIEC';
                                        break;
                                    }
                                }
                            }    
                            
                            $insert[$key][$dateByMonth] = $return;
                        }
    
                    }
    
                    ksort($insert[$key]);
                    $total = array_sum(array_column($insert[$key], 'total'));
                }
    
                $timeKeepingDetail[] = [
                    'timekeeping_id' => $timeKeeping->id,
                    'code'           => $key,
                    'detail'         => json_encode($insert[$key]),
                    'created_by'     => $timeKeeping->created_by,
                    'total'          => $total,
                    'staff_id'       => $staff_id
                ];
            }
            $date = [];
            if (count($user_ids) > 0) {
                foreach ($user_ids as $key => $user) {
                    $contract = $contracts->where('user_id', $user)->first();
                    $data_contracts = Contract::getContractsInAMonth($user, $data['month'], $data['year']);

                    if (is_null($contract)) continue;
                    if (strtotime($contract->set_notvalid_on) < strtotime($start) && $contract->type_status == 2) {
                        continue;
                    }
                    $status_contract = '';
                    if ($contract->type_status == 1) {
                        $status_contract = 'DANG_HOAT_DONG';
                    } else if ($contract->type_status == 2) {
                        $status_contract = 'DIEU_CHUYEN';
                    } else if ($contract->type_status == 7) {
                        $status_contract = 'CHO_NGHI_VIEC';
                    }
                    $nghi_nhan_vien = StaffDayOff::where('user_id', $user)->where('start', '<=', $end)->where('end', '>=', $start)->get();
                    $nghi_phong_ban = CalendarDepartment::where('department_id', $data['department_id'])->where('categories', 'holiday')->get();
                    
                    foreach ($this->getDateByMonth($data['month'], $data['year']) as $k1 => $v1) {
                        $dateByMonth = strtotime($v1);
                        $timeCheckIn  = $timeCheckOut = $totalHours = $status = $total = $ve_som = $di_muon = 0;
                        $contract_type = '';
        
                        $return = [
                            'time_check_out'    => $timeCheckOut,
                            'time_check_in'     => $timeCheckIn,
                            'total_hours'       => $totalHours,
                            'status'            => $status,
                            'total'             => $total,
                            'contract_type'     => $contract_type,
                            've_som'            => $ve_som,
                            'di_muon'           => $di_muon,
                            'time'              => 'NGHI_LAM',
                            'status_contract'   => $status_contract,
                            'day_off'           => '',
                            'edit'              => 0,
                            'total_work'        => 0,
                        ];

                        
                        $selectDayOff = StaffDayOff::selectDayOff($workSchedule, $nghi_phong_ban, $nghi_nhan_vien, $user, date('Y-m-d', $dateByMonth), $data['department_id'], $data['month'], $data['year']);
                        $return['day_off'] = $selectDayOff;

                        if (in_array($selectDayOff, $loai_ngay_nghi) || in_array($selectDayOff, $nghi_nua_luong)) {
                            $return['time_check_out'] = $timeCheckOut;
                            $return['time_check_in'] = $timeCheckIn;
                            if (in_array($selectDayOff, $nghi_co_luong)) {
                                $return['total'] = 1;
                            }
                            if (in_array($selectDayOff, $nghi_nua_luong)) {
                                $return['total'] = 0.5;
                            }
                        }

                        if (count($data_contracts) == 1) { // kiểm tra hợp đồng trong 1 tháng
                            $return['contract_type'] = $contract->is_main == 2 ? 'HOP_DONG' : 'THU_VIEC';
                        } else {
                            foreach ($data_contracts as $p => $v) {
                                if ($dateByMonth <= strtotime($v['end'])) {
                                    $return['contract_type'] = $v['is_main'] == 2 ? 'HOP_DONG' : 'THU_VIEC';
                                    break;
                                }
                            }
                        }
                        $date[$dateByMonth] = $return;

                        $total = array_sum(array_column($date, 'total'));
                    }

                    $timeKeepingDetail[] = [
                        'timekeeping_id' => $timeKeeping->id,
                        'code'           => $key,
                        'detail'         => json_encode($date),
                        'created_by'     => $timeKeeping->created_by,
                        'total'          => $total,
                        'staff_id'       => $user
                    ];
                }
            }
        } else {
            $date = [];
            if (count($user_ids) > 0) {
                foreach ($user_ids as $key => $user) {
                    $contract = $contracts->where('user_id', $user)->first();
                    if (is_null($contract)) continue;
                    if (strtotime($contract->set_notvalid_on) < strtotime($start) && $contract->type_status == 2) {
                        continue;
                    }
                    $data_contracts = Contract::getContractsInAMonth($user, $data['month'], $data['year']);
                    $status_contract = '';
                    if ($contract->type_status == 1) {
                        $status_contract = 'DANG_HOAT_DONG';
                    } else if ($contract->type_status == 2) {
                        $status_contract = 'DIEU_CHUYEN';
                    } else if ($contract->type_status == 7) {
                        $status_contract = 'CHO_NGHI_VIEC';
                    }
                    $nghi_nhan_vien = StaffDayOff::where('user_id', $user)->where('start', '<=', $end)->where('end', '>=', $start)->get();
                    $nghi_phong_ban = CalendarDepartment::where('department_id', $data['department_id'])->where('categories', 'holiday')->get();
                    
                    foreach ($this->getDateByMonth($data['month'], $data['year']) as $k1 => $v1) {
                        $dateByMonth = strtotime($v1);
                        $timeCheckIn  = $timeCheckOut = $totalHours = $status = $total = $ve_som = $di_muon = 0;
                        $contract_type = '';
        
                        $return = [
                            'time_check_out'    => $timeCheckOut,
                            'time_check_in'     => $timeCheckIn,
                            'total_hours'       => $totalHours,
                            'status'            => $status,
                            'total'             => $total,
                            'contract_type'     => $contract_type,
                            've_som'            => $ve_som,
                            'di_muon'           => $di_muon,
                            'time'              => 'NGHI_LAM',
                            'status_contract'   => $status_contract,
                            'day_off'           => '',
                            'edit'              => 0,
                            'total_work'        => 0,
                        ];
                        
                        $selectDayOff = StaffDayOff::selectDayOff($workSchedule, $nghi_phong_ban, $nghi_nhan_vien, $user, date('Y-m-d', $dateByMonth), $data['department_id'], $data['month'], $data['year']);
                        $return['day_off'] = $selectDayOff;

                        if (in_array($selectDayOff, $loai_ngay_nghi) || in_array($selectDayOff, $nghi_nua_luong)) {
                            $return['time_check_out'] = $timeCheckOut;
                            $return['time_check_in'] = $timeCheckIn;
                            if (in_array($selectDayOff, $nghi_co_luong)) {
                                $return['total'] = 1;
                            }
                            if (in_array($selectDayOff, $nghi_nua_luong)) {
                                $return['total'] = 0.5;
                            }
                        }

                        if (count($data_contracts) == 1) { // kiểm tra hợp đồng trong 1 tháng
                            $return['contract_type'] = $contract->is_main == 2 ? 'HOP_DONG' : 'THU_VIEC';
                        } else {
                            foreach ($data_contracts as $p => $v) {
                                if ($dateByMonth <= strtotime($v['end'])) {
                                    $return['contract_type'] = $v['is_main'] == 2 ? 'HOP_DONG' : 'THU_VIEC';
                                    break;
                                }
                            }
                        }
                        $date[$dateByMonth] = $return;

                        $total = array_sum(array_column($date, 'total'));
                    }
                    $timeKeepingDetail[] = [
                        'timekeeping_id' => $timeKeeping->id,
                        'code'           => $key,
                        'detail'         => json_encode($date),
                        'created_by'     => $timeKeeping->created_by,
                        'total'          => $total,
                        'staff_id'       => $user
                    ];
                }
            }
        }

        if (DB::table('timekeeping_detail')->insert($timeKeepingDetail)) {
            return true;
        }

        return false;
    }

    public function tinhCongTheoCa($results, $timeKeeping, $data, $checkInOut, $workSchedule, $department, $staffs, $contracts)
    {
        $insert = $timeKeepingDetail = [];
        $loai_ngay_nghi = ['H', 'L', 'S', 'W', 'D', 'O', 'C', 'T', 'BB', 'H/2 H/2', 'L/2 L/2', 'S/2 S/2', 'W/2 W/2', 'D/2 D/2', 'O/2 O/2', 'C/2 C/2', 'T/2 T/2', 'BB/2 BB/2'];
        $nghi_co_luong = ['H', 'L', 'W', 'D', 'T', 'H/2 H/2', 'L/2 L/2', 'W/2 W/2', 'D/2 D/2', 'T/2 T/2', 'L/2 T/2', 'W/2 T/2', 'D/2 T/2'];
        $nghi_nua_luong = ['H/2', 'L/2', 'W/2', 'D/2', 'T/2'];

        $dayoffs = CalendarDepartment::getDayOff($department->id);
        $dayoffs = collect($dayoffs);

        $start = date('Y-m-d', strtotime($data['year'] . '-' . (($data['month'] - 1)) . '-' . 26));
        $end = date('Y-m-d', strtotime($data['year'] . '-' . $data['month'] . '-' . 26));
        
        // $nghi_nhan_vien = StaffDayOff::whereIn('user_id', $staffs->pluck('id')->toArray())->where('start', '<=', $end)->where('end', '>=', $start)->get();
        // $nghi_phong_ban = CalendarDepartment::where('department_id', $data['department_id'])->where('categories', 'holiday')->get();
        $newborns = Newborn::whereIn('user_id', $staffs->pluck('id')->toArray())->whereNull('deleted_at')->get();
        $shift_times = ShiftTime::where('department_id', $department->id)->with('category')->get();

        $user_ids = $staffs->pluck('id')->toArray();

        if (count($results) > 0) {
            foreach ($results as $key => $item) {
                $staff_id = $staffs->where('code_timekeeping', $key)->pluck('id')->first();
                $data_contracts = Contract::getContractsInAMonth($staff_id, $data['month'], $data['year']);
                $contract = $contracts->where('user_id', $staff_id)->first();
                if (is_null($contract)) continue;

                if (strtotime($contract->set_notvalid_on) < strtotime($start) && $contract->type_status == 2) {
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
    
                $shift_users = ModelsShift::getShiftEveryDay($data['year'], $data['month'], $staff_id);
                $shift_users = collect($shift_users);

                $status_contract = '';
                if ($contract->type_status == 1) {
                    $status_contract = 'DANG_HOAT_DONG';
                } else if ($contract->type_status == 2) {
                    $status_contract = 'DIEU_CHUYEN';
                } else if ($contract->type_status == 7) {
                    $status_contract = 'CHO_NGHI_VIEC';
                }

                $nghi_nhan_vien = StaffDayOff::where('user_id', $staff_id)->where('start', '<=', $end)->where('end', '>=', $start)->get();
                $nghi_phong_ban = CalendarDepartment::where('department_id', $data['department_id'])->where('categories', 'holiday')->get();

                foreach ($this->getDateByMonth($data['month'], $data['year']) as $k1 => $v1) {
                    $dateByMonth = strtotime($v1);
                    $timeCheckIn  = $timeCheckOut = $totalHours = $status = $total = $ve_som = $di_muon = $shift = $total_work = 0;
                    $contract_type = $time = $shift_type = '';

                    $return = [
                        'time_check_out'    => $timeCheckOut,
                        'time_check_in'     => $timeCheckIn,
                        'total_hours'       => $totalHours,
                        'status'            => $status,
                        'total'             => $total,
                        'contract_type'     => $contract_type,
                        've_som'            => $ve_som,
                        'di_muon'           => $di_muon,
                        'time'              => 'NGHI_LAM',
                        'shift'             => $shift,
                        'shift_type'        => $shift_type,
                        'status_contract'   => $status_contract,
                        'day_off'           => '',
                        'edit'              => 0,
                        'total_work'        => 0,
                    ];
                    

                    $selectDayOff = StaffDayOff::selectDayOff($workSchedule, $nghi_phong_ban, $nghi_nhan_vien, $staff_id, date('Y-m-d', $dateByMonth), $data['department_id'], $data['month'], $data['year']);
                    $return['day_off'] = $selectDayOff;

                    if (in_array($selectDayOff, $loai_ngay_nghi)) {
                        if (count($data_contracts) == 1) { // kiểm tra hợp đồng trong 1 tháng
                            $return['contract_type'] = $contract->is_main == 2 ? 'HOP_DONG' : 'THU_VIEC';
                        } else {
                            foreach ($data_contracts as $p => $v) {
                                if ($dateByMonth <= strtotime($v['end'])) {
                                    $return['contract_type'] = $v['is_main'] == 2 ? 'HOP_DONG' : 'THU_VIEC';
                                    break;
                                }
                            }
                        }
                        if (in_array($selectDayOff, $nghi_co_luong)) {
                            $return['total'] = 1;
                        }
    
                        $insert[$key][$dateByMonth] = $return;
                    } else {
                        $dayoff = $dayoffs->where('start', date('Y-m-d', $dateByMonth))->first();
                        if (array_key_exists($dateByMonth, $item)) { // xử lý có chấm vân tay
                            $shift = $shift_users->where('date', date('Y-m-d', $dateByMonth))->first()['shift'];
                            if (is_null($shift)) $shift = 100; // không có ca làm, =100 để tính ot
                            $return['shift'] = $shift;
                            $shift_time = $shift_times->where('category_shift_id', $shift)->first();

                            if (isset($shift_time->category)) {
                                if ($shift_time->category->type == 1) {
                                    $return['shift_type'] = self::NGAY;
                                } else if ($shift_time->category->type == 2) {
                                    $return['shift_type'] = self::HANH_CHINH;
                                } else if ($shift_time->category->type == 3) {
                                    $return['shift_type'] = self::DEM;
                                } 
                            }

                            $limit_timein = date('Y-m-d ' . $shift_time->limit_time_in, $dateByMonth);
                            

                            if ($shift != 100) {
                                if ($shift_time->limit_time_out < $shift_time->limit_time_in) {
                                    $dd = date('Y-m-d', $dateByMonth);
                                    $limit_timeout = date('Y-m-d ' . $shift_time->limit_time_out, strtotime($dd . ' +1 day'));
                                } else {
                                    $limit_timeout = date('Y-m-d ' . $shift_time->limit_time_out, $dateByMonth);
                                }
                            } else {
                                $limit_timeout = date('Y-m-d ' . '23:59:00', $dateByMonth);
                            }

                            $check_time = $checkInOut->where('primary_code', $key)->where('timeint', '>=', strtotime($limit_timein))->where('timeint', '<=', strtotime($limit_timeout));
                            $check_time = json_decode(json_encode($check_time), true);
                            
                            if (count($check_time) == 0) {
                                $timeCheckIn = $timeCheckOut = 0;
                            } else {
                                $current_out = current($check_time);
                                $end_in = end($check_time);
                                if ($check_time) $timeCheckIn = date('Y-m-d H:i:s', $end_in['timeint']);
                                if ($check_time) $timeCheckOut = date('Y-m-d H:i:s', $current_out['timeint']);
                            }
                            
                            if ($timeCheckIn != 0 && $timeCheckOut != 0) {
                                if (!is_null($dayoff)) {
                                    $dateDayOff = $this->nghiNuaNgay($staff_id, date('Y-m-d', $dateByMonth), $nghi_nhan_vien); //nhân viên xin nghỉ nửa ngày

                                    if ($dayoff['from_type'] == 1 && $dayoff['to_type'] == 2) {
                                        $return['time_check_in'] = $timeCheckIn;
                                        $return['time_check_out'] = $timeCheckOut;
                                        if (count($data_contracts) == 1) { // kiểm tra hợp đồng trong 1 tháng
                                            $return['contract_type'] = $contract->is_main == 2 ? 'HOP_DONG' : 'THU_VIEC';
                                        } else {
                                            foreach ($data_contracts as $p => $v) {
                                                if ($dateByMonth <= strtotime($v['end'])) {
                                                    $return['contract_type'] = $v['is_main'] == 2 ? 'HOP_DONG' : 'THU_VIEC';
                                                    break;
                                                }
                                            }
                                        }                
                                        
                                    } else if ($dayoff['from_type'] == 1 && $dayoff['to_type'] == 1 && $dateDayOff != '0.5A') { // pb nghỉ sáng làm chiều
                                        if (in_array($selectDayOff, $nghi_nua_luong)) { // pb làm sáng, nhân viên nghỉ sáng, k tính toán
                                            $return['total'] = 0.5;
                                        } else {
                                            $return = $this->handlingTime($return, $timeCheckOut, $timeCheckIn, $shift_time->start_mid_shift, $shift_time->time_out);
                                            $return['time'] = self::LAM_CHIEU;
                                            $return['total'] = $return['total_work'] = 0.5;
                                            if ($return['status'] == self::DU_NGAY_CONG) $return['status'] = self::NUA_CONG;
                                        }

                                    } else if ($dayoff['from_type'] == 2 && $dayoff['to_type'] == 2 && $dateDayOff != '0.5M') { //pb nghỉ chiều làm sáng
                                        if (in_array($selectDayOff, $nghi_nua_luong)) { // pb nghỉ chiều làm sáng, nhân viên nghỉ sáng, ko tính toán
                                            $return['total'] = 0.5;
                                        } else {
                                            $return = $this->handlingTime($return, $timeCheckOut, $timeCheckIn, $shift_time->time_in, $shift_time->off_mid_shift);
                                            $return['time'] = self::LAM_SANG;
                                            $return['total'] = 0.5;
                                            if ($return['status'] == self::DU_NGAY_CONG) $return['status'] = self::NUA_CONG;
                                        }
                                    }

                                    if (count($data_contracts) == 1) { // kiểm tra hợp đồng trong 1 tháng
                                        $return['contract_type'] = $contract->is_main == 2 ? 'HOP_DONG' : 'THU_VIEC';
                                    } else {
                                        foreach ($data_contracts as $p => $v) {
                                            if ($dateByMonth <= strtotime($v['end'])) {
                                                $return['contract_type'] = $v['is_main'] == 2 ? 'HOP_DONG' : 'THU_VIEC';
                                                break;
                                            }
                                        }
                                    }  

                                    $return = $this->tinhOtCa($return, $data, $shift_time, $department, $dateByMonth, $dayoff, $staff_id);
                                    
                                    $insert[$key][$dateByMonth] = $return;
                                } else {
                                    switch ($shift) {
                                        case 100:  
                                            $out = current($item[$dateByMonth]);
                                            $in = end($item[$dateByMonth]);
            
                                            if ($in) $timeCheckIn = date('H:i:s', $in['timeint']);
                                            if ($out) $timeCheckOut = date('H:i:s', $out['timeint']);
                
                                            $return['time_check_in'] = $timeCheckIn;
                                            $return['time_check_out'] = $timeCheckOut;
                                            if (count($data_contracts) == 1) { // kiểm tra hợp đồng trong 1 tháng
                                                $return['contract_type'] = $contract->is_main == 2 ? 'HOP_DONG' : 'THU_VIEC';
                                            } else {
                                                foreach ($data_contracts as $p => $v) {
                                                    if ($dateByMonth <= strtotime($v['end'])) {
                                                        $return['contract_type'] = $v['is_main'] == 2 ? 'HOP_DONG' : 'THU_VIEC';
                                                        break;
                                                    }
                                                }
                                            }
                        
                                            $insert[$key][$dateByMonth] = $return;
    
                                            break;
                                        default:  
                                       
                                            $newborn = $newborns->where('user_id', $staff_id)->where('start', '<=',  date('Y-m-d', $dateByMonth))
                                                                        ->where('end', '>=', date('Y-m-d', $dateByMonth))
                                                                        ->first();
    
                                            if (!is_null($newborn)) {
                                                $check_to = new DateTime($shift_time->off_mid_shift);
                                                $check_af = new DateTime($shift_time->start_mid_shift);
                                                $gio_nghi_trua = $check_to->diff($check_af)->format('%H:%I:%S');
                                                
                                                $t_h = new DateTime($return['total_hours']);
                                                $gio_nghi_trua1 = new DateTime($gio_nghi_trua);
            
                                                $so_gio_thuc_te = $gio_nghi_trua1->diff($t_h)->format('%H');
                                                $so_gio_thuc_te_i = $gio_nghi_trua1->diff($t_h)->format('%I');
            
                                            
                                                if (intval($so_gio_thuc_te_i) >= 30) {
                                                    $so_gio_thuc_te = intval($so_gio_thuc_te) + 0.5;
                                                }
                                                
                                                if ($so_gio_thuc_te < $newborn->time) {
                                                    $return['status'] = self::NUA_CONG;
                                                    $return['total'] = 0.5;
                                                } else {
                                                    $return['status'] = self::DU_NGAY_CONG;
                                                    $return['total'] = 1;
                                                }
                                                
                                            } else {
                                                $dateDayOff = $this->nghiNuaNgay($staff_id, date('Y-m-d', $dateByMonth), $nghi_nhan_vien); //nhân viên xin nghỉ

                                                if ($dateDayOff == '0.5A') { // nghỉ chiều làm sáng
                                                    $return = $this->handlingTime($return, $timeCheckOut, $timeCheckIn, $shift_time->time_in, $shift_time->off_mid_shift);
                                                    $return['time'] = self::LAM_SANG;
                                                    $return['total'] = $return['total_work'] = 0.5;
                                                    if ($return['status'] == self::DU_NGAY_CONG) $return['status'] = self::NUA_CONG;

                                                    if (in_array($selectDayOff, $nghi_nua_luong)) {
                                                        $return['total'] = 1;
                                                    }

                                                } else if ($dateDayOff == '0.5M') { // nghỉ sáng làm chiều
                                                    $return = $this->handlingTime($return, $timeCheckOut, $timeCheckIn, $shift_time->start_mid_shift, $shift_time->time_out);
                                                    $return['time'] = self::LAM_CHIEU;
                                                    $return['total'] = $return['total_work'] = 0.5;
                                                    if ($return['status'] == self::DU_NGAY_CONG) $return['status'] = self::NUA_CONG;

                                                    if (in_array($selectDayOff, $nghi_nua_luong)) {
                                                        $return['total'] = 1;
                                                    }
                                                } else {
                                                    $return = $this->handlingTime($return, $timeCheckOut, $timeCheckIn, $shift_time->time_in, $shift_time->time_out);
                                                    $return['total_work'] = $return['total'];
                                                    $return['time'] = self::CA_NGAY;
                                                }
                                            }
                                            
                                            if (count($data_contracts) == 1) { // kiểm tra hợp đồng trong 1 tháng
                                                $return['contract_type'] = $contract->is_main == 2 ? 'HOP_DONG' : 'THU_VIEC';
                                            } else {
                                                foreach ($data_contracts as $p => $v) {
                                                    if ($dateByMonth <= strtotime($v['end'])) {
                                                        $return['contract_type'] = $v['is_main'] == 2 ? 'HOP_DONG' : 'THU_VIEC';
                                                        break;
                                                    }
                                                }
                                            }

                                            $return = $this->tinhOtCa($return, $data, $shift_time, $department, $dateByMonth, $dayoff, $staff_id);
                                            
                                            $insert[$key][$dateByMonth] = $return;
                                        break;
                                    }

                                }
                            } else {
                                if (count($data_contracts) == 1) { // kiểm tra hợp đồng trong 1 tháng
                                    $return['contract_type'] = $contract->is_main == 2 ? 'HOP_DONG' : 'THU_VIEC';
                                } else {
                                    foreach ($data_contracts as $p => $v) {
                                        if ($dateByMonth <= strtotime($v['end'])) {
                                            $return['contract_type'] = $v['is_main'] == 2 ? 'HOP_DONG' : 'THU_VIEC';
                                            break;
                                        }
                                    }
                                }
                                $insert[$key][$dateByMonth] = $return;
                            }

                        } else { // xử lý không chấm vân tay
                            if (in_array($selectDayOff, $nghi_nua_luong)) {
                                $return['total'] = 0.5;
                            }
                            if (in_array($selectDayOff, $nghi_co_luong)) {
                                $return['total'] = 1;
                            }

                            if (count($data_contracts) == 1) { // kiểm tra hợp đồng trong 1 tháng
                                $return['contract_type'] = $contract->is_main == 2 ? 'HOP_DONG' : 'THU_VIEC';
                            } else {
                                foreach ($data_contracts as $p => $v) {
                                    if ($dateByMonth <= strtotime($v['end'])) {
                                        $return['contract_type'] = $v['is_main'] == 2 ? 'HOP_DONG' : 'THU_VIEC';
                                        break;
                                    }
                                }
                            }

                            $insert[$key][$dateByMonth] = $return;
                        }
                    }

                    ksort($insert[$key]);
                    $total = array_sum(array_column($insert[$key], 'total'));
                }

                $timeKeepingDetail[] = [
                    'timekeeping_id' => $timeKeeping->id,
                    'code'           => $key,
                    'detail'         => json_encode($insert[$key]),
                    'created_by'     => $timeKeeping->created_by,
                    'total'          => $total,
                    'staff_id'       => $staff_id
                ];

            }

            $date = [];
            if (count($user_ids) > 0) {
                foreach ($user_ids as $key => $user) {
                    $data_contracts = Contract::getContractsInAMonth($user, $data['month'], $data['year']);
                    $contract = $contracts->where('user_id', $user)->first();
                    if (is_null($contract)) continue;
                    if (strtotime($contract->set_notvalid_on) < strtotime($start) && $contract->type_status == 2) {
                        continue;
                    }
                    $status_contract = '';
                    if ($contract->type_status == 1) {
                        $status_contract = 'DANG_HOAT_DONG';
                    } else if ($contract->type_status == 2) {
                        $status_contract = 'DIEU_CHUYEN';
                    } else if ($contract->type_status == 7) {
                        $status_contract = 'CHO_NGHI_VIEC';
                    }

                    $nghi_nhan_vien = StaffDayOff::where('user_id', $user)->where('start', '<=', $end)->where('end', '>=', $start)->get();
                        $nghi_phong_ban = CalendarDepartment::where('department_id', $data['department_id'])->where('categories', 'holiday')->get();

                    foreach ($this->getDateByMonth($data['month'], $data['year']) as $k1 => $v1) {
                        $dateByMonth = strtotime($v1);
                        $timeCheckIn  = $timeCheckOut = $totalHours = $status = $total = $ve_som = $di_muon = $shift = $total_work = 0;
                        $contract_type = $time = $shift_type = '';

                        $return = [
                            'time_check_out'    => $timeCheckOut,
                            'time_check_in'     => $timeCheckIn,
                            'total_hours'       => $totalHours,
                            'status'            => $status,
                            'total'             => $total,
                            'contract_type'     => $contract_type,
                            've_som'            => $ve_som,
                            'di_muon'           => $di_muon,
                            'time'              => 'NGHI_LAM',
                            'shift'             => $shift,
                            'shift_type'        => $shift_type,
                            'status_contract'   => $status_contract,
                            'day_off'           => '',
                            'edit'              => 0,
                            'total_work'        => 0,
                        ];
                        
                        $selectDayOff = StaffDayOff::selectDayOff($workSchedule, $nghi_phong_ban, $nghi_nhan_vien, $user, date('Y-m-d', $dateByMonth), $data['department_id'], $data['month'], $data['year']);
                        $return['day_off'] = $selectDayOff;

                        if (in_array($selectDayOff, $loai_ngay_nghi) || in_array($selectDayOff, $nghi_nua_luong)) {
                            $return['time_check_out'] = $timeCheckOut;
                            $return['time_check_in'] = $timeCheckIn;
                            if (in_array($selectDayOff, $nghi_co_luong)) {
                                $return['total'] = 1;
                            }
                            if (in_array($selectDayOff, $nghi_nua_luong)) {
                                $return['total'] = 0.5;
                            }
                        }

                        if (count($data_contracts) == 1) { // kiểm tra hợp đồng trong 1 tháng
                            $return['contract_type'] = $contract->is_main == 2 ? 'HOP_DONG' : 'THU_VIEC';
                        } else {
                            foreach ($data_contracts as $p => $v) {
                                if ($dateByMonth <= strtotime($v['end'])) {
                                    $return['contract_type'] = $v['is_main'] == 2 ? 'HOP_DONG' : 'THU_VIEC';
                                    break;
                                }
                            }
                        }
                        $date[$dateByMonth] = $return;

                        $total = array_sum(array_column($date, 'total'));
                    }

                    $timeKeepingDetail[] = [
                        'timekeeping_id' => $timeKeeping->id,
                        'code'           => $key,
                        'detail'         => json_encode($date),
                        'created_by'     => $timeKeeping->created_by,
                        'total'          => $total,
                        'staff_id'       => $user
                    ];
                }
            }
        } else {
            $date = [];
            if (count($user_ids) > 0) {
                foreach ($user_ids as $key => $user) {
                    $data_contracts = Contract::getContractsInAMonth($user, $data['month'], $data['year']);
                    $contract = $contracts->where('user_id', $user)->first();
                    if (is_null($contract)) continue;
                    if (strtotime($contract->set_notvalid_on) < strtotime($start) && $contract->type_status == 2) {
                        continue;
                    }
                    $status_contract = '';
                    if ($contract->type_status == 1) {
                        $status_contract = 'DANG_HOAT_DONG';
                    } else if ($contract->type_status == 2) {
                        $status_contract = 'DIEU_CHUYEN';
                    } else if ($contract->type_status == 7) {
                        $status_contract = 'CHO_NGHI_VIEC';
                    }

                    $nghi_nhan_vien = StaffDayOff::where('user_id', $user)->where('start', '<=', $end)->where('end', '>=', $start)->get();
                    $nghi_phong_ban = CalendarDepartment::where('department_id', $data['department_id'])->where('categories', 'holiday')->get();

                    foreach ($this->getDateByMonth($data['month'], $data['year']) as $k1 => $v1) {
                        $dateByMonth = strtotime($v1);
                        $timeCheckIn  = $timeCheckOut = $totalHours = $status = $total = $ve_som = $di_muon = $shift = $total_work = 0;
                        $contract_type = $time = $shift_type = '';
                        $selectDayOff = null;

                        $return = [
                            'time_check_out'    => $timeCheckOut,
                            'time_check_in'     => $timeCheckIn,
                            'total_hours'       => $totalHours,
                            'status'            => $status,
                            'total'             => $total,
                            'contract_type'     => $contract_type,
                            've_som'            => $ve_som,
                            'di_muon'           => $di_muon,
                            'time'              => 'NGHI_LAM',
                            'shift'             => $shift,
                            'shift_type'        => $shift_type,
                            'status_contract'   => $status_contract,
                            'day_off'           => '',
                            'edit'              => 0,
                            'total_work'        => 0,
                        ];
                        

                        $selectDayOff = StaffDayOff::selectDayOff($workSchedule, $nghi_phong_ban, $nghi_nhan_vien, $user, date('Y-m-d', $dateByMonth), $data['department_id'], $data['month'], $data['year']);
                        $return['day_off'] = $selectDayOff;

                        if (in_array($selectDayOff, $loai_ngay_nghi) || in_array($selectDayOff, $nghi_nua_luong)) {
                            $return['time_check_out'] = $timeCheckOut;
                            $return['time_check_in'] = $timeCheckIn;
                            if (in_array($selectDayOff, $nghi_co_luong)) {
                                $return['total'] = 1;
                            }
                            if (in_array($selectDayOff, $nghi_nua_luong)) {
                                $return['total'] = 0.5;
                            }
                        }

                        if (count($data_contracts) == 1) { // kiểm tra hợp đồng trong 1 tháng
                            $return['contract_type'] = $contract->is_main == 2 ? 'HOP_DONG' : 'THU_VIEC';
                        } else {
                            foreach ($data_contracts as $p => $v) {
                                if ($dateByMonth <= strtotime($v['end'])) {
                                    $return['contract_type'] = $v['is_main'] == 2 ? 'HOP_DONG' : 'THU_VIEC';
                                    break;
                                }
                            }
                        }
                        $date[$dateByMonth] = $return;

                        $total = array_sum(array_column($date, 'total'));
                    }

                    $timeKeepingDetail[] = [
                        'timekeeping_id' => $timeKeeping->id,
                        'code'           => $key,
                        'detail'         => json_encode($date),
                        'created_by'     => $timeKeeping->created_by,
                        'total'          => $total,
                        'staff_id'       => $user
                    ];
                }
            }
        }

        if (DB::table('timekeeping_detail')->insert($timeKeepingDetail)) {
            return true;
        }

        return false;
    }

    public function handlingTime($return, $timeCheckOut, $timeCheckIn, $startTime, $endTime, $workSchedule = null)
    {
        $return['time_check_out'] = $timeCheckOut;
        $return['time_check_in'] = $timeCheckIn;

        $timeCheckIn = date('H:i:s', strtotime($timeCheckIn));
        $timeCheckOut = date('H:i:s', strtotime($timeCheckOut));

        $check_in = new Carbon($timeCheckIn);
        $check_out = new Carbon($timeCheckOut);
        
        $return['total_hours'] = $check_in->diff($check_out)->format('%H:%I:%S');

        if ($timeCheckIn == $timeCheckOut && $timeCheckIn == "00:00:00" && $timeCheckOut = "00:00:00") {
            $return['shift'] = 0;
        } else if ($timeCheckIn == $timeCheckOut
            || $timeCheckIn == "00:00:00" && $timeCheckOut != "00:00:00"
            || $timeCheckOut == "00:00:00" && $timeCheckIn != "00:00:00"
            || $timeCheckOut == "00:00:00"
        ) {
            if ($timeCheckIn == $timeCheckOut && is_null($return['shift'])) {
                if ($timeCheckIn <= "12:00:00")  $return['time_check_out'] = "0";
                if ($timeCheckOut > "12:00:00")  $return['time_check_in'] = "0";
            }
            
            $return['status'] = self::QUEN_QUET;
            $return['total'] = 0.5;

        } else if ($timeCheckIn <= $startTime && $timeCheckOut >= $endTime) {
            $return['status'] = self::DU_NGAY_CONG;
            $return['total'] = 1;

        } else if ($timeCheckIn > $startTime && $timeCheckOut < $endTime) {
            $return['status'] = self::DIMUON_VESOM;
            $return['total'] = 0.5;

        } else if ($timeCheckIn > $startTime && $timeCheckOut >= $endTime) {
            $from_morning = date_format(date_create($workSchedule->from_morning), "H:i:s");
            $return['status'] = self::DI_MUON;
            $return['total'] = 0.5;

            $di_muon_h = $check_in->diff($from_morning)->format('%H');
            $di_muon_i = $check_in->diff($from_morning)->format('%I');

            if ($di_muon_i <= 29 ) $di_muon_i = 0;
            if ($di_muon_i >= 30 ) $di_muon_i = 0.5;
            if ($di_muon_i >= 59 ) $di_muon_i = 1;

            $return['di_muon'] = $di_muon_h + $di_muon_i;

        } else if ($timeCheckIn <= $startTime && $timeCheckOut < $endTime) {
            $to_afternoon = date_format(date_create($workSchedule->to_afternoon), "H:i:s");
            $return['status'] = self::VE_SOM;
            $return['total'] = 0.5;

            $ve_som_h = $check_out->diff($to_afternoon)->format('%H');
            $ve_som_i = $check_out->diff($to_afternoon)->format('%I');

            if ($ve_som_i <= 29 ) $ve_som_i = 0;
            if ($ve_som_i >= 30 ) $ve_som_i = 0.5;
            if ($ve_som_i >= 59 ) $ve_som_i = 1;

            $return['ve_som'] = $ve_som_h + $ve_som_i;
        }

        return $return;
    }

    public function nghiNuaNgay($userId, $date = null, $nghi_nhan_vien)
    {
        $date = Carbon::createFromDate($date)->format('Y-m-d');
       
        $dayOff = $nghi_nhan_vien->where('user_id', $userId)->where('start', '<=', $date)->where('end', '>=', $date)->where('status', 1)->first();
        if (!count($dayOff)) return '';
        $code = 1;
        if ($dayOff->start < $date && $date < $dayOff->end) return $code;
        elseif ($dayOff->total < 1) return $dayOff->from_type == Schedule::TIME_OFF_AFTERNOON ? '0.5A' : '0.5M';
        elseif ($date == $dayOff->start) return $dayOff->from_type == Schedule::TIME_OFF_AFTERNOON ? '0.5A' : $code;
        elseif ($date == $dayOff->end) return $dayOff->to_type == Schedule::TIME_OFF_MORNING ? '0.5M' : $code;
        return '';
    }

    public function detail(Request $request, $id)
    {
        $code_day_offs = DefineTimekeeping::codeDayOff();
        $search = $request->input('fullname');
        $getDays = $getDates = [];
        $totalDayOf = 0;
        $detail = TimeKeeping::find($id);
        if (empty($detail)) {
            return redirect()->route('admin.timekeeping.index');
        }
        $detail->load('company', 'department');
        $getDateByMonth = $this->getDateByMonth($detail->month, $detail->year);
        $start = $detail->year . '-' . ($detail->month - 1) . '-' . 26;
        $end = $detail->year . '-' . $detail->month . '-' . 25;

        $start = date('Y-m-d', strtotime($detail->year . '-' . (($detail->month - 1)) . '-' . 26));
        $end = date('Y-m-d', strtotime($detail->year . '-' . $detail->month . '-' . 25));

        foreach ($getDateByMonth as $key => $item) {
            $getDays[] = substr(Carbon::parse($item)->format('l'), 0, 3);
            $getDates[] = Carbon::parse($item)->format('d');
            $getDateByMonth[$key] = strtotime($item);
        }
        $totalDay = count($getDays);
        $arr = array_count_values($getDays);

        $user_ids = ConcurrentContract::where('company_id', $detail->company_id)->where('department_id', $detail->department_id)->pluck('user_id');
        
        $concurrent_contract = ConcurrentContract::where('status', 1)->where('company_id', $detail->company_id)
            ->where('department_id', $detail->department_id)
            ->pluck('user_id');

        $timekeeping_ids = TimeKeepingDetail::whereHas('timekeeping', function($q) use ($detail) {
            $q->where('month', $detail->month)->where('year', $detail->year)->where('version', 1);
        })->whereIn('staff_id', $concurrent_contract)->pluck('id');
        // $timekeeping_ids = [];

        if (is_null($timekeeping_ids)) $timekeeping_ids = [];

        if (!empty($search)) {
            $items = TimeKeepingDetail::where('timekeeping_id', $id)->whereHas('staff', function ($query) use ($search) {
                $query->where('fullname', 'like', '%' . $search . '%');
            })->orWhereIn('id', $timekeeping_ids)->get();
        } else {
            $infoPermission = PermissionUserObject::getMorePermissions(Auth::user()->id, 'timekeeping.read');
           
            if (Auth::user()->hasRole('NV') && count($infoPermission['departments']) == 0) {
                $items = TimeKeepingDetail::where('timekeeping_id', $id)->where('staff_id', Auth::user()->id)->get();
            } else {
                $items = TimeKeepingDetail::where('timekeeping_id', $id)->orWhereIn('id', $timekeeping_ids)->get();
            }
        }

        $items->load('staff');
        $total_day_request = 0;

        foreach ($items as $key => $item) {
            $total_day_request = OverTimes::totalWorkingInMonth($detail->month, $detail->year, $item->timekeeping->department_id);

            $nghi_huong_luong = $nghi_cong_tac = $nghi_cong_tac_nua = $nghi_phep = $nghi_phep_nua = 0;

            $concurrent_contract = ConcurrentContract::where('status', 1)
                    ->where('department_id', $detail->department_id)
                    ->where('user_id', $item->staff_id)->first();
            if (!is_null($concurrent_contract)) {
                $items[$key]['concurrent_contract'] = 1;
            }
            
            $items[$key]['detail'] = json_decode($item->detail, true);
            $total_work = StaffDayOff::countTotalInMonthForTimeKeeping($item->staff_id, $detail->month, $detail->year);
            $cong = collect($item->detail);
           
            $items[$key]['total_day_request'] = $total_day_request;

            $tongHop = TimeKeeping::tongHop($item->id, $detail->department->type, $total_day_request);
            $items[$key]['total'] = $tongHop['total'];
            $items[$key]['total_hd'] = $tongHop['total_hd'];
            $items[$key]['total_tv'] = $tongHop['total_tv'];
            $items[$key]['dem_hd'] = $tongHop['dem_hd'];
            $items[$key]['dem_tv'] = $tongHop['dem_tv'];

            if ($detail->department->type == \App\Define\Department::DECLARATION_OFFICE) {
                $items[$key]['shift_day'] = $tongHop['shift_day'];
                $items[$key]['shift_hc'] = $tongHop['shift_hc'];
                $items[$key]['shift_night'] = $tongHop['shift_night'];
            }

            foreach ($code_day_offs as $name => $code) {
                $items[$key][$name] = $tongHop[$name];
            }

        }
        
        $getShift = Shift::getShift();

        if ($request->export == self::EXPORT) {
            return [
                'items' => $items,
                'detail' => $detail,
                'getDays' => $getDays,
                'getDates' => $getDates,
                'getShift' => $getShift
            ];
        }

        return view('backend.timekeeping.detail-v1', compact('items', 'detail', 'getDays', 'getDates', 'total_day_request', 'getShift'));
    }

    public function tinhOtHanhChinh($return, $data, $workSchedule, $department, $dateByMonth, $dayoff, $staff_id)
    {
        $ngay = $dem = $dem_thuong_ko_ot_ngay = $dem_thuong_co_ot_ngay = 0;
        $type_ot = '';
        $ot = [
            'ngay'                       => $ngay,
            'dem'                        => $dem,
            'dem_thuong_ko_ot_ngay'      => $dem_thuong_ko_ot_ngay,
            'dem_thuong_co_ot_ngay'      => $dem_thuong_co_ot_ngay,
            'type_ot'                    => $type_ot,
        ];

        if ($return['status'] == self::QUEN_QUET || $return['status'] == self::DIMUON_VESOM) {
            return $return + $ot;
        }

        if (is_null($dayoff)) {
            if ($return['time_check_out'] < $workSchedule->ot) return $return + $ot;
        }

        $overTimes = OverTimes::getOT($data['month'], $data['year'], $staff_id);
        if (empty($overTimes)) return $return + $ot;
        
        $overTimes = collect($overTimes);
        $overTime = $overTimes->where('date', date('Y-m-d', $dateByMonth))->first();

        if (is_null($overTime))  return $return + $ot;

        $check_out = new Carbon($return['time_check_out']);
        $check_in = new Carbon($return['time_check_in']);
        $time_ot = new Carbon($workSchedule->ot);

        if ($overTime['type'] == OverTime::TYPE_NORMAL) {
            $ot['type_ot'] = self::NGAY_THUONG;

            if (strtotime($check_out) > strtotime($time_ot) 
                && $return['time'] == self::CA_NGAY 
            ) {
                $check_ot = $check_out->diff($time_ot)->format('%H:%I:%S');

                $covert_check_ot_h = intval(date_format(date_create($check_ot), "H"));
                $covert_check_ot_i = intval(date_format(date_create($check_ot), "i"));

                $covert_check_ot_i = $this->lamTronOt($covert_check_ot_i);
                $check_ot = $covert_check_ot_h + $covert_check_ot_i;
                if ($check_ot > $overTime['hours']) $check_ot = $overTime['hours'];

                $ot['ngay'] = max(($check_ot - $return['di_muon']), 0);
            }
        } else {
            if ($overTime['type'] == OverTime::TYPE_DAYOFF) $ot['type_ot'] = self::NGAY_NGHI;
            else $ot['type_ot'] = self::NGAY_LE;

            if ($return['time'] == self::LAM_SANG) { // ot chiều
                $from_afternoon = new Carbon($workSchedule->from_afternoon);
                $to_afternoon = new Carbon($workSchedule->to_afternoon);

                if (strtotime($check_out) > strtotime($from_afternoon)) {
                    if (strtotime($check_out) > strtotime($to_afternoon)) {
                        $check_ot = $to_afternoon->diff($from_afternoon)->format('%H:%I:%S');

                        $covert_check_ot_h = intval(date_format(date_create($check_ot), "H"));
                        $covert_check_ot_i = intval(date_format(date_create($check_ot), "i"));

                        $covert_check_ot_i = $this->lamTronOt($covert_check_ot_i);
                        $check_ot = $covert_check_ot_h + $covert_check_ot_i;
                        if ($check_ot > $overTime['hours']) $check_ot = $overTime['hours'];

                        $ot['ngay'] = $check_ot;
                    }
                }
                
            } else if ($return['time'] == self::LAM_CHIEU) { //ot sáng
                $to_morning = new Carbon($workSchedule->to_morning);
                if (strtotime($check_in) < strtotime($to_morning)) {
                    $check_ot = $check_in->diff($to_morning)->format('%H:%I:%S');

                    $covert_check_ot_h = intval(date_format(date_create($check_ot), "H"));
                    $covert_check_ot_i = intval(date_format(date_create($check_ot), "i"));

                    $covert_check_ot_i = $this->lamTronOt($covert_check_ot_i);
                    $check_ot = $covert_check_ot_h + $covert_check_ot_i;
                    if ($check_ot > $overTime['hours']) $check_ot = $overTime['hours'];

                    $ot['ngay'] = $check_ot;
                }
            } else {
                $to_morning = new Carbon($workSchedule->to_morning);
                $from_afternoon = new Carbon($workSchedule->from_afternoon);

                $nghi_trua = $to_morning->diff($from_afternoon)->format('%H:%I:%S');

                $nghi_trua_h = intval(date_format(date_create($nghi_trua), "H"));
                $nghi_trua_i = intval(date_format(date_create($nghi_trua), "i"));

                $nghi_trua_i = $this->lamTronOt($nghi_trua_i);

                $nghi_trua = $nghi_trua_h + $nghi_trua_i;

                $check_ot = $check_in->diff($check_out)->format('%H:%I:%S');
                $covert_check_ot_h = intval(date_format(date_create($check_ot), "H"));
                $covert_check_ot_i = intval(date_format(date_create($check_ot), "i"));
                $covert_check_ot_i = $this->lamTronOt($covert_check_ot_i);

                $check_ot = $covert_check_ot_h + $covert_check_ot_i - $nghi_trua;

                if ($check_ot > $overTime['hours']) $check_ot = $overTime['hours'];

                if ($check_ot > 0) $ot['ngay'] = $check_ot;
            }
        }

        $ot['total_ot'] = $ot['ngay'] + $ot['dem'] + $ot['dem_thuong_ko_ot_ngay'] + $ot['dem_thuong_co_ot_ngay'];

        return $return + $ot;
    }

    public function lamTronOt($phut)
    {
        if ($phut <= 29 ) $phut = 0;
        if ($phut >= 30 ) $phut = 0.5;
        if ($phut >= 59 ) $phut = 1;

        return $phut;
    }

    public function updateTimekeeping(Request $request, $id)
    {
        $return = \Response::json([
            'status' => 'FAIL',
            'message' => 'Lỗi'
        ]);

        if ($request->ajax()) {
            $data = $request->all();
            $timekeepingDetail = TimeKeepingDetail::find($id)->load('timekeeping');

            if (is_null($timekeepingDetail)) return $return;
            $detail = json_decode($timekeepingDetail->detail, true);

            $data_old = [
                $data['key'] => $detail[$data['key']]
            ];
            $dayoffs = CalendarDepartment::getDayOff($timekeepingDetail->timekeeping->department_id);
            $dayoffs = collect($dayoffs);
            $dayoff = $dayoffs->where('start', date('Y-m-d', $data['key']))->first();
            if ($dayoff['from_type'] == 1 && $dayoff['to_type'] == 2) {
                $return = \Response::json([
                    'status' => 'FAIL',
                    'message' => 'Ngày nghỉ phòng ban'
                ]);

                return $return;
            }

            if ($timekeepingDetail->timekeeping->department->type == \App\Define\Department::FUNCTIONAL_OFFICE 
                && in_array($data['status'], [1, 10])) {

                if (!is_null($dayoff)) {
                    if (($dayoff['from_type'] == $dayoff['to_type']) && $data['status'] == 1) {
                        return \Response::json([
                            'status' => 'FAIL',
                            'message' => 'Phòng ban đi làm nửa ngày, không thể sửa đi làm cả ngày'
                        ]);
                    }
                     
                    if ($dayoff['from_type'] == 1 && $dayoff['to_type'] == 1) {
                        $detail[$data['key']]['time'] = self::LAM_CHIEU;
                    } else if ($dayoff['from_type'] == 2  && $dayoff['to_type'] == 2) {
                        $detail[$data['key']]['time'] = self::LAM_SANG;
                    }
                    
                } else {
                    $detail[$data['key']]['time'] = self::CA_NGAY;
                }

                $data['status'] == 1 ? $total = 1 : $total = 0.5;
                $detail[$data['key']]['status'] = $data['status'];
                $detail[$data['key']]['total_work'] = $total;
                $detail[$data['key']]['edit'] = 1;
                if ($total == 0.5) {
                    $detail[$data['key']]['total'] += $total;
                } else if ($total == 1) {
                    $detail[$data['key']]['total'] = $total;
                }
            } else {
                unset($data['status']);
                $ca_nua = $data['shift'];
                $data['shift'] = str_replace('_', '', $data['shift']);

                if(preg_match("/_/i", $ca_nua)) $ca_nua = 10;
                
                $shift_users = ModelsShift::getShiftEveryDay($data['year'], $data['month'], $timekeepingDetail->staff_id);
                $shift_users = collect($shift_users);
                $shift = $shift_users->where('date', date('Y-m-d', $data['key']))->first()['shift'];
                if (is_null($shift)) {
                    return \Response::json([
                        'status' => 'FAIL',
                        'message' => 'Nhân viên chưa có ca làm việc'
                    ]);
                }

                $cate_shift = CategoryShift::find($shift);
                if ($data['shift'] != $shift) {
                    return \Response::json([
                        'status' => 'FAIL',
                        'message' => 'Nhân viên được sếp lịch làm ca: ' . $cate_shift->shortened_name
                    ]);
                }

                if ($cate_shift->type == 1) {
                    $detail[$data['key']]['shift_type'] = self::NGAY;
                } else if ($cate_shift->type == 2) {
                    $detail[$data['key']]['shift_type'] = self::HANH_CHINH;
                } else if ($cate_shift->type == 3) {
                    $detail[$data['key']]['shift_type'] = self::DEM;
                } 
                $detail[$data['key']]['shift'] = $shift;
                
                if ($ca_nua == 10) { // chỉnh sửa làm nửa ngày
                    if (!is_null($dayoff)) {
                        if ($dayoff['from_type'] == 1 && $dayoff['to_type'] == 1) {
                            $detail[$data['key']]['time'] = self::LAM_CHIEU;
                        } else if ($dayoff['from_type'] == 2  && $dayoff['to_type'] == 2) {
                            $detail[$data['key']]['time'] = self::LAM_SANG;
                        }
                    } else {
                        $detail[$data['key']]['time'] = self::LAM_SANG;
                    }

                    $detail[$data['key']]['status'] = self::NUA_CONG;
                    $detail[$data['key']]['total_work'] = 0.5;
                    $detail[$data['key']]['edit'] = 1;
                    $detail[$data['key']]['total'] += 0.5;
                } else {
                    $detail[$data['key']]['status'] = self::DU_NGAY_CONG;
                    $detail[$data['key']]['time'] = self::CA_NGAY;
                    $detail[$data['key']]['total_work'] = 1;
                    $detail[$data['key']]['total'] = 1;
                    $detail[$data['key']]['edit'] = 1;
                }
            }

            $data_new = [
                $data['key'] => $detail[$data['key']]
            ];

            try {
                $timekeepingDetail->update([
                    'detail' => json_encode($detail),
                ]);
    
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
    
                Log::create($response);

                return \Response::json([
                    'status' => 'SUCCESS',
                    'message' => 'Cập nhập thành công',
                ]);
    
            } catch (Exception $e) {
                return \Response::json([
                    'status' => 'FAIL',
                    'message' => $e->getMessage(),
                ]);
            }

        } else {
            return $return;
        }
    }

    public function otDetail(Request $request, $id)
    {
        $search = $request->input('fullname');
        $getDays = $getDates = [];

        $timekeeping = Timekeeping::find($id);
        if (empty($timekeeping)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.ot.index');
        } 
        $timekeeping->load('company', 'department');

        $getDateByMonth = $this->getDateByMonth($timekeeping->month, $timekeeping->year);

        foreach ($getDateByMonth as $key => $item) {
            $getDays[] = substr(Carbon::parse($item)->format('l'), 0, 3);
            $getDates[] = Carbon::parse($item)->format('d');
            $getDateByMonth[$key] = strtotime($item);
        }

        if (!empty($search)) {
            $items = TimeKeepingDetail::with('staff')->where('timekeeping_id', $id)->whereHas('staff', function ($query) use ($search) {
                $query->where('fullname', 'like', '%' . $search . '%');
            })->get();
        } else {
            $infoPermission = PermissionUserObject::getMorePermissions(Auth::user()->id, 'timekeeping.read');
            if (Auth::user()->hasRole('NV') && count($infoPermission['departments']) == 0) {
                $items = TimeKeepingDetail::with('staff')->where('timekeeping_id', $id)->where('staff_id', Auth::user()->id)->get();
            } else {
                $items = TimeKeepingDetail::with('staff')->where('timekeeping_id', $id)->get();
            }
        }

        if (empty($items)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.ot.index');
        }

        foreach ($items as $key => $item) {
            $detail = json_decode($item->detail, true);
            $items[$key]['detail'] = $detail;
            $detail = collect($detail);

            $dem_thuong_ko_ot_ngay = $detail->sum('dem_thuong_ko_ot_ngay');
            $dem_thuong_co_ot_ngay = $detail->sum('dem_thuong_co_ot_ngay');
            $dem_nghi = $detail->where('type_ot', self::NGAY_NGHI)->sum('dem');
            $dem_le = $detail->where('type_ot', self::NGAY_LE)->sum('dem');

            $ngay_thuong = $detail->where('type_ot', self::NGAY_THUONG)->sum('ngay');
            $ngay_nghi = $detail->where('type_ot', self::NGAY_NGHI)->sum('ngay');
            $ngay_le = $detail->where('type_ot', self::NGAY_LE)->sum('ngay');

            $items[$key]['dem_thuong_ko_ot_ngay'] = $dem_thuong_ko_ot_ngay;
            $items[$key]['dem_thuong_co_ot_ngay'] = $dem_thuong_co_ot_ngay;
            $items[$key]['dem_nghi'] = $dem_nghi;
            $items[$key]['dem_le'] = $dem_le;
            $items[$key]['ngay_thuong'] = $ngay_thuong;
            $items[$key]['ngay_nghi'] = $ngay_nghi;
            $items[$key]['ngay_le'] = $ngay_le;
            $items[$key]['total_ot_tv'] = $detail->where('contract_type', 'THU_VIEC')->sum('total_ot');
            $items[$key]['total_ot_hd'] = $detail->where('contract_type', 'HOP_DONG')->sum('total_ot');

            $an_chinh_ngay_di_lam = $detail->where('total', 1)->count();
            $an_chinh_ngay_thuong = $detail->where('type_ot', self::NGAY_THUONG)->where('total_ot', '>=', 6)->count();
            $an_chinh_nua_cong = $detail->where('type_ot', self::NGAY_NGHI)->where('total_work', 0.5)->where('total_ot', '>', 0)->count();
            $an_chinh_ngay_nghi = $detail->where('type_ot', self::NGAY_NGHI)->where('status', self::NGHI_LAM)->where('total_ot', '>', 4)->count();
            $an_chinh_ot = $an_chinh_ngay_thuong + $an_chinh_nua_cong + $an_chinh_ngay_nghi;

            $items[$key]['an_chinh_ot'] = $an_chinh_ot;
            $items[$key]['an_chinh_ngay_di_lam'] = $an_chinh_ngay_di_lam;
            $items[$key]['an_chinh'] = $an_chinh_ngay_di_lam + $an_chinh_ot;

            $an_phu_ngay_thuong = $detail->where('type_ot', self::NGAY_THUONG)->where('total_ot', '>=', 3)->where('total_ot', '<', 6)->count();
            $an_phu_nua_cong = $detail->where('type_ot', self::NGAY_NGHI)->where('total_work', 0.5)->where('total_ot', '>=', 7)->count();
            $an_phu_ngay_nghi = $detail->where('type_ot', self::NGAY_NGHI)->where('status', self::NGHI_LAM)->where('total_ot', '>=', 11)->count();
            $items[$key]['an_phu'] = $an_phu_ngay_thuong + $an_phu_nua_cong + $an_phu_ngay_nghi;
        }
        $configOt = TimeKeeping::configOt($timekeeping->company_id)->toArray();
        $data = [
            'getDays' => $getDays,
            'getDates' => $getDates,
            'items' => $items,
            'timekeeping' => $timekeeping,
            'configOt' => $configOt
        ];
        if ($request->export == 1) return $data;

        return view('backend.timekeeping.detail-ot-v1', $data);

    }

    public function updateOt(Request $request, $id)
    {
        $return = \Response::json([
            'status' => 'FAIL',
            'message' => 'Có lỗi xảy ra'
        ]);

        if ($request->ajax()) {
            $timeKeepingDetail = TimeKeepingDetail::find(intval($id))->load('timekeeping');
            if (is_null($timeKeepingDetail)) return $return;
            try {
                $data = $request->all();
                $key = $data['key'];
                $type = $data['type'];

                $overTimes = OverTimes::getOT($timeKeepingDetail->timekeeping->month, $timeKeepingDetail->timekeeping->year, $timeKeepingDetail->staff_id);
                $overTimes = collect($overTimes);
                $date_over_time = $overTimes->where('date', date('Y-m-d', $key))->first();

                if (empty($date_over_time)) {
                    return \Response::json([
                        'status' => 'FAIL',
                        'message' => 'Không có lịch OT'
                    ]);
                }

                $dep_type = $timeKeepingDetail->timekeeping->department->type;

                if ($type == 'night' && $dep_type == DefineDepartment::FUNCTIONAL_OFFICE) { //Hành chính không có OT dêm
                    return \Response::json([
                        'status' => 'FAIL',
                        'message' => 'Lỗi không có lịch OT'
                    ]);
                }

                $detail = json_decode($timeKeepingDetail->detail, true);
                $data_old = $detail[$key];

                if ($date_over_time['type'] == 1) {
                    $detail[$key]['type_ot'] = self::NGAY_THUONG;
                } else if ($date_over_time['type'] == 2) {
                    $detail[$key]['type_ot'] = self::NGAY_NGHI;
                } else {
                    $detail[$key]['type_ot'] = self::NGAY_LE;
                }

                if ($dep_type == DefineDepartment::FUNCTIONAL_OFFICE) { // Hành chính
                    if ($data['ot'] > $date_over_time['hours'] ) {
                        return \Response::json([
                            'status' => 'FAIL',
                            'message' => 'Số giờ OT tối đa không quá, với lịch: ' . $date_over_time['hours']
                        ]);
                    }
                   
                    $detail[$key]['ngay'] = $data['ot'];
                    $detail[$key]['note_edit_ot_day'] = $data['note'];
                    $detail[$key]['edit'] = 1;
                    $detail[$key]['ot_change'] = $type;
                    
                } else { // Ca
                    if ($data['type'] == 'day' && !is_null($date_over_time['hours']['day'])) {
                        if ($data['ot'] > $date_over_time['hours']['day']) {
                            return \Response::json([
                                'status' => 'FAIL',
                                'message' => 'Số giờ OT lớn hơn số giờ trong lịch: ' . $date_over_time['hours']['day']
                            ]);
                        }

                        $detail[$key]['ngay'] = $data['ot'];
                        $detail[$key]['note_edit_ot_day'] = $data['note'];
                        $detail[$key]['edit'] = 1;
                        $detail[$key]['ot_change'] = $type;
                    }
                    if ($data['type'] == 'night' && !is_null($date_over_time['hours']['night']) && $date_over_time['type'] == 1) {
                        if ($data['ot'] > $date_over_time['hours']['night']) {
                            return \Response::json([
                                'status' => 'FAIL',
                                'message' => 'Số giờ OT lớn hơn số giờ trong lịch: ' . $date_over_time['hours']['night']
                            ]);
                        }

                        if ($detail[$key]['ngay'] > 0) {
                            $detail[$key]['dem_thuong_co_ot_ngay'] = $data['ot'];
                        } else {
                            $detail[$key]['dem_thuong_ko_ot_ngay'] = $data['ot'];
                        }

                        $detail[$key]['note_edit_ot_night'] = $data['note'];
                        $detail[$key]['edit'] = 1;
                        $detail[$key]['ot_change'] = $type;

                    } else if ($data['type'] == 'night' && !is_null($date_over_time['hours']['night']) && $date_over_time['type'] != 1) {
                        if ($data['ot'] > $date_over_time['hours']['night']) {
                            return \Response::json([
                                'status' => 'FAIL',
                                'message' => 'Số giờ OT lớn hơn số giờ trong lịch: ' . $date_over_time['hours']['night']
                            ]);
                        }

                        $detail[$key]['night'] = $data['ot'];
                        $detail[$key]['note_edit_ot_night'] = $data['note'];
                        $detail[$key]['edit'] = 1;
                        $detail[$key]['ot_change'] = $type;
                    }
                    $detail[$key]['total_ot'] = $detail[$key]['ngay'] + $detail[$key]['dem'] + $detail[$key]['dem_thuong_ko_ot_ngay'] + $detail[$key]['dem_thuong_co_ot_ngay'];
                }

                $timeKeepingDetail->update([
                    'detail' => json_encode($detail),
                ]);

                $response = [
                    'data_new' => json_encode($detail[$key]),
                    'data_old' => json_encode($data_old),
                    'action_by' => $request->user()->id,
                    'action_at' => date('Y-m-d H:i:s'),
                    'field' => 'ot',
                    'note' => $data['note'],
                    'log_type' => get_class($timeKeepingDetail),
                    'log_id' => $timeKeepingDetail->id,
                ];

                DB::table('logs')->insert($response);

                return \Response::json([
                    'status' => 'SUCCESS',
                    'message' => 'Cập nhật thành công'
                ]);
                
            } catch (Exception $e) {
                return \Response::json([
                    'status' => 'FAIL',
                    'message' => $e->getMessage()
                ]);
            }

        } else {
            return $return;
        }
    }


    public function recalculate(Request $request, $id)
    {
        $return = \Response::json([
            'status' => 'FAIL',
            'message' => trans('system.error')
        ]);

        if ($request->ajax()) {
            $timekeeping = TimeKeeping::find($id);
            if (is_null($timekeeping)) return $return;
            
            DB::beginTransaction();
            try {
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

                if (count($timekeeping->timeKeepingDetail) == 0) {
                    return \Response::json([
                        'status' => 'FAIL',
                        'message' => 'Nhân viên kiêm nhiệm, không được đổ lại'
                    ]);
                }

                if (empty($workSchedule) && $department->type == \App\Define\Department::FUNCTIONAL_OFFICE) {
                    return \Response::json([
                        'status' => 'FAIL',
                        'message' => trans('timekeeping.error_workschedule')
                    ]);
                }

                User::syncAttendanceMachine();

                $contracts = Contract::where('department_id', $data['department_id'])->whereIn('type_status', [1, 2, 7])->orderBy('id', 'DESC')->get();
          
                $staffs = User::whereIn('id', $contracts->pluck('user_id')->toArray())
                        ->where('active', 1)
                        ->get();
                $checkInOut = DB::connection('mysql2')->table('CHECKINO')->whereIn('primary_code', $staffs->pluck('code_timekeeping')->toArray())
                    ->where('timeint', '>=', strtotime($startDate))
                    ->where('timeint', '<=', strtotime($endDate))
                    ->orderBy('timeint', 'DESC')
                    ->get();

                $timekeeping->updated_at = date('Y-m-d H:i:s');
                $timekeeping->user = Auth::user()->id;
                $timekeeping->version = 1;
                $timekeeping->save();

                $log_ids = Log::whereIn('log_id', $timekeeping->timeKeepingDetail()->pluck('id')->toArray())->pluck('id');
                if (count($log_ids) > 0) Log::destroy($log_ids);
                
                if ($timekeeping->timeKeepingDetail()->delete()) {
                    $this->checkInOut($timekeeping, $data, $checkInOut, $workSchedule, $department, $staffs, $contracts); 
                }

                DB::commit();

                return \Response::json([
                    'status' => 'SUCCESS',
                    'message' => trans('timekeeping.success'),
                    'link' => route('admin.timekeepings.detail', $timekeeping->id)
                ]);
                
            } catch (Exception $e) {
                DB::rollBack();
                return \Response::json([
                    'status' => 'SUCCESS',
                    'message' => $e->getMessage()
                ]);
            }

        } else {
            return $return;
        }
    }

    public function tinhOtCa($return, $data, $shift_time, $department, $dateByMonth, $dayoff, $staff_id)
    {
        $ngay = $dem = $dem_thuong_ko_ot_ngay = $dem_thuong_co_ot_ngay = 0;
        $type_ot = '';
        $ot = [
            'ngay'                       => $ngay,
            'dem'                        => $dem,
            'dem_thuong_ko_ot_ngay'      => $dem_thuong_ko_ot_ngay,
            'dem_thuong_co_ot_ngay'      => $dem_thuong_co_ot_ngay,
            'type_ot'                    => $type_ot,
            'total_ot'                   => 0
        ];

        if ($return['status'] == self::QUEN_QUET || $return['status'] == self::DIMUON_VESOM) {
            return $return + $ot;
        }

        $overTimes = OverTimes::getOT($data['month'], $data['year'], $staff_id);
        if (empty($overTimes)) return $return + $ot;
        $overTimes = collect($overTimes);
        $overTime = $overTimes->where('date', date('Y-m-d', $dateByMonth))->first();

        if (is_null($overTime)) return $return + $ot;

        $shift_night = ShiftTime::where('department_id', $department->id)->whereHas('category', function($q) {
            $q->where('type', 3);
        })->first();

        $checkin = new Carbon($return['time_check_in']);
        $checkout = new Carbon($return['time_check_out']);
        
        $h_in = date('Y-m-d', strtotime($return['time_check_in']));
        $gio_vao = $h_in . ' ' . $shift_time->time_in;
        $gio_vao_toi = $h_in . ' ' . $shift_night->time_in;

        $h_out = date('Y-m-d', strtotime($return['time_check_out']));
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
       
        if ($overTime['type'] == OverTime::TYPE_NORMAL) {
            $ot['type_ot'] = self::NGAY_THUONG;

            if ($shift_time->category->type == 3) {  // làm đêm thường chỉ có ot ngày
                if (!is_null($overTime['hours']['day'])) {
                    // $date_gio_tinh_ot_ra = $gio_tinh_ot_ra->addDays(1);
                    // $checkout = $checkout->addDays(1);
                    $duoc_ot_vao_h = $duoc_ot_vao_i = $duoc_ot_ra_h = $duoc_ot_ra_i = 0;
                    if (strtotime($return['time_check_in']) < strtotime($gio_vao_toi)) {
                        $duoc_ot_vao_h = $checkin->diff($gio_tinh_ot_vao_toi)->format('%H');
                        $duoc_ot_vao_i = $checkin->diff($gio_tinh_ot_vao_toi)->format('%I');
                    }

                    if (strtotime($return['time_check_out']) > $gio_ra_toi && date('Y-m-d', strtotime($gio_ra_toi)) > date('Y-m-d', strtotime($gio_vao_toi))) {
                        $duoc_ot_ra_h = $gio_tinh_ot_ra_toi->diff($checkout)->format('%H');
                        $duoc_ot_ra_i = $gio_tinh_ot_ra_toi->diff($checkout)->format('%I');
                    }

                    $check_vao_i = $this->lamTronOt($duoc_ot_vao_i);
                    $check_ra_i = $this->lamTronOt($duoc_ot_ra_i);
                    
                    $check_ot = intval($duoc_ot_ra_h) + intval($duoc_ot_vao_h) + $check_vao_i + $check_ra_i; 
                    if ($check_ot > $overTime['hours']['day']) $check_ot = $overTime['hours']['day'];
                    $ot['ngay'] = max(($check_ot - $return['ve_som'] - $return['di_muon']), 0);
                }
            } else { // làm ngày thưởng ot, ngày và đêm
                if (!is_null($overTime['hours']['day'])) { //làm ngày thường ot ngày
                    if (strtotime($return['time_check_in']) < strtotime($gio_vao)) {
                        $duoc_ot_vao_h = $checkin->diff($gio_tinh_ot_vao)->format('%H');
                        $duoc_ot_vao_i = $checkin->diff($gio_tinh_ot_vao)->format('%I');
                    } else {
                        $duoc_ot_vao_h = 0;
                        $duoc_ot_vao_i = 0;
                    }

                    if (strtotime($return['time_check_out']) > strtotime($gio_ra)) {
                        if (strtotime($return['time_check_out']) > strtotime($gio_tinh_ot_vao_toi) && !is_null($shift_night)) {
                            $duoc_ot_ra_h = $gio_tinh_ot_ra->diff($gio_tinh_ot_vao_toi)->format('%H');
                            $duoc_ot_ra_i = $gio_tinh_ot_ra->diff($gio_tinh_ot_vao_toi)->format('%I');
                        } else {
                            $duoc_ot_ra_h = $gio_tinh_ot_ra->diff($checkout)->format('%H');
                            $duoc_ot_ra_i = $gio_tinh_ot_ra->diff($checkout)->format('%I');
                        }
                    }

                    $check_vao_i = $this->lamTronOt($duoc_ot_vao_i);
                    $check_ra_i = $this->lamTronOt($duoc_ot_ra_i);
                    $check_ot = intval($duoc_ot_ra_h) + intval($duoc_ot_vao_h) + $check_vao_i + $check_ra_i; 
                    if ($check_ot > $overTime['hours']['day']) $check_ot = $overTime['hours']['day'];
                    $ot['ngay'] = max(($check_ot - $return['ve_som'] - $return['di_muon']), 0);
                }

                if (!is_null($overTime['hours']['night'])) {   //làm ngày thường ot đêm
                    if (!is_null($shift_night)) {
                        
                        if (strtotime($return['time_check_out']) > strtotime($gio_tinh_ot_vao_toi) //tính ot với thời gian checkout với thời gian vào làm ca đêm
                            && strtotime($gio_tinh_ot_vao_toi) > strtotime($gio_ra)
                        ) {
                            $check_ot1 = $gio_tinh_ot_vao_toi->diff($checkout)->format('%H:%I:%S');
                            $covert_check_ot_h1 = intval(date_format(date_create($check_ot1), "H"));
                            $covert_check_ot_i1 = intval(date_format(date_create($check_ot1), "i"));
                            $check_i = $this->lamTronOt($covert_check_ot_i1);
                            $ot = $covert_check_ot_h1 + $check_i;

                            if ($ot > $overTime['hours']['night']) $ot = $overTime['hours']['night'];
                            $ot = max(($ot - $return['ve_som'] - $return['di_muon']), 0);

                            if ($ot['ngay'] > 0) { // ot đêm có ot ngày
                                $ot['dem_thuong_co_ot_ngay'] = $ot;
                            } else { // ot đêm không ot ngày
                                $ot['dem_thuong_ko_ot_ngay'] = $ot;
                            }
                        } else if (strtotime($return['time_check_in']) < strtotime($gio_tinh_ot_ra_toi)) { // tính ot với thời gian checkin với giờ ra ca đêm
                            $check_ot1 = $gio_tinh_ot_ra_toi->diff($checkin)->format('%H:%I:%S');
                            $covert_check_ot_h1 = intval(date_format(date_create($check_ot1), "H"));
                            $covert_check_ot_i1 = intval(date_format(date_create($check_ot1), "i"));
                            $check_i = $this->lamTronOt($covert_check_ot_i1);
                            $ot = $covert_check_ot_h1 + $check_i;

                            if ($ot > $overTime['hours']['night']) $ot = $overTime['hours']['night'];
                            $ot = max(($ot - $return['ve_som'] - $return['di_muon']), 0);

                            if ($ot['ngay'] > 0) { // ot đêm có ot ngày
                                $ot['dem_thuong_co_ot_ngay'] = $ot;
                            } else { // ot đêm không ot ngày
                                $ot['dem_thuong_ko_ot_ngay'] = $ot;
                            }
                        }
                    }
                }
            }
        } else if ($overTime['type'] == OverTime::TYPE_DAYOFF || $overTime['type'] == OverTime::TYPE_HOLIDAY) {
            if ($overTime['type'] == OverTime::TYPE_DAYOFF) $ot['type_ot'] = self::NGAY_NGHI;
            else $ot['type_ot'] = self::NGAY_LE;

            if (!is_null($overTime['hours']['day'])) { // ot ngày nghỉ 
                if ($return['time'] == 'LAM_SANG') {
                    if ($return['shift'] == 3) {
                        if (strtotime($return['time_check_in']) < strtotime($gio_vao_toi)) {
                            $check_ot = $gio_tinh_ot_vao_toi->diff($checkin)->format('%H:%I:%S');
                        } else {
                            $check_ot = 0;
                        }
                    } else {
                        if (strtotime($return['time_check_out']) > strtotime($gio_vao_chieu)) {
                            $start_mid_shift = new Carbon($gio_vao_chieu);
                            if (strtotime($return['time_check_out']) > strtotime($gio_tinh_ot_vao_toi) && !is_null($shift_night)) {
                                $check_ot = $start_mid_shift->diff($gio_tinh_ot_vao_toi)->format('%H:%I:%S');
                            } else {
                                $check_ot = $start_mid_shift->diff($checkout)->format('%H:%I:%S');
                            }
                        } else {
                            $check_ot = 0;
                        }
                    }
                } else if ($return['time'] == 'LAM_CHIEU') { // ot sáng
                    if ($return['shift'] == 3) {
                        if (strtotime($return['time_check_out']) > strtotime($gio_ra_toi)) {
                            $check_ot = $gio_tinh_ot_ra->diff($checkout)->format('%H:%I:%S');
                        }
                    } else {
                        if ($return['time_check_in'] < $gio_ra_ca_sang) {
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
                $check_i = $this->lamTronOt($covert_check_ot_i);
                $check_ot = $covert_check_ot_h + $check_i;
                if ($check_ot > $overTime['hours']['day']) $check_ot = $overTime['hours']['day'];
                if ($return['time'] == 'NGHI_LAM' && $check_ot >= 5) $check_ot -= 1; // ot ngày nghỉ trừ 1 tiếng ăn ca, nếu giờ >= 5
                $ot['ngay'] = max(($check_ot - $return['ve_som'] - $return['di_muon']), 0);
            }

            if (!is_null($overTime['hours']['night']) && !is_null($shift_night)) {
                if (strtotime($return['time_check_out']) > strtotime($gio_tinh_ot_vao_toi)) { //làm sáng tính ot chiều
                    if ($return['shift'] == 3) {
                        if ($return['time'] == self::LAM_SANG) {
                            if (strtotime($return['time_check_out']) > strtotime($gio_vao_chieu)) {
                                $start_mid_shift = new Carbon($gio_vao_chieu);
                                $check_ot1 = $checkout->diff($start_mid_shift)->format('%H:%I:%S');
                            } else {
                                $check_ot1 = 0;
                            }
                           
                        } else if ($return['time'] == self::LAM_CHIEU) { // làm chiều tính ot sáng
                            if (strtotime($return['time_check_in']) < strtotime($gio_vao_toi)) {
                                $check_ot1 = $checkin->diff($gio_tinh_ot_vao_toi)->format('%H:%I:%S');
                            } else {
                                $check_ot1 = 0;
                            }
                        } else { //tính ot cả ngày
                            $check_ot1 = $checkin->diff($checkout)->format('%H:%I:%S');
                        }
                    } else {
                        if (strtotime($return['time_check_out']) > strtotime($gio_vao_toi)) {
                            $check_ot1 = $checkout->diff($gio_tinh_ot_vao_toi)->format('%H:%I:%S');
                        } else {
                           $check_ot1 = 0; 
                        }
                    }

                    $covert_check_ot_h = intval(date_format(date_create($check_ot1), "H"));
                    $covert_check_ot_i = intval(date_format(date_create($check_ot1), "i"));
                    $check_i = $this->lamTronOt($covert_check_ot_i);
                    $check_ot = $covert_check_ot_h + $check_i;
                    if ($check_ot > $overTime['hours']['night']) $check_ot = $overTime['hours']['night'];
                    if ($return['time'] == 'NGHI_LAM' && $check_ot >= 5) $check_ot -= 1; // ot ngày nghỉ trừ 1 tiếng ăn ca, nếu giờ >= 5
                    $ot['dem'] = max(($check_ot - $return['ve_som'] - $return['di_muon']), 0);
                }
            }
        }

        $ot['total_ot'] = $ot['ngay'] + $ot['dem'] + $ot['dem_thuong_ko_ot_ngay'] + $ot['dem_thuong_co_ot_ngay'];

        return $return + $ot;
    }

    public function exportExcel(Request $request, $id)
    {
        $timekeeping = TimeKeeping::find($id);
        if (is_null($timekeeping)) {
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
        $data['timekeeping'] = $timekeeping;

        return \Excel::download(new \App\Exports\TimekeepingExport($data), 'Bang-cong' . '_' . date('H.m_d-m-Y') . '.xlsx');
    }

    public function exportExcelOt(Request $request, $id)
    {
        $timekeeping = TimeKeeping::find($id);
        if (is_null($timekeeping)) {
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
