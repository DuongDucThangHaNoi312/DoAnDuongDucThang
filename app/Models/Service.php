<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $table='services';
    protected $fillable = ['name', 'price'];
    public $timestamps = false;

    public static function rules($id = 0)
    {
        return [
            'name' => 'required|max:50',
            'price' => 'required|max:255',
        ];
    }

}
