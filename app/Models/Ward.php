<?php

namespace App;

use Illuminate\Support\Facades\Cache;

class Ward extends \Eloquent {

	public $timestamps = false;

    protected $fillable = ['name', 'status', 'district_id', 'code', 'province_id'];

	public static function boot()
    {
        parent::boot();

        static::updated(function($ward)
        {
            self::clearCache($ward);
        });

        static::deleted(function($ward)
        {
            self::clearCache($ward);
        });

        static::created(function($ward)
        {
            self::clearCache($ward);
        });

        static::saved(function($ward)
        {
            self::clearCache($ward);
        });
    }

    public static function clearCache($ward)
    {
        Cache::forget('ward_name_by_district_' . $ward->district_id);
        Cache::forget('ward_by_district_' . $ward->district_id);
    }

    public static function getWardByDistrict($districtId)
    {
        $wards = [];
        if (!Cache::has('ward_by_district_' . $districtId)) {
            $wards = Ward::where('district_id', $districtId)->pluck('code', 'id')->toArray();
            if (0 == count($wards)) return [];
            $wards = json_encode($wards);
            if ($wards) Cache::forever('ward_by_district_' . $districtId, $wards);
        } else {
            $wards = Cache::get('ward_by_district_' . $districtId);
        }
        return json_decode($wards, 1);
    }

    public static function getWardNameByDistrict($districtId)
    {
        $wards = [];
        if (!Cache::has('ward_name_by_district_' . $districtId)) {
            $wards = Ward::where('district_id', $districtId)->pluck('name', 'id')->toArray();
            if (0 == count($wards)) return [];
            $wards = json_encode($wards);
            if ($wards) Cache::forever('ward_name_by_district_' . $districtId, $wards);
        } else {
            $wards = Cache::get('ward_name_by_district_' . $districtId);
        }
        return json_decode($wards, 1);
    }
}
