<?php
namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;

trait GenCodeTraits{

    public function genCodeUser($model, $fied){
       $fiedMax = $model->max($fied);
       return $fiedMax;
    }

}
