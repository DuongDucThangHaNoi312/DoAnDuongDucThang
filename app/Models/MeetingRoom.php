<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeetingRoom extends Model
{
    protected $table = 'meeting_rooms';

    protected $fillable = ['name', 'telephone', 'description', 'status', 'price'];

    public static function rules($id = 0)
    {
        return [
            'name' => 'required',
            'telephone' => 'required',
            'price' => 'required|numeric',
        ];
    }

    public static function countMeetingRoom()
    {
        return self::where('status', 1)->count();
    }

}
