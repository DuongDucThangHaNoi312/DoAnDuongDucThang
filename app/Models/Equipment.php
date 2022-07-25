<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    protected $table='equipments';
    protected $fillable = ['name', 'type', 'created_by', 'updated_by', 'price', 'number'];


    public static function rules($id = 0)
    {
        return [
            'name' => 'required',
            'price' => 'required|numeric',
            'number' => 'required|numeric',
        ];
    }

}
