<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StaffInventoriesController extends Controller
{
    public function createBulk(){
       return view('backend.staff-inventories.create_multiple');
    }
    public function download(Request $request){
        $file= public_path() . "/assets/media/files/templates/inventories-staffs.xlsx";
        $headers = [
            'Content-Type: application/xls',
        ];
        return response()->download($file, 'template-staff.xlsx', $headers);

    }
}
