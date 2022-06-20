<?php

namespace App;

use App\Models\Contract;
use Illuminate\Database\Eloquent\Model;

class StaffPosition extends Model
{
    protected $table = 'staff_positions';
    protected $hidden = ['remember_token'];
    protected $guarded = [];

    public static function rules($id = 0)
    {
        return [
            'code' => 'required|max:50|regex:/^[A-Za-z0-9_.-]+$/|unique:staff_positions,code' . ($id == 0 ? '' : ',' . $id),
            'weight' => 'required|max:255',
        ];
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function getStaffPositionsForOption()
    {
        return $this->pluck('name', 'id')->toArray();
    }
}
