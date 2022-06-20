<?php
namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait StorageImageTraits{

    public function storeTraitUploadMutilple($file,$foderName){
            $fileNameOrigin=$file->getclientoriginalname();
            $fileNameHash=str_random(20).'.'.$file->getclientoriginalname();
            $filePath=$file->storeAs('public/'.$foderName.'/'.auth()->id(),$fileNameHash);
            $dataUploadTrait=[
                'file_name'=>$fileNameOrigin,
                'file_path'=>Storage::url($filePath),
            ];
            return $dataUploadTrait;
        }

}
