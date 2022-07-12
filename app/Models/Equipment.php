<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    protected $table='equipments';
    protected $fillable = ['code', 'type', 'created_by', 'updated_by', 'price', 'number'];


    public static function rules($id = 0)
    {
        return [
            'code' => 'required',
            'price' => 'required|numeric',
            'number' => 'required|numeric',
        ];
    }

}
