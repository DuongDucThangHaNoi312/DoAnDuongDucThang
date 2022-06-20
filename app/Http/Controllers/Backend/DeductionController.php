<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Deduction;
use App\Models\DetailDeduction;
use App\PermissionUserObject;
use App\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Models\Adjustment;
use App\Models\ConcurrentContract;
use App\Models\Contract;
use App\Models\Department;
use Carbon\Carbon;

class DeductionController extends Controller
{
    public function index()
    {
        $companies = Company::all();
        $companies->load('users');
        return view('backend.deduction.index', compact('companies'));
    }

    public function create($company_id, Request $request)
    {
        $deductions = $total_arr = [];
        $total_non_tax_arr = [];
        $total_tax_arr = [];
        $total_money_arr = [];
        $status_approved = [];
        $total = $con_lai = 0;

        $company = Company::find($company_id);
        $query = '1=1';
        $name_user = ($request->input('name_user'));
        $department_id = ($request->input('department_id'));
        $year = $request->input('year');
        if (is_null($year)) $year = date('Y');
        if (!is_null($name_user)) $query .= " AND fullname like '%{$name_user}%' ";
        if (!is_null($department_id)) $query .= " AND department_id = '{$department_id}' ";
        if (empty($company)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.deductions.index');
        }
        $infoPermission = PermissionUserObject::getMorePermissions(Auth::id(), 'deductions.read');
        $kiemNhiem = [];

        $currentMonth = $month ?? Carbon::now()->month;
        $currentYear = $year ?? Carbon::now()->year;
        $checkDate = Carbon::createFromDate($currentYear, $currentMonth, 26)->format('Y-m-d');

        $userIdContracts = Contract::where('company_id', $company_id)
            ->where('department_id', $department_id)
            ->where('status', 1)
            ->where('is_used', 1)
            ->whereDate('set_notvalid_on', '>=', $checkDate)
            ->pluck('user_id')->toArray();

        if (!is_null($department_id)) {
            $kiemNhiem = ConcurrentContract::where('department_id', $department_id)->pluck('user_id')->toArray();
            $kiemNhiem = array_merge($kiemNhiem, $userIdContracts);
        } else {
            $id_deps = Department::where('company_id', $company_id)->pluck('id')->toArray();
            $kiemNhiem = ConcurrentContract::whereIn('department_id', $id_deps)->pluck('user_id')->toArray();
            $kiemNhiem = array_merge($kiemNhiem, $userIdContracts);
        }
        if ($infoPermission['departments']) {
            $users = User::whereRaw($query)->whereIn('department_id', $infoPermission['departments'])->when($kiemNhiem, function ($qKiemNhiem) use ($kiemNhiem) {
                $qKiemNhiem->orWhereIn('id', array_unique($kiemNhiem));
            })->get();
        } else {
            $users = User::whereRaw($query)->where('company_id', $company_id)->when($kiemNhiem, function ($qKiemNhiem) use ($kiemNhiem) {
                $qKiemNhiem->orWhereIn('id', array_unique($kiemNhiem));
            })->get();
        }
        $name_users_arr = $users->pluck('fullname', 'id')->toArray();
        $code_users_arr = $users->pluck('code', 'code')->toArray();
        $data = Deduction::where('year', $year)->get();

        foreach ($data as $key => $item) {
            $months = explode(', ', $item->month);
            foreach ($months as $index => $value) {
                $total += $item->detailDeduction()->sum('money');

                $deductions[$item->user_id][$value] = [
                    'month' => $value,
                    'year'  => $item->year,
                    'id'    => $item->id,
                    'money' => number_format($item->detailDeduction()->sum('money')),
                    'total_tax' => number_format($item->totalTax()->sum('money')),
                    'total_non_tax' => number_format($item->totalNonTax()->sum('money')),
                ];
                if (intval($value) <= intval(date('m'))) {
                    $con_lai += $item->detailDeduction()->sum('money');
                }
                ksort($deductions[$item->user_id]);
            }
            $total_arr[$item->user_id] = [
                'total' => number_format($total),
                'con_lai' => number_format($total - $con_lai)
            ];
        }
        $id_user_arr = ($users->pluck('id')->toArray());

        foreach ($deductions as $key => $deduction) {
            if (in_array($key, $id_user_arr))
                for ($i = 1; $i <= 12; $i++) {
                    $total_non_tax_arr[$i] +=  intval(str_replace(",", "", $deduction[$i]['total_non_tax']));
                    $total_tax_arr[$i] +=  intval(str_replace(",", "", $deduction[$i]['total_tax']));
                    $total_money_arr[$i] +=  intval(str_replace(",", "", $deduction[$i]['money']));
                }
        }

        return view('backend.deduction.create', compact('total_money_arr', 'total_tax_arr', 'total_non_tax_arr', 'company', 'deductions', 'total_arr', 'users', 'name_users_arr', 'code_users_arr', 'name_user', 'status_approved'));
    }

    public function store(Request $request)
    {
        $response = $deduction = $arr = [];
        $data = $request->all();
        if ($data['month_start'] > $data['month_end']) {
            Session::flash('message', trans('Tháng kết thúc phải lớn hơn tháng bắt đầu '));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.deductions.create', $data['company_id']);
        }
        DB::beginTransaction();
        try {
            $user = $request->user();
            $company = Company::find($data['company_id']);

            if (empty($user) || empty($company)) {
                Session::flash('message', trans('system.have_an_error'));
                Session::flash('alert-class', 'danger');
                return redirect()->route('admin.deductions.create', $data['company_id']);
            }
            $deductions = Deduction::where('user_id', $data['user_id'])->where('year', date('Y'))->get();

            for ($i = $data['month_start']; $i <= $data['month_end']; $i++) {
                $arr[] = intval($i);
            }

            $arrMonthDBs = [];
            foreach ($deductions as $key => $value) {
                $arrMonth = array_map('intval', explode(',', $value->month));
                array_push($arrMonthDBs, $arrMonth);
            }
            $arrMonthDBs = array_merge(...$arrMonthDBs);
            $arrayMontDiff = array_diff($arr, $arrMonthDBs);

            if (count($arrMonthDBs[0]) == 0) {
                $deduction = [
                    'month'      => implode(', ', $arr),
                    'year'       => date('Y'),
                    'type'       => 1,
                    'created_by' => $request->user()->id,
                    'user_id'    => $data['user_id'],
                    'department_id' => $data['department_id']
                ];
            } else {
                $deduction = [
                    'month'      => implode(', ', $arrayMontDiff),
                    'year'       => date('Y'),
                    'type'       => 1,
                    'created_by' => $request->user()->id,
                    'user_id'    => $data['user_id'],
                    'department_id' => $data['department_id']
                ];
            }
            $insert = Deduction::create($deduction);
            foreach ($data['name'] as $key => $value) {
                if (!is_null($value)) {
                    $response[] = [
                        'deduction_id' => $insert->id,
                        'name'         => $value,
                        'money'        => $data['money'][$key],
                        'type'         => $data['type'][$key],
                        'note'         => $data['note'][$key],
                    ];
                }
            }
            DetailDeduction::insert($response);

            DB::commit();
            Session::flash('message', trans('system.success'));
            Session::flash('alert-class', 'success');
            return redirect()->route('admin.deductions.create', $data['company_id']);
        } catch (Exception $e) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.deductions.create', $data['company_id']);
        }
    }

    public function getDeduction($id)
    {
        $data = DetailDeduction::where('deduction_id', $id)->with('adjustment')->get();
        $total = array_sum(array_column($data->toArray(), 'money'));
        if (empty($data)) {
            return false;
        }
        return \Response::json([
            'status'  => 'SUCCESS',
            'message' => trans('system.success'),
            'data'    => $data,
            'total'   => number_format($total, '0', ',', '.')
        ]);
    }

    public function insert(Request $request)
    {
        $data = $request->all();
        // dd(date('Y'));
        // dd($data);
        $deductions = Deduction::where('user_id', $data['user'])->where('year', date('Y'))->where('type', 1)->get();
        if (!empty($deductions->toArray())) {
            foreach ($deductions as $key => $deduction) {
                $departmentId = $deduction->department_id;
                $months = explode(', ', $deduction->month);
                if (in_array($data['month'], $months)) {
                    $new_month = implode(', ', array_diff($months, [$data['month']]));
                    if ($new_month == '') {
                        try {
                            DB::beginTransaction();
                            $deduction->detailDeduction()->delete();
                            $deduction->delete();
                            DB::commit();
                        } catch (\Exception $e) {
                            DB::rollBack();
                            return back()->withErrors($e)->withInput();
                        }
                    } else {
                        $deduction->update([
                            'month' => $new_month
                        ]);
                    }
                }
            }
        } else {
            $deductions = Deduction::where('user_id', $data['user'])->where('year', date('Y'))->where('month', $data['month'])->first();
            $departmentId = $deductions->department_id;
            try {
                DB::beginTransaction();
                $deductions->detailDeduction()->delete();
                $deductions->delete();
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->withErrors($e)->withInput();
            }
        }


        try {

            $add = Deduction::create([
                'month'      => $data['month'],
                'year'       => date('Y'),
                'created_by' => $request->user()->id,
                'user_id'    => $data['user'],
                'department_id' => $departmentId,
                'type'      => 1,
            ]);
            foreach ($data['name'] as $key => $value) {
                if (!is_null($value)) {
                    $insert[] = [
                        'deduction_id'    => $add->id,
                        'name'            => $value,
                        'money'           => $data['money'][$key],
                        'type'            => $data['type'][$key],
                        'note'         => $data['note'][$key],
                    ];
                }
            }
            if (count($insert) > 0) {
                DetailDeduction::insert($insert);
            }
        } catch (Exception $e) {
            Session::flash('message', 'Có lỗi xảy ra');
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.deductions.create', $data['company_id']);
        }

        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');
        return redirect()->route('admin.deductions.create', $data['company_id']);
    }

    public function selectTax(Request $request)
    {
        if ($request->ajax()) {
            $valueSelected = $request->input('valueSelected');
            $tax = Adjustment::find(intval($valueSelected));
            // dd($tax);
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
}
