<?php

namespace App\Http\Controllers\Backend;

use App\Defines\Schedule;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TimeKeeping;
use App\Models\TimeKeepingDetail;
use App\Models\Contract;
use App\Models\Department;
use App\Models\AllowanceCategory;
use App\Models\ConcurrentContract;
use App\Models\Payroll;
use App\Models\PayrollUser;
use App\Models\AppendixAllowance;
use App\Models\Allowance;
use App\Models\CalendarDepartment;
use App\Models\CategoryShift;
use App\Models\OtherAmount;
use App\Models\OverTimes;
use App\Position;
use App\Qualification;
use App\Models\Log;
use App\StaffDayOff;
use App\Target;
use App\Models\Deduction;
use App\Models\Impale;
use App\Models\PayOff;
use App\Models\SalaryDrive;
use App\Models\SalaryDriveDetail;
use App\Models\UnionFund;
use App\PermissionUserObject;
use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class PayrollController extends Controller
{
    const SALARY_ACTIVE = 1; //lương hoạt động
    const EXPORT = 1;

    public function index()
    {
        $query = PermissionUserObject::getQueryPermission(Auth::id());
        $payrolls = Payroll::whereRaw($query)->orderBy('year', 'DESC')->orderBy('month', 'DESC')->get();
        if (!empty($payrolls)) $payrolls->load('company', 'department', 'user_by', 'userPayroll');

        foreach ($payrolls as $key => $item) {
            if ($item->userPayroll) {
                $payrolls[$key]['total'] = array_sum(array_column($item->userPayroll->toArray(), 'total_real_salary'));
            }   
        }
      
        return view('backend.payroll.index', compact('payrolls'));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $return = [
            'status'    => 'FAIL',
            'message'   => 'Có lỗi'
        ];

        $checkTimekeeping = TimeKeeping::where('company_id', $data['company_id'])->where('department_id', $data['department_id'])
                                        ->where('month', $data['month'])->where('year', $data['year'])->first();

        if (empty($checkTimekeeping)) {
            Session::flash('message', 'Lỗi không tìm thấy bảng chấm công');
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.payrolls.index');
        }

        if ($checkTimekeeping->version == 1) {
            dd('update...');
        }
        $payroll = Payroll::where('company_id', $data['company_id'])->where('department_id', $data['department_id'])
                            ->where('month', $data['month'])->where('year', $data['year'])->get();
        if (!empty($payroll->toArray())) {
            Session::flash('message', 'Bảng lương đã tồn tại');
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.payrolls.index');
        }

        DB::beginTransaction();
        try {
            $data['created_by'] = $request->user()->id;
            $payroll = Payroll::create($data);
            if (empty($payroll)) {
                $return['message'] = 'Lỗi';
                return $return;
            }
            $this->calculate($request, $data, $payroll, $checkTimekeeping);

            DB::commit();
            Session::flash('message', trans('system.success'));
            Session::flash('alert-class', 'success');
            return redirect()->route('admin.payrolls.detail', $payroll->id);

        } catch (Exception $e) {

            DB::rollBack();
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.payrolls.index');
        }
        
    }

    public function tntt($taxable_income)
    {
        $personal_income_tax = 0;
        if ($taxable_income <= 5000000) {
            $personal_income_tax = $taxable_income * 0.05;
        } else if ($taxable_income > 5000000 && $taxable_income <= 10000000) {
            $personal_income_tax = $taxable_income * 0.1 - 250000;
        } else if ($taxable_income > 10000000 && $taxable_income <= 18000000) {
            $personal_income_tax = $taxable_income * 0.15 - 750000;
        } else if ($taxable_income > 18000000 && $taxable_income <= 32000000) {
            $personal_income_tax = $taxable_income * 0.2 - 1650000;
        } else if ($taxable_income > 32000000 && $taxable_income <= 52000000) {
            $personal_income_tax = $taxable_income * 0.25 - 3250000;
        } else if ($taxable_income > 53000000 && $taxable_income <= 80000000) {
            $personal_income_tax = $taxable_income * 0.3 - 5850000;
        } else if ($taxable_income > 80000000) {
            $personal_income_tax = $taxable_income * 0.35 - 9850000;
        }

        return $personal_income_tax;
    }

    public function calculate(Request $request, $data, $payroll, $checkTimekeeping)
    {
        $coefficient_ot = 8;
        $config_time_ot = DB::table('config_time_ot')->where('department_id', $data['department_id'])->first();
                    
        if (!is_null($config_time_ot)) $coefficient_ot = $config_time_ot->coefficient;

        $month_kpi = $data['month'] - 1;
        $year_kpi = $data['year'];

        if ($month_kpi == 0) {
            $month_kpi = 12;
            $year_kpi = $data['year'] - 1;
        }

        if ($data['month'] == 1) 
        $allowances_ids = '';
        $response = $bhxh = $typeOtTv = $typeOtHd = $response1 = [];

        // $start = date('Y-m-d', strtotime($data['year'] . '-' . (($data['month'] - 1)) . '-' . 26));
        // $end = date('Y-m-d', strtotime($data['year'] . '-' . $data['month'] . '-' . 25));
        
        $start = date('Y-m-d', strtotime($data['year'] . '-' . (($data['month'] - 1)) . '-' . 26));
        $end = date('Y-m-d', strtotime($data['year'] . '-' . $data['month'] . '-' . 26));


        $department = Department::find($data['department_id']);
        if (!is_null($data['user_id'])) {
            $userIds = [$data['user_id']];
        } else {
            $userIds = TimeKeepingDetail::where('timekeeping_id', $checkTimekeeping->id)->pluck('staff_id');
        }

        $concurrents = ConcurrentContract::where('department_id', $data['department_id'])->where('status', 1)->get();
        // $concurrents = ConcurrentContract::where('id', '113')->where('status', 1)->get();
        
        $contracts = Contract::whereIn('user_id', $userIds)->whereIn('type_status', [1, 7])->get(['id', 'user_id', 'basic_salary', 'is_main', 'type_status', 'set_notvalid_on']);
        // $contracts = [];
        $config_insurrance = DB::table('config_insurrance')->where('company_id', $data['company_id'])->first();
        $config_food_allowances = DB::table('config_food_allowances')->where('company_id', $data['company_id'])->first();
        foreach ($contracts as $index => $contract) {
            $total_salary = $total_real_salary = $food_allowance_nonTax = $food_allowance_tax = $total_allowances = $salary_bh = $basic_salary_tv = $basic_salary_hd = $basic_salary = 0;
            $working_salary_non_tax = $working_salary_tax = $salary_ot_non_tax = $salary_ot_tax = $salary_concurrent = 0;
            $personal_income_tax = $family_allowances = $income_taxes = $taxable_income = $insurance_premiums = $level_config_tv = $level_config_hd = $total_deduction = 0;
            $expense = $expense1 = $allowanceTargetByWorking = $allowanceByWorking = $allowanceTarget = $phuCapOtChiuThue = $phucapOtMienThue = 0;
            $total_payoff = $total_payoff_tax = $total_deduction_tax = $total_deduction = 0;
            $total_impale = $an_phu = $an_chinh = 0;
            $allowanceByWorking  = $allowanceTargetByWorking = $allowanceTarget = $dataAllowance = 0;

            $target = Target::where('user_id', $contract->user_id)->where('month', $month_kpi)->where('year', $year_kpi)->orderBy('id', 'DESC')->first();
            if (is_null($target)) {
                $target = 100;
            } else {
                $target = $target->kpi;
            }
            // $basic_salary = $contract->basic_salary; //lương cơ bản
            //lương cơ bản hợp đồng, thử việc
            $data_contracts = Contract::getContractsInAMonth($contract->user_id, $data['month'], $data['year']);
            if (count($data_contracts) >= 2) {
                foreach ($data_contracts as $d => $data_contract) {
                    $luong = Contract::find($data_contract['id']);
                    if ($data_contract['is_main'] == 1) $basic_salary_tv = $luong->basic_salary;
                    if ($data_contract['is_main'] == 2) $basic_salary_hd = $luong->basic_salary;
                }
            } else {
                if ($contract->is_main == 1) $basic_salary_tv = $contract->basic_salary; 
                if ($contract->is_main == 2) $basic_salary_hd = $contract->basic_salary;
            }
            $insurance_premiums = $contract->insurancePremiums->sum('pivot.expense'); // Các khoản Phụ cấp đóng bảo hiểm
            $salary_bh = $basic_salary_hd + $insurance_premiums; //Lương đóng bảo hiểm
            $non_tax_allowances = $contract->nonTaxAllowances->sum('pivot.expense'); //phụ cấp miễn thuế
            $tax_allowances = $contract->taxableAllowances->sum('pivot.expense'); //phụ cấp chịu thuế
            $dataFoodAllowances  = $contract->foodAllowance->where('id', 1)->first();
            $food_allowance = $dataFoodAllowances->pivot->expense; // phụ cấp ăn

            // $total_allowances = array_sum(array_column($contract->allowances->toArray(), 'expense')); // Tổng các khoản phụ cấp chưa nhân kpi
            $dataTarget = $contract->checkTarget->sum('pivot.expense'); // Phu cấp tính kpi ko theo ngày công
            $dataAllowanceTargetByWorking = $contract->allowanceTargetByWorking->sum('pivot.expense'); //phụ cấp tính kpi theo ngày công
            // $total_allowances = ($total_allowances - $food_allowance - $dataTarget) + ($dataTarget * ($target / 100)); // Tổng các khoản phụ cấp đã nhân kpi
            $dataAllowanceByWorking = $contract->allowanceByWorking->sum('pivot.expense'); // phụ không tính kpi theo ngày công
            $dataAllowance = $contract->dataAllowance->sum('pivot.expense'); // Các loại phụ cấp lấy ko kpi, ko theo ngày công, lấy mặc định

            $phuCapOtChiuThue = $contract->phuCapOtChiuThue->sum('pivot.expense'); //Phụ cấp Ot chịu thuế
            $phucapOtMienThue = $contract->phucapOtMienThue->sum('pivot.expense'); //Phụ cấp Ot miễn thuế

            $data_allowances = Allowance::where('contract_id', $contract->id)->where('active', 1)->pluck('id');
            !empty($data_allowances) ? $allowances_ids = implode(' ', $data_allowances->toArray()) : '';

            $ngi_khong_luong = Payroll::nghiKhongLuong($contract->user_id, $data['month'], $data['year'], 'O');
            $phu_cap_chuyen_can = $contract->phuCapChuyenCan[0]->pivot->expense;
            $nghi_l = StaffDayOff::countDayOffs($contract->user_id, $data['month'], $data['year'], 'L', $data['department_id']);
            $total_work_c = StaffDayOff::countDayOffs($contract->user_id, $data['month'], $data['year'], 'C', $data['department_id']);


            if (($ngi_khong_luong >= 0.5 || $nghi_l > 1.5 || $total_work_c >= 0.5)) {
                $dataAllowance = $dataAllowance - $phu_cap_chuyen_can;
            }


            if (count($contract->appendixAllowances) > 0) {
                $salary_active = AppendixAllowance::where('contract_id', $contract->id)->where('salary_active', self::SALARY_ACTIVE)
                                                    ->get(['salary', 'contract_id'])->first();
                if (!empty($salary_active) && $contract->id == $salary_active->contract_id) {
                    // $basic_salary = $salary_active->salary;
                    if (count($data_contracts) >= 2) {
                        foreach ($data_contracts as $d => $data_contract) {
                            $luong = Contract::find($data_contract['id']);
                            if ($data_contract['is_main'] == 1) $basic_salary_tv = $luong->salary;
                            if ($data_contract['is_main'] == 2) $basic_salary_hd = $luong->salary;
                        }
                    } else {
                        if ($contract->is_main == 1) $basic_salary_tv = $salary_active->salary; 
                        if ($contract->is_main == 2) $basic_salary_hd = $salary_active->salary; 
                    }
                }

                $insurance_premiums1 = $contract->appendixAllowances1->sum('pivot.expense');
                $salary_bh = $basic_salary_hd + $insurance_premiums1;
                if (!is_null($insurance_premiums1)) $salary_bh = $basic_salary_hd + $insurance_premiums1 + $insurance_premiums;

                $non_tax_allowances1 = $contract->nonTaxAllowances1->sum('pivot.expense');
                if (!is_null($non_tax_allowances1)) $non_tax_allowances = $non_tax_allowances1 + $non_tax_allowances;

                $tax_allowances1 = $contract->taxableAllowances1->sum('pivot.expense');
                if (!is_null($tax_allowances1)) $tax_allowances = $tax_allowances1 + $tax_allowances;

                $dataFoodAllowances1  = $contract->foodAllowance1->where('id', 1)->first();
                $food_allowance1 = $dataFoodAllowances1->pivot->expense;
                if (!is_null($food_allowance1)) $food_allowance = $food_allowance + $food_allowance1;

                $data_allowances1 = array_sum(array_column($contract->appendixAllowances->toArray(), 'expense'));
                $dataTarget1 = $contract->checkTarget1->sum('pivot.expense');

                $total_allowances1 = ($data_allowances1 - $food_allowance1 - $dataTarget1) + ($dataTarget1 * ($target / 100));
                if (!is_null($total_allowances1)) $total_allowances = $total_allowances1 + $total_allowances;
                $data_allowances1 = AppendixAllowance::where('contract_id', $contract->id)->where('allowance_active', 1)->pluck('id');
                !empty($data_allowances1) ? $allowances_ids1 = implode(' ', $data_allowances1->toArray()) : '';
            }

            $request->export = 1;
            // $keepingDetailId = TimeKeepingDetail::where('timekeeping_id', $checkTimekeeping->id)->where('staff_id', $contract->user_id)
            //                 ->pluck('id')->first();
            $timekeepingDetail = new TimekeepingController();
            $detail = $timekeepingDetail->detail($request, $checkTimekeeping->id);
            $value = $detail['items']->where('staff_id', $contract->user_id)->first();
            // foreach ($detail['items'] as $key => $value) {
                // if ($value->staff_id == 155) {
                    //lương làm việc thực tế
                    // $nghiXinThem = 0;
                    if ($department->type != 1) {
                        //miễn thuế
                        $ca_dem_tv = $ca_dem_hd = 0;
                        foreach ($value->detail as $d => $detail1) {
                            $danh_muc_ca = CategoryShift::find(intval($detail1['shift']));
                            if ($danh_muc_ca->type == 3) {
                                if ($detail1['total_tv'] > 0) $ca_dem_tv += $detail['total_tv'];
                                if ($detail1['total_hd'] > 0) $ca_dem_hd += $detail1['total_hd'];
                            }
                            
                            // $checkHoliday = StaffDayOff::checkDateHasEvent($value->staff_id, date('Y-m-d', $d));
                            // if (in_array($checkHoliday, ['L', 'T', 'L/2 L/2', 'T/2 T/2', 'H'])) {
                            //     if ($detail1['an_chinh'] == 0 || is_null($detail1['an_chinh'])) $nghiXinThem++;
                            // }
                        }
                        
                        $working_salary_non_tax = (($ca_dem_tv * $basic_salary_tv) + (($ca_dem_hd * $basic_salary_hd))) * ((30 / 100) / $detail['total_day_request']);
                    }
                   
                    //chịu thuế
                    $total_work = StaffDayOff::countTotalInMonthForTimeKeeping($value->staff_id, $value->timekeeping->month, $value->timekeeping->year, $data['department_id']);
                    $total_tv = array_sum(array_column($value->detail, 'total_tv'));
                    $total_hd = array_sum(array_column($value->detail, 'total_hd'));

                    if ($total_hd) $total_hd = $total_hd + $total_work['L'] + $total_work['D'] + $total_work['W'];

                    $full_cong = $total_hd + $total_tv;

                    if ($full_cong > 0) $allowanceTargetByWorking = $dataAllowanceTargetByWorking * (($full_cong / $detail['total_day_request']) * ($target / 100)); // phụ cấp tính theo ngày công đã nhân kpi
                    if ($full_cong > 0) $allowanceByWorking = $dataAllowanceByWorking * (($full_cong / $detail['total_day_request'])); // phụ cấp tính theo ngày công không nhân kpi
                    if ($full_cong > 0) $allowanceTarget = $dataTarget * ($target / 100); // phụ cấp nhân kpi ko tính theo ngày công

                    // $full_pay_leave =  $value->day_off_70_salary * 0.7;
                    $full_pay_leave =  $total_work['C'] * 0.7;
                    $working_salary_tax = ($total_tv * ($basic_salary_tv / $detail['total_day_request'])) + (($total_hd + $full_pay_leave) * ($basic_salary_hd) / $detail['total_day_request']);

                   
                    //lương kiêm nhiệm
                    // $concurrent = ConcurrentContract::where('contract_id', $contract->id)->where('status', 1)->pluck('salary');
                    // $salary_concurrent = array_sum($concurrent->toArray()) / $detail['total_day_request'] * $value->total * ($target / 100);
                    // $an_phu = array_sum(array_column($value->detail, 'an_phu'));
                    // $an_chinh = array_sum(array_column($value->detail, 'an_chinh')) + intval($total_work['D']) + intval($total_work['W']) + $nghiXinThem;

                    $otDetail = $timekeepingDetail->otDetail($request, $checkTimekeeping->id);
                    $soBuaAn = $otDetail['items']->where('staff_id', $contract->user_id)->first();
                    $an_phu = $soBuaAn['an_phu'];
                    $an_chinh = $soBuaAn['an_chinh'];
                    if ($food_allowance == 25000) {
                        //phụ cấp ăn miễn thuế ca
                        $a_food_allowance_nonTax = $an_chinh * 25000 + $an_phu * 15000;

                        if ($a_food_allowance_nonTax > $config_food_allowances->money) {
                            $food_allowance_nonTax = $config_food_allowances->money;
                        } else {
                            $food_allowance_nonTax = $a_food_allowance_nonTax;
                        }

                         //phụ cấp ăn chịu thuế
                        $food_allowance_tax = $a_food_allowance_nonTax - $food_allowance_nonTax;

                    } else {
                        //phụ câp ăn miễn thuế hành chính
                        $a_food_allowance_nonTax = ($total_hd / $detail['total_day_request'] * $food_allowance);
                        if ($a_food_allowance_nonTax > $config_food_allowances->money) {
                            $food_allowance_nonTax = $config_food_allowances->money;
                        } else {
                            $food_allowance_nonTax = $a_food_allowance_nonTax;
                        }

                        //phụ cấp ăn chịu thuế
                        $food_allowance_tax = $a_food_allowance_nonTax - $food_allowance_nonTax;
                    }
                    
                    //tính lại phụ cấp nhân kpi theo ngày nghỉ

                    // $countStaffDayOff = StaffDayOff::countDayOffsInMonth($contract->user_id, $data['month'], $data['year']);
                    // if ($countStaffDayOff > 5) $total_allowances = ($total_allowances / $detail['total_day_request']) * $value->total; 
                    //lương ot chịu thuế
                    
                    $total_hours_ot_tv = array_sum(array_column($value->detail, 'ot_tv')) + array_sum(array_column($value->detail, 'hours_night_not_day_tv')) + array_sum(array_column($value->detail, 'hours_night_have_day_tv')) + array_sum(array_column($value->detail, 'night_tv'));
                    $total_hours_ot_hd = array_sum(array_column($value->detail, 'ot_hd')) + array_sum(array_column($value->detail, 'hours_night_not_day_hd')) + array_sum(array_column($value->detail, 'hours_night_have_day_hd')) + array_sum(array_column($value->detail, 'night_hd'));
                    $salary_ot_tax = (($basic_salary_hd + $phuCapOtChiuThue) / $detail['total_day_request'] / $coefficient_ot * $total_hours_ot_hd) + ($total_hours_ot_tv * ($basic_salary_tv / $detail['total_day_request'] / $coefficient_ot));

                    //lương ot miễn thuế
                    $salary_ot_non_tax = $this->tinhOt($value, $level_config_tv, $level_config_hd, $basic_salary_hd, $phucapOtMienThue, $detail, $basic_salary_tv, $coefficient_ot);
                // }
                
                
            // }
            
            //Đóng bảo hiểm
            $kinhPhiCongDoan = UnionFund::where('user_id', $contract->user_id)->whereNull('deleted_at')
                                ->where('start', '<', $start)->first();
                            // ->where('month', '<=', $data['month'])->where('year',  '<=', $data['year'])->first();
            if (!is_null($kinhPhiCongDoan)) {
                $bhxh_user = $bhyt_user = $bhtn_user = $union_user = $bhxh_company = $bhyt_company = $bhtn_company = $union_company = 0;

                if (!empty($config_insurrance)) {
                    if ($salary_bh > $config_insurrance->money) {
                        $union_user = $config_insurrance->money * ($config_insurrance->union_user / 100);
                        $union_company = $config_insurrance->money * ($config_insurrance->union_company / 100);

                    } else {
                        $union_user = $salary_bh * ($config_insurrance->union_user / 100);
                        $union_company = $salary_bh * ($config_insurrance->union_company / 100);
                    }
                }

            } else {
                if ($total_hd >= 14) {
                    if (!empty($config_insurrance)) {
                        if ($salary_bh > $config_insurrance->money) {
                            $bhxh_user = $config_insurrance->money * ($config_insurrance->bhxh_user / 100);
                            $bhyt_user = $config_insurrance->money * ($config_insurrance->bhyt_user / 100);
                            $bhtn_user = $salary_bh * ($config_insurrance->bhtn_user / 100);
                            $union_user = $config_insurrance->money * ($config_insurrance->union_user / 100);
                
                            $bhxh_company = $config_insurrance->money * ($config_insurrance->bhxh_company / 100);
                            $bhyt_company = $config_insurrance->money * ($config_insurrance->bhyt_company / 100);
                            $bhtn_company = $salary_bh * ($config_insurrance->bhtn_company / 100);
                            $union_company = $config_insurrance->money * ($config_insurrance->union_company / 100);
                        } else {
                            $bhxh_user = $salary_bh * ($config_insurrance->bhxh_user / 100);
                            $bhyt_user = $salary_bh * ($config_insurrance->bhyt_user / 100);
                            $bhtn_user = $salary_bh * ($config_insurrance->bhtn_user / 100);
                            $union_user = $salary_bh * ($config_insurrance->union_user / 100);
            
                            $bhxh_company = $salary_bh * ($config_insurrance->bhxh_company / 100);
                            $bhyt_company = $salary_bh * ($config_insurrance->bhyt_company / 100);
                            $bhtn_company = $salary_bh * ($config_insurrance->bhtn_company / 100);
                            $union_company = $salary_bh * ($config_insurrance->union_company / 100);
                        }
                    } 
                } else {
                    
                    $bhxh_user = $bhyt_user = $bhtn_user = $union_user = $bhxh_company = $bhyt_company = $bhtn_company = $union_company = 0;
                    if ($total_work['S'] >= 14) {
                        if (!empty($config_insurrance)) {
                            if ($salary_bh > $config_insurrance->money) {
                                $bhyt_user = $config_insurrance->money * ($config_insurrance->bhyt_user / 100);
                                $bhyt_company = $config_insurrance->money * ($config_insurrance->bhyt_company / 100);

                            } else {
                                $bhyt_user = $salary_bh * ($config_insurrance->bhyt_user / 100);
                                $bhyt_company = $salary_bh * ($config_insurrance->bhyt_company / 100);
                            }
                        }

                        // $bhyt_user = $config_insurrance->money * ($config_insurrance->bhyt_user / 100);
                        // $bhyt_company = $config_insurrance->money * ($config_insurrance->bhyt_company / 100);
                    }
    
                    $ngay_tinh_bhyt = 0;
    
                    if ($contract->type_status == 7) {
                        if (!is_null($contract->set_notvalid_on)) {
                            $month = date('m', strtotime($contract->set_notvalid_on));
                            $year = date('Y', strtotime($contract->set_notvalid_on));
    
                            if ($month == $data['month'] && $year == $data['year']) {
                                for ($i = 26; $i <= 31; $i++) {
                                    $date = $i . '-' . ($data['month'] - 1) . '-' . $data['year'];
                                    if ($value->detail[strtotime($date)]['total_hd'] == 1) {
                                        $ngay_tinh_bhyt = 1;
                                        break;
                                    }
                                }
                            }
                        }
    
                    }
    
                    if ($ngay_tinh_bhyt == 1) {
                        $bhyt_user = $salary_bh * ($config_insurrance->bhyt_user / 100);
                        $bhyt_company = $salary_bh * ($config_insurrance->bhyt_company / 100);
                    }
                }
            }

            //giảm trừ gia cảnh
            $dependent_person = User::countUserRelationship($contract->user_id);
            $family_allowances = 11000000 + 4400000 * $dependent_person;

            $total_allowances = $allowanceByWorking + $allowanceTargetByWorking + $allowanceTarget + $dataAllowance; // tổng phụ cấp

            //Tổng thu nhập

            //các khoản tăng
            $total_payoff = $total_payoff_tax = 0;
            $payoffs = PayOff::where('user_id', $contract->user_id)->where('year', $data['year'])->where('month', $data['month'])
                            ->where('department_id', $data['department_id'])
                            ->get();
            if (count($payoffs) > 0) {
                foreach ($payoffs as $payoff) {
                    $total_payoff += $payoff->amount_money_non_tax + $payoff->amount_money_tax;
                    $total_payoff_tax += $payoff->amount_money_tax;
                }
            } else {
                $total_payoff = $total_payoff_tax = 0;
            }
            
            $impales = Impale::where('user_id', $contract->user_id)->where('year', $data['year'])->where('month', $data['month'])->get();
            if (count($impales) > 0) {
                foreach ($impales as $impale) {
                    $total_impale += $impale->amount_money;
                }
            } else {
                $total_impale = 0;
            }

            $total_salary = $working_salary_non_tax + $working_salary_tax + $food_allowance_nonTax + $food_allowance_tax + $total_allowances + $salary_ot_non_tax + $salary_ot_tax;
            //Phụ cấp không tính thuế
            $phuCapKhongTinhThue = $contract->phuCapKhongTinhThue->sum('pivot.expense');
            
            $deductions = Deduction::where('user_id', $contract->user_id)->where('year', $data['year'])->get();
            if (count($deductions) > 0) {
                foreach ($deductions as $d => $deduction) {
                    if (in_array($data['month'], explode(', ', $deduction->month))) {
                        $total_deduction = $deduction->detailDeduction->sum('money'); // các khoản giảm trừ khác
                        $total_deduction_tax = $deduction->totalTax->sum('money'); // các khoản giảm trừ khác chịu thuế
                    }
                }
            } else {
                $total_deduction_tax = $total_deduction = 0;
            }
            

            //Thu nhập chịu thuế 
            $income_taxes = $total_salary - $salary_ot_non_tax - $food_allowance_nonTax - $working_salary_non_tax - (($phuCapKhongTinhThue / $detail['total_day_request']) * ($full_cong)) + $total_payoff_tax - $total_deduction_tax;

            //Thu nhập tính thuế
            $taxable_income = $income_taxes - $bhxh_user - $bhyt_user - $bhtn_user - $family_allowances;
            if ($taxable_income < 0) $taxable_income = 0;
            
            //thuế thu nhập cá nhân
            $personal_income_tax = $this->tntt($taxable_income);

            $personal_income_tax < 0 ? $personal_income_tax = 0 : $personal_income_tax;

            //tổng thực nhận


            $total_real_salary = $total_salary + $total_payoff - $bhxh_user - $bhyt_user - $bhtn_user - $union_user - $personal_income_tax - $total_deduction;

            $bhxh = [
                'bhxh_user'     => $bhxh_user,
                'bhyt_user'     => $bhyt_user,
                'bhtn_user'     => $bhtn_user,
                'union_user'    => $union_user,
                'bhxh_company'  => $bhxh_company,
                'bhyt_company'  => $bhyt_company,
                'bhtn_company'  => $bhtn_company,
                'union_company' => $union_company
            ];

            // if ($full_cong == 0) $salary_bh = 0;

            // if ($total_salary == 0) {
            //     $income_taxes = $taxable_income = $personal_income_tax = 0;
            // }

            $response[] = [
                'total_salary'              => max(intval($total_salary), 0),
                'total_real_salary'         => max(round($total_real_salary), 0) ,
                'food_allowance_nonTax'     => max(intval($food_allowance_nonTax), 0),
                'food_allowance_tax'        => max(intval($food_allowance_tax), 0),
                'total_allowances'          => max(intval($total_allowances), 0),
                'basic_salary'              => $basic_salary,
                'salary_bh'                 => max(intval($salary_bh), 0),
                'working_salary_non_tax'    => max(intval($working_salary_non_tax), 0),
                'working_salary_tax'        => max(intval($working_salary_tax), 0),
                'salary_ot_non_tax'         => max(intval($salary_ot_non_tax), 0),
                'salary_ot_tax'             => max(intval($salary_ot_tax), 0),
                'salary_concurrent'         => max(intval($salary_concurrent), 0),
                'bh'                        => json_encode($bhxh),
                'income_taxes'              => max(intval($income_taxes), 0),
                'taxable_income'            => max(intval($taxable_income), 0),
                'personal_income_tax'       => max(intval($personal_income_tax), 0),
                'family_allowances'         => max(intval($family_allowances), 0),
                'user_id'                   => $contract->user_id,
                'payroll_id'                => $payroll->id,
                'allowances_ids'            => $allowances_ids,
                'basic_salary_tv'           => $basic_salary_tv,
                'basic_salary_hd'           => $basic_salary_hd,
                'allowances_ids1'            => $allowances_ids1,
                'total_payoff'              => max(intval($total_payoff), 0),
                'total_payoff_tax'              => intval($total_payoff_tax),
                'total_deduction'              => intval($total_deduction),
                'total_deduction_tax'              => intval($total_deduction_tax),
                'total_impale'              => max(intval($total_impale), 0),
                'contract_id' => $contract->id,
                'timekeeping_id' => $value->id,
            ];
        }

        PayrollUser::insert($response);
        
        if (count($concurrents) > 0) {
            foreach ($concurrents as $key => $concurrent) {
                $target = Target::where('user_id', $concurrent->user_id)->where('month', $month_kpi)->where('year', $year_kpi)->orderBy('id', 'DESC')->first();
                if (is_null($target)) {
                    $target = 100;
                } else {
                    $target = $target->kpi;
                }

                $request->export = 1;
                $checkTimekeeping = TimeKeeping::where('month', $data['month'])->where('year', $data['year'])->whereHas('timeKeepingDetail', function ($q) use ($concurrent) {
                    $q->where('staff_id', $concurrent->user_id);
                })->first();

                $timekeepingDetail = new TimekeepingController();
                $detail = $timekeepingDetail->detail($request, $checkTimekeeping->id);

                if ($checkTimekeeping->id) {
                    $value = $detail['items']->where('staff_id', $concurrent->user_id)->first();
                    if (!is_null($value)) {
                        if ($start < date('Y-m-d', strtotime($concurrent->valid_from)) && date('Y-m-d', strtotime($concurrent->valid_from)) < $end) {
                            $array_new = $array_old = [];
                            foreach ($value->detail as $kDe => $de) {
                                if ($kDe < strtotime($concurrent->valid_from)) continue;
                                $array_new[$kDe] = $de;
                            }
                            $basic_salary_hd =  $concurrent->salary;

                            $basic_salary_hd_new = $basic_salary_hd_old = 0;
                            $basic_salary_hd_new = $concurrent->salary;
                            $countStaffDayOffNew = $this->countTotalInMonthForTimeKeeping($concurrent, $data['month'], $data['year'], $data['department_id']);
                            $countStaffDayOff = StaffDayOff::countTotalInMonthForTimeKeeping($concurrent->user_id, $data['month'], $data['year'], $data['department_id']);

                            $concurrent_old = ConcurrentContract::where('user_id', $concurrent->user_id)->where('department_id', $data['department_id'])->where('status', 0)->orderBy('id', 'DESC')->first();

                            if (!is_null($concurrent_old)) {
                                $basic_salary_hd_old = $concurrent_old->salary;
                                // if ($start < date('Y-m-d', strtotime($concurrent->valid_from))
                                //     && date('Y-m-d', strtotime($concurrent->valid_from)) < date('Y-m-d', strtotime($concurrent->valid_to))
                                // ) {
                                //     foreach ($value->detail as $kDe => $de) {
                                //         if ($kDe > strtotime($concurrent->valid_from)) continue;
                                //         $array_old[$kDe] = $de;
                                //     }
                                // }
                                // else if ($start < date('Y-m-d', strtotime($concurrent->valid_from))
                                //     && date('Y-m-d', strtotime($concurrent->valid_from)) > date('Y-m-d', strtotime($concurrent->valid_to))
                                // ) {
                                //     foreach ($value->detail as $kDe => $de) {
                                //         if ($kDe > strtotime($concurrent->valid_to)) continue;
                                //         $array_old[$kDe] = $de;
                                //     }
                                // }

                                foreach ($value->detail as $kDe => $de) {
                                    if ($kDe >= strtotime($concurrent->valid_from)) continue;
                                    $array_old[$kDe] = $de;
                                }
                            }

                            $total_hd_new = $total_tv_new = $total_hd_old = $total_tv_old = 0;
                            if (count($array_new)) {
                                $total_hd_new = array_sum(array_column($array_new, 'total'));
                                // $total_tv_new = array_sum(array_column($array_new, 'total_tv'));
                            }

                            if (count($array_old)) {
                                $total_hd_old = array_sum(array_column($array_old, 'total'));
                                // $total_tv_old = array_sum(array_column($array_old, 'total_tv'));
                            }

                            $total_hd_new += $countStaffDayOffNew['L'] + $countStaffDayOffNew['W'] + $countStaffDayOffNew['D'];
                            $total_hd_old += max($countStaffDayOff['L'] - $countStaffDayOffNew['L'], 0) +  max($countStaffDayOff['W'] - $countStaffDayOffNew['W'], 0) +  max($countStaffDayOff['D'] - $countStaffDayOffNew['D'], 0);

                            $salary_concurrent = ($basic_salary_hd_new * ($total_hd_new / $detail['total_day_request']) * ($target / 100)) + ($basic_salary_hd_old * ($total_hd_old / $detail['total_day_request']) * ($target / 100));
                        } else {
                            $total_work = StaffDayOff::countTotalInMonthForTimeKeeping($value->staff_id, $value->timekeeping->month, $value->timekeeping->year, $value->timekeeping->department_id);
    
                            $total_tv = array_sum(array_column($value->detail, 'total_tv'));
                            $total_hd = array_sum(array_column($value->detail, 'total_hd'));
            
                            if ($total_hd) $total_hd = $total_hd + $total_work['L'] + $total_work['D'] + $total_work['W'];
            
                            $basic_salary_hd =  $concurrent->salary;
            
                            $salary_concurrent = $basic_salary_hd * ($total_hd / $detail['total_day_request']) * ($target / 100);
                        }

                        // $total_work = StaffDayOff::countTotalInMonthForTimeKeeping($value->staff_id, $value->timekeeping->month, $value->timekeeping->year);
    
                        // $total_tv = array_sum(array_column($value->detail, 'total_tv'));
                        // $total_hd = array_sum(array_column($value->detail, 'total_hd'));
        
                        // if ($total_hd) $total_hd = $total_hd + $total_work['L'] + $total_work['D'] + $total_work['W'];
        
                        // $basic_salary_hd =  $concurrent->salary;
        
                        // $salary_concurrent = $basic_salary_hd * ($total_hd / $detail['total_day_request']) * ($target / 100);
                        
                        $total_payoff_tax = $taxable_income = $personal_income_tax = $income_taxes = 0;
                        $payoffs = PayOff::where('user_id', $concurrent->user_id)->where('department_id', $concurrent->department_id)
                                            ->where('year', $data['year'])->where('month', $data['month'])
                                            ->get();

                        $total_payoff_tax = $payoffs->where('type', 'CHIU_THUE')->sum('amount_money_tax');
                        $total_payoff_non_tax = $payoffs->where('type', 'MIEN_THUE')->sum('amount_money_non_tax');
                        $total_payoff = $total_payoff_non_tax + $total_payoff_tax;

                        if ($salary_concurrent > 0)  $income_taxes = $taxable_income = $salary_concurrent + $total_payoff_tax;
                        

                        if ($taxable_income > 0) {
                            $personal_income_tax = $this->tntt($taxable_income);
                            $personal_income_tax < 0 ? $personal_income_tax = 0 : $personal_income_tax;
                        }

                        $bhxh = [
                            'bhxh_user'     => 0,
                            'bhyt_user'     => 0,
                            'bhtn_user'     => 0,
                            'union_user'    => 0,
                            'bhxh_company'  => 0,
                            'bhyt_company'  => 0,
                            'bhtn_company'  => 0,
                            'union_company' => 0
                        ];
                        $total = $total_hd + $total_tv;

                        $total_real_salary = $salary_concurrent + $total_payoff - $personal_income_tax;

                        if ($total == 0) {
                            $salary_concurrent = $total_real_salary = $salary_concurrent = $basic_salary_hd = 0;
                            $income_taxes = $taxable_income = $personal_income_tax = 0;
                        }
                        if ($salary_concurrent == 0) {
                            $income_taxes = $taxable_income = $personal_income_tax = 0;
                        }
                        $response1[] = [
                            'total_salary'              => max($salary_concurrent, 0),
                            'total_real_salary'         => max($total_real_salary, 0),
                            'food_allowance_nonTax'     => 0,
                            'food_allowance_tax'        => 0,
                            'total_allowances'          => 0,
                            'basic_salary'              => 0,
                            'salary_bh'                 => 0,
                            'working_salary_non_tax'    => 0,
                            'working_salary_tax'        => max($salary_concurrent, 0),
                            'salary_ot_non_tax'         => 0,
                            'salary_ot_tax'             => 0,
                            'salary_concurrent'         => max($salary_concurrent, 0),
                            'bh'                        => json_encode($bhxh),
                            'income_taxes'              => max(intval($income_taxes), 0),
                            'taxable_income'            => max(intval($taxable_income), 0),
                            'personal_income_tax'       => max(intval($personal_income_tax), 0),
                            'family_allowances'         => 0,
                            'user_id'                   => $concurrent->user_id,
                            'payroll_id'                => $payroll->id,
                            'allowances_ids'            => $allowances_ids,
                            'basic_salary_tv'           => 0,
                            'basic_salary_hd'           => $basic_salary_hd,
                            'allowances_ids1'            => $allowances_ids1,
                            'total_payoff'              => intval($total_payoff),
                            'total_payoff_tax'          => intval($total_payoff_tax),
                            'timekeeping_id' => $value->id
                        ];
    
                    }
                }
                
                
            }

            if (count($response1) > 0) {
                PayrollUser::insert($response1);

            }
        }
        
        

        return true;
    }

    public function detail($id)
    {
        $payroll = Payroll::find($id);
        if (empty($payroll)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.payrolls.index');
        }
        $payroll->load('company', 'department');
        $infoPermission = PermissionUserObject::getMorePermissions(Auth::user()->id, 'payrolls.read');
        $payroll_detail = PayrollUser::where('payroll_id', $id)->get();

        // if ($infoPermission && !is_null($infoPermission['check'])) {
        //     $payroll_detail = PayrollUser::where('payroll_id', $id)->where('user_id', Auth::user()->id)->get();

        // } else {
        //     $payroll_detail = PayrollUser::where('payroll_id', $id)->get();
        // }
        // $payroll_detail->load('staff');
        // $ids = implode(',', array_pluck($payroll_detail, 'id'));
        
        foreach ($payroll_detail as $key => $item) {
            $code = preg_replace('/[^0-9]/', '', $item->staff->code);

            $item['code'] = $code;
        }

        $payroll_detail = $payroll_detail->sortBy('code');

        return view('backend.payroll.detail', compact('payroll', 'payroll_detail'));
    }

    public function userPayroll(Request $request, $id)
    {
        $user_payroll = PayrollUser::find($id);
        if (empty($user_payroll)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.payrolls.index');
        }
     
        $user_payroll->load('staff');
        $position = Position::find($user_payroll->staff->position_id);
        $qualification = Qualification::find($user_payroll->staff->qualification_id);
        $user_payroll['position'] = $position->name;
        $user_payroll['qualification'] = $qualification->name;


        if ($user_payroll->bh) {
            $user_payroll['bh'] = json_decode($user_payroll->bh);
        }
        $payroll = Payroll::find($user_payroll->payroll_id);
        $payroll->load('company', 'department', 'user_by');
        $timekeeping = TimeKeeping::where('month', $payroll->month)->where('year', $payroll->year)
                                    ->where('company_id', $payroll->company_id)->where('department_id', $payroll->department_id)
                                    ->first();
        $timekeepingDetail = TimeKeepingDetail::where('timekeeping_id', $timekeeping->id)->where('staff_id', $user_payroll->user_id)->first();
            
        $shifts = json_decode($timekeepingDetail->detail, true);
        foreach ($shifts as $s => $sh) {
            if ($sh['total_tv'] == 1) $shift_tv = array_count_values(array_values(array_column($shifts, 'shift')));
            if ($sh['total_hd'] == 1) $shift_hd = array_count_values(array_values(array_column($shifts, 'shift')));
        }
        if ($timekeepingDetail) {
            $total_work = StaffDayOff::countTotalInMonthForTimeKeeping($user_payroll->user_id, $timekeepingDetail->timekeeping->month, $$timekeepingDetail->timekeeping->year);
            if (!empty($total_work)) {
                $user_payroll['actual_workdays'] = $timekeepingDetail->total + $total_work['L'] + $total_work['D'] + $total_work['W'];
            }
            $user_payroll['total_day_request'] = OverTimes::totalWorkingInMonth($payroll->month, $payroll->year, $payroll->department_id) + $total_work['H'];
            $user_payroll['an_chinh'] = array_sum(array_column(json_decode($timekeepingDetail->detail, true) , 'an_chinh'));
            $user_payroll['an_phu'] = array_sum(array_column(json_decode($timekeepingDetail->detail, true) , 'an_phu'));

            if (empty($shift_tv)) {
                $user_payroll['ca_ngay_tv'] = array_sum(array_column($shifts, 'total_tv'));

            } else {
                $user_payroll['ca_ngay_tv'] = intval($shift_tv['1']) + intval($shift_tv['2']);
            }
            if (empty($shift_hd)) {
                $user_payroll['ca_ngay_hd'] = array_sum(array_column($shifts, 'total_hd')) + $total_work['L'] + $total_work['D'] + $total_work['W'];
            } else {
                $user_payroll['ca_ngay_hd'] = intval($shift_hd['1']) + intval($shift_hd['2']) + $total_work['L'] + $total_work['D'] + $total_work['W'];
            }

            $user_payroll['ca_dem_tv'] = intval($shift_tv['3']);
            $user_payroll['ca_dem_hd'] = intval($shift_hd['3']);
            $user_payroll['day_off_70_salary'] = $timekeepingDetail->day_off_70_salary;
        }
        // ->where('type_status', 1)
        $contract = Contract::where('user_id', $user_payroll->user_id)->orderBy('id', 'DESC')->first();
        $contract->load('appendixAllowances');
        $user_payroll['day_off_luong'] = StaffDayOff::countDayOffs($contract->id, $payroll->month, $payroll->year);

        $allowances_ids = explode(' ', $user_payroll->allowances_ids);
        $allowances_ids1 = explode(' ', $user_payroll->allowances_ids1);

        if (count($contract->appendixAllowances) > 0) { 
            $allowances1 = AppendixAllowance::whereIn('id', $allowances_ids1)->whereNotIn('allowance_id', [1])->where('contract_id', $contract->id)->where('allowance_active', 1)->get();
            $allowances1->load('category');
        }
        $allowances = Allowance::whereIn('id', $allowances_ids)->whereNotIn('category_id', [1])->where('contract_id', $contract->id)->where('active', 1)->get();
        $allowances->load('allowanceCategory');

        $getSeniority = Contract::getSeniority($user_payroll->user_id);
        // dd($getSeniority);
        $target = Target::where('user_id', $user_payroll->user_id)->whereMonth('timestamp', $payroll->month - 1)->whereYear('timestamp', $payroll->year)->first();
        $countStaffDayOff = StaffDayOff::countDayOffsInMonth($user_payroll->user_id, $payroll->month, $payroll->year);

        foreach ($allowances as $key => $allowance) {
            if ($allowance->allowanceCategory->type == 1 || $allowance->category->type == 1) {
                if ($countStaffDayOff > 5) {
                    $allowances[$key]['money'] = ($allowance->expense * ($target / 100)) / $user_payroll['total_day_request'] * $user_payroll['actual_workdays'];
                } else {
                    $allowances[$key]['money'] = $allowance->expense * ($target / 100);
                }
            } else {
                if ($countStaffDayOff > 5) {
                    $allowances[$key]['money'] = $allowance->expense / $user_payroll['total_day_request'] * $user_payroll['actual_workdays'];
                } else {
                    $allowances[$key]['money'] = $allowance->expense;
                }
            }
        }

        if (!empty($allowances1)) {
            foreach ($allowances1 as $key => $allowance) {
                if ($allowance->allowanceCategory->type == 1 || $allowance->category->type == 1) {
                    if ($countStaffDayOff > 5) {
                        $allowances1[$key]['money'] = ($allowance->expense * ($target / 100)) / $user_payroll['total_day_request'] * $user_payroll['actual_workdays'];
                    } else {
                        $allowances1[$key]['money'] = $allowance->expense * ($target / 100);
                    }
                } else {
                    if ($countStaffDayOff > 5) {
                        $allowances1[$key]['money'] = $allowance->expense / $user_payroll['total_day_request'] * $user_payroll['actual_workdays'];
                    } else {
                        $allowances1[$key]['money'] = $allowance->expense;
                    }
                }
            }
        }

        $other_amounts = OtherAmount::getOtherAmounts($id, 1);
        !empty($other_amounts) ? $total_other_amounts = array_sum(array_column($other_amounts->toArray(), 'money')) : $total_other_amounts = 0;
        $deductions = Deduction::where('user_id', $user_payroll->user_id)->where('year', $payroll->year)->get();
        foreach ($deductions as $d => $deduction) {
            if (in_array($payroll->month, explode(', ', $deduction->month))) {
                $total_deductions = $deduction->detailDeduction->sum('money');
                $data_deductions[] = $deduction;
            }
        }
        $logs = $this->getLog($id);
        if ($request->export == self::EXPORT) {
            return [
                'user_payroll'         => $user_payroll,
                'allowances'           => $allowances,
                'allowances1'          => $allowances1,
                'other_amounts'        => $other_amounts,
                'deductions'           => $data_deductions,
            ];
        }

        return view('backend.payroll.user_detail', compact('user_payroll', 'payroll', 'allowances', 'getSeniority', 'total_other_amounts', 'other_amounts', 'total_deductions', 'deductions', 'logs', 'allowances1'));
    }

    public function checkContract($userId)
    {
        $check = Contract::where('staff_id', $userId)->get();
        if ($userId && count($check) > 2) {
            return 1; //Nhân viên có nhiều hợp đồng
        }

        return 0; //Nhân viên có 1 hợp đồng
    }

    public function destroy($id)
    {
        $payroll = Payroll::find($id);
        if (is_null($payroll)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.payrolls.index');
        }
		try {
			DB::beginTransaction();
			$payroll->userPayroll()->delete();
			$payroll->delete();
			DB::commit();
		} catch (\Exception $e) {
			DB::rollBack();
			return back()->withErrors($e)->withInput();
		}
        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');
        return redirect()->route('admin.payrolls.index');
    }

    public function otherAmounts(Request $request)
    {
        $response = [];
        $data = $request->all();
        foreach ($data['name'] as $key => $value) {
            if (!is_null($value)) {
                $response[] = [
                    'payroll_user_id' => $data['payroll_user_id'],
                    'name'            => $value,
                    'money'           => $data['money'][$key],
                    'type'            => $data['type'],
                    'created_by'      => $request->user()->id
                ];
            }
        }
        
        if (!empty($data['ids'])) {
            OtherAmount::whereIn('id', $data['ids'])->delete();
        }
        if (OtherAmount::insert($response)) {
            Session::flash('message', trans('system.success'));
            Session::flash('alert-class', 'success');
            return redirect()->route('admin.payrolls.user-detail', $data['payroll_user_id']);
        }

        Session::flash('message', trans('system.have_an_error'));
        Session::flash('alert-class', 'danger');
        return redirect()->route('admin.payrolls.user-detail', $data['payroll_user_id']);
    }

    public function recalculate(Request $request, $payrollId)
    {
        $payrollUser = PayrollUser::find($payrollId);
        $payrollUser->load('payroll');
        $data = [
            'company_id'    => $payrollUser->payroll->company_id,
            'department_id' => $payrollUser->payroll->department_id,
            'month'         => $payrollUser->payroll->month,
            'year'          => $payrollUser->payroll->year,
            'user_id'           => $payrollUser->user_id 
        ];

        $checkTimekeeping = TimeKeeping::where('company_id', $data['company_id'])->where('department_id', $data['department_id'])
                                        ->where('month', $data['month'])->where('year', $data['year'])->first();
        $payroll = $payrollUser->payroll;

        if ($payrollUser->logs()) $payrollUser->logs()->delete();
        if ($payrollUser->otherAmounts()) $payrollUser->otherAmounts()->delete();

        if ($payrollUser->delete()) {
            if ($this->calculate($request, $data, $payroll, $checkTimekeeping) == true) {
                return \Response::json([
                    'status'           => 'SUCCESS',
                    'message'          => 'Tính lại thành công',
                    'url'              => route('admin.payrolls.user-detail', PayrollUser::get()->last()->id)
                ]); 
            }
        }
        // if ($this->calculate($request, $data, $payroll, $checkTimekeeping) == true) {
        //     return \Response::json([
        //         'status'           => 'SUCCESS',
        //         'message'          => 'Tính lại thành công',
        //         'url'              => route('admin.payrolls.user-detail', PayrollUser::get()->last()->id)
        //     ]); 
        // }
        
        return \Response::json([
            'status'  => 'FAIL',
            'message' => 'Có lỗi',
        ]); 
    }

    public function update($id)
    {
        $user_payroll = PayrollUser::find($id);
        $user_payroll->load('logs');
        if (empty($user_payroll)) {
            return \Response::json([
                'status' => 'FAIL',
                'message' => 'Lỗi'
            ]);
        }
        $other_amounts = OtherAmount::getOtherAmounts($id, 1);
        $deductions = OtherAmount::getOtherAmounts($id, 2);
        $total_other_amounts = array_sum(array_column($other_amounts->toArray(), 'money'));
        $total_deductions = array_sum(array_column($deductions->toArray(), 'money'));
        
        if ($total_other_amounts == intval($user_payroll->total_other_amounts) && $total_deductions == intval($user_payroll->total_deductions)
              && empty($user_payroll->logs->toArray())
        ) {
            return \Response::json([
                'status' => 'FAIL',
                'message' => 'Không có thay đổi'
            ]);
        } else {
            $bh = json_decode($user_payroll->bh, true);
            $ltt = $user_payroll->working_salary_tax + $user_payroll->working_salary_non_tax; //lương thực tế
            $pc_an = $user_payroll->food_allowance_tax + $user_payroll->food_allowance_nonTax; //phụ cấp ăn
            $luong_ot = $user_payroll->salary_ot_tax + $user_payroll->salary_ot_non_tax; //lương làm thêm

            $total_salary = $ltt + $pc_an + $user_payroll->total_allowances + $luong_ot + $total_other_amounts + $user_payroll->salary_concurrent;
            $income_taxes = $total_salary - $user_payroll->salary_ot_non_tax - $user_payroll->food_allowance_nonTax;
            $taxable_income = $income_taxes - $bh['bhxh_user'] - $bh['bhyt_user'] - $bh['bhtn_user'] - $user_payroll->family_allowances;
            $personal_income_tax = $this->tntt($taxable_income);
            $total_real_salary = $total_salary - $bh['bhxh_user'] - $bh['bhyt_user'] - $bh['bhtn_user'] - $bh['union_user'] - $total_deductions;

            $response = [
                'total_salary'          => $total_salary,
                'total_real_salary'     => round($total_real_salary),
                'income_taxes'          => $income_taxes,
                'taxable_income'        => $taxable_income,
                'personal_income_tax'   => $personal_income_tax,
                'total_other_amounts'   => $total_other_amounts,
                'total_deductions'      => $total_deductions
            ];

            $check = PayrollUser::where('id', $id)->update($response);
            if ($check) {
                return \Response::json([
                    'status' => 'SUCCESS',
                    'message' => 'Cập nhật lương thành công'
                ]);
            }
        }
    }

    public function approved($id)
    {
        $payroll = Payroll::find($id);

        if (is_null($payroll)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.payrolls.index');
        }

        $timekeeping = TimeKeeping::where('month', $payroll->month)->where('year', $payroll->year)
                                    ->where('company_id', $payroll->company_id)->where('department_id', $payroll->department_id)
                                    ->first();

        if ($timekeeping->status != 'APPROVED') {
            Session::flash('message', 'Bảng công chưa chốt');
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.payrolls.detail', $id);
        }

        if ($payroll->update([
            'status' => 'APPROVED',
            'user_approved' => Auth::user()->id,
            'date_approved' => date('Y-m-d H:i:s')
        ])) {
            Session::flash('message', 'Duyệt thành công');
            Session::flash('alert-class', 'success');
            return redirect()->route('admin.payrolls.detail', $id);
        }
    }

    public function log(Request $request, $id)
    {
        $data = $request->all();
        $tax = $nonTax = '';
        $payroll_detail = PayrollUser::find($id);
        if (empty($payroll_detail)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.payrolls.user-detail', $id);
        }

        switch ($data['type']) {
            case 'FOOD_ALLOWANCE':
                $tax    = 'food_allowance_tax';
                $nonTax = 'food_allowance_nonTax';
                break;
            case 'WORKING_SALARY':
                $tax    = 'working_salary_tax';
                $nonTax = 'working_salary_non_tax';
                break;
            case 'SALARY_OT':
                $tax    = 'salary_ot_tax';
                $nonTax = 'salary_ot_non_tax';
                break;
        }

        if ($this->createLog($data, $payroll_detail, $tax, $nonTax, $request->user()->id, $id) == true) {
            Session::flash('message', trans('system.success'));
            Session::flash('alert-class', 'success');
            return redirect()->route('admin.payrolls.user-detail', $id);
        }

        Session::flash('message', 'Không có thay đổi');
        Session::flash('alert-class', 'success');
        return redirect()->route('admin.payrolls.user-detail', $id);
    }

    public function createLog($data, $payroll_detail, $tax, $nonTax, $userId, $payrollId)
    {
        if ($data[$tax] == intval($payroll_detail->$tax) 
            && $data[$nonTax] == intval($payroll_detail->$nonTax)) {

            return false;
        } else {
            $arr[0] = [
                'field'      => $tax,
                'value_new'  => $data[$tax],
                'value_old'  => $payroll_detail->$tax
            ];
            $arr[1] = [
                'field'       => $nonTax,
                'value_new'   => $data[$nonTax],
                'value_old'   => $payroll_detail->$nonTax
            ];

            if ($data[$tax] == intval($payroll_detail->$tax)) unset($arr[0]);                
            if ($data[$nonTax] == intval($payroll_detail->$nonTax)) unset($arr[1]);
            
            foreach (array_values($arr) as $key => $value) {
                $response[] = [
                    'data_new'    => $value['value_new'],
                    'data_old'    => $value['value_old'],
                    'action_by'   => $userId,
                    'action_at'   => date('Y-m-d H:i:s'),
                    'field'       => $value['field'],
                    'note'        => $data['note_' . $value['field']],
                    'log_type'    => get_class($payroll_detail),
                    'log_id'      => $payrollId
                ];

            }
            if (PayrollUser::where('id', $payrollId)->update([
                $tax    => $data[$tax],
                $nonTax => $data[$nonTax],
            ])) {
                if (Log::insert($response)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function getLog($id)
    {
        $data = [];
        $payroll_type = [
            'food_allowance_tax'        => 'Phụ cấp ăn chịu thuế',
            'food_allowance_nonTax'     => 'Phụ cấp miễn thuế',
            'working_salary_tax'        => 'Tổng lương thực tế chịu thuế',
            'working_salary_non_tax'    => 'Tổng lương thực tế miễn thuế',
            'salary_ot_tax'             => 'Tổng lương làm thêm chịu thuế',
            'salary_ot_non_tax'         => 'Tổng lương làm thêm miễn thuế'
        ];
        $logs = PayrollUser::find($id)->logs;

        foreach ($logs as $key => $log) {
            $data[] = [
                'id'        => $log->id,
                'content'   => $payroll_type[$log->field],
                'data_new'  => number_format(intval($log->data_new), 0, ',', '.'),
                'data_old'  => number_format(intval($log->data_old), 0, ',', '.'),
                'note'      => is_null($log->note) ? '' : $log->note,
                'user'      => $log->user->fullname,
                'action_at' => date('d-m-Y H:i:s', strtotime($log->action_at))
            ];
        }
        array_multisort(array_column($data, 'id'), SORT_DESC, $data);

        return $data;
    }

    public function approvedMany(Request $request)
    {
        $ids = $request->ids;
        $check = 0;
        $items = PayrollUser::whereIn('id', $ids)->whereNull('status')->get();
        if (count($items) > 0) {
            foreach ($items as $key => $item) {
                $update = PayrollUser::where('id', $item->id)->update([
                    'status' => 1
                ]);
                
                if ($update) $check++;
            }
            if ($check > 0) {
                return \Response::json([
                    'status' => 'SUCCESS',
                    'message' => 'Duyệt thành công'
                ]);
            }
        }

        return \Response::json([
            'status' => 'FAIL',
            'message' => 'Bảng lương đã được duyệt'
        ]);
    }

    public function exportExcel(Request $request, $id)
    {
        $data = [];
        $payroll = Payroll::find($id);
        if (empty($payroll)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.payrolls.index');
        }
        if ($payroll->version == 1) {
            dd('update...');
        }
        $payroll->load('company', 'department');
        $payroll_detail = PayrollUser::with('timekeepingDetail')->where('payroll_id', $id)->get();

        return \Excel::download(new \App\Exports\PayrollsExport($payroll, $payroll_detail), 'luong' . '_' . date('H.m_d-m-Y') . '.xlsx');
    }

    public function exportExcel1(Request $request)
    {
        $data = $request->all();
        $request->request->add(['export' => 1]);

        $companyData = Company::find($data['company_id']);
        if (is_null($companyData)) {
            Session::flash('message', 'Công ty không tồn tại.');
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.payrolls.index');
        }
        $deptData = Department::where('company_id', $data['company_id'])
            ->where('status', 1)
            ->pluck('name', 'id')
            ->toArray();
        if (count($deptData) == 0) {
            Session::flash('message', 'Công ty không có phòng ban.');
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.payrolls.index');
        }

        $data['dept'] = $deptData;
        $payrolls = Payroll::where('company_id', $data['company_id'])
            ->where('month', $data['month'])
            ->where('year', $data['year'])
            ->get();
        $salaryDrivers = SalaryDrive::handleDataExcel($data);

        if (count($payrolls) == 0 && count($salaryDrivers) == 0) {
            Session::flash('message', 'Không có dữ liệu');
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.payrolls.index');
        }

        if ($payrolls[0]->version == 1) {
            $payroll = new PayrollV1Controller();
            //$payrolls->load('userPayroll', 'user_by');
            foreach ($payrolls as $key => $item) {
                foreach($item->userPayroll as $index => $value) {
                    $request->export = self::EXPORT;
                    $return = $payroll->detail($request, $item->id);
                    $payrolls[$key]['payroll_details'] = $return['payroll_details'];
                }
                //$payrolls[$key]['total'] = array_sum(array_column($item->userPayroll->toArray(), 'total_real_salary'));
            }
            $finalData = ['payroll' => $payrolls, 'salary_driver' => $salaryDrivers, 'dept' => $deptData, 'company' => $companyData];
            return \Excel::download(new \App\Exports\PayrollExportByCompany($finalData), 'LUONG' . '_'. mb_strtoupper(str_slug($companyData->shortened_name)) . '_' . date('H.m.i_d-m-Y') . '.xlsx');

        } else {
            $payrolls->load('userPayroll', 'company', 'user_by');
            foreach ($payrolls as $key => $item) {
                foreach($item->userPayroll as $index => $value) {
                    $request->export = self::EXPORT;
                    $return = $this->userPayroll($request, $value->id);
                    $payrolls[$key]['userPayroll'][$index] = $return['user_payroll'];
                }
                $payrolls[$key]['total'] = array_sum(array_column($item->userPayroll->toArray(), 'total_real_salary'));
            }
            $finalData = ['payroll' => $payrolls, 'salary_driver' => $salaryDrivers, 'dept' => $deptData];
            return \Excel::download(new \App\Exports\PayrollExportByCompany($finalData), 'luong' . '_'. $payrolls[0]->company->name . '_' . date('H.m_d-m-Y') . '.xlsx');
        }
        
    }

    public function exportUser(Request $request, $id)
    {
        $request->export = self::EXPORT;
        $user_payroll = $this->userPayroll($request, $id);
        $payroll = Payroll::find($user_payroll['user_payroll']->payroll_id);

        return \Excel::download(new \App\Exports\UserPayrollExport($user_payroll, $payroll), 'luong' . '_'. $user_payroll['user_payroll']->staff->fullname . '_' . date('H.m_d-m-Y') . '.xlsx');
    }

    public function getNameCt($name)
    {
       
        
    }

    public function payrollCt(Request $request)
    {
        if ($request->ajax()) {
            $data = $request->all();
            if ($data['name'] == 'allowance') {
                if ($data['salary_concurrent'] > 0) return response()->json(['data' => 0, 'status' => 200]);
                $contract = Contract::where('user_id', $data['userId'])->where('type_status', 1)->first();
                $pc = Allowance::where('contract_id', $contract->id)->where('category_id', $data['allowance'])->first();
                $cate = $pc->allowanceCategory;
                
                // $cate = AllowanceCategory::find($data['allowance']);
                if ($cate->status == 1 && $cate->type == 0 && $cate->type_work == 'BY_WORKING_DAY') {
                    if ($cate->id == 2) {
                        $return = '=' . ($pc->expense) . '*(M-Làm tại nhà)/D';
                    } else {
                        $return = '=' . ($pc->expense) . '*M/D';
                    }
                }

                if ($cate->status == 1 && $cate->type_work == 'BY_WORKING_DAY' && $cate->type == 1) {
                    $return = '=' . ($pc->expense)  . '*(M/D)*(BA/100)';
                }
        
                if ($cate->status == 1 && $cate->type_work == '' && $cate->type == 0) {
                    $return = '=' . ($pc->expense);
                }
                return response()->json(['data' => $return, 'status' => 200]);
            }
            if ($data['salary_concurrent'] > 0 && $data['name'] == 'working_salary_tax') {
                return response()->json(['data' => '=Q*(M/D)*(BA/100)', 'status' => 200]);
            }

            $get_names = Payroll::getCt($data);

            if ($get_names[$data['name']] == 'GET') return response()->json(['data' => $data['value'], 'status' => 200]);
            else  return response()->json(['data' => $get_names[$data['name']], 'status' => 200]);
        }
    }

    public function store1(Request $request)
    {
        $data = $request->all();
        $payroll = Payroll::find(intval($data['id']));
        
        if (is_null($payroll)) {
            Session::flash('message', 'Có lỗi xảy ra');
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.payrolls.index');
        }

        $checkTimekeeping = TimeKeeping::where('company_id', $data['company_id'])->where('department_id', $data['department_id'])
                                        ->where('month', $data['month'])->where('year', $data['year'])->first();
        
        if (empty($checkTimekeeping)) {
            Session::flash('message', 'Lỗi không tìm thấy bảng chấm công');
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.payrolls.index');
        }

        DB::beginTransaction();
        
        try {
            $payroll->user = Auth::user()->id;
            $payroll->updated_at = date('Y-m-d H:i:s');
            $payroll->save();

            $payroll->userPayroll()->delete();
            $this->calculate($request, $data, $payroll, $checkTimekeeping);

            DB::commit();
            
            Session::flash('message', trans('system.success'));
            Session::flash('alert-class', 'success');
            return redirect()->route('admin.payrolls.detail', $data['id']);
        } catch (Exception $e) {
            DB::rollBack();
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.payrolls.detail', $data['id']);
        }
       
    }

    public function tinhOt($value, $level_config_tv, $level_config_hd, $basic_salary_hd, $phucapOtMienThue, $detail, $basic_salary_tv, $coefficient_ot)
    {
        
        foreach ($value->detail as $kk => $value1) {
                        
            if (!is_null($value1['type_ot'])) {
                if ($value1['type_ot'] == 1) {
                    $ot_1_day_hd += $value1['ot_hd']; //ngày thường
                    $ot_1_hours_night_not_day_hd += $value1['hours_night_not_day_hd'];
                    $ot_1_hours_night_have_day_hd += $value1['hours_night_have_day_hd'];

                    $ot_1_day_tv += $value1['ot_tv']; //ngày thường
                    $ot_1_hours_night_not_day_tv += $value1['hours_night_not_day_tv'];
                    $ot_1_hours_night_have_day_tv += $value1['hours_night_have_day_tv'];

                }
                if ($value1['type_ot'] == 2) {
                    $ot_2_day_hd += $value1['ot_hd'];
                    $ot_2_night_hd += $value1['night_hd'];

                    $ot_2_day_tv += $value1['ot_tv'];
                    $ot_2_night_tv += $value1['night_tv'];
                }
                if ($value1['type_ot'] == 3) {
                    $ot_3_day_hd += $value1['ot_hd'];
                    $ot_3_night_hd += $value1['night_hd'];

                    $ot_3_day_tv += $value1['ot_tv'];
                    $ot_3_night_tv += $value1['night_tv'];
                }
            }

            
        }
       
        $typeOtHd = [
            1 => $ot_1_day_hd ?? 0,
            2 => $ot_2_day_hd ?? 0,
            3 => $ot_3_day_hd ?? 0,
            4 => $ot_2_night_hd ?? 0,
            5 => $ot_1_hours_night_not_day_hd ?? 0,
            6 => $ot_3_night_hd ?? 0,
            7 => $ot_1_hours_night_have_day_hd ?? 0
        ];

        $typeOtTv = [
            1 => $ot_1_day_tv ?? 0,
            2 => $ot_2_day_tv ?? 0,
            3 => $ot_3_day_tv ?? 0,
            4 => $ot_2_night_tv ?? 0,
            5 => $ot_1_hours_night_not_day_tv ?? 0,
            6 => $ot_3_night_tv ?? 0,
            7 => $ot_1_hours_night_have_day_tv ?? 0
        ];
        
        foreach ($typeOtTv as $kkk => $otTv) {
            $config_ot = DB::table('config_ot')->where('type', $kkk)->first();
            if ($config_ot) $level_config_tv += (($config_ot->value - 100) / 100) * $otTv;
        }
        foreach ($typeOtHd as $kkk => $otHd) {
            $config_ot = DB::table('config_ot')->where('type', $kkk)->first();
            if ($config_ot) $level_config_hd += (($config_ot->value - 100) / 100) * $otHd;
        }

        $salary_ot_non_tax = ((($basic_salary_hd + $phucapOtMienThue) / $coefficient_ot / $detail['total_day_request']) * $level_config_hd) + (($basic_salary_tv / $coefficient_ot / $detail['total_day_request'] * $level_config_tv));

        return $salary_ot_non_tax;
    }

    public function createBulk()
    {
        return view('backend.payroll.create-bulk');
    }

    public function download()
    {
        $file = public_path() . "/assets/media/files/templates/template_luong_lai_xe1.xlsx";
        $headers = [
            'Content-Type: application/xls',
        ];
        return response()->download($file, 'luong-lai-xe' . time() . '.xlsx', $headers);
    }

    public function readBulk(Request $request)
    {
        ini_set('memory_limit', '4096M');
        $response = ['message' => trans('system.have_an_error')];
        $statusCode = 200;
        if ($request->ajax()) {
            try {
                $file = $request->file;
                switch ($file->getClientOriginalExtension()) {
                    case 'xlsx':
                        $data = \Excel::toArray(new \App\Imports\PayrollsImport, $file);
                        if ($data) $data = $data[0];
                        
                        $response['message'] = view('backend.payroll.excel_im', compact('data'))->render();
                        break;
                    default:
                        throw new \Exception("Không hỗ trợ định dạng", 1);
                }
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

    public function saveBulk(Request $request)
    {
        $response = ['message' => trans('system.have_an_error')];
        $statusCode = 200;
        if ($request->ajax()) {
            try {
                $data = $request->data;
                $month = $request->month;
                $year = $request->year;
              
                
                if (!is_array($data) || count($data) == 0) {
                    $statusCode = 400;
                    throw new \Exception(trans('system.have_an_error'), 1);
                }
                unset($data[0]);
                $data = array_values($data);

                foreach ($data as $k => $d) {

                    $code = trim($d[2]);
                    $user = User::where('code', $code)->first();
                    // $check = PayrollUser::where('user_id', $user->id)->where('month', $month)->where('year', $year)->first();

                    // if (!is_null($check)) {
                    //     throw new \Exception("Nhân viên đã tạo lương khoán tại dòng số " . (($d[0] - 1) ?? "") . "");
                    // }

                    if ($code != $user->code) {
                        throw new \Exception("Kiểm tra lại Mã nhân viên tại dòng số " . (($d[0] - 1) ?? "") . "");
                    }

                    $basic_salary_tv = str_replace(',', '',trim($d[15]));
                    $basic_salary_hd = str_replace(',', '',trim($d[16]));
                    $salary_bh = str_replace(',', '',trim($d[17]));

                    $working_salary_tax = str_replace(',', '',trim($d[18]));
                    $working_salary_non_tax = str_replace(',', '',trim($d[19]));
                    $salary_ot_non_tax = str_replace(',', '',trim($d[20]));
                    $salary_ot_tax = str_replace(',', '',trim($d[21]));
                    $total_salary = str_replace(',', '',trim($d[33]));

                    $bhxh_user = str_replace(',', '',trim($d[34]));
                    $bhyt_user = str_replace(',', '',trim($d[35]));
                    $union_user = str_replace(',', '',trim($d[36]));
                    $bhtn_user = str_replace(',', '',trim($d[37]));
                    $bhxh_company = str_replace(',', '',trim($d[38]));
                    $bhyt_company = str_replace(',', '',trim($d[39]));
                    $union_company = str_replace(',', '',trim($d[40]));
                    $bhtn_company = str_replace(',', '',trim($d[41]));

                    $income_taxes = str_replace(',', '',trim($d[47]));
                    $family_allowances = str_replace(',', '',trim($d[49]));
                    $taxable_income  = str_replace(',', '',trim($d[50]));
                    $personal_income_tax   = str_replace(',', '',trim($d[51]));
                    $total_real_salary    = str_replace(',', '',trim($d[54]));

                    
                    $bhxh = [
                        'bhxh_user'     => $bhxh_user,
                        'bhyt_user'     => $bhyt_user,
                        'bhtn_user'     => $bhtn_user,
                        'union_user'    => $union_user,
                        'bhxh_company'  => $bhxh_company,
                        'bhyt_company'  => $bhyt_company,
                        'bhtn_company'  => $bhtn_company,
                        'union_company' => $union_company
                    ];
                    

                    $insert[] = [
                        'total_salary'              => intval($total_salary),
                        'total_real_salary'         => round($total_real_salary),
                        'basic_salary'              => $basic_salary_hd,
                        'salary_bh'                 => intval($salary_bh),
                        'working_salary_non_tax'    => intval($working_salary_non_tax),
                        'working_salary_tax'        => intval($working_salary_tax),
                        'salary_ot_non_tax'         => intval($salary_ot_non_tax),
                        'salary_ot_tax'             => intval($salary_ot_tax),
                        'bh'                        => json_encode($bhxh),
                        'income_taxes'              => intval($income_taxes),
                        'taxable_income'            => intval($taxable_income),
                        'personal_income_tax'       => intval($personal_income_tax),
                        'family_allowances'         => intval($family_allowances),
                        'user_id'                   => $user->id,
                        'basic_salary_tv'           => $basic_salary_tv,
                        'basic_salary_hd'           => $basic_salary_hd,
                        'month'                     => $month,
                        'year'                      => $year
                    ];
                    
                }

                foreach ($insert as $item) {
                    PayrollUser::insert($item);
                }
                
                $response['message'] = trans('system.success');
                Session::flash('message', $response['message']);
                Session::flash('alert-class', 'success');

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

    public function bh(Request $request, $id)
    {
        if ($request->ajax()) {
            $data = $request->all();

            $payroll_user = PayrollUser::find(intval($id));
            if (is_null($payroll_user)) {
                return response()->json(['status' => 400, 'message' => 'Có lỗi']);
            }

            $total_real_salary = $taxable_income = 0;
            $taxable_income = $payroll_user->income_taxes - $data['bhxh_user'] - $data['bhyt_user'] - $data['bhtn_user'] - $payroll_user->family_allowances;
            if ($taxable_income < 0) $taxable_income = 0; 

            $personal_income_tax = $this->tntt($taxable_income);

            $bhxh = [
                'bhxh_user'     => $data['bhxh_user'],
                'bhyt_user'     => $data['bhyt_user'],
                'bhtn_user'     => $data['bhtn_user'],
                'union_user'    => $data['union_user'],
                'bhxh_company'  => $data['bhxh_company'],
                'bhyt_company'  => $data['bhyt_company'],
                'bhtn_company'  => $data['bhtn_company'],
                'union_company' => $data['union_company']
            ];

            $total_real_salary = $payroll_user->total_salary + $payroll_user->total_payoff - $payroll_user->total_deduction - $personal_income_tax - $data['bhxh_user'] - $data['bhyt_user'] - $data['bhtn_user'] - $data['union_user'];
            $payroll_user->taxable_income = $taxable_income;
            $payroll_user->personal_income_tax = $personal_income_tax;
            $payroll_user->bh = json_encode($bhxh);
            $payroll_user->total_real_salary = $total_real_salary;
            
            try {
                $payroll_user->save();

                return response()->json(['status' => 200, 'message' => 'Sửa bảo hiểm thành công']);

            } catch (Exception $e) {
                return response()->json(['status' => 400, 'message' => $e->getMessage()]);
            }
        }
    }

    public function thue(Request $request, $id)
    {
        if ($request->ajax()) {
            $data = $request->all();
            $payroll_user = PayrollUser::find(intval($id));

            if (is_null($payroll_user)) {
                return response()->json(['status' => 400, 'message' => 'Có lỗi']);
            }
            if ($payroll_user->income_taxes == $data['income_taxes']) {
                return response()->json(['status' => 400, 'message' => 'Thu nhập chịu thuế không thay đổi']);
            }

            try {
                $bh = json_decode($payroll_user->bh, true);
                $taxable_income = $data['income_taxes'] - $payroll_user->family_allowances - $bh['bhxh_user'] - $bh['bhyt_user'] - $bh['bhtn_user'];
                if ($taxable_income < 0) $taxable_income = 0;
                $personal_income_tax = $this->tntt($taxable_income);
                if ($personal_income_tax < 0) $personal_income_tax = 0;
                $total_real_salary = $payroll_user->total_salary + $payroll_user->total_payoff - $payroll_user->total_deduction - $personal_income_tax - $bh['bhxh_user'] - $bh['bhyt_user'] - $bh['bhtn_user'] - $bh['union_user'];

                $payroll_user->income_taxes = max($data['income_taxes'],0);
                $payroll_user->taxable_income = max($taxable_income,0);
                $payroll_user->personal_income_tax = max($personal_income_tax, 0);
                $payroll_user->total_real_salary = $total_real_salary;

                $payroll_user->save();
                return response()->json(['status' => 200, 'message' => 'Sửa bảo hiểm thành công']);

            } catch (Exception $e) {
                return response()->json(['status' => 400, 'message' => $e->getMessage()]);
            }
            
        }
    }

    public function salaryUser()
    {
        $payroll_detail = PayrollUser::orderBy('salary_concurrent', 'ASC')->with('payroll', 'timekeepingDetail')->where('user_id', Auth::user()->id)->whereHas('payroll', function ($q) {
            $q->where('status', 'APPROVED');
        })->get()->sortByDesc('payroll.month')->sortByDesc('payroll.year');

        $salary_drives = SalaryDriveDetail::where('user_id', Auth::user()->id)->with('salaryDrive')->whereHas('salaryDrive', function ($q) {
            $q->where('approved', 1);
        })->get()->sortByDesc('salaryDrive.month')->sortByDesc('salaryDrive.year');
        $isNV = false;
        if (Auth::user()->hasRole('NV')) {
            $isNV = true;
        }
        return view('backend.payroll.user', compact('isNV', 'payroll_detail', 'salary_drives'));
    }

    public function countTotalInMonthForTimeKeeping($concurrent, $month = null, $year = null, $department_id = null)
    {
        $contract = ConcurrentContract::where('user_id', $concurrent->user_id)->where('department_id', $department_id)->orderBy('id', 'DESC')->first();

        $beforeDate = Carbon::createFromDate($year, $month - 1, 26)->format('Y-m-d');
        $afterDate = Carbon::createFromDate($year, $month, 25)->format('Y-m-d');
        if (!is_null($contract)) {

            if (strtotime($contract->valid_from) > strtotime($beforeDate)) {
                $beforeDate = date('Y-m-d', strtotime($contract->valid_from));
            }
        }

        $departmentId = User::find($concurrent->user_id)->department_id;
        $holidays = CalendarDepartment::countHolidays($departmentId, $beforeDate, $afterDate);
        $temp = StaffDayOff::where('user_id', $concurrent->user_id)
            ->where('start', '<=', $afterDate)
            ->where('end', '>=', $beforeDate)
            ->where('status', 1)
            ->get();
        if (!count($temp)) return ['L' => 0, 'D' => 0, 'W' => 0, 'H' => $holidays];
        $dayOffs = StaffDayOff::where('user_id', $concurrent->user_id)
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
}
