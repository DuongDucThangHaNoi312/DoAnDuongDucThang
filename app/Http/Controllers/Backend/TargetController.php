<?php

namespace App\Http\Controllers\Backend;

use App\Defines\Staff;
use App\Helpers\GetOption;
use App\Target;
use App\User;
use App\Models\Company;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class TargetController extends Controller
{
    const DAY_ALLOWED_CAL_TARGET_FROM = 10;
    const DAY_ALLOWED_CAL_TARGET_TO = 26;

    public function index(Request $request)
    {
        $query = "1=1";
        $companysOption = GetOption::getCompaniesForOption();

        Cache::forget('staff_permission_manager_leave');
        $infoPermission = \App\PermissionUserObject::getMorePermissions();
        Cache::put('staff_permission_manager_leave', $infoPermission);

        $departmentOption = GetOption::getDepartmentsForOptionPermissionIndex($infoPermission);

        return view('backend.targets.index', compact('companysOption', 'departmentOption'));
    }

    public function getData(Request $request)
    {
        $query = "1=1";

        $infoPermission = Cache::get('staff_permission_manager_leave');
        if (!$infoPermission) {
            $infoPermission = \App\PermissionUserObject::getMorePermissions("", "targets.read");
            Cache::put('staff_permission_manager_leave', $infoPermission);
        }

        $query .= User::getQueryPermission($infoPermission, Auth::id(), 'users.id');

        $targets = Target::with('user', 'userBy', 'user.company', 'user.department')->whereHas('user', function ($q) use ($query) {
            $q->whereRaw($query);
        })->orderBy('year', 'DESC')->orderBy('month', 'DESC');

        return DataTables::of($targets)
            ->addIndexColumn()
            ->addColumn('fullname_code', function ($targets) {
                return $targets->user->fullname . "-" . $targets->user->code;
            })
            ->addColumn('shortened_name', function ($targets) {
                return $targets->user->company->shortened_name;
            })
            ->addColumn('department_name', function ($targets) {
                return $targets->user->department->name;
            })
            ->addColumn('month_year', function ($targets) {
                return $targets->month . '/' . $targets->year;
            })
            ->addColumn('created_by_fullname', function ($targets) {
                return $targets->userBy->fullname;
            })
            ->filter(function ($instance) use ($request) {
                if ($request->get('name')) {
                    $userId = User::where(DB::raw('CONCAT(fullname, "-", code)'), 'like', "%" . $request->get('name') . "%")->pluck('id')->toArray();
                    $instance->whereIn('user_id', $userId);
                }
                if ($request->get('company')) {
                    $instance->whereHas('user.company', function ($queryCompany) use ($request) {
                        $queryCompany->where('id', intval($request->get('company')));
                    });
                }
                if ($request->get('department')) {
                    $instance->whereHas('user.department', function ($queryDepartment) use ($request) {
                        $queryDepartment->where('id', intval($request->get('department')));
                    });
                }
                if ($request->get('kpi')) {
                    $instance->whereKpi($request->get('kpi'));
                }
                if ($request->get('monthYear')) {
                    $monthYear = explode("/", $request->get('monthYear'));
                    $instance->where([
                        ['month', intval($monthYear[0])],
                        ['year', intval($monthYear[1])]
                    ]);
                }
            })
            ->make(true);
    }

    public function create(Request $request)
    {
        // $day = now()->format('d');
        // if ($day > self::DAY_ALLOWED_CAL_TARGET_FROM && $day < self::DAY_ALLOWED_CAL_TARGET_TO) {
        //     Session::flash('message', 'Chỉ được phép đánh giá KPI từ ngày 26 tháng ' . now()->subMonth(1)->format('m') . ' đến ngày 10 tháng ' . now()->format('m'));
        //     Session::flash('alert-class', 'danger');
        //     return redirect()->route('admin.targets.index');
        // }
        $query = "1=1 AND is_leave is null";
        $page_num = intval($request->input('page_num', \App\Define\Constant::PAGE_NUM_20));
        $company_filter = $request->input('company_filter') ?? '';
        $department_filter = $request->input('department_filter') ?? '';
        if ($company_filter) $query .= " AND company_id = {$company_filter}";
        if ($department_filter) $query .= " AND department_id = {$department_filter}";
        if (empty($request->all())) {
            $infoPermission = \App\PermissionUserObject::getMorePermissions();
            $query .= User::getQueryPermission($infoPermission, Auth::id(), 'id');
        }
        $users  = User::whereRaw($query)
            ->with('targetCurrentMonth')
            ->whereNotIn('fullname', Staff::USER_EXCEPT)->get();
        $mc     = date('m');
        $year   = date('Y');
        $companies = GetOption::getCompaniesForOption();
        return view('backend.targets.create', compact('company_filter', 'department_filter', 'companies', 'users', 'mc', 'year'));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $list_user_id = $request->user_id;
        $kpi = $request->kpi;
        $description = $request->description;
        $note = $request->note;
        $year = explode('-', $request->timestamp);
        if ($year[0] < 2021 || $year[0] > date("Y")) {
            $errors = new \Illuminate\Support\MessageBag;
            $errors->add('error', 'Năm set KPI trong khoảng: 2021-' . date("Y"));
            return back()->withErrors($errors)->withInput();
        }
        if (!isset($year[1]) || $year[1] > 12 || $year[1] < 1) {
            $errors = new \Illuminate\Support\MessageBag;
            $errors->add('error', 'Tháng/năm không đúng định dạng');
            return back()->withErrors($errors)->withInput();
        }
        $month  = $year[1];
        $year   = $year[0];
        if (date("Y") == $year) {
            if (date("m") - $month > 1) {
                $errors = new \Illuminate\Support\MessageBag;
                $errors->add('error', 'Chỉ được set của tháng trước');
                return back()->withErrors($errors)->withInput();
            }
        } else {
            if ($month <> 12 || date("m") <> 1) {
                $errors = new \Illuminate\Support\MessageBag;
                $errors->add('error', 'Chỉ được set của tháng 12 năm trước');
                return back()->withErrors($errors)->withInput();
            }
        }
        foreach ($list_user_id as $key => $user) {
            if (is_null($kpi[$key])) continue;
            Target::updateOrCreate([
                'user_id'   => $user,
                'month'     => $month,
                'year'      => $year,
            ], [
                'timestamp'     => $timestamp,
                'kpi'           => $kpi[$key],
                'description'   => $description[$key],
                'created_by'    => $request->user()->id,
                'note'          => $note[$key],
            ]);
        }
        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');
        return redirect()->route('admin.targets.index');
    }

    public function update(Request $request, $id)
    {
        $targets = Target::find(intval($id));
        if (is_null($targets)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.targets.index');
        }
        $validator = \Validator::make($data = $request->only(['user_id', 'description', 'kpi', 'created_by', 'timestamp']), Target::rules(intval($id)));
        $validator->setAttributeNames(trans('kpi'));

        $data['created_by'] = \auth()->id();
        $targets->update($data);
        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');
        return redirect()->route('admin.targets.index');
    }

    public function destroy($id)
    {
        $targets = Target::find(intval($id));
        if (is_null($targets)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.targets.index');
        }
        $targets->delete();

        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');
        return redirect()->route('admin.targets.index');
    }

    public function getKpiByMonth(Request $request)
    {
        if ($request->ajax()) {
            $data = $request->all();
            $targets = Target::where('month', $data['month'])->where('year', $data['year'])->get()->toArray();
            if (!empty($targets)) return response()->json(['status' => 200, 'data' => $targets]);
            return response()->json(['status' => 400, 'data' => []]);
        }
    }
}
