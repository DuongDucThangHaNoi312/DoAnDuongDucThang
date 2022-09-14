<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rental extends Model
{
    protected $table = 'rental_histories';

    protected $fillable = ['meeting_room_id', 'user_id', 'rental_start', 'renral_end', 'status', 'price_meeting_room', 'name_meeting_room', 'total_money', 'path_img_meeting_room'];

    public function rentalServices()
	{
		return $this->hasMany(RentalService::class, 'rental_history_id', 'id');
	}

	public function rentalEquipments()
	{
		return $this->hasMany(RentalEquipment::class, 'rental_history_id', 'id');
	}

}
