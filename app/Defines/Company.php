<?php

namespace App\Define;

use App\User;
use App\Qualification;
use Illuminate\Support\Arr;

class Company
{
     const COMPANY_ACTIVE = 1;
     const COMPANY_INACTIVE = 0;

     const CODE_COMPANY_HCM_DN = ['JPAC-HCM', 'JLV-HCM', 'JPAC-DN'];

     public static function getUser(){
         $user = User::whereNotIN('fullname',['System','Administrator'])->get();
         return Arr::pluck($user, 'fullname', 'id');
     }

     public static function getQualification(){
         $quali = Qualification::all();
         return Arr::pluck($quali, 'name', 'id');

     }
}