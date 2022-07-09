<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    protected $table='equipments';
    protected $fillable = ['type', 'code', 'created_by', 'updated_by', 'price'];

}
