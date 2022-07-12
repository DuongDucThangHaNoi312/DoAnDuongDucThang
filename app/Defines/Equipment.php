<?php

namespace App\Defines;

class Equipment
{
    const  GHE = "GHE";
    const  BAN = "BAN";
    const  MICRO = "MICRO";
    const  LOA  = "LOA";
    const  MAYCHIEU  = "MAYCHIEU";
    
    

    static function OptionEquipment()
    {
       return [
         self::GHE => "Ghế",
         self::BAN => "Bàn",
         self::MICRO => "Micro",
         self::LOA => "Loa",
         self::MAYCHIEU => "Máy chiếu",
       ];
    }
    
}