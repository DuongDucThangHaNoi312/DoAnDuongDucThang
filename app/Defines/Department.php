<?php

namespace App\Define;

use App\Models\Company;
use App\Target;
use App\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Department
{
    const DEPARTMENT_ACTIVE     = 1;
    const DEPARTMENT_INACTIVE   = 0;

    const FUNCTIONAL_OFFICE     = 1; //Hải đang dùng cái này check phòng ban hành chính
    const DECLARATION_OFFICE    = 2; //Hải đang dùng cái này check phòng ban theo ca
    const BORDER_OFFICE         = 3;
    const WAREHOUSE_OFFICE      = 4;
    const TRUCK_OFFICE = 5;

    const OFFICE_TYPE = 1;
    const SHIFT_TYPE = 2;

    const HOURS                 = 9;

    public static function getCompanyNamesForOption()
    {
        $name = Company::all();
        return Arr::pluck($name, 'shortened_name', 'id');
    }

    public static function getDepartmentNamesForOption()
    {
        $name = DB::table('departments')
            ->select('id', 'name')
            ->whereNotIn('id', DB::table('department_relationships')->select('department_id'))
            ->get();
        return Arr::pluck($name, 'name', 'id');
    }
    public static function getDepartmentNamesEditForOption($groupId)
    {

        $name = DB::table('departments')
            ->select('id', 'name')
            ->whereNotIn('id', DB::table('department_relationships')->select('department_id')->where('group_id','<>',$groupId))
            ->get();
        return Arr::pluck($name, 'name', 'id');
    }

    public static function getUser()
    {
        $targets = Target::all();
        $targets->load('user');
        $auth = Auth::user()->department_id;
        $users = User::where('department_id', $auth)->where('position_id', '!=', 2)->get();
        return Arr::pluck($users, 'users');
    }

    public static function getTypeDepartmentGroups()
    {
        return [
            self::FUNCTIONAL_OFFICE => trans('departments.type_groups.'. self::FUNCTIONAL_OFFICE ),
            self::DECLARATION_OFFICE => trans('departments.type_groups.'. self::DECLARATION_OFFICE ),
            self::BORDER_OFFICE => trans('departments.type_groups.'. self::BORDER_OFFICE),
            self::WAREHOUSE_OFFICE => trans('departments.type_groups.'. self::WAREHOUSE_OFFICE),
            self::TRUCK_OFFICE => trans('departments.type_groups.'. self::TRUCK_OFFICE)
        ];
    }

}