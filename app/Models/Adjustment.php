<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Adjustment extends Model
{
    protected $table = 'adjustments';
    protected $fillable = ['code', 'title','status','type','action','amount'];
    
    public static function rules($id = 0)
    {
        return [
            'code' => 'min:3|max:14|required|regex:/^[A-Za-z0-9_.-]+$/|unique:adjustments,code' . ($id == 0 ? '' : ',' . $id),
            'title'=>'required|max:50',
            'type' =>  'required',
            'status' =>  'required',
            'amount' => 'bail|max:10',
        ];
    }

    public static function category()
    {
        $category = Adjustment::where('type', 1)->where('action', 1)->get();
		return Arr::pluck($category->toArray(), 'title', 'id');
    }


    public function payOffs()
    {
        return $this->hasMany(PayOff::class , 'category', 'id');
    }
   
    public function detailDedutions()
    {
        // return $this->hasMany(DetailDeduction::class , 'name', 'id');
        return $this->hasMany(DetailDeduction::class , 'id', 'name');
    }
}
