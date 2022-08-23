<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ListAPIController extends Controller
{
    public function getAllApi()
    {
        return  view('backend.api.list');
    }
}
