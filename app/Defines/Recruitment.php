<?php
namespace App\Define;
use App\Models\Company;
use App\Models\Department;
use App\Position;
use App\Qualification;
use App\StaffTitle;
use Illuminate\Support\Arr;


class Recruitment
{
    const GENDER_MALE = 0;
    const GENDER_FEMALE = 1;
    const UNIVERSITY = 0;
    const COLLEGE = 1;


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
    public static function getTitleNamesForOption()
    {
        $name = Position::all();
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