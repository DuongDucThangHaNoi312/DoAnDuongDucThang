<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RentalEquipment extends Model
{
    protected $table = 'rental_equipments';

    protected $fillable = ['equipment_id', 'count', 'rental_history_id'];

}