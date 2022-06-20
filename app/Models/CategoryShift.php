<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryShift extends Model
{
    protected $table = 'category_shift';
    protected $fillable = [
        'title',
        'type',
        'status',
        'shortened_name',
        'color',
    ];
}
