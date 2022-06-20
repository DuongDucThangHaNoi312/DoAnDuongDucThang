<?php

namespace App;

use Illuminate\Support\Facades\Cache;

class District extends \Eloquent {

	public $timestamps = false;

    protected $fillable = ['name', 'status', 'province_id', 'code'];

	public static function boot()
    {
        parent::boot();

        static::updated(function($district)
        {
            self::clearCache($district);
        });

        static::deleted(function($district)
        {
            self::clearCache($district);
        });

        static::created(function($district)
        {
            self::clearCache($district);
        });

        static::saved(function($district)
        {
            self::clearCache($district);
        });
    }

    public static function clearCache($district)
    {
        Cache::forget('districts_provinces');
        Cache::forget('districts_all');
        Cache::forget('ward_by_district_' . $district->id);
        Cache::forget('districts_by_province_' . $district->province_id);
        Cache::forget('districts_name_by_province_' . $district->province_id);
    }

    public static function getByAll()
    {
        $districts = [];
        if (!Cache::has('districts_all')) {
            $districts = District::pluck('code', 'id')->toArray();
            if (0 == count($districts)) return [];
            $districts = json_encode($districts);
            if ($districts) Cache::forever('districts_all', $districts);
        } else {
            $districts = Cache::get('districts_all');
        }
        return json_decode($districts, 1);
    }

    public static function getByProvince($provinceId)
    {
        $districts = [];
        if (!Cache::has('districts_by_province_' . $provinceId)) {
            $districts = District::where('province_id', $provinceId)->pluck('code', 'id')->toArray();
            if (0 == count($districts)) return [];
            $districts = json_encode($districts);
            if ($districts) Cache::forever('districts_by_province_' . $provinceId, $districts);
        } else {
            $districts = Cache::get('districts_by_province_' . $provinceId);
        }
        return json_decode($districts, 1);
    }

    public static function getNameByProvince($provinceId)
    {
        $districts = [];
        if (!Cache::has('districts_name_by_province_' . $provinceId)) {
            $districts = District::where('province_id', $provinceId)->pluck('name', 'id')->toArray();
            if (0 == count($districts)) return [];
            $districts = json_encode($districts);
            if ($districts) Cache::forever('districts_name_by_province_' . $provinceId, $districts);
        } else {
            $districts = Cache::get('districts_name_by_province_' . $provinceId);
        }
        return json_decode($districts, 1);
    }
}
