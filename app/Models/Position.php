<?php

namespace App;

use App\Models\Contract;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    protected $table ='positions';
    protected $guarded =[];
    public static function rules($id = 0)
    {
        return [
            'code' => 'required|max:50|regex:/^[A-Za-z0-9_.-]+$/|unique:positions,code' . ($id == 0 ? '' : ',' . $id),
            'name' => 'required|max:255|unique:positions,name'. ($id == 0 ? '' : ',' . $id),
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
