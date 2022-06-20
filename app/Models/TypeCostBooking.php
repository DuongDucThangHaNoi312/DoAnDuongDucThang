<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeCostBooking extends Model
{
    protected $connection = 'mysql_booking';
    protected $table = "type_costs";
}
