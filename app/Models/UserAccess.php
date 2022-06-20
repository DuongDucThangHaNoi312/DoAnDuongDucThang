<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserAccess extends Model
{

    protected $fillable = [ 'ip', 'location', 'device_type', 'device_name', 'device_id', 'reported', 'user_id', 'notification_token', 'status' ];
}