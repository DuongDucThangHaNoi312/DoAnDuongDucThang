<?php

namespace App\Models;

use App\User;
use App\Position;
use App\StaffDayOff;
use App\Defines\Staff;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $table = 'departments';
    protected $fillable = ['code', 'name', 'telephone', 'description','status'];

    public static function rules($id = 0)
    {
        return [
            'code' => 'required|max:255',
            'name' => 'required|max:255',
            'telephone' => 'required|regex:/(0)[0-9]/iD|numeric|min:10',
        ];
    }
    
}
