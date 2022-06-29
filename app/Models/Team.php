<?php

namespace App\Models;

use App\PermissionUserObject;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Team extends Model
{
    protected $table = 'teams';
    protected $fillable = [
        'name',
        'description',
        'user_id',
        'created_by',
        'department_id'
    ];

    public function users()
    {
        return $this->hasMany(UserTeam::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function usersDetail()
    {
        return $this->belongsToMany(User::class, UserTeam::class, 'team_id', 'user_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public static function selectTeamByDepartment($departmentId)
    {
        $return = [];
        if (!Auth::user()->hasRole('LEADER')) {
            $data = Team::where('department_id', $departmentId)->get();
        } else {
            $infoPermission = PermissionUserObject::getMorePermissions(Auth::user()->id, 'departments.read');
            if ($infoPermission['teams']) $data = Team::whereIn('id', $infoPermission['teams'])->get();
        }
        if ($data) $return = array_pluck($data->toArray(), 'name', 'id');

        return $return;
    }
}
