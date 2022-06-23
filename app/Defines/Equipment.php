<?php

namespace App\Defines;

class Equipment
{
    const  GHE = "GHE";
    const  BAN = "BAN";
    const  MICRO = "MICRO";
    const  LOA  = "LOA";
    
    

    static function OptionEquipment()
    {
       return [
         self::GHE => "Ghế",
         self::BAN => "Bàn",
         self::MICRO => "Micro",
         self::LOA => "Loa",
       ];
    }
    
}