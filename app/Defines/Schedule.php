<?php

namespace App\Defines;

class Schedule
{
    const DAY_OFF_SICK = 'S';
    const DAY_OFF_BABE = 'BB';
    const DAY_OFF_WEDDING = 'W';
	const DAY_OFF_FUNERAL = 'D';
    const DAY_OFF_NO_SALARY = 'O';
	const DAY_OFF_70_SALARY = 'C';
	const DAY_OFF_12 = 'L';
	const DAY_OFF_MISSION = 'T';
    const TIME_OFF_ALL = 3;
    const TIME_OFF_MORNING = 1;
    const TIME_OFF_AFTERNOON = 2;
    const TIME_TYPE = 1;
    const TIME_TYPES = 2;
    const SICK_LIMIT = 1; //So ngay
	const WEDDING_LIMIT = 3;
	const FUNERAL_LIMIT = 3;
	const LEAVE_NOT_NEED_APPROVAL = ['O'];
	const DATE_START_SALARY = 26;
	const DATE_END_SALARY = 25;
	const WORK_FROM_HOME = 'F';

    public static function getLimitDatOffByType($type)
    {
        if ($type == self::DAY_OFF_SICK) return self::SICK_LIMIT;
        elseif ($type == self::DAY_OFF_WEDDING) return self::WEDDING_LIMIT;
        elseif ($type == self::DAY_OFF_FUNERAL) return self::FUNERAL_LIMIT;
        else return '';
	}

	public static function getTimeOffForOption()
	{
		return [
			self::TIME_OFF_MORNING => trans('schedules.time-offs.' . self::TIME_OFF_MORNING),
			self::TIME_OFF_AFTERNOON => trans('schedules.time-offs.' . self::TIME_OFF_AFTERNOON),
		];
	}

    public static function getTimeShiftOffForOption()
    {
        return [
            self::TIME_OFF_MORNING => trans('schedules.time-shift-offs.' . self::TIME_OFF_MORNING),
            self::TIME_OFF_AFTERNOON => trans('schedules.time-shift-offs.' . self::TIME_OFF_AFTERNOON),
        ];
    }

	public static function getTimeTypeForOption()
	{
		return [self::TIME_TYPE => trans('schedules.time-types.' . self::TIME_TYPE), self::TIME_TYPES => trans('schedules.time-types.' . self::TIME_TYPES)];
	}

	public static function getTimeOffForOptionDay()
	{
		return [self::TIME_OFF_MORNING => trans('schedules.time-offs.' . self::TIME_OFF_MORNING), self::TIME_OFF_AFTERNOON => trans('schedules.time-offs.' . self::TIME_OFF_AFTERNOON) ];
	}

	public static function getDayOffType()
	{
		return [
			'day_off_sick' 				=> self::DAY_OFF_SICK,
			'day_off_babe' 				=> self::DAY_OFF_BABE,
			'day_off_wedding'           => self::DAY_OFF_WEDDING,
			'day_off_funeral'   		=> self::DAY_OFF_FUNERAL,
			'day_off_no_salary'         => self::DAY_OFF_NO_SALARY,
			'day_off_70_salary'         => self::DAY_OFF_70_SALARY,
			'day_off_12'				=> self::DAY_OFF_12
		];
	}

	public static function getDayOffTypeForOption()
	{
		return [
            self::DAY_OFF_12        => trans('schedules.day-offs.' . self::DAY_OFF_12),
            self::DAY_OFF_SICK      => trans('schedules.day-offs.' . self::DAY_OFF_SICK),
			self::DAY_OFF_WEDDING   => trans('schedules.day-offs.' . self::DAY_OFF_WEDDING),
			self::DAY_OFF_FUNERAL   => trans('schedules.day-offs.' . self::DAY_OFF_FUNERAL),
			self::DAY_OFF_NO_SALARY => trans('schedules.day-offs.' . self::DAY_OFF_NO_SALARY),
			self::DAY_OFF_70_SALARY => trans('schedules.day-offs.' . self::DAY_OFF_70_SALARY),
			self::DAY_OFF_MISSION   => trans('schedules.day-offs.' . self::DAY_OFF_MISSION),
            self::DAY_OFF_BABE      => trans('schedules.day-offs.' . self::DAY_OFF_BABE),
            self::WORK_FROM_HOME      => trans('schedules.day-offs.' . self::WORK_FROM_HOME),

        ];
	}

	public static function arrTypeLeave()
    {
        return [self::DAY_OFF_12, self::DAY_OFF_SICK, self::DAY_OFF_NO_SALARY, self::DAY_OFF_FUNERAL, self::DAY_OFF_WEDDING, self::DAY_OFF_70_SALARY];
    }

    public static function arrTypeLeaveNeedApproval()
    {
        return [self::DAY_OFF_12, self::DAY_OFF_SICK, self::DAY_OFF_FUNERAL, self::DAY_OFF_WEDDING, self::DAY_OFF_70_SALARY, self::DAY_OFF_MISSION, ];
    }

    public static function leaveSub()
    {
        return [self::DAY_OFF_12, self::DAY_OFF_SICK, self::DAY_OFF_FUNERAL];
    }

    public static function arrTypeLeaveForExcel()
    {
        return [self::DAY_OFF_12, self::DAY_OFF_SICK, self::DAY_OFF_BABE, self::DAY_OFF_NO_SALARY,  self::DAY_OFF_FUNERAL, self::DAY_OFF_WEDDING, self::DAY_OFF_70_SALARY, self::DAY_OFF_MISSION];
    }

    public static function dayOffFree() // Những loại ngày nghỉ có thể xin đè lên ngày nghỉ phòng ban
    {
        return [self::DAY_OFF_BABE];
    }

    public static function dayOffNoLimit() // Những loại ngày nghỉ k có giới hạn số ngày nghỉ
    {
        return [ self::DAY_OFF_MISSION, self::DAY_OFF_BABE, self::DAY_OFF_70_SALARY, self::DAY_OFF_NO_SALARY ];
    }

	public static function colorDayOffs()
	{
		return [
			self::DAY_OFF_SICK => '#00a65a',
			self::DAY_OFF_BABE => '#00c0ef',
			self::DAY_OFF_WEDDING => '#3c8dbc',
			self::DAY_OFF_NO_SALARY => '#bc6fbd',
			self::DAY_OFF_70_SALARY => '#f39c12',
			self::DAY_OFF_FUNERAL => '#425256',
			self::DAY_OFF_12 => '#679a96',
			self::DAY_OFF_MISSION => '#f1e72b',
			self::WORK_FROM_HOME => '#5330bd',
		];
	}
}