<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailDeduction extends Model
{
    protected $table = 'detail_deduction';
    protected $fillable = [
        'deduction_id',
        'name',
        'money',
        'type',
        'note',
    ];
    public function adjustment()
    {
        // return $this->belongsTo(Adjustment::class, 'id', 'name');
        return $this->belongsTo(Adjustment::class, 'name', 'id');
    }
}
