<?php

namespace App;

use App\Models\Contract;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Staff extends Authenticatable
{
    use SoftDeletes;

    protected $table = 'staffs';
    protected $dates = ['deleted_at'];
    protected $hidden = ['remember_token'];
    protected $guarded = [];

    public static function rules($id = 0)
    {
        return [
            'code' => 'required|max:50|regex:/^[A-Za-z0-9_.-]+$/|unique:users,code' . ($id == 0 ? '' : ',' . $id),
            'addresses' => 'required|max:255',
            'nationality' => 'required|max:255',
            'id_card_no' => 'required|numeric|unique:users|digits_between:9,10,id_card_no'.($id == 0 ? '' : ',' . $id),
            'issued_on' => 'required',
            'issued_at' => 'required|max:255',
            'fullname' => 'required|max:255',
            'date_of_birth' => 'required',
            'gender'=>'required',
            'status'=>'required',
            'password' => 'string|min:6|confirmed'
        ];
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function getStaffsForOption()
    {
        return $this->pluck('name', 'id')->toArray();
    }

    public function dayOffs()
    {
        return $this->hasMany(StaffDayOff::class);
    }
}