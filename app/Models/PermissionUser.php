<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PermissionUser extends Model
{
    use SoftDeletes;
    protected $table = 'permission_user';
    protected $fillable = [ 'permission_id', 'user_id', 'user_type', 'manager_other', 'deleted_by', 'deleted_at' ];
}