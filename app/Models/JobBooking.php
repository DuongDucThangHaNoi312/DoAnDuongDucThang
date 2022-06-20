<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobBooking extends Model
{
    protected $connection = 'mysql_booking';
    protected $table = "jobs";

    public function jobDetail(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(JobDetailBooking::class, 'job_id', 'id');
    }
}
