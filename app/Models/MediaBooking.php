<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaBooking extends Model
{
    protected $connection = 'mysql_booking';
    protected $table = "media";
}
