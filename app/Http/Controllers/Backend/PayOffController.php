<?php

namespace App\Http\Controllers\Backend;

use App\Define\Constant;
use App\Defines\Schedule;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Adjustment;
use App\Models\Company;
use App\Models\ConcurrentContract;
use App\Models\Contract;
use App\Models\Department;
use App\Models\PayOff;
use App\Models\Payroll;
use App\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class PayOffController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->all();
        if (!isset($data['type']) && count($data) > 0) {

            if (is_null($data['department_id'])) {
                Session::flash('danger', 'Chọn phòng ban để xem');
                Session::flash('alert-class', 'success');
                return redirect()->route('admin.payoffs.index');
            
            } else {
                $user_ids = Contract::where('department_id', $data['department_id'])->whereIn('type_status', [1, 7])->pluck('user_id')->toArray();
                $user_ids_sub = ConcurrentContract::where('department_id', $data['department_id'])->pluck('user_id')->toArray();
                $arr_users = array_merge($user_ids, $user_ids_sub);

                $users = User::whereIn('id', $arr_users)->get(['id', 'fullname', 'code', 'company_id']);

                return view('backend.payoffs.index', compact('users', 'data'));

            }

            Session::flash('danger', 'Chọn phòng ban để xem');
            Session::flash('alert-class', 'success');
            return redirect()->route('admin.payoffs.index');
        }
        if (!is_null($data['company_id']) &&  !is_null($data['year'])) {
            if (!is_null($data['department_id'])) {
                $user_ids = Contract::where('department_id', $data['department_id'])->whereIn('type_status', [1, 7])->pluck('user_id')->toArray();
                $user_ids_sub = ConcurrentContract::where('department_id', $data['department_id'])->pluck('user_id')->toArray();
                $arr_users = array_merge($user_ids, $user_ids_sub);

                $users = User::whereIn('id', $arr_users)->get(['id', 'fullname', 'code', 'company_id']);
            } else {
                $users = User::where('company_id', $data['company_id'])->get(['id', 'fullname', 'code', 'company_id']);

            }

        }
        return view('backend.payoffs.index', compact('users', 'data'));
    }

    public function create(Request $request)
    {
        $user_current = Auth::user();
        $type = '';
        $status = '';
        $data = $request->all();

        $startDate = date('Y-m-d 00:00:00', strtotime($data['year'] . '-' . (($data['month'] - 1)) . '-' . 26));

        if (!is_null($data['department_id'])) {
            $user_ids = Contract::where('department_id', $data['department_id'])->whereIn('type_status', [1, 7])->pluck('user_id')->toArray();
            $user_dieu_chuyen = Contract::where('department_id', $data['department_id'])->where('type_status', 2)->where('set_notvalid_on', '>', $startDate)->pluck('user_id')->toArray();
            $user_ids_sub = ConcurrentContract::where('department_id', $data['department_id'])->pluck('user_id')->toArray();
            $userBoSung = PayOff::where('check', 1)->where('department_id', $data['department_id'])->where('month', $data['month'])->pluck('user_id')->toArray();
            $arr_users = array_merge($user_ids, $user_ids_sub, $user_dieu_chuyen, $userBoSung);
            $type = 'search';
            $users = User::whereIn('id', $arr_users)->where('active', 1)->get(['id', 'fullname', 'code']);
        }
        $month = $data['month'];
        $year = $data['year'];
        $payroll = Payroll::where('department_id', $data['department_id'])->where('month', $data['month'])->where('year', $data['year'])->first();
        if (!is_null($payroll)) {
            if ($payroll->status == 'APPROVED') $status = 'APPROVED';
        }

        return view('backend.payoffs.create', compact('users', 'type', 'data','user_current', 'status'));
    }

    public function store(Request $request)
    {
        if ($request->ajax()) {
            $data = $request->all();
            $return = [];
            DB::beginTransaction();
            try {
                foreach ($data['user_ids'] as $key => $user_id) {
                    $payoff = PayOff::where('user_id', $user_id)->where('month', $data['month'])->where('year', $data['year'])
                    ->where('department_id', $data['department_id'])
                    ->get();
                    
                    $count = count($data['content_' . $user_id]);
                    for ($i = 0; $i < $count; $i++) {
                        $content = $data['content_' . $user_id][$i];
                        if (!is_null($content)) {
                            $amount_money_non_tax = str_replace(',', '', $data['amount_money_non_tax_' . $user_id][$i]);
                            $amount_money_tax = str_replace(',', '', $data['amount_money_tax_' . $user_id][$i]);
    
                            $return[] = [
                                'category' => intval ($data['content_' . $user_id][$i]),
                                'amount_money_non_tax' => $amount_money_non_tax == '' ? 0 : $amount_money_non_tax,
                                'amount_money_tax' => $amount_money_tax == '' ? 0 : $amount_money_tax,
                                'user_id' => $user_id,
                                'created_by' => Auth::user()->id,
                                'month' => $data['month'],
                                'year' => $data['year'],
                                'note' => $data['note_' . $user_id][$i],
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                                'department_id' => $data['department_id'],
                                'check' => $payoff[0]->check == 1 ? 1 : 0
                            ];
                        }
                    }

                    PayOff::destroy($payoff->pluck('id')->toArray());

                }

                $count_return =  count($return);

                if ($count_return != 0) {
                    for ($i=0; $i < $count_return; $i++) { 
                        $check = ($return[$i]['amount_money_non_tax']);
                        if(str_contains( $check,'Miễn Thuế'))
                        {
                            $amount_money_non_tax = $return[$i]['amount_money_tax'];
                            $amount_money_tax = 0;
                            $type = 'MIEN_THUE';
                        }
                        else
                        {
                            $amount_money_non_tax = 0;
                            $amount_money_tax = $return[$i]['amount_money_tax'];
                            $type = 'CHIU_THUE';
                        }
                       $temp =[ 
                           'category' => $return[$i]['category'],
                           'amount_money_non_tax' => $amount_money_non_tax,
                           'amount_money_tax' => $amount_money_tax,
                           'user_id' => $return[$i]['user_id'],
                           'created_by' => $return[$i]['created_by'],
                           'month' => $return[$i]['month'],
                           'year' => $return[$i]['year'],
                           'note' => $return[$i]['note'],
                           'department_id' => $data['department_id'],
                           'type' => $type,
                           'check' => $return[$i]['check']
                       ];
                       PayOff::create($temp);
                    }
                    
                }

                DB::commit();
                return response()->json(['status' => 200, 'message' => 'Thành công']);

            } catch (Exception $e) {
                DB::rollBack();
                return response()->json(['status' => 400, 'message' => $e->getMessage()]);
            }
        }
    }

    public function selectTax(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $tax = Adjustment::find(intval($id));
            if (is_null($tax)) {
                return response()->json(['status' => 400, 'message' => 'Có lỗi']);
            }
            if ($tax->status == 1) $str = 'tax';
            if ($tax->status == 2) $str = 'non_tax';
            $data = [
                'amount' => $tax->amount,
                'str' => $str,
            ];
            return response()->json(['status' => 200, 'data' => $data]);
        }
    }

    public function createBulk()
    {
        return view('backend.payoffs.create-bulk');
    }

    public function download()
    {
        $file = public_path() . "/assets/media/files/templates/dieu_chinh_tang.xlsx";
        $headers = [
            'Content-Type: application/xls',
        ];
        return response()->download($file, 'TEMPLATE-DIEU-CHINH-TANG-' . time() . '.xlsx', $headers);
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
                        $data = \Excel::toArray(new \App\Imports\PayoffImport, $file);
                        if ($data) $data = $data[0];
                        
                        foreach ($data as $key => $item) {
                            if (!is_null($item[1])) continue;
                            unset($data[$key]);
                        }

                        $data = array_values($data);

                        $response['message'] = view('backend.payoffs.excel', compact('data'))->render();

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
                $company_id = $request->company_id;

                if (!$month || !$year)
                    throw new \Exception('Dữ liệu Tháng/Năm là bắt buộc');
                if (!$company_id)
                    throw new \Exception('Công ty là bắt buộc');
                if (!is_array($data) || count($data) == 0) {
                    $statusCode = 400;
                    throw new \Exception(trans('system.have_an_error'), 1);
                }

                $listUserCodes = $listUserIds = $listCodeAdjustments = [];
                foreach ($data as $k => $d) {
                    $code = trim($d[1]);
                    $codeAdjustment = trim($d[3]);
                    $amount_money = trim($d[5]);
                    if (!$code)
                        throw new \Exception("Kiểm tra lại Mã nhân viên tại dòng số " . (($d[0] - 1) ?? "") . "");
                    if (!$codeAdjustment)
                        throw new \Exception("Mã điều chỉnh tăng không được để trống tại dòng số " . ($d[0] ?? "") . "");
                    if ($amount_money == '') {
                        throw new \Exception("Số tiền không được để trống tại dòng số " . ($d[0] ?? "") . "");
                    }
                    $listUserCodes[] = $code;
                    $listCodeAdjustments[] = $codeAdjustment;
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
                $adjustmentData = Adjustment::whereIn('code', $listCodeAdjustments)
                    ->where('type', \App\Defines\Adjustment::INCREASE_ADJUSTMENT)
                    ->get()
                    ->groupBy('code');
                $startDate = Constant::getDateFromDayMonthYear($year, $month-1, Schedule::DATE_START_SALARY);
                $endDate = Constant::getDateFromDayMonthYear($year, $month, Schedule::DATE_END_SALARY);
                $queryContract = "user_id in ({$listUserIdStr}) AND company_id = {$company_id})";
                /*$contractData = Contract::whereRaw($queryContract)
                    ->get()
                    ->keyBy('user_id');
                $concurrentContractData = ConcurrentContract::where('status', 1)
                    ->where('company_id', $company_id)
                    ->where('valid_from', '<=', $endDate)
                    ->where('valid_to', '>=', $startDate)
                    ->get()
                    ->keyBy('user_id');
                if (count($contractData) == 0 && count($concurrentContractData) == 0)
                    throw new \Exception('Tất cả nhân viên chưa có hợp đồng hoặc không thuộc cty đã chọn');*/


                foreach ($data as $k => $d) {
                    $code = trim($d[1]);
                    $code_dc_tang = trim($d[3]);
                    $amount_money = trim($d[5]);
                    $note = trim($d[6]);
                    $codeDep = trim($d[7]);

                    $user = $userData[$code];
                    if (is_null($user))
                        throw new \Exception("Mã nhân viên {$code} không đúng tại dòng số " . ($d[0] ?? "") . "");
                    $contract = Contract::where('company_id', $company_id)->where('user_id', $user->id)->whereIn('type_status', [1, 2, 7])->orderBy('id', 'DESC')->first();

                    if (is_null($contract)) {
                        $kiemNhiem = ConcurrentContract::where('company_id', $company_id)->where('user_id', $user->id)->first();
                        if (is_null($kiemNhiem)) {
                            throw new \Exception("Không có hợp đồng của nhân viên tại dòng số " . ($d[0] ?? "") . "");
                        }
                    }

                    $dc = $adjustmentData[$code_dc_tang];
                    if (is_null($dc)) {
                        throw new \Exception("Mã điều chỉnh tăng không đúng tại dòng số " . ($d[0] ?? "") . "");
                    }

                    $check = 0;
                    $department_id = null;
                    if ($codeDep != '') {
                        $dep = Department::with('company')->where('code', $codeDep)->first();
                        if (is_null($dep)) {
                            throw new \Exception("Mã phòng ban không đúng tại dòng số " . ($d[0] ?? "") . "");
                        }
                        if ($company_id != $dep->company_id) {
                            throw new \Exception("Mã phòng ban không thuộc công ty đã chọn tại dòng số " . ($d[0] ?? "") . "");
                        }
                        $department_id = $dep->id;
                        $check = 1;
                    }

                    $amount_money_tax = $amount_money_non_tax = null;
                    if ($dc->status == 1) {
                        $amount_money_tax = $amount_money;
                        $type = 'CHIU_THUE';
                    } else if ($dc->status == 2) {
                        $amount_money_non_tax = $amount_money;
                        $type = 'MIEN_THUE';
                    } else {
                        throw new \Exception("Loại điều chỉnh tăng chưa có tình trạng thuế tại dòng số " . ($d[0] ?? "") . "");
                    }
                    if (is_null($department_id)) {
                        if (!is_null($contract)) {
                            $department_id = $contract->department_id;
                        } else if (is_null($contract) && !is_null($kiemNhiem)) {
                            $department_id = $kiemNhiem->department_id;
                        } else {
                            throw new \Exception("Lỗi hợp đồng tại dòng số " . ($d[0] ?? "") . "");
                        }
                    }

                    $insert[] = [
                        'amount_money_non_tax' => $amount_money_non_tax,
                        'amount_money_tax' => $amount_money_tax,
                        'user_id' => $user->id,
                        'created_by' => Auth::user()->id,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                        'month' => $month,
                        'year' => $year,
                        'note' => $note,
                        'category' => $dc->id,
                        'department_id' => $department_id,
                        'type' => $type,
                        'check' => $check
                    ];
                }
                DB::beginTransaction();
                PayOff::insert($insert);

                DB::commit();
                
                $response['message'] = trans('system.success');
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
