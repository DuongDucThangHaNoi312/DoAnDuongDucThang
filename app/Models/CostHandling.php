<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CostHandling extends Model
{
    protected $connection = 'mysql_booking';
    protected $table = "costs_job";

    public function typeCost()
    {
        return $this->belongsTo('App\Models\TypeCostHandling', 'type_cost_id', 'id');
    }

    public static function scopeChonVo($query) {
        return $query->where('costs_job.type_cost_id', \App\Models\TypeCostHandling::CHON_VO);
    }
}
