<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InsuranceDetail extends Model
{
    protected $table = 'insurance_details';

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
