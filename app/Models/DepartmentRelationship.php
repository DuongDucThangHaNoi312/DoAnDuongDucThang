<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepartmentRelationship extends Model
{
    protected $fillable = ['department_id', 'group_id'];

    public static function rules($id = 0)
    {
        return [
            'department_id' => 'required',
        ];
    }

    public function departmentGroups(){
        return $this->belongsTo(DepartmentGroup::class);
    }
    public function department(){
        return $this->belongsTo(Department::class);
    }
}
