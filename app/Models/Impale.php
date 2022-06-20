<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Impale extends Model
{
    protected $table = 'impales';
    protected $fillable = [
        'content',
        'amount_money',
        'user_id',
        'created_by',
        'month',
        'year'
    ];

    public function createdByImpale()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
