<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CostDetailBooking extends Model
{
    protected $connection = 'mysql_booking';
    protected $table = "cost_detail";
}
