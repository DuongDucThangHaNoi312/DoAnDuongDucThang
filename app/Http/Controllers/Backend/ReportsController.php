<?php

namespace App\Http\Controllers\Backend;

use App\Define\Report;
use App\Helpers\GetOption;
use App\Models\ConcurrentContract;
use App\StaffDayOff;
use App\User;
use App\Staff;
use App\StaffFamily;
use App\Models\Company;
use App\Models\Contract;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Models\DepartmentGroup;
use App\Http\Controllers\Controller;
use App\Models\AllowanceCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        $types = \App\Define\Report::getAllReportsForOption();
        return view('backend.reports.index', compact('types'));
    }

    public function export(Request $request, $fileType = 'excel')
    {
        $cachedData = Cache::get('reports_data_' . $request->user()->id);
        if($cachedData && isset($cachedData['type'])) {
            switch ($fileType) {
                case 'excel':
                    if ($cachedData['type'] === Report::STAFF_LEAVE) return \Excel::download(new \App\Exports\LeaveExport($cachedData['type'], $cachedData['data']), 'JPT-HRM_' . str_slug(trans('reports.types.' . $cachedData['type'])) . '_' . date('Hmi-dm'). '.xlsx');
                    else return \Excel::download(new \App\Exports\ReportExport($cachedData['type'], $cachedData['data']), 'JPT-HRM_' . str_slug(trans('reports.types.' . $cachedData['type'])) . '_' . date('Hmi-dm'). '.xlsx');
                    break;
                default:
                    break;
            }
        } else {
            Session::flash('message', 'Bạn cần tạo báo cáo trước!');
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.reports.index');
        }
    }

    public function store(Request $request)
    {
        $response = ['message' => trans('system.have_an_error')];
        $statusCode = 200;
        if ($request->ajax()) {
            try {
                $type       = trim($request->input('type'));
                $filters    = $request->input('data');
                $query      = "1=1";    
                switch ($type) {
                    case \App\Define\Report::STAFF_DEPENDENCY:
                        $company        = $filters['company'] ?? -1;
                        $department     = $filters['department'] ?? -1;
                        $combined       = $filters['combined'] ?? -1;
                        $dependent      = $filters['dependent'] ?? -1;
                        $relationship   = $filters['relationship'] ?? "";
                        $age_operator   = $filters['age_operator'] ?? -1;
                        $age            = $filters['age'] ?? 0;
                        if ($company <> -1) {
                            $companies = Company::where('id', $company)->where('status', 1)->pluck('shortened_name', 'id')->toArray();
                        } else {
                            $companies = GetOption::getCompaniesForOption();;
                        }
                        if ($relationship) {
                            $relationships = \App\Defines\Staff::getFamilyRelationshipsForOption();
                            if (isset($relationships[$relationship])) {
                                $query .= " AND relationship = '" . $relationship . "'";
                            }
                        }
                        if ($age_operator <> -1 && $age > 0) {
                            $operators = \App\Define\Report::getOperators();
                            if (isset($operators[$age_operator])) {
                                $query .= " HAVING age " . $age_operator . ' ' . $age;
                            }
                        }
                        if ($dependent <> -1) {
                            $query .= " AND dependent=" . $dependent;
                        }
                        $data = [];
                        $_departments = [];
                        $_staffs = [];
                        $rowspan = [];
                        foreach ($companies as $companyId => $companyName) {
                            if ($department <> -1) {
                                $departments = Department::where('company_id', $companyId)->where('id', $department)->where('status', 1)->pluck('name', 'id')->toArray();
                            } else {
                                /*$departments = Department::where('company_id', $companyId)->where('status', 1)->pluck('name', 'id')->toArray();*/
                                $departments = $departments = GetOption::getDepartmentsForOptionPermissionForReport($companyId);
                            }
                            if (count($departments) == 0) continue;
                            $data[$companyId] = [];
                            foreach ($departments as $departmentId => $departmentName) {
                                $contacts = Contract::where('department_id', $departmentId)->where('status', 1)->where('type_status', 1)->get()->keyBy('user_id');
                                $staffs = [];
                                $total = 0;
                                foreach($contacts as $contact) {
                                    $staff = User::find($contact->user_id);
                                    if (is_null($staff)) continue;
                                    $families = StaffFamily::whereRaw('staff_id=' . $staff->id . ' AND ' . $query)->selectRaw("CASE WHEN (YEAR(CURRENT_TIMESTAMP) - YEAR(dob)) < 1 THEN 1 ELSE (YEAR(CURRENT_TIMESTAMP) - YEAR(dob)) END AS age, fullname, relationship, dob, dependent, dependent_from, dependent_to, tax_code")->get()->toArray();
                                    if (count($families)) {
                                        $data[$companyId][$departmentId][$staff->id] = $families;
                                        $_staffs[$staff->id] = $staff->code . '-' . $staff->fullname;
                                        $total += count($families);
                                    }
                                }
                                $_departments[$departmentId] = $departmentName;
                                $rowspan[$companyId][$departmentId] = $total;
                            }
                        }
                        Cache::forever('reports_data_' . $request->user()->id, ['type' => $type, 'data' => ['data' => $data, 'companies' => $companies, 'departments' => $_departments, 'staffs' => $_staffs, 'rowspan' => $rowspan]]);
                        $renderView = view("backend.reports.view.{$type}", ['type' => $type, 'data' => ['data' => $data, 'companies' => $companies, 'departments' => $_departments, 'staffs' => $_staffs, 'rowspan' => $rowspan]])->render();
                        break;

                    case \App\Define\Report::STAFF_LEAVE:
                        $company        = $filters['company'] ?? -1;
                        $department     = $filters['department'] ?? -1;
                        $fromMonth = $filters['from_month'] ?? 1;
                        $toMonth = $filters['to_month'] ?? 12;
                        $year = $filters['year'] ?? now()->year;
                        $companies = $departments = [];
                        if ($company <> -1) {
                            $companies = \App\Models\Company::where('id', $company)->where('status', 1)->pluck('shortened_name', 'id')->toArray();
                        } else {
                            /*$companies = \App\Models\Company::where('status', 1)->pluck('shortened_name', 'id')->toArray();*/
                            $companies = GetOption::getCompaniesForOption();
                        }
                        $data = $rowspan = $userAll = $_departments = [];
                        foreach ($companies as $companyId => $companyName) {
                            if ($department <> -1) {
                                $departments = Department::where('company_id', $companyId)->where('id', $department)->where('status', 1)->pluck('name', 'id')->toArray();
                            } else {
                               /* $departments = Department::where('company_id', $companyId)->where('status', 1)->pluck('name', 'id')->toArray();*/
                                $departments = GetOption::getDepartmentsForOptionPermissionForReport($companyId);
                            }
                            if (count($departments) == 0) continue;
                            $data[$companyId] = [];
                            $total = 0;
                            foreach ($departments as $departmentId => $departmentName) {
                                $users = User::where('department_id', $departmentId)->where('active', 1)->get(['id', 'code', 'fullname', 'staff_start', 'rest', 'original_rest']);
                                if (count($users) == 0) continue;
                                foreach ($users as $user) {
                                    $data[$companyId][$departmentId][$user->id] = $user;
                                }
                                $total += count($users);
                                $userId = $users->pluck('id')->toArray();
                                $userAll = array_merge($userAll, $userId);
                                $_departments[$departmentId] = $departmentName;
                            }
                            $rowspan[$companyId] = $total;
                        }
                        $leave = StaffDayOff::countTotalPerDayOffForUsers($userAll, $fromMonth,$toMonth, $year);
                        Cache::forever('reports_data_' . $request->user()->id,
                            ['type' => $type, 'data' => ['data' => $data, 'leave' => $leave, 'year'=> $year, 'companies' => $companies, 'departments' => $_departments, 'rowspan' => $rowspan]]);
                        $renderView = view("backend.reports.view.{$type}",
                            ['type' => $type, 'data' => ['data' => $data,  'leave' => $leave, 'year'=> $year, 'companies' => $companies, 'departments' => $_departments, 'rowspan' => $rowspan]])->render();
                        break;
                    case \App\Define\Report::STAFF_KPI:
                        $company        = $filters['company'] ?? -1;
                        $department     = $filters['department'] ?? -1;
                        $fromMonth = $filters['from_month'] ?? 1;
                        $toMonth = $filters['to_month'] ?? 12;
                        $year = $filters['year'] ?? now()->year;
                        $companies = $departments = [];
                        if ($company <> -1) {
                            $companies = \App\Models\Company::where('id', $company)->where('status', 1)->pluck('shortened_name', 'id')->toArray();
                        } else {
                            /*$companies = \App\Models\Company::where('status', 1)->pluck('shortened_name', 'id')->toArray();*/
                            $companies = GetOption::getCompaniesForOption();
                        }
                        $data = $rowspan = $userAll = $_departments = [];
                        foreach ($companies as $companyId => $companyName) {
                            if ($department <> -1) {
                                $departments = Department::where('company_id', $companyId)->where('id', $department)->where('status', 1)->pluck('name', 'id')->toArray();
                            } else {
                                /*$departments = Department::where('company_id', $companyId)->where('status', 1)->pluck('name', 'id')->toArray();*/
                                $departments =  GetOption::getDepartmentsForOptionPermissionForReport($companyId);
                            }
                            if (count($departments) == 0) continue;
                            $data[$companyId] = [];
                            $total = 0;
                            foreach ($departments as $departmentId => $departmentName) {
                                $users = User::where('department_id', $departmentId)->where('active', 1)->get(['id', 'code', 'fullname', 'staff_start', 'rest', 'original_rest']);
                                $_departments[$departmentId] = $departmentName;
                                if (count($users) == 0) continue;
                                foreach ($users as $user) {
                                    $data[$companyId][$departmentId][$user->id] = $user;
                                }
                                $total += count($users);
                                $userId = $users->pluck('id')->toArray();
                                $userAll = array_merge($userAll, $userId);
                            }
                            $rowspan[$companyId] = $total;
                        }
                        $allDeptIds = array_keys($_departments) ?? [];
                        $now = now()->format('Y-m-d');
                        $startD = date('Y-m-d', strtotime($year . '-' . $fromMonth . '-' . 26));
                        $endD = date('Y-m-d', strtotime($year . '-' . $toMonth . '-' . 25));
                        $concurrentContracts = ConcurrentContract::with('user')->whereIn('department_id', $allDeptIds)
                            ->where('valid_from', '<', $endD)
                            ->where('valid_to', '>=', $startD)
                            ->get(['id', 'user_id', 'company_id', 'department_id']);
                        if ($concurrentContracts) {
                            foreach ($concurrentContracts as $item) {
                                $data[$item->company_id][$item->department_id][$item->user_id] = $item->user;
                                $rowspan[$item->company_id] += 1;
                                array_push($userAll, $item->user_id);
                            }
                        }
                        $kpiData = \App\Target::whereIn('user_id', $userAll)
                            ->where('year', $year)
                            ->whereBetween('month', [$fromMonth, $toMonth])
                            ->orderBy('user_id')
                            ->orderBy('created_at', 'desc')
                            ->get()->groupBy(['user_id', 'month']);
                        $kpiUsers = \App\Target::getKpiUser($kpiData);
                        $temp = ['type' => $type, 'data' => ['data' => $data, 'kpiUsers' => $kpiUsers ,'year'=> $year, 'fromMonth' => $fromMonth, 'toMonth' => $toMonth, 'companies' => $companies, 'departments' => $_departments, 'rowspan' => $rowspan]];
                        Cache::forever('reports_data_' . $request->user()->id, $temp);
                        $renderView = view("backend.reports.view.{$type}", $temp)->render();
                        break;
          
                    case \App\Define\Report::CONTRACT:
                        $company        = $filters['company'] ?? -1;
                        $department     = $filters['department'] ?? -1;
                        $department_group   = $filters['department_group'] ?? -1;
                        $position_id   = $filters['position_id'] ?? -1;
                        $type_status   = $filters['type_status'] ?? -1;
                        $infoPermission = \App\PermissionUserObject::getMorePermissions();

                        if ($company <> -1) {
                            $query .= " AND company_id = " . intval($company);
                        } else {
                            if ($infoPermission['companies']) $query .= " AND company_id IN(" . implode(',', $infoPermission['companies']) . ")";
                        }
                       
                        if ($department_group <> -1) {
                            $query .= " AND department_group_id = " . intval($department_group);
                        }
                       
                        if ($department <> -1) {
                            $query .= " AND department_id = " . intval($department);
                        } else {
                            if ($infoPermission) {
                                $deptId = Auth::user()->department_id;
                                $deptIdArr = $infoPermission['departments'];
                                if (!in_array($deptId, $deptIdArr)) array_push($deptIdArr, $deptId);
                                $query .= " AND department_id IN(" . implode(',', $deptIdArr) . ")";
                            }
                        }
                       
                        if ($position_id <> -1) {
                            $query .= " AND position_id = " . intval($position_id);
                        }
                        
                        if ($type_status <> -1) {
                            $query .= " AND type_status = " . intval($type_status);
                        }

                        if (!is_null($filters['set_notvalid_on'])) {
                            $set_notvalid_on = explode('-', $filters['set_notvalid_on']);

                            $set_notvalid_on_from = trim($set_notvalid_on[0]);
                            $set_notvalid_on_from = strtotime(str_replace('/', '-', $set_notvalid_on_from));
                            $set_notvalid_on_from = date("Y-m-d", $set_notvalid_on_from);
                            $query = $query.' AND (set_notvalid_on >= "'. $set_notvalid_on_from .'")' ;

                            $set_notvalid_on_to = trim($set_notvalid_on[1]);
                            $set_notvalid_on_to = strtotime(str_replace('/', '-', $set_notvalid_on_to));
                            $set_notvalid_on_to = date("Y-m-d", $set_notvalid_on_to);
                            $query = $query.' AND (set_notvalid_on <= "'. $set_notvalid_on_to .'")' ;
                        }
                        
                        if (!is_null($filters['valid'])) {
                            $valid = explode('-', $filters['valid']);

                            $valid_from = trim($valid[0]);
                            $valid_from = strtotime(str_replace('/', '-', $valid_from));
                            $valid_from = date("Y-m-d", $valid_from);
                            $query = $query.' AND (valid_from >= "'. $valid_from. '")' ;
                            
                            $valid_to = trim($valid[1]);
                            $valid_to = strtotime(str_replace('/', '-', $valid_to));
                            $valid_to = date("Y-m-d", $valid_to);
                            $query = $query.' AND (valid_from <= "'. $valid_to. '")' ;
                        }
                        $contracts = Contract::whereRaw($query)->orderBy('company_id')->orderBy('department_id')->get();
                        $contracts->load('position', 'company', 'department', 'user', 'departmentGroup', 'qualification', 'allowances');
                        $allowanceCategory = AllowanceCategory::where('status', 1)->pluck('name','id')->toArray();
                        $typeStatus = \App\Defines\Contract::getTypeStatusForOption();  
                        $types = \App\Defines\Contract::getType();  
                        $temp = ['type' => $type, 'data' => ['contracts' => $contracts, 'typeStatus' => $typeStatus, 'types' => $types, 'allowanceCategory' => $allowanceCategory]];
                        Cache::forever('reports_data_' . $request->user()->id, $temp);
                        $renderView = view("backend.reports.view.{$type}", $temp)->render();
                        break;
                     
                    default: throw new \Exception("Bạn không có quyền truy nhập tài nguyên này.", 1);
                }

                $response['message']  = $renderView;
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

    public function getFilter(Request $request)
    {
        $response = ['message' => trans('system.have_an_error')];
        $statusCode = 200;
        if($request->ajax()) {
            try {
                $type = trim($request->input('type'));
//                $companies = Company::where('status', 1)->pluck('shortened_name', 'id')->toArray();
                $companies = GetOption::getCompaniesForOption();
                $departments = GetOption::getDepartmentsForOptionPermission();
//                $departments = \DB::table('departments')->where('departments.status', 1)->join('companies', 'companies.id', '=', 'departments.company_id')->selectRaw("CONCAT(companies.shortened_name, ' - ', departments.name) as name, departments.id")->pluck('name', 'id')->toArray();
                switch ($type) {
                    case \App\Define\Report::STAFF_DEPENDENCY:
                        $response['message'] = view('backend.reports.filter.FILTER_' . $type, compact('departments', 'companies'))->render();
                        break;
                    case \App\Define\Report::STAFF_LEAVE:
                        $monthOption = \App\Define\Timekeeping::getMonth();
                        $yearOption = \App\Define\Timekeeping::getYear();
                        $response['message'] = view('backend.reports.filter.FILTER_' . $type, compact('departments', 'companies', 'yearOption', 'monthOption'))->render();
                        break;
                    case \App\Define\Report::STAFF_KPI:
                        $monthOption = \App\Define\Timekeeping::getMonth();
                        $yearOption = \App\Define\Timekeeping::getYear();
                        $response['message'] = view('backend.reports.filter.FILTER_' . $type, compact('departments', 'companies', 'yearOption', 'monthOption'))->render();
                        break;
                    case \App\Define\Report::CONTRACT:
                        $monthOption = \App\Define\Timekeeping::getMonth();
                        $yearOption = \App\Define\Timekeeping::getYear();
                        $typeStatus = \App\Defines\Contract::getTypeStatusForOption();
                        $positions = \App\Helpers\GetOption::getStaffPositionsForOption();
                        $departmentGroups = DepartmentGroup::pluck('name', 'id')->toArray();
                        $response['message'] = view('backend.reports.filter.FILTER_' . $type, compact('departmentGroups', 'positions', 'typeStatus', 'departments', 'companies', 'yearOption', 'monthOption'))->render();
                        break;
                    default:
                        $response['message'] = "";
                        break;
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
}