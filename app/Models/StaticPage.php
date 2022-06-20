<?php

namespace App;

use Illuminate\Support\Facades\Cache;

class StaticPage extends \Eloquent
{
    protected $fillable = [ 'slug', 'title', 'description', 'status' ];
    public static function rules($id = 0, $type = "") {
        return [
            'title'                 => 'required|max:255',
            'description'           => ($type == \App\Define\Constant::STATIC_PAGE_IMAGE ? '' : 'required'),
        ];
    }
    public static function boot()
    {
        parent::boot();
        static::updated(function($page)
        {
            self::clearCache($page);
        });
    }

    public static function clearCache($page)
    {
        \Redis::flushall();
        Cache::forget('static_pages_single_' . $page->slug);
        Cache::forget('static_pages_full_' . $page->slug);
    }

    public static function getByAllWihoutGroup()
    {
        $staticPages = [];
        if (!Cache::has('static_pages')) {
            $staticPages = StaticPage::where('status', 1)->whereNull('group')->select('description', 'title', 'slug')->get()->keyBy('slug');
            $staticPages = json_encode($staticPages);
            Cache::forever('static_pages', $staticPages);
        } else {
            $staticPages = Cache::get('static_pages');
        }

        return json_decode($staticPages, 1);
    }

    public static function getBySlug($slug)
    {
        $staticPage = [];
        if (!Cache::has('static_pages_single_' . $slug)) {
            $staticPage = StaticPage::where('status', 1)->where('slug', $slug)->select('description')->first();
            $staticPage = is_null($staticPage) ? '' : $staticPage->description;
            Cache::forever('static_pages_single_' . $slug, $staticPage);
        } else {
            $staticPage = Cache::get('static_pages_single_' . $slug);
        }

        return $staticPage;
    }

    public static function getFullBySlug($slug)
    {
        $staticPage = [];
        if (!Cache::has('static_pages_full_' . $slug)) {
            $staticPage = StaticPage::where('status', 1)->where('slug', $slug)->select('description', 'title')->first()->toArray();
            $staticPage = json_encode($staticPage);
            Cache::forever('static_pages_full_' . $slug, $staticPage);
        } else {
            $staticPage = Cache::get('static_pages_full_' . $slug);
        }

        return json_decode($staticPage, 1);
    }

    public static function getByGroup($group)
    {
        $group = intval($group);
        $staticPages = [];
        if (!Cache::has('static_pages_group_' . $group)) {
            $staticPages = StaticPage::where('status', 1)->where('group', $group)->orderBy('position')->select('slug', 'description', 'title')->get();
            $staticPages = json_encode($staticPages);
            Cache::forever('static_pages_group_' . $group, $staticPages);
        } else {
            $staticPages = Cache::get('static_pages_group_' . $group);
        }

        return json_decode($staticPages, 1);
    }
}