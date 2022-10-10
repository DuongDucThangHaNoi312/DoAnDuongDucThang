<?php
namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use DB;

trait GenCodeTraits{

    public function genCodeUser(){
       $code = DB::table('users')->max('code');
       return $code;
    }

}
