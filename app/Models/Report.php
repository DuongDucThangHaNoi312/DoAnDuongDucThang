<?php
namespace App;

class Report extends \Eloquent {

    public static function rules($id = 0) {
        return [
            'name'  => 'required|max:255',
            'slug'  => 'unique|max:100',
        ];
    }

    protected $fillable = ['name', 'slug', 'status'];

    public function roles()
    {
        return $this->belongsToMany("\App\Role", 'report_roles', 'report_id', 'role_id');
    }
}