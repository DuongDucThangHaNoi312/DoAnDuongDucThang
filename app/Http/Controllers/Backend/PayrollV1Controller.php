<?php

namespace App\Http\Controllers\Backend;

use App\Define\Constant;
use App\Defines\Schedule;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AllowanceCategory;
use App\Models\ConcurrentContract;
use App\Models\Contract;
use App\Models\Deduction;
use App\Models\OverTimes;
use App\Models\PayOff;
use App\Models\Payroll;
use App\Models\PayrollUser;
use App\Models\TimeKeeping;
use App\Models\TimeKeepingDetail;
use App\Models\UnionFund;
use App\Target;
use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class PayrollV1Controller extends Controller
{
    public function store(Request $request) // dùng tính lương
    {
        try {
            $data = $request->all();
            $checkTimekeeping = TimeKeeping::where('company_id', $data['company_id'])
                ->where('department_id', $data['department_id'])
                ->where('month', $data['month'])
                ->where('year', $data['year'])
                ->where('version', 1)
                ->first();

            if (is_null($checkTimekeeping))
                throw new \Exception('Lỗi không tìm thấy bảng chấm công');

            if ($checkTimekeeping->version != 1) {
                throw new \Exception('Processing ver2...');
            }

            $payroll = Payroll::where('department_id', $data['department_id'])
                ->where('month', $data['month'])
                ->where('year', $data['year'])
                ->first();

            if (!is_null($payroll))
                throw new \Exception('Bảng lương đã tồn tại');

            DB::beginTransaction();

            $data['created_by'] = $request->user()->id;
            $data['version'] = 1;
            $payroll = Payroll::create($data);
            // $payroll = 1;
            $this->calculate($request, $data, $payroll, $checkTimekeeping);

            DB::commit();
            Session::flash('message', 'Thêm mới bảng lương thành công');
            Session::flash('alert-class', 'success');
            return redirect()->route('admin.payroll.detail', $payroll->id);
        } catch (Exception $e) {
            DB::rollBack();
            Session::flash('message', $e->getMessage());
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.payrolls.index');
        }
    }

    public function calculate(Request $request, $data, $payroll, $checkTimekeeping)
    {
        $response = [];
        $coefficient_ot = 8; // hệ số tính ot
        $config_time_ot = DB::table('config_time_ot')->where('department_id', $data['department_id'])->first();
        if (!is_null($config_time_ot)) $coefficient_ot = $config_time_ot->coefficient;
        $month_kpi = $data['month'] - 1;
        $year_kpi = $data['year'];
        if ($month_kpi == 0) {
            $month_kpi = 12;
            $year_kpi = $data['year'] - 1;
        }
        $start = date('Y-m-d', strtotime($data['year'] . '-' . (($data['month'] - 1)) . '-' . 26));
        $end = date('Y-m-d', strtotime($data['year'] . '-' . $data['month'] . '-' . 26));
        $endReal = date('Y-m-d', strtotime($data['year'] . '-' . $data['month'] . '-' .Schedule::DATE_END_SALARY));

        $config_insurrance = DB::table('config_insurrance')->where('company_id', $data['company_id'])->first();
        $config_food_allowances = DB::table('config_food_allowances')->where('company_id', $data['company_id'])->first();

        $userIds = TimeKeepingDetail::where('timekeeping_id', $checkTimekeeping->id)
            ->pluck('staff_id')
            ->toArray();
        $groupContracts = Contract::with('allowances')
            ->whereIn('user_id', $userIds)
            ->where('department_id', $data['department_id'])
            ->where('type_status', '<>', \App\Defines\Contract::END_PART_TIME)
            ->whereDate('valid_from', '<', $end)
            ->orderBy('id')
            ->get(['id', 'valid_from', 'user_id', 'basic_salary', 'is_main', 'type_status', 'set_notvalid_on'])
            ->groupBy('user_id');

        $partTimeNotValidContracts = Contract::where('type_status', \App\Defines\Contract::END_PART_TIME)
            ->whereDate('set_notvalid_on', '<=', $end)
            ->whereDate('set_notvalid_on', '>', $start)
            ->whereIn('user_id', $userIds)
            ->get()
            ->keyBy('user_id');

        //dd($contracts->groupBy('user_id')->toArray());
        // lay nhung hop dong con lai la trang thai cho nghi viec nhung ngay hieu luc van o trong thang
        //$other_contracts = Contract::with('allowances')->whereIn('user_id', $userIds)->where('type_status', 6)->whereDate('valid_from', '<=', $end)->whereDate('set_notvalid_on', '>=', $end)->get(['id', 'valid_from', 'user_id', 'basic_salary', 'is_main', 'type_status', 'set_notvalid_on']);
        //$contracts = $contracts->merge($other_contracts);
        $listContracts = [];
        /*foreach ($contracts as $key => $item) {
            if (is_null($item->set_notvalid_on)) {
                $listContracts[$item->user_id] = $key;
                continue;
            }
            if (date('Y-m-d', strtotime($item->set_notvalid_on)) <= $start)
                unset($contracts[$key]);
            else
                $listContracts[$item->user_id] = $key;
        }*/
        foreach ($groupContracts as $userId => $items) {
            if (count($items) == 2) {
                $checkArr = Contract::isSameTwoContracts($items);
                if ($checkArr['is_same']) {
                    $groupContracts[$userId][0]->valid_from = $checkArr['min'];
                    $groupContracts[$userId][0]->set_notvalid_on = $checkArr['max'];
                    unset($groupContracts[$userId][1]);
                }
            }
            foreach ($items as $key => $item) {
                if (is_null($item->set_notvalid_on)) {
                    $listContracts[$item->user_id] = $key;
                    continue;
                }
                if (date('Y-m-d', strtotime($item->set_notvalid_on)) <= $start)
                    unset($groupContracts[$userId][$key]);
                else
                    $listContracts[$item->user_id] = $key;
            }

        }
        foreach ($partTimeNotValidContracts as $userId => $item) {
            if (!$listContracts[$userId]) { // Nếu trong tháng chỉ có duy nhất hđ thử việc thì cho hđ này vào contracts
                $groupContracts->push($item);
                unset($partTimeNotValidContracts[$userId]);
            }
        }
        if (count($groupContracts) > 0) {
            foreach ($groupContracts as $uId => $contracts) {
                foreach ($contracts as $key => $contract) {
                    $calculateAllowance = $bhxh = [];

                    $total_salary = $total_real_salary = $food_allowance_nonTax = $food_allowance_tax = $total_allowances = $salary_bh = $basic_salary_tv = $basic_salary_hd = $basic_salary = 0;
                    $working_salary_non_tax = $working_salary_tax = $salary_ot_non_tax = $salary_ot_tax = $salary_concurrent = 0;
                    $personal_income_tax = $family_allowances = $income_taxes = $taxable_income = $insurance_premiums = $level_config_tv = $level_config_hd = $total_deduction = 0;
                    $expense = $expense1 = $allowanceTargetByWorking = $allowanceByWorking = $allowanceTarget = $phuCapOtChiuThue = $phucapOtMienThue = 0;
                    $total_payoff = $total_payoff_tax = $total_deduction_tax = $total_deduction = $total_deduction_non_tax = 0;
                    $total_impale = $an_phu = $an_chinh = 0;
                    $allowanceByWorking  = $allowanceTargetByWorking = $allowanceTarget = $dataAllowance = 0;

                    $target = Target::where('user_id', $contract->user_id)->where('month', $month_kpi)->where('year', $year_kpi)->orderBy('id', 'DESC')->first();
                    if (is_null($target)) $target = 100;
                    else $target = $target->kpi;

                    $timekeeping = new TimeKeepingV1Controller();
                    //$timekeepingDetail = $timekeeping->detail($request, $checkTimekeeping->id);
                    //lương cơ bản hợp đồng, thử việc
                    //$data_contracts = Contract::getContractsInAMonth($contract->user_id, $data['month'], $data['year']);
                    /*if (0&&count($data_contracts) >= 2) {
                        foreach ($data_contracts as $d => $data_contract) {
                            $luong = Contract::find($data_contract['id']);
                            if ($luong->valid_from < $end) {
                                if ($data_contract['is_main'] == 1) $basic_salary_tv = $luong->basic_salary;
                                if ($data_contract['is_main'] == 2) $basic_salary_hd = $luong->basic_salary;
                                $contract = $luong;
                            }

                        }
                    } else {
                        if ($contract->is_main == 1) $basic_salary_tv = $contract->basic_salary;
                        if ($contract->is_main == 2) $basic_salary_hd = $contract->basic_salary;
                    }*/
                    if ($contract->is_main == 1) $basic_salary_tv = $contract->basic_salary;
                    elseif ($contract->is_main == 2) $basic_salary_hd = $contract->basic_salary;
                    if ($partTimeNotValidContracts[$contract->user_id]) $basic_salary_tv = $partTimeNotValidContracts[$contract->user_id]['basic_salary'];

                    $insurance_premiums = $contract->insurancePremiums->sum('pivot.expense'); // Các khoản Phụ cấp đóng bảo hiểm
                    $salary_bh = $basic_salary_hd + $insurance_premiums; //Lương đóng bảo hiểm

                    $non_tax_allowances = $contract->nonTaxAllowances->sum('pivot.expense'); //phụ cấp miễn thuế
                    $tax_allowances = $contract->taxableAllowances->sum('pivot.expense'); //phụ cấp chịu thuế

                    $dataFoodAllowances  = $contract->foodAllowance->where('id', 1)->first();
                    $food_allowance = $dataFoodAllowances->pivot->expense; // phụ cấp ăn

                    $phuCapOtChiuThue = $contract->phuCapOtChiuThue->sum('pivot.expense'); //Phụ cấp Ot chịu thuế
                    $phucapOtMienThue = $contract->phucapOtMienThue->sum('pivot.expense'); //Phụ cấp Ot miễn thuế

                    $phuCapKhongTinhThue = $contract->phuCapKhongTinhThue->sum('pivot.expense'); //Phụ cấp không tính thuế

                    $request->export = 1;
                    $startDate = $partTimeNotValidContracts[$contract->user_id] ? $start : $contract->valid_from->format('Y-m-d');
                    $endDate = $contract->set_notvalid_on ? date('Y-m-d', strtotime($contract->set_notvalid_on)) : $end;
                    $request->request->add([
                        'contract_id' => $contract->id ,
                        'start_date' => $startDate,
                        'end_date' => $contract->set_notvalid_on ? date('Y-m-d', strtotime($contract->set_notvalid_on)) : $end,
                        'start_date_main' => $contract->valid_from->format('Y-m-d'),
                        'is_main' => $contract->is_main,
                        'has_parttime' => !!$partTimeNotValidContracts[$contract->user_id]
                    ]);
                    //$timekeeping = new TimeKeepingV1Controller();
                    $timekeepingDetail = $timekeeping->detail($request, $checkTimekeeping->id);
                    $cong = $timekeepingDetail['items']->where('staff_id', $contract->user_id)->first();
                    $startDateTz = strtotime($startDate. ' 00:00:00');
                    $endDateTz = strtotime($endDate. ' 00:00:00');
                    $tmpCong = [];
                    if ($contract->is_main != 1 && !$partTimeNotValidContracts[$contract->user_id]) {
                        foreach ($cong['detail'] as $k => $v) {
                            if ($k >= $startDateTz && $k < $endDateTz) {
                                $tmpCong[$k] = $v;
                            }
                        }
                        $cong->detail = $tmpCong;
                    }
                    // tổng số công làm chính thức
                    // $full_cong_hd = $cong['total']; //sai nhe
                    $nghiHuongLuong = $cong['nghi_phep'] + $cong['nghi_cuoi'] + $cong['nghi_hieu'] + $cong['nghi_le_hd'] + $cong['lam_tai_nha'];
                    $full_cong_hd = $cong['total_hd'] + $nghiHuongLuong + $cong['nghi_cong_tac'] + $cong['nghi_70_luong'];

                    $calculateAllowance = $this->calculateAllowance($contract, $target, $cong, $full_cong_hd); // tính các khoản phụ cấp
                    $timekeepingDetailOt = $timekeeping->otDetail($request, $checkTimekeeping->id);
                    $cong_ot = $timekeepingDetailOt['items']->where('staff_id', $contract->user_id)->first();
                    //phụ cấp ăn
                    if ($food_allowance == 25000) { //mức 25k 1 bữa
                        //$otDetail = $timekeeping->otDetail($request, $checkTimekeeping->id);
                        //$otDetail = $otDetail['items']->where('staff_id', $contract->user_id)->first();

                        //phụ cấp ăn miễn thuế ca
                        $a_food_allowance_nonTax = $cong_ot['an_chinh'] * 25000 + $cong_ot['an_phu'] * 15000;
                        if ($a_food_allowance_nonTax > $config_food_allowances->money) {
                            $food_allowance_nonTax = $config_food_allowances->money;
                        } else {
                            $food_allowance_nonTax = $a_food_allowance_nonTax;
                        }

                        //phụ cấp ăn chịu thuế
                        $food_allowance_tax = $a_food_allowance_nonTax - $food_allowance_nonTax;
                    } else {
                        //phụ cấp ăn miễn thuế
                        $a_food_allowance_nonTax = ($full_cong_hd / $cong['total_day_request'] * $food_allowance);
                        if ($a_food_allowance_nonTax > $config_food_allowances->money) {
                            $food_allowance_nonTax = $config_food_allowances->money;
                        } else {
                            $food_allowance_nonTax = $a_food_allowance_nonTax;
                        }

                        //phụ cấp ăn chịu thuế
                        $food_allowance_tax = max(($a_food_allowance_nonTax - $food_allowance_nonTax), 0);
                    }

                    //lương làm việc thực tế miễn thuế, tính cho ca
                    $working_salary_non_tax = (($cong['dem_tv'] * $basic_salary_tv) + (($cong['dem_hd'] * $basic_salary_hd))) * ((30 / 100) / $cong['total_day_request']);

                    //lương làm việc thực tế chịu thuế
                    $full_pay_leave =  $cong['nghi_70_luong'] * 0.7;
                    if ($contract->is_main == 1)
                        $working_salary_tax = (($cong['total_tv'] + $cong['nghi_le_tv'] + $cong['nghi_cong_tac']) * ($basic_salary_tv / $cong['total_day_request']));
                    else
                        $working_salary_tax = (($cong['total_tv'] + $cong['nghi_le_tv']) * ($basic_salary_tv / $cong['total_day_request'])) + (($full_cong_hd + $full_pay_leave) * ($basic_salary_hd / $cong['total_day_request']));
                    //lương ot chịu thuế
                    //$timekeepingDetailOt = $timekeeping->otDetail($request, $checkTimekeeping->id);
                    //$cong_ot = $timekeepingDetailOt['items']->where('staff_id', $contract->user_id)->first();
                    $salary_ot_tax = (($basic_salary_hd + $phuCapOtChiuThue) / $cong['total_day_request'] / $coefficient_ot * $cong_ot['total_ot_hd']) + ($cong_ot['total_ot_tv'] * ($basic_salary_tv / $cong['total_day_request'] / $coefficient_ot));

                    //lương ot miễn thuế
                    $salary_ot_non_tax = $this->tinhOt($cong, $level_config_tv, $level_config_hd, $basic_salary_hd, $phucapOtMienThue, $basic_salary_tv, $coefficient_ot, $data);

                    //Đóng bảo hiểm
                    $kinhPhiCongDoan = UnionFund::where('user_id', $contract->user_id)
                        ->whereNull('deleted_at')
                        ->where('start', '<', $start)
                        ->first();
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
                        $checkCong = $full_cong_hd + $cong['nghi_70_luong'];
                        if ($checkCong >= 14) { // check làm đủ 14 ngày mới đóng bảo hiểm
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
                            if ($cong['nghi_om'] >= 14) {
                                if (!empty($config_insurrance)) {
                                    if ($salary_bh > $config_insurrance->money) {
                                        $bhyt_user = $config_insurrance->money * ($config_insurrance->bhyt_user / 100);
                                        $bhyt_company = $config_insurrance->money * ($config_insurrance->bhyt_company / 100);
                                    } else {
                                        $bhyt_user = $salary_bh * ($config_insurrance->bhyt_user / 100);
                                        $bhyt_company = $salary_bh * ($config_insurrance->bhyt_company / 100);
                                    }
                                }
                            }

                            $ngay_tinh_bhyt = 0;

                            if ($contract->type_status == 7) {
                                if (!is_null($contract->set_notvalid_on)) {
                                    $month = date('m', strtotime($contract->set_notvalid_on));
                                    $year = date('Y', strtotime($contract->set_notvalid_on));

                                    if ($month == $data['month'] && $year == $data['year']) {
                                        for ($i = 26; $i <= 31; $i++) {
                                            $d = $i . '-' . ($data['month'] - 1) . '-' . $data['year'];
                                            $date = date('Y-m-d', strtotime($d));
                                            if ($cong->detail[strtotime($date)]['total'] == 1 && $cong->detail[strtotime($date)]['contract_type'] == 'HOP_DONG') {
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

                    //tổng thu nhập
                    $total_allowances = array_sum($calculateAllowance);
                    $total_salary = $working_salary_non_tax + $working_salary_tax + $food_allowance_nonTax + $food_allowance_tax + $total_allowances + $salary_ot_non_tax + $salary_ot_tax;
                    //giảm trừ gia cảnh
                    $dependent_person = User::countUserRelationship($contract->user_id);
                    $family_allowances = 11000000 + 4400000 * $dependent_person;

                    //các khoản tăng
                    $total_payoff = $total_payoff_tax = $total_payoff_non_tax =  0;
                    $payoffs = PayOff::where('user_id', $contract->user_id)
                        ->where('year', $data['year'])
                        ->where('month', $data['month'])
                        ->where('department_id', $data['department_id'])
                        ->get();
                    $total_payoff_tax = $payoffs->where('type', 'CHIU_THUE')->sum('amount_money_tax');
                    $total_payoff_non_tax = $payoffs->where('type', 'MIEN_THUE')->sum('amount_money_non_tax');
                    $total_payoff = $total_payoff_tax + $total_payoff_non_tax;
                    // if (count($payoffs) > 0) {
                    //     foreach ($payoffs as $payoff) {
                    //         $total_payoff_tax += $payoff->amount_money_tax;
                    //         $total_payoff_non_tax += $payoff->amount_money_non_tax;
                    //     }
                    //     $total_payoff = $total_payoff_tax + $total_payoff_non_tax;
                    // } else {
                    //     $total_payoff = $total_payoff_tax = $total_payoff_non_tax = 0;
                    // }

                    //các khoản giảm trừ
                    $deductions = Deduction::where('user_id', $contract->user_id)
                        ->where('year', $data['year'])
                        ->where('department_id', $data['department_id'])
                        ->get();
                    if (count($deductions) > 0) {
                        foreach ($deductions as $d => $deduction) {
                            if (in_array($data['month'], explode(', ', $deduction->month))) {
                                $total_deduction = $deduction->detailDeduction->sum('money'); // các khoản giảm trừ khác
                                $total_deduction_tax = $deduction->totalTax->sum('money'); // các khoản giảm trừ khác chịu thuế
                            }
                        }
                        $total_deduction_non_tax = $total_deduction - $total_deduction_tax;
                    } else {
                        $total_deduction_tax = $total_deduction = $total_deduction_non_tax = 0;
                    }

                    //Thu nhập chịu thuế
                    if (in_array($contract->user_id, Payroll::koTinhThue())) {
                        $income_taxes = 0;
                    } else {
                        $income_taxes = $total_salary - $salary_ot_non_tax - $food_allowance_nonTax - $working_salary_non_tax - (($phuCapKhongTinhThue / $cong['total_day_request']) * ($full_cong_hd)) + $total_payoff_tax - $total_deduction_tax;
                    }
                    //Thu nhập tính thuế
                    $taxable_income = max(($income_taxes - $bhxh_user - $bhyt_user - $bhtn_user - $family_allowances), 0);

                    //thuế thu nhập cá nhân
                    $personal_income_tax = $this->tntt($taxable_income);
                    $personal_income_tax < 0 ? $personal_income_tax = 0 : $personal_income_tax;

                    //tổng thực nhận
                    $total_real_salary = $total_salary + $total_payoff - $bhxh_user - $bhyt_user - $bhtn_user - $union_user - $personal_income_tax - $total_deduction;

                    $response[] = [
                        'total_salary'              => max(Constant::roundNumber($total_salary), 0),
                        'total_real_salary'         => max(Constant::roundNumber($total_real_salary), 0),
                        'food_allowance_nonTax'     => max(intval($food_allowance_nonTax), 0),
                        'food_allowance_tax'        => max(intval($food_allowance_tax), 0),
                        'total_allowances'          => max(intval($total_allowances), 0),
                        'basic_salary'              => $basic_salary,
                        'salary_bh'                 => max(intval($salary_bh), 0),
                        'working_salary_non_tax'    => max(Constant::roundNumber($working_salary_non_tax), 0),
                        'working_salary_tax'        => max(Constant::roundNumber($working_salary_tax), 0),
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
                        'allowances_ids'            => null,
                        'basic_salary_tv'           => $basic_salary_tv,
                        'basic_salary_hd'           => $basic_salary_hd,
                        'allowances_ids1'           => null,
                        'total_payoff'              => max(intval($total_payoff), 0),
                        'total_payoff_tax'          => intval($total_payoff_tax),
                        'total_payoff_non_tax'      => intval($total_payoff_non_tax),
                        'total_deduction'           => intval($total_deduction),
                        'total_deduction_tax'       => intval($total_deduction_tax),
                        'total_deduction_non_tax'   => intval($total_deduction_non_tax),
                        'total_impale'              => max(intval($total_impale), 0),
                        'contract_id'               => $contract->id,
                        'timekeeping_id'            => $cong->id,
                        'calculateAllowance'        => json_encode($calculateAllowance),
                        'tong_hop'                  => $cong->tong_hop
                    ];
                }
            }
        }

        $concurrents = ConcurrentContract::with('contract')
            ->where('department_id', $data['department_id'])
            ->where('status', 1)
            ->where('valid_from', '<', $end)
            ->where('valid_to', '>=', $start)
            ->get();

        if (count($concurrents) > 0) {
            foreach ($concurrents as $key => $concurrent) {
                $calculateAllowance = $bhxh = [];
                $target = Target::where('user_id', $concurrent->user_id)
                    ->where('month', $month_kpi)
                    ->where('year', $year_kpi)
                    ->orderBy('id', 'DESC')
                    ->first();
                if (is_null($target)) {
                    $target = 100;
                } else {
                    $target = $target->kpi;
                }

                $request->export = 1;
                $timekeeping = new TimeKeepingV1Controller();

                $checkTimekeeping1 = TimeKeeping::where('month', $data['month'])
                    ->where('year', $data['year'])
                    ->where('department_id', $concurrent->contract->department_id)
                    ->where('version', 1)
                    ->whereHas('timeKeepingDetail', function ($q) use ($concurrent) {
                        $q->where('staff_id', $concurrent->user_id);
                    })->first();

                if (is_null($checkTimekeeping1)) continue;
                $request->request->add(['start_date' => $concurrent->valid_from->format('Y-m-d'), 'end_date' => $concurrent->valid_to->format('Y-m-d') ]);

                $timekeepingDetail = $timekeeping->detail($request, $checkTimekeeping1->id);
                $cong = $timekeepingDetail['items']->where('staff_id', $concurrent->user_id)->first();

                //đoạn này chưa hiểu lắm
                if ($start < date('Y-m-d', strtotime($concurrent->valid_from)) && date('Y-m-d', strtotime($concurrent->valid_from)) < $end) {
                    $array_new = $array_old = [];
                    foreach ($cong->detail as $kDe => $de) {
                        if ($kDe < strtotime($concurrent->valid_from)) continue;
                        $array_new[$kDe] = $de;
                    }

                    $basic_salary_hd =  $concurrent->salary;

                    $basic_salary_hd_new = $basic_salary_hd_old = 0;
                    $basic_salary_hd_new = $concurrent->salary;
                    $concurrent_old = ConcurrentContract::where('user_id', $concurrent->user_id)->where('department_id', $data['department_id'])->where('status', 0)->orderBy('id', 'DESC')->first();

                    if (!is_null($concurrent_old)) {
                        $basic_salary_hd_old = $concurrent_old->salary;
                        foreach ($cong->detail as $kDe => $de) {
                            if ($kDe >= strtotime($concurrent->valid_from)) continue;
                            $array_old[$kDe] = $de;
                        }
                    }

                    $total_hd_new = $total_tv_new = $total_hd_old = $total_tv_old = 0;

                    if (count($array_new)) {
                        $total_hd_new = array_sum(array_column($array_new, 'total'));
                    }

                    if (count($array_old)) {
                        $total_hd_old = array_sum(array_column($array_old, 'total'));
                    }

                    $salary_concurrent = ($basic_salary_hd_new * ($total_hd_new / $cong['total_day_request']) * ($target / 100)) + ($basic_salary_hd_old * ($total_hd_old / $cong['total_day_request']) * ($target / 100));
                } else {
                    $basic_salary_hd =  $concurrent->salary;
                    $salary_concurrent = $basic_salary_hd * ($cong['total'] / $cong['total_day_request']) * ($target / 100);
                }

                $total_payoff = $total_payoff_tax = $total_payoff_non_tax = $taxable_income = $personal_income_tax = $income_taxes = 0;

                //các khoản tăng
                $payoffs = PayOff::where('user_id', $concurrent->user_id)->where('department_id', $concurrent->department_id)
                    ->where('year', $data['year'])->where('month', $data['month'])
                    ->get();

                $total_payoff_tax = $payoffs->where('type', 'CHIU_THUE')->sum('amount_money_tax');
                $total_payoff_non_tax = $payoffs->where('type', 'MIEN_THUE')->sum('amount_money_non_tax');
                $total_payoff = $total_payoff_non_tax + $total_payoff_tax;

                //các khoản giảm trừ
                $deductions = Deduction::where('user_id', $concurrent->user_id)->where('year', $data['year'])->where('department_id', $data['department_id'])->get();
                if (count($deductions) > 0) {
                    foreach ($deductions as $d => $deduction) {
                        if (in_array($data['month'], explode(', ', $deduction->month))) {
                            $total_deduction = $deduction->detailDeduction->sum('money'); // các khoản giảm trừ khác
                            $total_deduction_tax = $deduction->totalTax->sum('money'); // các khoản giảm trừ khác chịu thuế
                        }
                    }
                    $total_deduction_non_tax = $total_deduction - $total_deduction_tax;
                } else {
                    $total_deduction_tax = $total_deduction = $total_deduction_non_tax = 0;
                }

                if ($salary_concurrent > 0)  $income_taxes = $taxable_income = $salary_concurrent + $total_payoff_tax - $total_deduction_tax;

                if ($taxable_income > 0) {
                    $personal_income_tax = $this->tntt($taxable_income);
                    $personal_income_tax = $personal_income_tax < 0 ? 0 : $personal_income_tax;
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

                $total_real_salary = $salary_concurrent + $total_payoff - $personal_income_tax - $total_deduction_tax;

                if ($cong['total'] == 0) {
                    $salary_concurrent = $total_real_salary = $salary_concurrent = $basic_salary_hd = 0;
                    $income_taxes = $taxable_income = $personal_income_tax = 0;
                }
                if ($salary_concurrent == 0) {
                    $income_taxes = $taxable_income = $personal_income_tax = 0;
                }

                $response[] = [
                    'total_salary'              => max(intval($salary_concurrent), 0),
                    'total_real_salary'         => max(intval($total_real_salary), 0),
                    'food_allowance_nonTax'     => 0,
                    'food_allowance_tax'        => 0,
                    'total_allowances'          => 0,
                    'basic_salary'              => 0,
                    'salary_bh'                 => 0,
                    'working_salary_non_tax'    => 0,
                    'working_salary_tax'        => max(intval($salary_concurrent), 0),
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
                    'allowances_ids'            => null,
                    'basic_salary_tv'           => 0,
                    'basic_salary_hd'           => $basic_salary_hd,
                    'allowances_ids1'            => null,
                    'total_payoff'              => intval($total_payoff),
                    'total_payoff_tax'          => intval($total_payoff_tax),
                    'total_payoff_non_tax'      => intval($total_payoff_non_tax),
                    'timekeeping_id'            => $cong->id,
                    'calculateAllowance'        => null,
                    'contract_id'               => $concurrent->id,
                    'total_deduction'           => 0,
                    'total_deduction_tax'       => 0,
                    'total_deduction_non_tax'   => 0,
                    'total_impale'              => 0,
                    'tong_hop'                  => $cong->tong_hop
                ];
            }
        }

        $boSungTang = PayOff::where('department_id', $data['department_id'])->where('month', $data['month'])->where('year', $data['year'])->where('check', 1)->get();
        if (count($boSungTang) > 0) {
            $user_ids = array_unique(array_values(array_column($boSungTang->toArray(), 'user_id')));
            if (count($user_ids) > 0) {
                foreach ($user_ids as $i => $id) {
                    $total_payoff_tax = $total_payoff_non_tax = $total_payoff = $taxable_income = $income_taxes = $personal_income_tax = 0;
                    //các khoản tăng
                    $payoffs = $boSungTang->where('user_id', $id);
                    $total_payoff_tax = $payoffs->where('type', 'CHIU_THUE')->sum('amount_money_tax');
                    $total_payoff_non_tax = $payoffs->where('type', 'MIEN_THUE')->sum('amount_money_non_tax');
                    $total_payoff = $total_payoff_non_tax + $total_payoff_tax;

                    $taxable_income = $income_taxes = $total_payoff_tax;
                    $personal_income_tax = $this->tntt($taxable_income);
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

                    $response[] = [
                        'total_salary'              => 0,
                        'total_real_salary'         => $total_payoff - $personal_income_tax,
                        'food_allowance_nonTax'     => 0,
                        'food_allowance_tax'        => 0,
                        'total_allowances'          => 0,
                        'basic_salary'              => 0,
                        'salary_bh'                 => 0,
                        'working_salary_non_tax'    => 0,
                        'working_salary_tax'        => 0,
                        'salary_ot_non_tax'         => 0,
                        'salary_ot_tax'             => 0,
                        'salary_concurrent'         => 0,
                        'bh'                        => json_encode($bhxh),
                        'income_taxes'              => max(intval($income_taxes), 0),
                        'taxable_income'            => max(intval($taxable_income), 0),
                        'personal_income_tax'       => max(intval($personal_income_tax), 0),
                        'family_allowances'         => 0,
                        'user_id'                   => $id,
                        'payroll_id'                => $payroll->id,
                        'allowances_ids'            => null,
                        'basic_salary_tv'           => 0,
                        'basic_salary_hd'           => 0,
                        'allowances_ids1'            => null,
                        'total_payoff'              => intval($total_payoff),
                        'total_payoff_tax'          => intval($total_payoff_tax),
                        'total_payoff_non_tax'      => intval($total_payoff_non_tax),
                        'timekeeping_id'            => 0,
                        'calculateAllowance'        => null,
                        'contract_id' => 0,
                        'total_deduction' => 0,
                        'total_deduction_tax' => 0,
                        'total_deduction_non_tax' => 0,
                        'total_impale' => 0
                    ];
                }
            }
        }
        PayrollUser::insert($response);
        return true;
    }

    public function calculateAllowance($contract, $kpi, $cong, $full_cong_hd) // tính các khoản phụ cấp
    {
        $results = [];
        if (count($contract->allowances) == 0) return $results;
        $allowances = $contract->allowances->whereNotIn('category_id', [1]); // loại trừ phụ cấp ăn tính riêng
        foreach ($allowances as $key => $item) {
            $amount_money = 0;
            $cate = AllowanceCategory::find($item->category_id);
            if ($cate->type == 0 && $cate->type_work == 'BY_WORKING_DAY') { // phụ cấp tính theo ngày công, không tính theo kpi
                if ($item->category_id == 2) {
                    $full_cong_hd_ = $full_cong_hd - $cong['lam_tai_nha']; // làm tại nhà giảm phụ cấp đi lại
                    $amount_money = $item->expense * $full_cong_hd_ / $cong['total_day_request'];
                } else {
                    $amount_money = $item->expense * $full_cong_hd / $cong['total_day_request'];
                }
            }
            if ($cate->type_work == 'BY_WORKING_DAY' && $cate->type == 1) { // phụ cấp tính theo ngày công, kpi
                $amount_money = $item->expense * (($full_cong_hd / $cong['total_day_request']) * ($kpi / 100));
            }
            if ($cate->type_work == '' && $cate->type == 0) { // phụ cấp ko tính theo ngày công, ko kpi
                $amount_money = $item->expense;
            }
            if ($cate->type_work == '' && $cate->type == 1) { // phụ cấp ko tính theo ngày công, nhân kpi
                $amount_money = $item->expense * ($kpi / 100);
            }
            if (($cong['nghi_khong_luong'] >= 0.5 || $cong['nghi_phep'] > 1.5 || $cong['nghi_70_luong'] >= 0.5 || $cong['nghi_om'] > 0)
                && $item->category_id == 10
            ) { // phụ cấp chuyên cần, điều kiện bổ sung
                $amount_money = 0;
            }
            $results[$item->category_id] = $amount_money;
        }
        return $results;
    }

    public function tinhOt($cong, $level_config_tv, $level_config_hd, $basic_salary_hd, $phucapOtMienThue, $basic_salary_tv, $coefficient_ot, $data)
    {
        $ot_1_day_hd = $ot_1_hours_night_not_day_hd = $ot_1_hours_night_have_day_hd = $ot_1_day_tv = $ot_1_hours_night_not_day_tv = $ot_1_hours_night_have_day_tv = 0;
        $ot_2_day_hd = $ot_2_night_hd = $ot_2_day_tv = $ot_2_night_tv = 0;
        $ot_3_day_hd = $ot_3_night_hd = $ot_3_day_tv = $ot_3_night_tv = 0;

        foreach ($cong->detail as $key => $item) {
            if ($item['type_ot'] == '') continue;
            if (is_null($item['type_ot'])) continue;

            if ($item['type_ot'] == 'NGAY_THUONG') {
                if ($item['contract_type'] == 'HOP_DONG') {
                    $ot_1_day_hd += $item['ngay']; //ngày thường
                    $ot_1_hours_night_not_day_hd += $item['dem_thuong_ko_ot_ngay'];
                    $ot_1_hours_night_have_day_hd += $item['dem_thuong_co_ot_ngay'];
                }

                if ($item['contract_type'] == 'THU_VIEC') {
                    $ot_1_day_tv += $item['ngay']; //ngày thường
                    $ot_1_hours_night_not_day_tv += $item['dem_thuong_ko_ot_ngay'];
                    $ot_1_hours_night_have_day_tv += $item['dem_thuong_co_ot_ngay'];
                }
            }

            if ($item['type_ot'] == 'NGAY_NGHI') {
                if ($item['contract_type'] == 'HOP_DONG') {
                    $ot_2_day_hd += $item['ngay'];
                    $ot_2_night_hd += $item['dem'];
                }

                if ($item['contract_type'] == 'THU_VIEC') {
                    $ot_2_day_tv += $item['ngay'];
                    $ot_2_night_tv += $item['dem'];
                }
            }

            if ($item['type_ot'] == 'NGAY_LE') {
                if ($item['contract_type'] == 'HOP_DONG') {
                    $ot_3_day_hd += $item['ngay'];
                    $ot_3_night_hd += $item['dem'];
                }

                if ($item['contract_type'] == 'THU_VIEC') {
                    $ot_3_day_tv += $item['ngay'];
                    $ot_3_night_tv += $item['dem'];
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

        foreach ($typeOtTv as $k => $otTv) {
            $config_ot = DB::table('config_ot')->where('company_id', $data['company_id'])->where('type', $k)->first();
            if ($config_ot) $level_config_tv += (($config_ot->value - 100) / 100) * $otTv;
        }
        foreach ($typeOtHd as $k => $otHd) {
            $config_ot = DB::table('config_ot')->where('company_id', $data['company_id'])->where('type', $k)->first();
            if ($config_ot) $level_config_hd += (($config_ot->value - 100) / 100) * $otHd;
        }

        $salary_ot_non_tax = ((($basic_salary_hd + $phucapOtMienThue) / $coefficient_ot / $cong['total_day_request']) * $level_config_hd) + (($basic_salary_tv / $coefficient_ot / $cong['total_day_request'] * $level_config_tv));

        return $salary_ot_non_tax;
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

    public function detail(Request $request, $id)
    {
        $payroll = Payroll::find($id);
        if (is_null($payroll)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.payrolls.index');
        }

        $payroll->load('company', 'department');
        $payroll_details = PayrollUser::with('timekeepingDetail', 'staff')
            ->where('payroll_id', $id)
            ->get();
        foreach ($payroll_details as $key => $item) {
            $tongHop = json_decode($item->tong_hop, true);
            if ($item->timekeeping_id != 0) {
                $dep_timekeeping = $item->timekeepingDetail->timekeeping->department_id;
                $typeDepartment = $item->timekeepingDetail->timekeeping->department->type;
                //$total_day_request = OverTimes::totalWorkingInMonth($payroll->month, $payroll->year, $dep_timekeeping);
                $total_day_request = $tongHop['total_day_request'];
            } else {
                $typeDepartment = 0;
                $total_day_request = 0;
            }
            //$tongHop = TimeKeeping::tongHop($item->timekeeping_id, $typeDepartment, $total_day_request);
            $payroll_details[$key]['total_day_request'] = $total_day_request;
            $payroll_details[$key]['tongHop'] = $tongHop;
            $payroll_details[$key]['bh'] = json_decode($item->bh, true);
            $payroll_details[$key]['calculateAllowance'] = json_decode($item->calculateAllowance, true);

            $item['sort_value'] = explode('JNR', $item->staff->code)[1];
        }

        $payroll_details = $payroll_details->sortBy('sort_value');

        if ($request->export == 1) {
            return [
                'payroll' => $payroll,
                'payroll_details' => $payroll_details
            ];
        }

        $isNV = false;
        if (Auth::user()->hasRole('NV')) {
            $isNV = true;
        }
        return view('backend.payroll.v1.detail', compact('isNV', 'payroll', 'payroll_details'));
    }

    public function store1(Request $request) // Hàm tính lại
    {
        $data = $request->all();
        $payroll = Payroll::find(intval($data['id']));

        if (is_null($payroll)) {
            Session::flash('message', 'Có lỗi xảy ra');
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.payrolls.index');
        }

        $checkTimekeeping = TimeKeeping::where('company_id', $data['company_id'])->where('department_id', $data['department_id'])
            ->where('month', $data['month'])->where('year', $data['year'])->where('version', 1)->first();

        if (is_null($checkTimekeeping)) {
            Session::flash('message', 'Lỗi không tìm thấy bảng chấm công');
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.payrolls.index');
        }

        DB::beginTransaction();

        try {
            $payroll->user = Auth::user()->id;
            $payroll->updated_at = date('Y-m-d H:i:s');
            $payroll->version = 1;
            $payroll->save();

            $payroll->userPayroll()->delete();
            $this->calculate($request, $data, $payroll, $checkTimekeeping);

            DB::commit();

            Session::flash('message', trans('system.success'));
            Session::flash('alert-class', 'success');
            return redirect()->route('admin.payroll.detail', $data['id']);
        } catch (Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
            // Session::flash('message', $e->getMessage());
            Session::flash('message', 'Đã có lỗi xả ra');
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.payroll.detail', $data['id']);
        }
    }

    public function exportDepartment(Request $request, $id)
    {
        $payroll = Payroll::find($id);
        if (is_null($payroll)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.payrolls.index');
        }
        $request->export = 1;
        $results = $this->detail($request, $id);

        return \Excel::download(new \App\Exports\PayrollExportDep($results['payroll'], $results['payroll_details']), 'luong' . '_' . date('H.m_d-m-Y') . '.xlsx');
    }

    public function payOffUser()
    {
        $payoffs = PayOff::with('adjustment', 'department')->where('user_id', Auth::user()->id)->orderBy('year', 'DESC')->orderBy('month', 'DESC')->get();

        return view('backend.payroll.v1.payoff', compact('payoffs'));
    }
}
