<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepartmentGroup extends Model
{
    protected $fillable = ['name','status','type', 'only_manager'];

    public static function rules($id = 0)
    {
        return [
            'name' => 'required'
        ];
    }
}
