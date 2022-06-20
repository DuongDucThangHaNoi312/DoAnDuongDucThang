<?php

namespace App\Models;

use App\User;
use App\Position;
use App\StaffDayOff;
use App\Defines\Staff;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $table = 'departments';
    protected $fillable = ['name','name_es', 'address','address_es', 'telephone', 'description', 'company_id', 'status', 'type', 'code'];

    public static function rules($id = 0)
    {
        return [
            'name' => 'required|max:255' . ($id == 0 ? '' : ',' . $id),
            'telephone' => 'required|regex:/(0)[0-9]/iD|numeric|min:10' . ($id == 0 ? '' : ',' . $id),
            'company_id' => 'required',
            'address' => 'required',
            'type' => 'required',
            'code'  => 'nullable|max:50|regex:/^[A-Za-z0-9_.-]+$/|unique:departments,code' . ($id == 0 ? '' : ',' . $id),
        ];
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function getDepartmentsForOption()
    {
        return $this->pluck('name', 'id')->toArray();
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function dayOffs()
    {
        return $this->hasManyThrough(StaffDayOff::class, User::class, 'department_id', 'user_id', 'id');
    }

    public function departmentDayOffs(){
        return $this->hasMany(CalendarDepartment::class,'department_id');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public static function checkDepartmentHasManager($departmentId)
    {
        $hasGroup = DepartmentRelationship::where('department_id', $departmentId)->first();
        if (!$hasGroup) {
            $contracts = Contract::where('department_id', $departmentId)->get();
            if (!$contracts) return true;
            foreach ($contracts as $contract) {
                if ($contract->position_id == Staff::WEIGHT_TP && $contract->check_valid != \App\Defines\Contract::NOT_VALID) return false;
            }
            return true;
        }
        $group = DepartmentGroup::where('id', $hasGroup->group_id)->first();
        if ($group->multi_manager) return true;
        return false;
    }

    public static function validateDepartmentPosition($departmentId, $positionId, $oldDepartmentId = null, $oldPositionId = null, $userId = null)
    {
        if ($oldPositionId && $oldPositionId == $positionId && $oldDepartmentId == $departmentId) return true;
        $position = Position::find($positionId);
        $isUnique = $position->unique_in_dept;
        $positionCode = $position->code;
        $contract = Contract::where('department_id', $departmentId)->where('status', 1)
            ->where('position_id', $positionId)->first();
        if ($contract) {
            return !$isUnique;
        }
        if ($positionCode != 'TP') return true;
        $hasGroup = DepartmentRelationship::where('department_id', $departmentId)->first();
        if (!$hasGroup) {
            return true;
        }
        $group = DepartmentGroup::find($hasGroup->group_id);
        if ($group->only_manager) {
            $childrenDept = DepartmentRelationship::where('group_id', $group->id)->get();
            foreach ($childrenDept as $child) {
                $contractChild = Contract::where('department_id', $child->department_id)->where('status', 1)
                    ->where('position_id', $positionId)->first();
                if ($contractChild && $contractChild->user_id != $userId) return false;
            }
            return true;
        }
        return true;
    }

    public static function checkDepartmentHasPosition($departmentId, $weight)
    {
        $contracts = Contract::where('department_id', $departmentId)->get();
        if (!$contracts) return false;
        foreach ($contracts as $contract) {
            if ($contract->position_id == $weight && $contract->check_valid != \App\Defines\Contract::NOT_VALID) return false;
        }
        return true;
    }

    public static function departmentsRole()
    {
        $department_group = [Auth::user()->department_id];
        $departmentID = Auth::user()->department_id;
        $departmentGroup = DepartmentRelationship::where('department_id', $departmentID)->first();
        if (count($departmentGroup) > 0) {
            $groups = DepartmentRelationship::where('group_id', $departmentGroup->group_id)->get();
            foreach ($groups as $group) {
                if ($group->department_id != $departmentID) {
                    array_push($department_group, $group->department_id);
                }
            }
        }
        $contractsId = Contract::where('user_id', Auth::user()->id)->where('status', 1)->first();
        $concurrentContracts = ConcurrentContract::where('contract_id', $contractsId->id)->get();
        foreach ($concurrentContracts as $concurrentContract) {
            if ($concurrentContract->department_id != $departmentID) {
                array_push($department_group, $concurrentContract->department_id);
            }
        }
        Cache::forever('department_group', array_unique($department_group));
        $cache = Cache::get('department_group');
        return $cache;
    }

    public static function departmentsOption(){
        $departmentIDs = Department::departmentsRole();
        $department = Department::whereIn('id',$departmentIDs)->pluck('name', 'id')->toArray();
        return $department;
    }

    public static function getDeptOffGroupOnlyManager($departmentId)
    {
        $hasGroup = DepartmentRelationship::where('department_id', $departmentId)->first();
        if ($hasGroup) {
            $group = DepartmentGroup::find($hasGroup->group_id);
            if ($group->only_manager) {
                return DepartmentRelationship::where('group_id', $group->id)->where('department_id', '<>', $departmentId)->pluck('department_id')->toArray();
            }
        }
        return false;
    }

    public static function getDeptOffGroup($departmentId)
    {
        $hasGroup = DepartmentRelationship::where('department_id', $departmentId)->first();
        if ($hasGroup) {
            $group = DepartmentGroup::find($hasGroup->group_id);
            $temp = DepartmentRelationship::where('group_id', $group->id)->where('department_id', '<>', $departmentId)->get();
            return count($temp) ? DepartmentRelationship::where('group_id', $group->id)->where('department_id', '<>', $departmentId)->pluck('department_id')->toArray() : [];
        }
        return false;
    }

    public static function getGroupOfDept($departmentId)
    {
        $hasGroup = DepartmentRelationship::where('department_id', $departmentId)->first();
        if (count($hasGroup) > 0) {
            return $hasGroup->group_id;
        }
        return null;
    }

    public static function isDeptWarehouse($id)
    {
        $deptName = Department::find(intval($id))->name;
        $pos = strpos($deptName, 'Kho');
        if ($pos === false) return false;
        else return true;
    }

    public static function getGroupFromMultiDepts($deptIds)
    {
        $result = [];
        $hasGroup = DepartmentRelationship::whereIn('department_id', $deptIds)->get();
        if (count($hasGroup) > 0) {
            foreach ($hasGroup as $item){
                $result[] = $item->group_id;
            }
        }
        return $result;
    }

    public static function countActiveDepts()
    {
        return self::where('status', 1)
            ->count();
    }
}
