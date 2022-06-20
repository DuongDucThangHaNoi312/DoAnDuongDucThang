<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WfThreadBooking extends Model
{
    protected $connection = 'mysql_booking';
    protected $table = "wf_threads";

}
