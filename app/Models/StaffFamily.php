<?php

namespace App;

class StaffFamily extends \Eloquent {

    protected $fillable = ["staff_id", "fullname", "relationship", "dob", "dependent", "gender", "tax_code", "dependent_from", "dependent_to"];

}