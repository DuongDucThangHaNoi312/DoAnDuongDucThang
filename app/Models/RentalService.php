<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RentalService extends Model
{
    protected $table = 'rental_services';

    protected $fillable = ['service_id', 'count', 'rental_history_id'];

}