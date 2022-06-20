<?php


namespace App\Helpers;

use App\User;
use App\Position;
use App\Qualification;
use App\StaffTitle;
use App\StaffPosition;
use App\Models\Company;
use App\Models\Contract;
use App\Models\Department;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class GetOption
{
    static function getStaffsForOption($id = null)
    {
//        $contractIds = Contract::pluck('staff_id');
//        $staffsOption = Staff::whereNotIn('id', $contractIds)->pluck('name', 'id')->toArray();
//        if ($id) {
//            $staff = Staff::where(['id'=>$id])->pluck('name', 'id')->toArray();
//            $staffsOption += $staff;
//        }
        return User::where('fullname', '<>', 'System')->where('fullname', '<>', 'Administrator')->pluck('fullname', 'id')->toArray();
    }

    static function getStaffTitlesForOption()
    {
        return Qualification::pluck('name', 'id')->toArray();
    }

    static function getStaffPositionsForOption()
    {
        return Position::pluck('name', 'id')->toArray();
    }

    static function getCompaniesForOption(array $company_ids = null)
    {
        $query = '1=1';
        $infoPermission = \App\PermissionUserObject::getMorePermissions();
        if ($infoPermission['companies']) $query .= " AND id IN(" . implode(',', $infoPermission['companies']) . ")";
        $companiesForOption = Company::whereRaw($query)->pluck('shortened_name', 'id')->toArray();
        if (is_null($company_ids)) return $companiesForOption;
        foreach ($company_ids as $id) {
            unset($companiesForOption[$id]);
        }
        return $companiesForOption;
    }

    static function getDepartmentsForOption($company_id)
    {
        return Company::find($company_id)->departments->pluck('name', 'id')->toArray();
    }

    static function getAllDepartmentsForOption()
    {
        return Department::pluck('name', 'id')->toArray();
    }

    static function getDepartmentsWithCompanyForOption($company_id)
    {
        return \DB::table('departments')->where('departments.status', 1)
            ->join('companies', 'companies.id', '=', 'departments.company_id')
            ->selectRaw("CONCAT(companies.shortened_name, ' - ', departments.name) as name, departments.id")
            ->pluck('name', 'id')
            ->toArray();
    }

    static function getDepartmentsForOptionPermission($infoPermission = null)
    {
        $query = '1=1';
        $query .= " AND departments.status = 1";
        $infoPermission = $infoPermission ?? \App\PermissionUserObject::getMorePermissions();
        if ($infoPermission) {
            $deptId = Auth::user()->department_id;
            $deptIdArr = $infoPermission['departments'];
            if ($deptId && !in_array($deptId, $deptIdArr)) array_push($deptIdArr, $deptId);
            $query .= " AND departments.id IN(" . implode(',', $deptIdArr) . ")";
        }
        $departmentsOption = Department::whereRaw($query)->with(['company'])->get();
        $result = [];
        if (count($departmentsOption)) {
            foreach ($departmentsOption as $dept) {
                $result[$dept->id] = $dept->company->shortened_name . ' - ' . $dept->name;
            }
        }
        return $result;
//        return \DB::table('departments')->whereRaw($query)->join('companies', 'companies.id', '=', 'departments.company_id')->selectRaw("CONCAT(companies.shortened_name, ' - ', departments.name) as name, departments.id")->pluck('name', 'id')->toArray();
    }

    static function getDepartmentsForOptionPermissionForReport($companyId, $infoPermission = null)
    {
        $query = '1=1';
        $query .= " AND departments.status = 1 AND company_id = {$companyId}";
        $infoPermission = $infoPermission ?? \App\PermissionUserObject::getMorePermissions();
        if ($infoPermission) {
            $deptId = Auth::user()->department_id;
            $deptIdArr = $infoPermission['departments'];
            if ($deptId && !in_array($deptId, $deptIdArr)) array_push($deptIdArr, $deptId);
            $query .= " AND departments.id IN(" . implode(',', $deptIdArr) . ")";
        }
        $departmentsOption = Department::whereRaw($query)->get();
        $result = [];
        if (count($departmentsOption)) {
            foreach ($departmentsOption as $dept) {
                $result[$dept->id] = $dept->name;
            }
        }
        return $result;
//        return Department::whereRaw($query)->pluck('name', 'id')->toArray();
    }

    public static function getArrDeptFromPermission($infoPermission = null)
    {
        $user = Auth::user();
        $deptId = $user->department_id;
        $infoPermission = $infoPermission ?? \App\PermissionUserObject::getMorePermissions();
        $deptPermission = [];
        if ($infoPermission) {
            $deptPermission = $infoPermission['departments'];
            if ($deptId && !in_array($deptId, $deptPermission) ) {
                array_push($deptPermission, $deptId);
            }
        }
        return $deptPermission;
    }

    static function getCompaniesForOptionIndex($infoPermission = null)
    {
        $query = '1=1';
        $infoPermission = $infoPermission ?? \App\PermissionUserObject::getMorePermissions();
        if ($infoPermission['companies']) $query .= " AND id IN(" . implode(',', $infoPermission['companies']) . ")";
        return Company::whereRaw($query)->pluck('shortened_name', 'id')->toArray();
    }

    static function getDepartmentsForOptionPermissionIndex($infoPermission = null)
    {
        $query = '1=1';
        $query .= " AND departments.status = 1";
        $infoPermission = $infoPermission ?? \App\PermissionUserObject::getMorePermissions();
        if ($infoPermission) {
            $deptId = Auth::user()->department_id;
            $deptIdArr = $infoPermission['departments'];
            if ($deptId && !in_array($deptId, $deptIdArr)) array_push($deptIdArr, $deptId);
            $query .= " AND departments.id IN(" . implode(',', $deptIdArr) . ")";
        }
        $departmentsOption = Department::whereRaw($query)->get();
        $result = [];
        if (count($departmentsOption)) {
            foreach ($departmentsOption as $dept) {
                $result[$dept->id] = $dept->name;
            }
        }
        return $result;
    }

    static function statusPayroll()
    {
        return [
            'Tất cả',
            'Đã duyệt',
            'Chưa duyệt'    
        ];
    }

    static function statusTimekeeping()
    {
        return [
            'Tất cả',
            'Đã chốt',
            'Chưa chốt'    
        ];
    }

    static function statusSalaryDeclaration()
    {
        return [
            'Tất cả',
            'TP duyệt',
            'KT duyệt',
            'Khởi tạo',    
        ];
    }
}