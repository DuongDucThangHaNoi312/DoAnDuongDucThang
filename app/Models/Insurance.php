<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Insurance extends Model
{
    protected $table = 'insurances';
    protected $fillable = [
        'month',
        'year',
        'created_by',
        'title',
        'status',
        'user_approved',
        'date_approved',
        'company_id'
    ];

    public function user_by()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function insurance_detail()
    {
        return $this->hasMany(InsuranceDetail::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
