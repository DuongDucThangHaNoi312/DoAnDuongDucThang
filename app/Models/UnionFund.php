<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class UnionFund extends Model
{
    protected $table = 'union_funds';
    protected $fillable = [
        'user_id',
        'start',
        'month',
        'year',
        'note',
        'created_by',
        'deleted_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
