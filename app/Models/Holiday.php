<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $table = 'holidays';
    protected $guarded = [];
    protected $fillable = ['id','start_date','end_date','holidays'];
    protected $dates=['start_date','end_date'];
}
