<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeCostHandling extends Model
{
    protected $connection = 'mysql_booking';
    protected $table = "type_costs";

    const CHON_VO = 40;
    const MO_TO_KHAI = 19;

    public function testIOD() {
        return 123;
    }
    
}
