<?php

namespace App\Models;

use App\Define\Department as DefineDepartment;
use App\Models\Department;
use App\Define\OverTime;
use App\Http\Controllers\Backend\PayrollController;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Mpdf\Tag\Time;
use App\PermissionUserObject;
use App\StaffDayOff;
use App\Target;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Payroll extends Model
{
    protected $table ='payrolls';
    protected $fillable = [
        'month',
        'year',
        'company_id',
        'department_id',
        'created_by',
        'status',
        'user_approved',
        'date_approved',
        'user',
        'version'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function user_by()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function userPayroll()
    {
        return $this->hasMany(PayrollUser::class);
    }

    public static function getTotalSalaryByMonth()
    {
        $total_money = 0;
        $month = array_fill(0, 12, 0);
        $return = $data = $company = $groups = [];

        $companies = Company::whereNotIn('shortened_name', \App\Define\Company::CODE_COMPANY_HCM_DN)
            ->where('status', 1)
            ->get();
        foreach ($companies as $key => $item) {
            $data[$item->id] = $month;
            $company[$item->id] = $item->shortened_name;
            $payrolls = Payroll::where('company_id', $item->id)->where('year', date('Y'))->get();
            $payrolls->load('userPayroll');
            foreach ($payrolls as $index => $payroll) {
                $groups[$payroll->month][] = $payroll->userPayroll->toArray();
            }
            foreach ($groups as $index => $value) {
                foreach ($value as $i => $v) {
                    $total_money = array_sum(array_column($v, 'total_real_salary')) * count($value);
                    $convert_money = intval($total_money) / 1000000;
                }
                $p = Payroll::find($v[0]['payroll_id']);
                if ($p->company_id === $item->id) {
                    $data[$item->id][$index] = intval(round($convert_money));
                }
            }
            $return = [array_values($data), array_values($company)];
        }

        return $return;
    }

    public static function totalDayRequest($userId, $month, $year)
    {
        $user = User::find($userId);
        // $total_work = StaffDayOff::countTotalInMonthForTimeKeeping($userId, $month, $year);

        return OverTimes::totalWorkingInMonth($month, $year, $user->department_id);
    }

    public static function totalWork($userId, $month, $year)
    {
        $user = User::find($userId);
        $timekeeping = TimeKeeping::where('department_id', $user->department_id)->where('month', $month)
                                    ->where('year', $year)
                                    ->first();
        $detail = TimeKeepingDetail::where('staff_id', $userId)->where('timekeeping_id', $timekeeping->id)->get('total')->first();

        $total_work = StaffDayOff::countTotalInMonthForTimeKeeping($userId, $month, $year);

        return $detail->total + $total_work['L'] + $total_work['D'] + $total_work['W'];
    }

    public static function totalWorkDepartment($userId, $month, $year, $department_id)
    {
        $user = User::find($userId);
        $timekeeping = TimeKeeping::where('department_id', $department_id)->where('month', $month)
                                    ->where('year', $year)
                                    ->first();
        $detail = TimeKeepingDetail::where('staff_id', $userId)->where('timekeeping_id', $timekeeping->id)->first();
        $value = json_decode($detail->detail, true);
        $total_tv = array_sum(array_column($value, 'total_tv'));
        $total_hd = array_sum(array_column($value, 'total_hd'));
        $total_work = StaffDayOff::countTotalInMonthForTimeKeeping($userId, $month, $year, $department_id);

        if ($total_hd) $total_hd = $total_hd + $total_work['L'] + $total_work['D'] + $total_work['W'];

        return $total_hd + $total_tv;
    }

    public static function userPayrollDetail($id)
    {
        $user_payroll = PayrollUser::find($id);

        if ($user_payroll->bh) {
            $user_payroll['bh'] = json_decode($user_payroll->bh);
        }
        $payroll = Payroll::find($user_payroll->payroll_id);
   
        $timekeepingDetail = TimeKeepingDetail::where('staff_id', $user_payroll->user_id)->whereHas('timekeeping', function ($q) use ($payroll) {
            // $q->where('month', $payroll->month)->where('year', $payroll->year);
            $q->where('department_id', $payroll->department_id)->where('month', $payroll->month)->where('year', $payroll->year);
        })->first();

        $shifts = json_decode($timekeepingDetail->detail, true);
        $shift_tv = $shift_hd = [];

        foreach ($shifts as $s => $sh) {
            if ($payroll->department->type == 2) {
                if ($sh['total_tv'] > 0) $shift_tv[$sh['shift']][] = $sh['total_tv'];
                if ($sh['total_hd'] > 0) $shift_hd[$sh['shift']][] = $sh['total_hd'];
            } else {
                if ($sh['total_tv'] > 0) $shift_tv = array_count_values(array_values(array_column($shifts, 'shift')));
                if ($sh['total_hd'] > 0) $shift_hd = array_count_values(array_values(array_column($shifts, 'shift')));
            }
            
        }
        
        if ($timekeepingDetail) {
            $total_work = StaffDayOff::countTotalInMonthForTimeKeeping($user_payroll->user_id, $timekeepingDetail->timekeeping->month, $$timekeepingDetail->timekeeping->year, $payroll->department_id);
            $nghi_huong_luong = $total_work['H'] + $total_work['T'];
            if (!empty($total_work)) {
                $user_payroll['actual_workdays'] = $timekeepingDetail->total + $total_work['L'] + $total_work['D'] + $total_work['W'];
            }
            $user_payroll['total_day_request'] = OverTimes::totalWorkingInMonth($payroll->month, $payroll->year, $payroll->department_id) + $total_work['H'];
            $nghiXinThem = 0;
            $food_allowance = 0;
           
            $contract = Contract::where('user_id', $user_payroll->user_id)->whereIn('type_status', [1, 7, 3])->orderBy('id', 'DESC')->first();
            $dataFoodAllowances  = $contract->foodAllowance->where('id', 1)->first();
            $food_allowance = $dataFoodAllowances->pivot->expense; // phụ cấp ăn

            $nghiHieuHi = $nghiLinhTinh = 0;

            if ($food_allowance == 25000) {
                foreach (json_decode($timekeepingDetail->detail, true) as $dateByMonth => $v) {
                    $total_ot = $v['ot_tv'] + $v['ot_hd'] + $v['night_hd'] + $v['night_tv'] + $v['hours_night_have_day_hd'] + $v['hours_night_not_day_hd'] + $v['hours_night_not_day_tv'] + $v['hours_night_have_day_tv'];


                    $checkHoliday = StaffDayOff::checkDateHasEvent($user_payroll->user_id, date('Y-m-d', $dateByMonth));
                    if (in_array($checkHoliday, ['D', 'W'])) {
                        $nghiHieuHi++;
                    }
                    $startDate = date('Y-m-d 00:00:00', strtotime($payroll->year . '-' . (($payroll->month - 1)) . '-' . 26));

                    $dieuChuyen = Contract::where('user_id', $user_payroll->user_id)->where('department_id', $payroll->department_id)
                                    ->where('type_status', 2)
                                    ->orderBy('id', 'DESC')
                                    ->where('set_notvalid_on', '>', $startDate)
                                    ->first();
                    if (!is_null($dieuChuyen)) {
                        $set_notvalid_on = strtotime($dieuChuyen->set_notvalid_on);
                        if ($dateByMonth < $set_notvalid_on) {
                            if (in_array($checkHoliday, ['L', 'T', 'L/2 L/2', 'T/2 T/2'])) {
                                if ($v['an_chinh'] == 0 || is_null($v['an_chinh']) || !isset($v['an_chinh'])) $nghiXinThem++;
                            }
                            if (($v['total'] == 1 && in_array($checkHoliday, ['L/2', 'D/2', 'W/2', 'H/2', 'T/2']))) {
                                if ($v['an_chinh'] == 0 || is_null($v['an_chinh']) || !isset($v['an_chinh'])) $nghiLinhTinh++;
                            }
                        }
    
                    } else {
                        if (in_array($checkHoliday, ['L', 'T', 'L/2 L/2', 'T/2 T/2', 'H'])) {
                            if ($v['an_chinh'] == 0 || is_null($v['an_chinh']) || !isset($v['an_chinh'])) $nghiXinThem++;
                        }
                        if (($v['total'] == 1 && in_array($checkHoliday, ['L/2', 'D/2', 'W/2', 'H/2', 'T/2']))
                            || (($v['total_hd'] == 0.5 || $v['total_tv'] == 0.5) && $total_ot > 0)
                        ) {
                            if ($v['an_chinh'] == 0 || is_null($v['an_chinh']) || !isset($v['an_chinh'])) $nghiLinhTinh++;
                        }
                    }
                    
                } 
                $user_payroll['an_chinh'] = array_sum(array_column(json_decode($timekeepingDetail->detail, true) , 'an_chinh')) + $nghiHieuHi + $nghiXinThem + $nghiLinhTinh;
                $user_payroll['an_phu'] = array_sum(array_column(json_decode($timekeepingDetail->detail, true) , 'an_phu'));
            } else {
                $user_payroll['an_chinh'] = 0;
                $user_payroll['an_phu'] = 0;
            }

            if (empty($shift_tv)) {
                $user_payroll['ca_ngay_tv'] = array_sum(array_column($shifts, 'total_tv'));

            } else {
                $ca_ngay_tv = 0;
                $ca_dem_tv = 0;
                foreach ($shift_tv as $k => $shift) {
                    $danh_muc_ca = CategoryShift::find($k);
                    if ($danh_muc_ca->type == 3) {
                        $ca_dem_tv += array_sum($shift); 
                    } else {
                        $ca_ngay_tv += array_sum($shift); 
                    }

                    
                }
                $user_payroll['ca_dem_tv'] = $ca_dem_tv;
                $user_payroll['ca_ngay_tv'] = $ca_ngay_tv;
            }
            if (empty($shift_hd)) {
                $ca_ngay_hd = array_sum(array_column($shifts, 'total_hd'));
                if ($ca_ngay_hd >= $nghi_huong_luong) {
                    $user_payroll['ca_ngay_hd'] = $ca_ngay_hd - $nghi_huong_luong;

                } else {
                    $user_payroll['ca_ngay_hd'] = $ca_ngay_hd;

                }
            } else {
                $ca_ngay_hd = 0;
                $ca_dem_hd = 0;

                foreach ($shift_hd as $k => $shift) {
                    $danh_muc_ca = CategoryShift::find($k);
                    if ($danh_muc_ca->type == 3) {
                        $ca_dem_hd += array_sum($shift); 
                    } else {
                        $ca_ngay_hd += array_sum($shift); 
                    }
                }

                $user_payroll['ca_dem_hd'] = $ca_dem_hd;
                $user_payroll['ca_ngay_hd'] = $ca_ngay_hd;

                if ($ca_dem_hd >= $nghi_huong_luong) {
                    $user_payroll['ca_dem_hd'] = $ca_dem_hd - $total_work['H'];
                }

                if ($ca_ngay_hd >= $nghi_huong_luong) {
                    $user_payroll['ca_ngay_hd'] = $ca_ngay_hd - $total_work['H'] - $total_work['T'];
                }

            }

            
            $user_payroll['day_off_70_salary'] = $timekeepingDetail->day_off_70_salary;
        }
        
        return [
            'user_payroll'         => $user_payroll,
            
        ];
    }
    
    public static function calculateAllowance($userId, $id, $totalDayRequest, $totalWorkDepartment, $month, $year, $salary_concurrent, $type = null, $department_id)
    {
        $month_kpi = $month - 1;
        $year_kpi = $year;

        if($month_kpi == 0) {
            $month_kpi = 12;
            $year_kpi = $year - 1;
        }

        $amount_money = 0;
        // ->where('type_status', 1)
        $contract = Contract::where('user_id', $userId)->where('department_id', $department_id)->orderBy('id', 'DESC')->first();
        $concurrent = ConcurrentContract::where('department_id', $department_id)->where('user_id', $userId)->where('status', 1)->first();
        if (!is_null($concurrent)) return $amount_money;
        if (intval($salary_concurrent) == 0 && !is_null($contract)) {
            $cate = AllowanceCategory::find($id);
            $allowance = DB::table('allowances')->where('contract_id', $contract->id)->where('category_id', $id)->first();
            $amount_money = 0;
            // $total_work = StaffDayOff::countDayOffs($userId, $month, $year, 'T');
            $total_work_c = \App\StaffDayOff::countDayOffs($userId, $month, $year, 'C');
    
            if ($cate->status == 1 && $cate->type == 0 && $cate->type_work == 'BY_WORKING_DAY') {
                // $ngi_khong_luong = Payroll::nghiKhongLuong($contract->user_id, $month, $year, 'O');
                // $nghi_l = StaffDayOff::countDayOffs($contract->user_id, $month, $year, 'L');
                
                $amount_money = $allowance->expense * $totalWorkDepartment / $totalDayRequest;

                if ($type == 'EXPORT') {
                    return number_format($amount_money);

                } else {
                    return number_format($amount_money, 0, ',', '.');

                }

            }
    
            if ($cate->status == 1 && $cate->type_work == 'BY_WORKING_DAY' && $cate->type == 1) {
                $target = Target::where('user_id', $userId)->where('month', $month_kpi)->where('year', $year_kpi)->orderBy('id', 'DESC')->first();
                $kpi = is_null($target) ? 0 : $target->kpi;

                $amount_money = $allowance->expense * ($totalWorkDepartment / $totalDayRequest) * ($kpi / 100);
                
                if ($type == 'EXPORT') {
                    return number_format($amount_money);

                } else {
                    return number_format($amount_money, 0, ',', '.');

                }
    
            }
    
            if ($cate->status == 1 && $cate->type_work == '' && $cate->type == 0) {
                $ngi_khong_luong = Payroll::nghiKhongLuong($contract->user_id, $month, $year, 'O');
                $nghi_l = StaffDayOff::countDayOffs($contract->user_id, $month, $year, 'L');

                if (($ngi_khong_luong >= 0.5 || $nghi_l > 1.5 || $total_work_c >= 0.5) && $id == 10) {
                    return 0;
                }

                if ($type == 'EXPORT') {
                    return number_format($allowance->expense);

                } else {
                    return number_format($allowance->expense, 0, ',', '.');
                }
            }
        }
        
        return $amount_money;
    }

    public static function getKpi($userId, $month, $year)
    {
        $month_kpi = $month - 1;
        $year_kpi = $year;

        if($month_kpi == 0) {
            $month_kpi = 12;
            $year_kpi = $year - 1;
        }

        $target = Target::where('user_id', $userId)->where('month', $month_kpi)->where('year', $year_kpi)->orderBy('id', 'DESC')->first();
        return is_null($target) ? 100 : $target->kpi;
    }

    public static function characterPayroll()
    {
        return [
            'A' => 'STT', 'B' => 'full_name', 'C' => 'code', 'D' => 'total_day_request', 'E' => 'total_work_department', 'F' => 'total_work_T', 'G' => 'an_chinh', 'H' => 'an_phu', 'I' => 'ca_dem_hd', 'J' => 'basic_salary_hd', 'K' => 'salary_bh', 'L' => 'working_salary_tax', 'M' => 'working_salary_non_tax', 'N' => 'food_allowance_tax', 'O' => 'food_allowance_nonTax', 'P' => 'allowance', 'Q' => 'allowance', 'R' => 'allowance', 'S' => 'allowance', 'T' => 'allowance', 
            'U' => 'allowance', 'V' => 'allowance', 'W' => 'allowance', 'X' => 'allowance', 'Y' => 'total_salary', 'Z' => 'bhxh_user', 'AA' => 'bhyt_user', 'AB' => 'union_user', 'AC' => 'bhtn_user', 'AD' => 'bhxh_company', 'AE' => 'bhyt_company', 'AF' => 'union_company', 'AG' => 'bhtn_company', 'AH' => '', 'AI' => 'total_deductions', 'AJ' => 'income_taxes', 'AK' => 'dependent_person', 'AL' => 'family_allowances', 'AM' => 'taxable_income', 'AN' => 'personal_income_tax',
            'AO' => 'kpi', 'AP' => 'total_real_salary', 'AQ' => 'AQ', 'AR' => 'AR', 'AS' => 'AS', 'AT' => 'AT', 'AU' => 'AU', 'AV' => 'AV', 'AW' => 'AW', 'AX' => 'AX', 'AY' => 'AY', 'AZ' => 'AZ', 'BA' => 'BA', 'BB' => 'BB', 'BC' => 'BC', 'BD' => 'BD'
        ];
    }

    public static function getCt($data)
    {
        // $user = User::find($data['userId'], ['department_id']);
        $contract = Contract::where('user_id', $data['userId'])->where('type_status', 1)->first();
        $dataFoodAllowances  = $contract->foodAllowance->where('id', 1)->first();
        $food_allowance = $dataFoodAllowances->pivot->expense; // phụ cấp ăn

        $food_allowance_nonTax = 0;
        $food_allowance_tax = 0;
        
        if ($data['salary_concurrent'] == 0) {
            if ($food_allowance == 25000) {
                $food_allowance_nonTax = '=Min(N*25000+O*15000), 730000)';
                $food_allowance_tax = '=(N*25000+O*15000) - W';
            } else {
                $food_allowance_nonTax = '=Min((F+H+J+I)/D*' . $food_allowance. '+N*25000+O*15000), 730000)';
                $food_allowance_tax = '=((F+H+J+I)/D*'.$food_allowance.'+N*25000+O*15000)-W';
            }
        }
        

        return [
            'default' => 'GET',
            'total_day_request' => 'GET',
            'total_work_department' => '=E+F+G+H+I+J',
            'total_work_T' => 'GET',
            'an_chinh' => 'GET',
            'an_phu' => 'GET',
            'ca_dem_hd' => 'GET',
            'basic_salary_hd' => 'GET',
            'salary_bh' => '=Q+Các khoản phụ cấp đóng bảo hiểm HĐ',
            'working_salary_tax' => '=(E+G)*(P/D)+(F+H+I+J+K*70%)*(Q/D)',
            'working_salary_non_tax' => '=(G*P)+(H*Q)*(30%/D)',
            'food_allowance_tax' => $food_allowance_tax,
            'food_allowance_nonTax' => $food_allowance_nonTax,
            'total_salary' => '=S+T+X+W+Y+Z+AA+AB+AC+AD+AE+AF+AG+U+V',
            'bhxh_user' => '=Min(R, 29800000)*8%',
            'bhyt_user' => '=Min(R, 29800000)*1.5%',
            'union_user' => '=Min(R, 29800000)*1%',
            'bhtn_user' => '=R*1%',
            'bhxh_company' => '=Min(R, 29800000)*17%',
            'bhyt_company' => '=Min(R, 29800000)*3%',
            'union_company' => '=Min(R, 29800000)*2%',
            'bhtn_company' => '=R*1%',
            'ttcn_2020' => 'GET',
            'total_deductions' => 'GET',
            'income_taxes' => '=AH+AU-AS-U-T-W-Phụ cấp miễn thuế HĐ',
            'ng_phu_thuoc' => 'GET',
            'gia_canh' => '=11000000+4400000*AW',
            'tntt' => '=AV-AI-AJ-AL-AX',
            'personal_income_tax' => 'GET',
            'kpi' => 'GET',
            'total_real_salary' => '=AH+AT+AU-AR-AS-AZ-AI-AJ-AL-AK'
        ];
    }

    public static function countTotalInMonthForTimeKeeping($userId, $month, $year, $department_id = null)
    {
        $count = StaffDayOff::countTotalInMonthForTimeKeeping($userId, $month, $year, $department_id);

        return $count['L'] + $count['D'] + $count['W'] + $count['H'];
    }

    public static function nghiKhongLuong($userId, $month = null, $year = null, $type = '')
    {
      
        $timekeeping = TimeKeepingDetail::where('staff_id', $userId)->whereHas('timekeeping', function ($q) use($month, $year) {
            $q->where('month', $month)->where('year', $year);
        })->first();
        $detail = json_decode($timekeeping->detail, true);
        $diMuon = $khong_di_lam = 0;
        $countO = StaffDayOff::countDayOffs($userId, $month, $year, $type, $timekeeping->timekeeping->department_id);
        $countS = StaffDayOff::countDayOffs($userId, $month, $year, 'S', $timekeeping->timekeeping->department_id);

        $dayoffs1 = CalendarDepartment::getDayOff($timekeeping->timekeeping->department_id);
        $dayoffs1 = collect($dayoffs1);
        // foreach ($detail as $k => $key) {
        //     if ($key['status'] == 0) {
        //         $nghi_phong_ban = $dayoffs1->where('start', date('Y-m-d', $k))->first();
        //         $nghi_k_xin = StaffDayOff::checkDateHasEvent($userId, date('Y-m-d', $k));
                
        //         if (is_null($nghi_phong_ban) && $nghi_k_xin == ' ') {
        //             $khong_di_lam += 1;
        //         }
        //     }
        //     if ($key['status'] != 2) continue;
            
            
        //     $diMuon += 0.5;
        // }

        $nghi_k_xin = 0;
        $nghi_k_xin_tt = 0;

        foreach ($detail as $dateByMonth => $i) {
            if (in_array($i['total_hd'], [0, null])) {
                $dayoff = $dayoffs1->where('start', date('Y-m-d', $dateByMonth))->first();
                $checkHoliday = StaffDayOff::checkDateHasEvent($timekeeping->staff_id, date('Y-m-d', $dateByMonth));
                if (is_null($dayoff) && $checkHoliday == ' ') $nghi_k_xin ++;
                if (!is_null($dayoff) && $checkHoliday == ' ') {
                    if ($dayoff['to_type'] == $dayoff['from_type']) $nghi_k_xin_tt += 0.5;
                }
                if (is_null($dayoff) && in_array($checkHoliday, ['L/2', 'T/2', 'W/2', 'D/2', 'C/2', 'O/2'])) $nghi_k_xin_tt += 0.5;
            }
        }

        return $countO + $nghi_k_xin + $nghi_k_xin_tt + $countS;
        // return $countO + $diMuon + $khong_di_lam;
    }

    public function userApproved()
    {
        return $this->belongsTo(User::class, 'user_approved', 'id');
    }

    public static function koTinhThue()
    {
        //return [143, 161];
        return [];
    }
}
