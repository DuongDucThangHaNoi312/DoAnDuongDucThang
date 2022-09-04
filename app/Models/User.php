<?php

namespace App\Models;

use App\Models\Company;
use App\Models\ConcurrentContract;
use App\Models\Contract;
use App\Models\DepartmentGroup;
use App\Models\DepartmentRelationship;
use App\Models\Team;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\HasApiTokens;
use Illuminate\Auth\UserInterface;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Reminders\RemindableInterface;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Deduction;
use App\Models\Impale;
use App\Models\PayOff;
use App\Models\UserTeam;

use Laratrust\Traits\LaratrustUserTrait;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, SoftDeletes, LaratrustUserTrait;

    protected $dates = ['deleted_at', 'date_of_birth', 'issued_on'];
    protected $hidden = ['password', 'remember_token'];
    protected $guarded = [];

    public static function rules($id = 0)
    {
        return [
            'code'          => 'required|max:50|regex:/^[A-Za-z0-9_.-]+$/|unique:users,code' . ($id == 0 ? '' : ',' . $id),
            'addresses'     => 'required|max:255',
            'nationality'   => 'required|max:255',
            'id_card_no'    => 'required|unique:users,id_card_no' . ($id == 0 ? '' : ',' . $id),
            'issued_on'     => 'required',
            'issued_at'     => 'required|max:255',
            'fullname'      => 'required|max:255',
            'date_of_birth' => 'required',
            'gender'        => 'required',
            'phone'         => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
            'email'         => 'nullable|max:50|min:10|email|unique:users,email' . ($id == 0 ? '' : ',' . $id),
        ];
    }


    public static function rule_image()
    {
        return [
            'image'  => 'required'
        ];
    }

    public function setValidFromAttribute($value)
    {
        $this->attributes['issued_on'] = date("Y-m-d 00:00:00", strtotime(str_replace('/', '-', $value)));
    }

    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    public function getAuthPassword()
    {
        return $this->password;
    }

    public function getReminderEmail()
    {
        return $this->email;
    }

    public function getRememberToken()
    {
        return $this->remember_token;
    }

    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }

    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    public static function apiRules($id = 0)
    {
        return [
            'email'             => 'required|max:50|min:10|email|unique:members,email' . ($id == 0 ? '' : ',' . $id),
            'fullname'          => 'required|max:50',
            'password'          => 'required|max:20|min:6',
        ];
    }

    public static function userInfoRules($timeline_id, $updated_username)
    {
        return [
            'username'  => ($updated_username ? '' : 'required|max:50|min:6|regex:/^[a-zA-Z0-9_]+([_.][a-zA-Z0-9_]+)*$/|unique:timelines,username,' . $timeline_id),
            'name'      => 'required',
            //'dob'       => 'date_format:"d/m/Y"',
            'gender'    => 'in:0,1,2',
            'about'     => 'max:255',
        ];
    }

    public static function updatePasswordRules()
    {
        return [
            'old_password'      => 'required|max:20|min:6',
            'new_password'      => 'required|max:20|min:6|different:old_password',
            're_new_password'   => 'same:new_password',
        ];
    }

    public static function setPasswordRules()
    {
        return [
            'new_password'      => 'required|max:20|min:6',
            're_new_password'   => 'same:new_password',
        ];
    }

    public static function apiSocialRules()
    {
        return [
            'email'             => 'max:50|min:10|email',
            'facebook_id'       => 'required_without_all:google_id',
            'google_id'         => 'required_without_all:facebook_id',
            'access_token'      => 'required',
        ];
    }

    public static function apiLoginRules()
    {
        return [
            'email'             => 'required|max:50|min:10|email',
            'password'          => 'required|max:20|min:6',
        ];
    }

    public function target()
    {
        return $this->hasMany(Target::class, 'user_id');
    }

    public function targetCurrentMonth()
    {
        $m = date('m');
        $y = date('Y');
        return $this->hasOne(Target::class, 'user_id')->where('month', $m)->where('year', $y);
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    public function department()
    {
        return $this->belongsTo('\App\Models\Department', 'department_id');
    }

    // public function position()
    // {
    //     return $this->belongsTo('App\Position', 'position_id');
    // }

    // public function UserImages()
    // {
    //     return $this->hasMany(UserImageModel::class, 'user_id');
    // }

    // public function contracts()
    // {
    //     return $this->hasMany(Contract::class);
    // }

    // public function activeContract()
    // {
    //     return $this->hasone(Contract::class)->where('type_status', 1);
    // }

    // public function nearContract()
    // {
    //     return $this->hasOne(Contract::class)->with(['company:id,shortened_name', 'department:id,name'])->latest();
    // }

    // public function getStaffsForOption()
    // {
    //     return $this->pluck('name', 'id')->toArray();
    // }

    // public function dayOffs()
    // {
    //     return $this->hasMany(StaffDayOff::class, 'user_id');
    // }

    // public static function userRole()
    // {
    //     $user_groups = [Auth::user()->id];
    //     $contractsId = Contract::where('user_id', Auth::user()->id)->where('status', 1)->first();
    //     $concurrentContracts = ConcurrentContract::where('contract_id', $contractsId->id)->get();
    //     foreach ($concurrentContracts as $concurrentContract) {
    //         if (Position::find($concurrentContract->position_id)->code == 'TP') {
    //             $users = User::where('department_id', $concurrentContract->department_id)->get();
    //             foreach ($users as $user) {
    //                 array_push($user_groups, $user->id);
    //             }
    //         }
    //     }
    //     $users = User::where('department_id', $contractsId->department_id)->get();
    //     foreach ($users as $user) {
    //         array_push($user_groups, $user->id);
    //     }
    //     $departmentId = $contractsId->department_id;
    //     $hasGroup = DepartmentRelationship::where('department_id', $departmentId)->first();
    //     if ($hasGroup) {
    //         $group = DepartmentGroup::find($hasGroup->group_id);
    //         if ($group->only_manager) {
    //             $deptSameManager = DepartmentRelationship::where('group_id', $group->id)->where('department_id', '<>', $departmentId)->pluck('department_id')->toArray();
    //             $users = User::whereIn('department_id', $deptSameManager)->get();
    //             foreach ($users as $user) {
    //                 array_push($user_groups, $user->id);
    //             }
    //         }
    //     }
    //     $cacheSave = Cache::forever('user_group', array_unique($user_groups));
    //     $cache = Cache::get('user_group');
    //     return $cache;
    // }

    // public function deductions()
    // {
    //     return $this->hasMany(Deduction::class);
    // }

    // public function role()
    // {
    //     return $this->belongsToMany(Role::class);
    // }

    public static function countUserRelationship($userId)
    {
        return StaffFamily::where('staff_id', $userId)->sum('dependent');
    }

    public function team()
    {
        return $this->hasOne(UserTeam::class);
    }

    // public function teamOfLead()
    // {
    //     return $this->hasOne(Team::class);
    // }

    // public function hasAnyRole(...$roles): bool
    // {
    //     return $this->hasRole($roles);
    // }

    // public function oneYearOld()
    // {
    //     $checkDate = date('Y-m-d', strtotime('-1 year'));

    //     return $this->hasMany(StaffFamily::class, 'staff_id')->where('dob', '>=', $checkDate);
    // }

    // public static function checkBaby($userId)
    // {
    //     $user = User::where('id', $userId)->get(['id', 'fullname'])->first();
    //     $user->load('oneYearOld');
    //     if (count($user->oneYearOld) >= 1) {
    //         return 1;
    //     }
    //     return 0;
    // }

    // public function permissionObjects()
    // {
    //     return $this->hasMany(PermissionUserObject::class);
    // }

    // public function permissionsUser()
    // {
    //     return $this->hasMany(PermissionUser::class);
    // }

    // public static function getQueryPermission($infoPermission, $userId, $key, $isUserModule = null)
    // {
    //     $query = "";
    //     if ($infoPermission) {
    //         if ($infoPermission['teams']) {
    //             $userIdTeam = UserTeam::whereIn('team_id', array_unique($infoPermission['teams']))->pluck('user_id')->toArray();
    //             array_push($userIdTeam, $userId);
    //             $userIds2 = [];
    //             if ($infoPermission['departments']) {
    //                 $depts = array_unique($infoPermission['departments']);
    //                 $query1 = '1=1';
    //                 $query1 .= " AND department_id IN(" . implode(',', $depts) . ")";
    //                 $userIds2 = User::whereRaw($query1)->pluck('id')->toArray();
    //             }
    //             $userIdEnd = array_merge($userIdTeam, $userIds2);
    //             $userIdEnd = array_unique($userIdEnd);
    //             $query = " AND " . $key . " IN(" . implode(',', $userIdEnd) . ")";
    //         } else {
    //             if ($infoPermission['departments']) {
    //                 if ($isUserModule) $query = " AND ( department_id IN(" . implode(',', array_unique($infoPermission['departments'])) . ") OR created_by = {$userId} )";
    //                 else $query = " AND department_id IN(" . implode(',', array_unique($infoPermission['departments'])) . ")";
    //             } else {
    //                 if ($isUserModule) $query = " AND (" . $key . " = {$userId}" . " OR created_by = {$userId} )";
    //                 else $query = " AND " . $key . " = {$userId}";
    //             }
    //         }
    //     }
    //     return $query;
    // }

    // public function contractActive()
    // {
    //     return $this->hasOne(Contract::class)->where('status', 1)->latest();
    // }

    // public static function countStaffFollowPer($infoPermission)
    // {
    //     $staffs = [];
    //     if ($infoPermission) {
    //         if ($infoPermission['departments']) {
    //             $staffs = User::whereIn('department_id', array_unique($infoPermission['departments']))->get();
    //         }
    //     }
    //     return count($staffs);
    // }

    // public static function syncAttendanceMachine()
    // {
    //     $userHasSubMachines = User::whereNotNull('code_timekeeping_subs')
    //         ->where('code_timekeeping_subs', '<>', '')
    //         ->pluck('code_timekeeping_subs', 'code_timekeeping')->toArray();
    //     $needConvert = [];
    //     foreach ($userHasSubMachines as $userMainMachine => $userHasSubMachines) {
    //         $tmp = explode(",", $userHasSubMachines);
    //         foreach ($tmp as $t) {
    //             //if ($t == intval($t)) {
    //                 $needConvert[intval($t)] = $userMainMachine;
    //             //}
    //         }
    //     }
    //     $rows = \DB::table("jupiter-attendance.CHECKINO")
    //         ->whereIn('primary_code', array_keys($needConvert))
    //         ->orWhere('primary_code', NULL)
    //         ->get();
    //     $sqlRaw = "";
    //     $counter = 1;
    //     foreach ($rows as $row) {
    //         $sqlRaw .= "UPDATE `jupiter-attendance`.CHECKINO SET primary_code=" . ($needConvert[$row->userenroll] ?? $row->userenroll) . " WHERE id=" . $row->id . ";";
    //         if (++$counter == 5) {
    //             \DB::unprepared($sqlRaw);
    //             $sqlRaw = "";
    //             $counter = 1;
    //         }
    //     }
    //     if ($sqlRaw) \DB::unprepared($sqlRaw);
    //     return true;
    // }

    // public function payoffs()
    // {
    //     return $this->hasMany(PayOff::class);
    // }

    // public function impales()
    // {
    //     return $this->hasMany(Impale::class);
    // }

    // public static function isCodeTimekeepingExist($codeTimekeeping = null, $userId = 0)
    // {
    //     $arrAllCode = User::where('code_timekeeping', '<>', '')->where('id', '<>', $userId)->pluck('code_timekeeping')->toArray();
    //     $arrAllCodeSub = User::where('code_timekeeping_subs', '<>', '')->where('id', '<>', $userId)->pluck('code_timekeeping_subs')->toArray();
    //     $arrAllCodeSubFormat = [];
    //     foreach ($arrAllCodeSub as $codeSub) {
    //         $itemExplode = explode(',', $codeSub);
    //         if (count($itemExplode) > 1) {
    //             foreach ($itemExplode as $item) {
    //                 $arrAllCodeSubFormat[] = $item;
    //             }
    //         } else {
    //             $arrAllCodeSubFormat[] = $codeSub;
    //         }
    //     }

    //     if (in_array($codeTimekeeping, array_merge($arrAllCode, $arrAllCodeSubFormat))) return true;
    //     return false;
    // }

    // public static function getUserByIds($userIds = [])
    // {
    //     return self::whereIn('id', $userIds)
    //         ->get();
    // }

    public static function countActives()
    {
        return self::count();
    }
}
