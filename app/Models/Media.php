<?php

namespace App;

use Illuminate\Support\Facades\Cache;

class Media extends \Eloquent {
    protected $fillable = ['source', 'title', 'created_by', 'status', 'type', 'size', 'position'];
}