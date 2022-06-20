<?php

namespace App\Define;


class CalendarDepartments
{
    const CALENDAR_ALL_DAY = 'ALL DAY';
    const CALENDAR_MORNING = 'MORNING';
    const CALENDAR_AFTERNOON = 'AFTERNOON';
    const CALENDAR_WORKING = 'WORKING ONE DAY';
    const CALENDAR_WORKING_DAYS = 'WORKING MULTIPLE DAYS';
    const CALENDAR_OFF_ONE_DAY = 'OFF ONE DAY';
    const CALENDAR_OFF_DAYS = 'OFF MULTIPLE DAYS';
    const WORKING_DAY = 3;
    const DAY_OFF = 1;
    const NORMAL = 1;
    const HOLIDAY = 2;


    public static function getTypeForOptions()
    {
        return [self::CALENDAR_ALL_DAY => trans('calendar_departments.types.' . self::CALENDAR_ALL_DAY), self::CALENDAR_MORNING => trans('calendar_departments.types.' . self::CALENDAR_MORNING),
            self::CALENDAR_AFTERNOON => trans('calendar_departments.types.' . self::CALENDAR_AFTERNOON)];
    }

    public static function getTimeForOptions()
    {
        return [ self::CALENDAR_MORNING => trans('calendar_departments.types.' . self::CALENDAR_MORNING),
            self::CALENDAR_AFTERNOON => trans('calendar_departments.types.' . self::CALENDAR_AFTERNOON)];
    }

    public static function getCategoriesOffForOptions()
    {
        return [ self::CALENDAR_OFF_ONE_DAY => trans('calendar_departments.types.' . self::CALENDAR_OFF_ONE_DAY),
            self::CALENDAR_OFF_DAYS => trans('calendar_departments.types.' . self::CALENDAR_OFF_DAYS)];
    }
    public static function getCategoriesWorkForOptions()
    {
        return [ self::CALENDAR_WORKING => trans('calendar_departments.types.' . self::CALENDAR_WORKING),
            self::CALENDAR_WORKING_DAYS => trans('calendar_departments.types.' . self::CALENDAR_WORKING_DAYS)];
    }



}