<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobDetailBooking extends Model
{
    protected $connection = 'mysql_booking';
    protected $table = "job_details";


    public function costDetailChooseContainers()
    {
        return $this->hasOne(CostDetailBooking::class, 'booking_detail_id', 'id')->where('type_cost_id', \App\Defines\ChooseContainer::TYPE_COST_CHOOSE_CONTAINER);
    }

    public function job()
    {
        return $this->belongsTo(JobBooking::class);

    }


    public function threadChonVo()
    {
        return $this->hasOne(WfThreadBooking::class, 'job_detail_id', 'id')->where('wf_def_detail_id', \App\Defines\NodeBooking::CHON_VO);
    }

}
