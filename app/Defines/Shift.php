<?php
namespace App\Define;

use App\Models\CategoryShift;
use App\Models\Company;
use App\Models\ShiftTime;
use App\Target;
use App\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Shift {

    const OFFICE_TIME = 1;
    const SHIFT_TIME = 2;
    const SHIFT_TIME_AND_OT = 3;
    const FIRST_SHIFT = 1;
    const SECOND_SHIFT = 2;
    const THREE_SHIFT = 3;
    const FOUR_SHIFT = 4;
    const FIRST_SHIFT_AND_OT = 4;
    const SECOND_SHIFT_AND_OT = 5;
    const NINE_HOUR = 9;

    const TYPE_NGAY = 1;
    const TYPE_HC = 2;
    const TYPE_DEM = 3;

    const NGAY = 'NGAY';
    const HC = 'HANH_CHINH';
    const DEM = 'DEM';

    public static function getNameShift($type)
    {
        if ($type == self::TYPE_NGAY) return self::NGAY;
        if ($type == self::TYPE_HC) return self::HC;
        if ($type == self::TYPE_DEM) return self::DEM;
        return '';
    }

    public static function getPeopleInDepartment($departmentId)
    {
        $name = DB::table('users')
            ->where('department_id', $departmentId)
            ->where('id','<>',Auth::user()->id)
            ->whereNotIn('fullname',['System','Administrator'])
            ->pluck('fullname', 'id')->toArray();
        return $name;
    }

    public static function getShift()
    {
        $data = [];
        $items = CategoryShift::where('status', 1)->get();
        foreach ($items as $key => $item) {
            $data[$item->id] = $item->shortened_name;
        }

        return $data;
    }
    public static function getShifts()
    {
        return [
            self::FIRST_SHIFT => trans('shifts.shifts.' . self::FIRST_SHIFT),
            self::SECOND_SHIFT => trans('shifts.shifts.' . self::SECOND_SHIFT),
            self::THREE_SHIFT => trans('shifts.shifts.' . self::THREE_SHIFT),
            self::FOUR_SHIFT => trans('shifts.shifts.' . self::FOUR_SHIFT)

        ];
    }

    public static function getAllShift()
    {
        $data = [];
        $type = '';
        $items = CategoryShift::where('status', 1)->get();
        foreach ($items as $key => $item) {
            switch ($item->type) {
                case 1:
                    $type = 'N';
                    break;
                case 2:
                    $type = 'HC';
                    break;
                case 3:
                    $type = 'Đ';
                    break;
            }
            $data[$item->id] = $item->shortened_name . '-' . $type;
        }

        return $data;
    }

    public static function getShiftByDepartment($departmentId)
    {
        $data = [];
        $data1 = [];
        $type = '';
        $cate_ids = ShiftTime::where('department_id', $departmentId)->whereHas('category', function($query) {
                                $query->where('status', 1);
                            })->pluck('category_shift_id');
        
        $data[0] = 'Không đi làm';
        foreach ($cate_ids as $key => $value) {
            $category = CategoryShift::find($value);
            switch ($category->type) {
                case 1:
                    $type = 'N';
                    break;
                case 2:
                    $type = 'HC';
                    break;
                case 3:
                    $type = 'Đ';
                    break;
            }
            $data[$value] = $category->shortened_name . ' - ' . $type;
            $data1[$value . '_'] = $category->shortened_name . ' - ' . $type . ' Làm nửa công';
        }

        return $data + $data1;
    }

    public static function getColorByDepartment($departmentId)
    {
        $data = [];
        $cate_ids = ShiftTime::where('department_id', $departmentId)->whereHas('category', function($query) {
                                $query->where('status', 1);
                            })->pluck('category_shift_id');

        foreach ($cate_ids as $key => $value) {
            $category = CategoryShift::find($value);
            
            $data[$value] = $category->color;
        }

        return $data;
    }

    public static function getColorShifts()
    {
        $data = [];
        $categories = CategoryShift::where('status', 1)->get();

        foreach ($categories as $key => $value) {
            
            $data[$value->id] = $value->color;
        }

        return $data;
    }

    public static function jsonAllShift()
    {
        $data = [];
        $type = '';
        $items = CategoryShift::where('status', 1)->get();
        foreach ($items as $key => $item) {
            switch ($item->type) {
                case 1:
                    $type = 'N';
                    break;
                case 2:
                    $type = 'HC';
                    break;
                case 3:
                    $type = 'Đ';
                    break;
            }
            $data[$item->id] = $item->shortened_name . '-' . $type;
        }

        return json_encode($data);
    }

    public static function getShiftByDepartment1($departmentId)
    {
        $data = [];
        $data1 = [];
        $type = '';
        $cate_ids = ShiftTime::where('department_id', $departmentId)->whereHas('category', function($query) {
                                $query->where('status', 1);
                            })->pluck('category_shift_id');
        
        
        foreach ($cate_ids as $key => $value) {
            $category = CategoryShift::find($value);
            switch ($category->type) {
                case 1:
                    $type = 'N';
                    break;
                case 2:
                    $type = 'HC';
                    break;
                case 3:
                    $type = 'Đ';
                    break;
            }
            $data[$value] = $category->shortened_name . ' - ' . $type;
        }

        return $data;
    }
}
