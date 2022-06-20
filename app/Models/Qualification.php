<?php

namespace App;

use App\Models\Contract;
use Illuminate\Database\Eloquent\Model;

class Qualification extends Model
{
    protected $table ='qualifications';
    protected $guarded =[];
    public static function rules($id = 0)
    {
        return [
            'code' => 'required|max:50|regex:/^[A-Za-z0-9_.-]+$/|unique:qualifications,code' . ($id == 0 ? '' : ',' . $id),
            'name' => 'required|max:255',
        ]
            ;
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function getStaffTitlesForOption()
    {
        return $this->pluck('name', 'id')->toArray();
    }
}
