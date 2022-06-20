<?php

namespace App\Http\Controllers\Backend;

use App\Define\Constant;
use App\Defines\Schedule;
use App\Models\ConcurrentContract;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\SalaryDrive;
use App\Models\SalaryDriveDetail;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class DriverController extends Controller
{
    public function index()
    {
        if (Auth::user()->id == 1) {
            $payrolls = SalaryDrive::orderBy('year', 'DESC')->orderBy('month', 'DESC')->get();
            $payrolls->load('company', 'user_by', 'salaryDriveDetail');
        } else {
            $payrolls = SalaryDrive::with('company', 'user_by', 'salaryDriveDetail')->where('created_by', Auth::user()->id)->orderBy('year', 'DESC')->orderBy('month', 'DESC')->get();
        }
        
        return view('backend.salary-driver.index', compact('payrolls'));
    }

    public function createBulk()
    {
        return view('backend.salary-driver.create-bulk');
    }

    public function download()
    {
        $file = public_path() . "/assets/media/files/templates/template_luong_lai_xe1.xlsx";
        $headers = [
            'Content-Type: application/xls',
        ];
        return response()->download($file, 'TEMPLATE-LUONG-LAI-XE' . time() . '.xlsx', $headers);
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
        $statusCode = 400;
        if ($request->ajax()) {
            try {
                $data = $request->data;
                $month = $request->month;
                $year = $request->year;
                $company_id = $request->company_id;
                $title = $request->title;
                $type = $request->type;

                if (!$month || !$year)
                    throw new \Exception('Dữ liệu Tháng/Năm là bắt buộc');
                if (!$company_id)
                    throw new \Exception('Công ty là bắt buộc');
                if (!$title)
                    throw new \Exception('Tiêu đề là bắt buộc');
                if (!$type)
                    throw new \Exception('Chưa chọn loại nhập lương');
                if (!is_array($data) || count($data) == 0)
                    throw new \Exception(trans('system.have_an_error'), 1);

                unset($data[0]);
                $data = array_values($data);

                $listUserCodes = $listUserIds = [];
                foreach ($data as $k => $d) {
                    $code = trim($d[2]);
                    if (!$code)
                        throw new \Exception("Kiểm tra lại Mã nhân viên tại dòng số " . (($d[0] - 1) ?? "") . "");
                    $listUserCodes[] = $code;
            }
                $userData = User::whereIn('code', $listUserCodes)
                    ->get()
                    ->keyBy('code');
                if (count($userData) == 0)
                    throw new \Exception('Không tồn tại nhân viên nào!');
                foreach ($userData as $item) {
                    $listUserIds[] = $item->id;
                }
                $listUserIdStr = implode(',', $listUserIds);
                $startDate = Constant::getDateFromDayMonthYear($year, $month-1, Schedule::DATE_START_SALARY);
                $endDate = Constant::getDateFromDayMonthYear($year, $month, Schedule::DATE_END_SALARY);
                $queryContract = "user_id in ({$listUserIdStr}) AND company_id = {$company_id} AND (set_notvalid_on is NULL OR set_notvalid_on > {$startDate})";
                $contractData = Contract::whereRaw($queryContract)
                    ->get()
                    ->keyBy('user_id');
                $concurrentContractData = ConcurrentContract::where('status', 1)
                    ->where('company_id', $company_id)
                    ->where('valid_from', '<=', $endDate)
                    ->where('valid_to', '>=', $startDate)
                    ->get()
                    ->keyBy('user_id');
                if (count($contractData) == 0 && count($concurrentContractData) == 0)
                    throw new \Exception('Tất cả nhân viên chưa có hợp đồng hoặc không thuộc cty đã chọn');

                foreach ($data as $k => $d) {
                    $code = trim($d[2]);
                    $user = $userData[$code];
                    if (is_null($user) || $code != $user->code) {
                        throw new \Exception("Kiểm tra lại Mã nhân viên {$code} tại dòng số " . (($d[0] - 1) ?? "") . "");
                    }
                    $contract = $contractData[$user->id] ? $contractData[$user->id] : $concurrentContractData[$user->id];
                    if (!$contract) {
                        throw new \Exception("Nhân viên {$user->fullname} k có hợp đồng hoặc khác công ty dòng số " . (($d[0] - 1) ?? "") . "");
                    }
                    // $check = SalaryDrive::where('company_id', $user->company_id)->where('month', $month)->where('year', $year)->first();
                    // if (!is_null($check)) {
                    //     throw new \Exception("Công ty đã tạo lương khoán tháng " . $month . '/' . $year);
                    // }

                    $total_day_request = trim($d[3]);
                    $ca_ngay_tv = trim($d[4]);
                    $ca_ngay_hd = trim($d[5]);
                    $ca_dem_tv = trim($d[6]);
                    $ca_dem_hd = trim($d[7]);
                    $cong_tac = trim($d[8]);
                    $nghi_huong_luong = trim($d[9]);
                    $nghi_dinh_chi = trim($d[10]);
                    $muon_k_luong = trim($d[11]);
                    $total_work = trim($d[12]);
                    $an_chinh = trim($d[13]);
                    $an_phu = trim($d[14]);

                    $basic_salary_tv = str_replace(',', '',trim($d[15]));
                    $basic_salary_hd = str_replace(',', '',trim($d[16]));
                    $salary_bh = str_replace(',', '',trim($d[17]));

                    $working_salary_tax = str_replace(',', '',trim($d[18]));
                    $working_salary_non_tax = str_replace(',', '',trim($d[19]));
                    $salary_ot_non_tax = str_replace(',', '',trim($d[20]));
                    $salary_ot_tax = str_replace(',', '',trim($d[21]));

                    $an_trua_non_tax = str_replace(',', '',trim($d[22]));
                    $an_trua_tax = str_replace(',', '',trim($d[23]));
                    $di_lai = str_replace(',', '',trim($d[24]));
                    $trach_nhiem = str_replace(',', '',trim($d[25]));
                    $cong_hien = str_replace(',', '',trim($d[26]));
                    $nang_suat = str_replace(',', '',trim($d[27]));
                    $dien_thoai = str_replace(',', '',trim($d[28]));
                    $cong_viec = str_replace(',', '',trim($d[29]));
                    $dac_thu = str_replace(',', '',trim($d[30]));
                    $khac = str_replace(',', '',trim($d[31]));
                    $chuyen_can = str_replace(',', '',trim($d[32]));
                    $total_salary = str_replace(',', '',trim($d[33]));

                    $bhxh_user = str_replace(',', '',trim($d[34]));
                    $bhyt_user = str_replace(',', '',trim($d[35]));
                    $union_user = str_replace(',', '',trim($d[36]));
                    $bhtn_user = str_replace(',', '',trim($d[37]));
                    $bhxh_company = str_replace(',', '',trim($d[38]));
                    $bhyt_company = str_replace(',', '',trim($d[39]));
                    $union_company = str_replace(',', '',trim($d[40]));
                    $bhtn_company = str_replace(',', '',trim($d[41]));

                    $quyet_toan = str_replace(',', '',trim($d[42]));
                    $deduction_non_tax = str_replace(',', '',trim($d[43]));
                    $deduction_tax = str_replace(',', '',trim($d[44]));
                    $increase_non_tax = str_replace(',', '',trim($d[45]));
                    $increase_tax = str_replace(',', '',trim($d[46]));
                    $income_taxes = str_replace(',', '',trim($d[47]));
                    $dependent_person = trim($d[48]);

                    $family_allowances = str_replace(',', '',trim($d[49]));
                    $taxable_income  = str_replace(',', '',trim($d[50]));
                    $personal_income_tax   = str_replace(',', '',trim($d[51]));
                    $kpi = trim($d[52]);
                    $dieu_chinh_khac   = str_replace(',', '',trim($d[53]));
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
                        'dieu_chinh_khac' => $dieu_chinh_khac == '' ? 0 : $dieu_chinh_khac,
                        'kpi' => $kpi == '' ? 0 : $kpi,
                        'dependent_person' => $dependent_person == '' ? 0 : $dependent_person,
                        'increase_tax' => $increase_tax == '' ? 0 : $increase_tax,
                        'increase_non_tax' => $increase_non_tax == '' ? 0 : $increase_non_tax,
                        'deduction_tax' => $deduction_tax == '' ? 0 : $deduction_tax,
                        'deduction_non_tax' => $deduction_non_tax == '' ? 0 : $deduction_non_tax,
                        'quyet_toan' => $quyet_toan == '' ? 0 : $quyet_toan,
                        'chuyen_can' => $chuyen_can == '' ?  0 : $chuyen_can,
                        'khac' => $khac == '' ?0 : $khac,
                        'dac_thu' => $dac_thu == '' ?0 : $dac_thu,
                        'cong_viec' => $cong_viec == '' ?0 : $cong_viec,
                        'dien_thoai' => $dien_thoai == '' ?0 : $dien_thoai,
                        'nang_suat' => $nang_suat == '' ? 0: $nang_suat,
                        'cong_hien' => $cong_hien == '' ? 0: $cong_hien,
                        'trach_nhiem' => $trach_nhiem == '' ? 0: $trach_nhiem,
                        'di_lai' => $di_lai == '' ? 0: $di_lai,
                        'an_trua_tax' => $an_trua_tax == '' ? 0 : $an_trua_tax ,
                        'an_trua_non_tax' => $an_trua_non_tax == '' ? 0 : $an_trua_non_tax,
                        'ca_dem_hd' => $ca_dem_hd == '' ? 0 : $ca_dem_hd,
                        'ca_dem_tv' => $ca_dem_tv == '' ? 0 : $ca_dem_tv,
                        'an_phu'         => $an_phu == '' ? 0 : $an_phu,
                        'an_chinh'         => $an_chinh == '' ? 0 : $an_chinh,
                        'total_work'         => $total_work == '' ? 0: $total_work,
                        'muon_k_luong'         => $muon_k_luong == '' ? 0 : $muon_k_luong,
                        'nghi_dinh_chi'         => $nghi_dinh_chi == '' ? 0 : $nghi_dinh_chi,
                        'nghi_huong_luong'         => $nghi_huong_luong == '' ? 0 : $nghi_huong_luong,
                        'cong_tac'         => $cong_tac == '' ? 0 : $cong_tac,
                        'ca_ngay_hd'         => $ca_ngay_hd == '' ? 0 : $ca_ngay_hd,
                        'ca_ngay_tv'         => $ca_ngay_tv == '' ? 0 : $ca_ngay_tv,
                        'total_day_request'         => $total_day_request == '' ? 0 : $total_day_request,
                        'total_salary'              => $total_salary == '' ? 0 : intval($total_salary),
                        'total_real_salary'         => $total_real_salary == '' ? 0 : intval($total_real_salary),
                        'salary_bh'                 => $salary_bh == '' ? 0 : intval($salary_bh),
                        'working_salary_non_tax'    => $working_salary_non_tax == '' ? 0 : intval($working_salary_non_tax),
                        'working_salary_tax'        => $working_salary_tax == '' ? 0 : intval($working_salary_tax),
                        'salary_ot_non_tax'         => $salary_ot_non_tax == '' ? 0 : intval($salary_ot_non_tax),
                        'salary_ot_tax'             => $salary_ot_tax == '' ? 0 : intval($salary_ot_tax),
                        'bh'                        => json_encode($bhxh),
                        'income_taxes'              => $income_taxes == '' ? 0 : intval($income_taxes),
                        'taxable_income'            => $taxable_income == '' ? 0 : intval($taxable_income),
                        'personal_income_tax'       => $taxable_income == '' ? 0 : intval($personal_income_tax),
                        'family_allowances'         => $family_allowances == '' ? 0 : intval($family_allowances),
                        'basic_salary_tv'           => $basic_salary_tv == '' ? 0 : $basic_salary_tv,
                        'basic_salary_hd'           => $basic_salary_hd == '' ? 0:  $basic_salary_hd,
                        'user_id'                   => $user->id,
                        'department_id'             => $contract->department_id,

                    ];
                    
                    $salary_drive = [
                        'company_id' => $company_id,
                        'month' => $month,
                        'year' => $year,
                        'created_by' => Auth::user()->id,
                        'title' => $title,
                        'type' => $type
                    ];
                }
                DB::beginTransaction();

                $salary_drive = SalaryDrive::create($salary_drive);
                foreach ($insert as $item) {
                    $item['salary_drive_id'] = $salary_drive->id;
                    SalaryDriveDetail::insert($item);
                }
                DB::commit();
                $statusCode = 200;
                $response['message'] = trans('system.success');
                Session::flash('message', $response['message']);
                Session::flash('alert-class', 'success');

            } catch (\Exception $e) {
                DB::rollBack();
                $response['message'] = $e->getMessage();
            } finally {
                return response()->json($response, $statusCode);
            }
        } else {
            $statusCode = 405;
            return response()->json($response, $statusCode);
        }
    }

    public function detail(Request $request, $id)
    {
        $total = [];
        $total_bh = []; 
        $total_bh['bhxh_user'] = 0;
        $total_bh['bhyt_user'] = 0;
        $total_bh['bhtn_user'] = 0;
        $total_bh['union_user'] = 0;
        $total_bh['bhxh_company'] = 0;
        $total_bh['bhyt_company'] = 0;
        $total_bh['bhtn_company'] = 0;
        $total_bh['bhtn_company'] = 0;

        if ($request->export == 1) {
            $payroll = SalaryDrive::where('company_id', $request->company_id)
                ->where('month', $request->month)
                ->where('year', $request->year)
                ->first();
            if (is_null($payroll)) return [];
            $payroll_detail = SalaryDriveDetail::where('salary_drive_id', $payroll->id)
                ->with('user')->get();
            if (count($payroll_detail) == 0) return [];
        } else {
            $payroll = SalaryDrive::find(intval($id));
            if (is_null($payroll)) {
                Session::flash('message', trans('system.have_an_error'));
                Session::flash('alert-class', 'danger');
                return redirect()->route('admin.drivers.index');
            }
            $payroll_detail = SalaryDriveDetail::where('salary_drive_id', intval($id))->with('user')->get();
        }
        foreach ($payroll_detail as $key => $value) {
            $bh[] = json_decode($value->bh, true);
        }        

        foreach ($bh as $key => $value) {
            $total_bh['bhxh_user'] += intval($value['bhxh_user']);
            $total_bh['bhyt_user'] += intval($value['bhyt_user']);
            $total_bh['bhtn_user'] += intval($value['bhtn_user']);
            $total_bh['union_user'] += intval($value['union_user']);
            $total_bh['bhxh_company'] += intval($value['bhxh_company']);
            $total_bh['bhyt_company'] += intval($value['bhyt_company']);
            $total_bh['bhtn_company'] += intval($value['bhtn_company']);
            $total_bh['union_company'] += intval($value['union_company']);
        }         
        $total = [
            'total_day_request' => $payroll_detail->sum('total_day_request'),
            'ca_ngay_tv' => $payroll_detail->sum('ca_ngay_tv'),
            'ca_ngay_hd' => $payroll_detail->sum('ca_ngay_hd'),
            'ca_dem_tv' => $payroll_detail->sum('ca_dem_tv'),
            'ca_dem_hd' => $payroll_detail->sum('ca_dem_hd'),
            'cong_tac' => $payroll_detail->sum('cong_tac'),
            'nghi_huong_luong' => $payroll_detail->sum('nghi_huong_luong'),
            'nghi_dinh_chi' => $payroll_detail->sum('nghi_dinh_chi'),
            'muon_k_luong' => $payroll_detail->sum('muon_k_luong'),
            'total_work' => $payroll_detail->sum('total_work'),
            'an_chinh' => $payroll_detail->sum('an_chinh'),
            'an_phu' => $payroll_detail->sum('an_phu'),
            'basic_salary_tv' => $payroll_detail->sum('basic_salary_tv'),
            'basic_salary_hd' => $payroll_detail->sum('basic_salary_hd'),
            'salary_bh' => $payroll_detail->sum('salary_bh'),
            'working_salary_non_tax' => $payroll_detail->sum('working_salary_non_tax'),
            'salary_ot_non_tax' => $payroll_detail->sum('salary_ot_non_tax'),
            'salary_ot_tax' => $payroll_detail->sum('salary_ot_tax'),
            'an_trua_non_tax' => $payroll_detail->sum('an_trua_non_tax'),
            'an_trua_tax' => $payroll_detail->sum('an_trua_tax'),
            'di_lai' => $payroll_detail->sum('di_lai'),
            'trach_nhiem' => $payroll_detail->sum('trach_nhiem'),
            'cong_hien' => $payroll_detail->sum('cong_hien'),
            'nang_suat' => $payroll_detail->sum('nang_suat'),
            'dien_thoai' => $payroll_detail->sum('dien_thoai'),
            'cong_viec' => $payroll_detail->sum('cong_viec'),
            'dac_thu' => $payroll_detail->sum('dac_thu'),
            'khac' => $payroll_detail->sum('khac'),
            'chuyen_can' => $payroll_detail->sum('chuyen_can'),
            'total_salary' => $payroll_detail->sum('total_salary'),
            'quyet_toan' => $payroll_detail->sum('quyet_toan'),
            'deduction_non_tax' => $payroll_detail->sum('deduction_non_tax'),
            'deduction_tax' => $payroll_detail->sum('deduction_tax'),
            'increase_non_tax' => $payroll_detail->sum('increase_non_tax'),
            'increase_tax' => $payroll_detail->sum('increase_tax'),
            'income_taxes' => $payroll_detail->sum('income_taxes'),
            'dependent_person' => $payroll_detail->sum('dependent_person'),
            'family_allowances' => $payroll_detail->sum('family_allowances'),
            'taxable_income' => $payroll_detail->sum('taxable_income'),
            'personal_income_tax' => $payroll_detail->sum('personal_income_tax'),
            'kpi' => $payroll_detail->sum('kpi'),
            'dieu_chinh_khac' => $payroll_detail->sum('dieu_chinh_khac'),
            'total_real_salary' => $payroll_detail->sum('total_real_salary'),
            // 'trach_nhiem' => $payroll_detail->sum('trach_nhiem'),
            'working_salary_tax' => $payroll_detail->sum('working_salary_tax'),
            'bhxh_user' => array_sum ($bh['bhxh_user']),
        ];
        if ($request->export == 1)
            return ['payroll' => $payroll, 'detail' => $payroll_detail, 'total' => $total, 'total_bh' => $total_bh];
        return view('backend.salary-driver.detail', compact('payroll', 'payroll_detail','total','total_bh'));
    }

    public function destroy($id)
    {
        $payroll = SalaryDrive::find(intval($id));
        if (is_null($payroll)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.drivers.index');
        }
		try {
			DB::beginTransaction();
			$payroll->salaryDriveDetail()->delete();
			$payroll->delete();
			DB::commit();
		} catch (\Exception $e) {
			DB::rollBack();
			return back()->withErrors($e)->withInput();
		}
        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');
        return redirect()->route('admin.drivers.index');
    }

    public function approved(Request $request, $id)
    {
        $response = ['message' => trans('system.have_an_error')];
        $statusCode = 200;
        
        if ($request->ajax()) {
            try {
                SalaryDrive::find($id)->update([
                    'approved' => 1,
                    'approved_by' => Auth::user()->id,
                    'approved_date' => date('Y-m-d H:i:s')
                ]);
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
}
