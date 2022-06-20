<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Shifts extends Model
{
    protected $table = 'shifts';
    protected $fillable = ['id','user_id','start','end','title','shifts','color'];

    public static function rules($id = 0)
    {
        return [
            'user_id' => 'required',
            //'shifts' => 'required',
            'start' => 'required',
            'end' => 'required|gte:start',
            'title' => 'required',
        ];
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public static function getShifts($user_id ,$isFormat = 1){
        $shifts = Shifts::where('user_id',$user_id)->get();
        if ($shifts && $isFormat) {
            foreach ($shifts as &$eventShifts) {
                $eventShifts->end = $eventShifts->end . "T23:59:00";
            }
        }
        return $shifts;
    }
}
