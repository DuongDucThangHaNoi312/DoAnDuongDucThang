<?php
namespace App\Define;
use App\Models\Company;
use App\Models\Department;
use App\Models\Team;
use App\StaffTitle;
use App\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class OverTime
{
    const GENDER_MALE = 0;
    const GENDER_FEMALE = 1;
    const UNIVERSITY = 0;
    const COLLEGE = 1;
    const TYPE_NORMAL = 1; //150
    const TYPE_DAYOFF = 2; // 200
    const TYPE_HOLIDAY = 3; // 300
    const NIGHT_DAYOFF = 4; // 270 
    const NIGHT_NORMAL = 5; // 200
    const NIGHT_HOLIDAY = 6; // 390
    const NIGHT_AND_DAY = 7; // 210


    public static function getCompanyNamesForOption()
    {
        $name = Company::all();
        return Arr::pluck($name, 'shortened_name', 'id');
    }
    public static function getDepartmentNamesForOption()
    {
        $name = Department::all();
        return Arr::pluck($name, 'name', 'id');
    }
    public static function getUserNamesForOption($departmentId = '')
    {
        if (Auth::user()->hasRole('TGD') || Auth::user()->hasRole('system')) {
            $name = User::all();
        } else if (Auth::user()->hasRole('TP')) {
            $name = User::where('department_id', Auth::user()->department->id)->get();
        } else if (Auth::user()->hasRole('LEADER')) {
            $team = Team::where('user_id', Auth::user()->id)->with('users')->get()->pluck('users.*.user_id');
            $userIds = $team[0];
            array_push($userIds, Auth::user()->id);
            $name = User::whereIn('id', $userIds)->get();
        }
        return Arr::pluck($name, 'fullname', 'id');
    }

    public static function getTitleNamesForOption()
    {
        $name = StaffTitle::all();
        return Arr::pluck($name, 'name', 'id');
    }
    public static function getGendersForOption()
    {
        return [self::GENDER_MALE => trans('recruitment.gender.' . self::GENDER_MALE),
            self::GENDER_FEMALE => trans('recruitment.gender.' . self::GENDER_FEMALE),];
    }
    public static function getLevelForOption()
    {
        return [self::UNIVERSITY => trans('recruitment.education_level.' . self::UNIVERSITY),
            self::COLLEGE => trans('recruitment.education_level.' . self::COLLEGE),];
    }


}