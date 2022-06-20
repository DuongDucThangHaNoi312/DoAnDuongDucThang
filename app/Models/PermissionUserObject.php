<?php

namespace App;

use App\Models\Team;
use App\Models\Company;
use App\Models\Department;
use App\Models\ConcurrentContract;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class PermissionUserObject extends Model
{
    use SoftDeletes;
    protected $table = 'permission_user_objects';
    protected $fillable = [ 'permission_user_id', 'object_id', 'object_type', 'deleted_by' ];

    public static function boot()
    {
        parent::boot();

        static::updated(function($model)
        {
            self::clearCache($model);
        });

        static::created(function($model)
        {
            self::clearCache($model);
        });

        static::deleted(function($model)
        {
            self::clearCache($model);
        });

        static::saved(function($model)
        {
            self::clearCache($model);
        });
    }

    public static function clearCache($model) {
        Cache::tags('staff_permissions')->flush();
    }

    public static function getCurrentModule($route)
    {
        $name = $route->getName();
        $module = "";

        if (substr($name, 0, 5) == 'admin' && count(explode('.', $name)) > 2) {
            $action = $route->getAction();
            if (isset($action['role'])) {
                $role = $action['role'];
                $role = substr($role, 6, strlen($role));
                $oneDot = explode('.', $role);
                if(isset($oneDot[1])) {
                    $module = str_replace('-', '_', substr($role, 0, strlen($role) - strlen(end($oneDot)) - 1)) . '.' . self::convertAction(end($oneDot));
                }
            } else {
                $routeName = substr($name, 6, strlen($name));
                $oneDot = explode('.', $routeName);
                if(isset($oneDot[1])) {
                    $module = str_replace('-', '_', substr($routeName, 0, strlen($routeName) - strlen(end($oneDot)) - 1)) . '.' . self::convertAction(end($oneDot));
                }
            }
        }
        return $module;
    }

    static function convertAction($action)
    {
        if ($action == 'index' || $action == 'show') {
            $action = 'read';
        } elseif ($action == 'edit' || $action == 'update') {
            $action = 'update';
        } elseif ($action == 'create' || $action == 'store') {
            $action = 'create';
        } elseif($action == 'destroy') {
            $action = 'delete';
        }
        return $action;
    }

    public static function getMorePermissions($staffId = "", $route = "")
    {
        /*
        cong ty => all,
        phong   => [kiem nhiem, phong hien tai, phong qly them] // neu la tp
                => [kiem nhiem, phong qly them] // neu la kiem nhiem
        detail  => [
            congtyid => all phong
        ]
        */
        if ($staffId == "") $staffId = auth()->guard('admin')->user()->id;
        if ($route == "") $route = PermissionUserObject::getCurrentModule(Route::getCurrentRoute());
        $data = [];
        $key = "staff_" . $staffId . "_permission";
        if (1) {
            if ($staffId == "") $user = auth()->guard('admin')->user();
            else $user = User::find(intval($staffId));
            if (is_null($user)) return [];
            if ($user->position_id == \App\Define\Constant::POSITION_TGD || $user->hasRole(['system', 'administrator'])) {
                return [];
            }
            $check = -1;
            $_allDepartments = [];
            $detail = [
                $user->company_id => [$user->department_id]
                // array_push($_allDepartments, $user->department_id);
            ];
            $ctyThemTuPhongKiemNhiemVaCungNhom = $phongQLThem = $phongCungNhom = $phongKiemNhiem = [];
            if (in_array($user->position_id, [\App\Define\Constant::POSITION_TP, \App\Define\Constant::POSITION_TPHCNS])) { // neu la truong phong
                $phongCungNhom = array_column(\DB::select("select department_id, department_id from department_relationships where group_id IN (select id from department_groups where id IN (select group_id from department_relationships where department_id=" . $user->department_id . ") AND status=1 AND only_manager=1)"), 'department_id', 'department_id'); // da bao gom ca phong hien tai roi
            } else {
                $check = 0;
            }
            // tim kiem nhiem tp khac du la nvien hay tp
            $phongKiemNhiem = ConcurrentContract::where('status', 1)->where('user_id', $user->id)->whereIn('position_id', [\App\Define\Constant::POSITION_TP, \App\Define\Constant::POSITION_TPHCNS])->pluck('department_id', 'department_id')->toArray();
            if (count($phongKiemNhiem)) {
                if (!in_array($user->position_id, [\App\Define\Constant::POSITION_TP, \App\Define\Constant::POSITION_TPHCNS])) $check = 1;
            }
            if (count($phongKiemNhiem)) {
                $ctyThemTuPhongKiemNhiemVaCungNhom = Department::whereIn('id', $phongCungNhom + $phongKiemNhiem)->where('status', 1)->pluck('company_id', 'id')->toArray();
                foreach($ctyThemTuPhongKiemNhiemVaCungNhom as $maPB => $maCT) {
                    if (!isset($detail[$maCT])) $detail[$maCT] = [];
                    if (!in_array($maPB, $detail[$maCT])) array_push($detail[$maCT], $maPB);
                }
            }
            $allCompanies = array_values($ctyThemTuPhongKiemNhiemVaCungNhom);
            if (!in_array($user->company_id, $allCompanies)) {
                array_push($allCompanies, $user->company_id);
            }
            // add cho permission ở phần phân quyền, ko ở phần add thêm
            foreach($user->allPermissions() as $permission) {
                $data[$permission->name] = [
                    'companies'     => $allCompanies,
                    'departments'   => array_values($phongCungNhom + $phongKiemNhiem),
                    'teams'         => [],
                    'detail'        => $detail,
                    'check'         => $check,
                ];
            }
            // tìm các permission add thêm
            $_permissions = Permission::select('module', 'id', 'name', 'action')->get()->keyBy('id')->toArray();
            $curPermissions = PermissionUser::where('user_id', $user->id)->whereIn('permission_id', array_keys($_permissions))->get();
            if ($curPermissions->count()) {
                $permissionObjects = PermissionUserObject::whereIn('permission_user_id', array_column($curPermissions->toArray(), 'id'))->get()->keyBy('id');
                foreach($curPermissions as $curPermission) {
                    if (!$curPermission->manager_other) {
                        $data[$_permissions[$curPermission->permission_id]['name']] = [
                            'companies'     => $allCompanies,
                            'departments'   => array_values($phongCungNhom + $phongKiemNhiem),
                            'teams'         => [],
                            'detail'        => $detail,
                            'check'         => $check,
                        ];
                    } else {
                        $detail1 = [];
                        $ctyQLThem = $permissionObjects->where('object_type', Company::class)->where('permission_user_id', $curPermission->id)->pluck('object_id')->toArray();
                        $phongQLThem = $permissionObjects->where('object_type', Department::class)->where('permission_user_id', $curPermission->id)->pluck('object_id', 'object_id')->toArray();
                        foreach($ctyQLThem as $ctyQLThemId) {
                            $tmp = Department::where('status', 1)->where('company_id', $ctyQLThemId)->pluck('id')->toArray();
                            // $detail[$ctyQLThemId] = $tmp;
                            // neu la nhan vien thi lay them cac phong tu cty quan ly them Aug06
                            // ca tp va hcns cung lay them Oct19
                            // if (!in_array($user->position_id, [\App\Define\Constant::POSITION_TP, \App\Define\Constant::POSITION_TPHCNS])) {
                                foreach($tmp as $t) {
                                    $phongQLThem[$t] = $t;
                                }
                            // }
                        }
                        $allDepartments = array_merge($phongQLThem, $phongCungNhom, $phongKiemNhiem);
                        $allDepartments = array_unique($allDepartments);
                        $ctyThemTuPhong = Department::whereIn('id', $allDepartments)->where('status', 1)->pluck('company_id', 'id')->toArray();
                        // quet lai cac phong ban lan nua
                        foreach($ctyThemTuPhong as $pbId => $ctyId) {
                            if (!isset($detail1[$ctyId])) $detail1[$ctyId] = [];
                            if (!in_array($pbId, $detail1[$ctyId])) {// && isset($phongQLThem[$pbId])
                                array_push($detail1[$ctyId], $pbId);
                            }
                        }
                        // last
                        $companies = $details = [];
                        foreach($detail as $k => $d) {
                            $companies[$k] = $k;
                            foreach($d as $d1) {
                                if (!isset($details[$k])) $details[$k] = [];
                                array_push($details[$k], $d1);
                            }
                            $details[$k] = array_unique($details[$k]);
                        }
                        foreach($detail1 as $k => $d) {
                            $companies[$k] = $k;
                            foreach($d as $d1) {
                                if (!isset($details[$k])) $details[$k] = [];
                                array_push($details[$k], $d1);
                            }
                            $details[$k] = array_unique($details[$k]);
                        }
                        $data[$_permissions[$curPermission->permission_id]['name']] = [
                            'companies'     => $companies,
                            'departments'   => $allDepartments,// chi co dept add them
                            'teams'         => $permissionObjects->where('object_type', Team::class)->where('permission_user_id', $curPermission->id)->pluck('object_id')->toArray(),
                            'detail'        => $details,
                            'check'         => $check,
                        ];
                    }
                }
            }
            $data = json_encode($data);
            // Cache::tags('staff_permissions')->forever($key, $data);
        } else {
            $data = Cache::tags('staff_permissions')->get($key);
        }
        $data = json_decode($data, 1);
        $currentRoute = explode('.', $route);
        if (isset($currentRoute[1]) && $currentRoute[1] == 'read') {
            $read = $data[$route] ?? [];
            $companies = $read['companies'] ?? [];
            $departments = $read['departments'] ?? [];
            $teams = $read['teams'] ?? [];
            $detail = $read['detail'] ?? [];
            // add them cac thong tin cho read
            foreach($data as $key => $d) {
                if (substr($key, 0, strlen($currentRoute[0])) == $currentRoute[0]) {
                    $addMore = $data[$key];
                    $companies = array_merge($companies, $addMore['companies']);
                    $departments = array_merge($departments, $addMore['departments']);
                    $teams = array_merge($teams, $addMore['teams']);
                    foreach($addMore['detail'] as $companyId => $moreDetail) {
                        if (isset($detail[$companyId])) {
                            $detail[$companyId] = array_values(array_unique(array_merge($detail[$companyId], $moreDetail)));
                        } else {
                            $detail[$companyId] = $moreDetail;
                        }
                    }
                }
            }
            return [
                'companies'     => array_values(array_unique($companies)),
                'departments'   => array_values(array_unique($departments)),
                'teams'         => array_values(array_unique($teams)),
                'detail'        => $detail,
                'check'         => $read['check'] ?? 0,
            ];
        }
        return $data[$route] ?? [];
    }

    public static function getQueryPermission($userId, $route = null)
    {
        $query = "1=1";
        $infoPermission = PermissionUserObject::getMorePermissions($userId, $route);
        if ($infoPermission['departments']) {
            // if ($infoPermission['companies']) $query .= " AND company_id IN(" . implode(',', $infoPermission['companies']) . ")";
            if ($infoPermission['departments']) $query .= " AND department_id IN(" . implode(',', $infoPermission['departments']) . ")";
        } else {
            $user = User::find(Auth::user()->id, ['department_id']);
            if (!empty($user->department_id)) $query .= " AND department_id IN(" . $user->department_id . ")";
        }

        return $query;
    }

    public static function getTeamQueryPermission($userId, $route = null)
    {
        $query = "1=1";
        $infoPermission = PermissionUserObject::getMorePermissions($userId, $route);
        if ($infoPermission['teams']) {
            if ($infoPermission['teams']) $query .= " AND id IN(" . implode(',', $infoPermission['teams']) . ")";
        } else {
            $query = '';
        }

        return $query;
    }
}