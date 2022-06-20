<?php namespace App;

use Illuminate\Support\Facades\Cache;

class Setting extends \Eloquent {

    public static function getValueByKey($key)
    {
        $redis  = \App::make('redis');
        $setting = $redis->get("setting_{$key}");
        if (!$setting) {
            $setting = Setting::where('key', $key)->first();
            if (!is_null($setting)) {
                $setting = $setting->value;
                $redis->set("setting_{$key}", $setting);
            }
        }

        return $setting;
    }

    public static function getValueAndNoteByKey()
    {
        $redis->set("ghn_pick_hubs", json_encode($return['message']));
        $redis  = \App::make('redis');
        $setting = $redis->get("setting_{$key}");
        if (!$setting) {
            $setting = Setting::where('key', $key)->first();
            if (!is_null($setting)) {
                $setting = [
                    'value' => $setting->value,
                    'note'  => $setting->note,
                ];
                $setting = json_encode($setting);
                $redis->set("setting_{$key}", $setting);
            }
        }

        return json_decode($setting, 1);
    }
}