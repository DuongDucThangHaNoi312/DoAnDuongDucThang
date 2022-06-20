<?php

namespace App\Http\Controllers\Backend;

use App\Define\Constant;
use App\Defines\Schedule;
use App\Models\ConcurrentContract;
use App\Models\Contract;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SalaryVan;
use App\Models\SalaryVanDetail;
use App\PermissionUserObject;
use App\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class VanController extends Controller
{
    public function index()
    {
        // $query = PermissionUserObject::getQueryPermission(Auth::id());
        if (Auth::user()->id == 1) {
            $vans = SalaryVan::with('company', 'user_by')->orderBy('year', 'DESC')->orderBy('month', 'DESC')->get();
        } else {
            $vans = SalaryVan::with('company', 'user_by')->orderBy('year', 'DESC')->orderBy('month', 'DESC')->where('created_by', Auth::user()->id)->get();
        }
        
        return view('backend.vans.index', compact('vans'));
    }

    public function createBulk()
    {
        return view('backend.vans.create-bulk');
    }

    public function download()
    {
        $file = public_path() . "/assets/media/files/templates/template_luong_khoan.xlsx";
        $headers = [
            'Content-Type: application/xls',
        ];
        return response()->download($file, 'luong-khoan' . time() . '.xlsx', $headers);
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
                        $data = \Excel::toArray(new \App\Imports\VansImport, $file);
                        if ($data) $data = $data[0];
                        
                        for ($i = 0; $i < 50; $i++) { 
                            if ($i == 2) unset($data[$i]);
                            if ($i >= 4 && is_null($data[$i][1])) unset($data[$i]);

                        }

                        $data = array_values($data);
                        $response['message'] = view('backend.vans.excel', compact('data'))->render();

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

                if (!$month || !$year)
                    throw new \Exception('Dữ liệu Tháng/Năm là bắt buộc');
                if (!$company_id)
                    throw new \Exception('Công ty là bắt buộc');
                if (!$title)
                    throw new \Exception('Tiêu đề là bắt buộc');
                if (!is_array($data) || count($data) == 0) {
                    throw new \Exception("Dữ liệu file rỗng hoặc lỗi", 1);
                }
                unset($data[0]);
                $data = array_values($data);
                $check = $insert = [];

                if ($data[0][5] == null) {
                    throw new \Exception("Thời gian Lương khoán đợt 1 không được để trống");
                }

                if ($data[0][6] == null) {
                    throw new \Exception("Thời gian Lương khoán đợt 2 không được để trống");
                }

                if ($data[0][7] == null) {
                    throw new \Exception("Thời gian Lương khoán đợt 3 không được để trống");
                }

                if ($data[0][8] == null) {
                    throw new \Exception("Chi phí cầu đường bến bãi đợi 1 không được để trống");
                }

                if ($data[0][9] == null) {
                    throw new \Exception("Chi phí cầu đường bến bãi đợi 2 không được để trống");
                }

                if ($data[0][10] == null) {
                    throw new \Exception("Chi phí cầu đường bến bãi đợi 3 không được để trống");
                }
                $listUserCodes = $listUserIds = [];
                foreach ($data as $k => $d) {
                    if ($k > 0 && $k < array_key_last($data)) {
                        $code = trim($d[3]);
                        if (!$code)
                            throw new \Exception("Kiểm tra lại Mã nhân viên tại dòng STT " . (($d[0]-2) ?? "") . "");
                        $listUserCodes[] = $code;
                    }
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
                    throw new \Exception('Tất cả nhân viên chưa có hợp đồng hoặc k thuộc cty đã chọn');
                foreach ($data as $k => $d) {
                    if ($k > 0 && $k < array_key_last($data)) {
                        $fullname = trim($d[1]);
                        $department = trim($d[2]);
                        $code = trim($d[3]);
                        $license_plates = trim($d[4]);
                        $contractual_wages_1 = trim($d[5]);
                        $contractual_wages_2 = trim($d[6]);
                        $contractual_wages_3 = trim($d[7]);
                        $wharf_1 = trim($d[8]);
                        $wharf_2 = trim($d[9]);
                        $wharf_3 = trim($d[10]);
    
                        $monthly_ticket = trim($d[11]);
                        $parking_fee = trim($d[12]);
                        $meal_allowance = trim($d[13]);
                        $total_contractual_wages = trim($d[14]);
                        $total_wharf = trim($d[15]);
                        $total = trim($d[16]);
                        $user = $userData[$code];

                        if (is_null($user) || $code != $user->code) {
                            throw new \Exception("Kiểm tra lại Mã nhân viên {$code} tại dòng STT " . (($d[0] - 2) ?? "") . "");
                        }
                        //$check[$user->company_id] = 0;
                        $contract = $contractData[$user->id] ? $contractData[$user->id] : $concurrentContractData[$user->id];
                        if (!$contract) {
                            throw new \Exception("Nhân viên {$user->fullname} chưa có hợp đồng hoặc khác công ty đã chọn.");
                        }
                        // $check_ = SalaryVan::where('month', $month)->where('year', $year)->where('company_id', $user->company_id)->first();
                        // if (count($check_) > 0) {
                        //     throw new \Exception("Công ty đã tồn tại bảng lương khoán tháng " . $month . '/' . $year);
                        // }

                        $accounting = DB::connection('db_accounting')->table('jobs')->where('code', $license_plates)->first();
                        $insert[] = [
                            'user_id' => $user->id,
                            'code' => $code,
                            'department_id' => $user->department_id,
                            'job_id' => $accounting->id,
                            'contractual_wages_1' => $contractual_wages_1 > 0 ? $contractual_wages_1 : null,
                            'contractual_wages_2' => $contractual_wages_2 > 0 ? $contractual_wages_2 : null,
                            'contractual_wages_3' => $contractual_wages_3 > 0 ? $contractual_wages_3 : null,
                            'wharf_1' => $wharf_1 > 0 ? $wharf_1 : null,
                            'wharf_2' => $wharf_2 > 0 ? $wharf_2 : null,
                            'wharf_3' => $wharf_3 > 0 ? $wharf_3 : null,
                            'monthly_ticket' => $monthly_ticket > 0 ? $monthly_ticket : null,
                            'parking_fee' => $parking_fee > 0 ? $parking_fee : null,
                            'meal_allowance' => $meal_allowance >0  ? $meal_allowance : null,
                            'total_contractual_wages' => $total_contractual_wages > 0 ? $total_contractual_wages : null,
                            'total_wharf' => $total_wharf > 0 ? $total_wharf : null,
                            'total' => $total > 0 ? $total : null,
                            'license_plates' => $license_plates,
                        ];
                    }
                }
                
                $salary_van = [
                    'company_id' => $company_id,
                    'time_1' => $data[0][5],
                    'time_2' => $data[0][6],
                    'time_3' => $data[0][7],
                    'cp_1' => $data[0][8],
                    'cp_2' => $data[0][9],
                    'cp_3' => $data[0][10],
                    'year' => $year,
                    'month' => $month,
                    'created_by' => Auth::user()->id,
                    'title' =>$title,
                ];
                DB::beginTransaction();
                $salary_van = SalaryVan::create($salary_van);
                foreach ($insert as $item) {
                    $item['salary_van_id'] = $salary_van->id;
                    SalaryVanDetail::create($item);
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


    public function show($id)
    {
        $salary_van = SalaryVan::find($id);
        if (empty($salary_van)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.vans.index');
        }

        $salary_vans = SalaryVanDetail::where('salary_van_id', $id)->get();
        return view('backend.vans.detail', compact('salary_vans', 'salary_van'));
    }

    public function destroy($id)
    {
        $payroll = SalaryVan::find($id);
        if (is_null($payroll)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.payrolls.index');
        }
		try {
			DB::beginTransaction();
			$payroll->salaryDetail()->delete();
			$payroll->delete();
			DB::commit();
		} catch (\Exception $e) {
			DB::rollBack();
			return back()->withErrors($e)->withInput();
		}
        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');
        return redirect()->route('admin.vans.index');
    }
    
    public function approved(Request $request, $id)
    {
        $response = ['message' => trans('system.have_an_error')];
        $statusCode = 200;
        
        if ($request->ajax()) {
            try {
                SalaryVan::find($id)->update([
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
