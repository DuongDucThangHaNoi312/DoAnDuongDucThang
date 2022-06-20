<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recruitment extends Model
{
    protected $table = 'recruitment_profiles';
    protected $fillable = ['id', 'name', 'dob', 'gender', 'id_card_no', 'email', 'telephone', 'education_level', 'permanent_residence', 'file_cv', 'description', 'company_id', 'department_id', 'recruitment_address', 'title_id',];
    protected $dates=['dob'];

    public static function rules($id = 0)
    {
        return [
            'name' => 'required',
            'id_card_no' => 'required|numeric|unique:recruitment_profiles,id_card_no'.($id == 0 ? '' : ',' . $id),
            'email' => 'required|unique:recruitment_profiles,email'.($id == 0 ? '' : ',' . $id),
            'telephone' => 'required|regex:/(0)[0-9]/iD|numeric|digits_between:9,10|unique:recruitment_profiles,telephone' . ($id == 0 ? '' : ',' . $id),
            'dob' => 'required',
            'gender'=>'required',
            'education_level'=>'required',
            'permanent_residence'=>'required',
            'company_id'=>'required',
            'department_id'=>'required',
            'title_id'=>'required',
            'recruitment_address'=>'required',
        ];
    }
}



