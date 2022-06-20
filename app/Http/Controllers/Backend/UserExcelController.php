<?php

namespace App\Http\Controllers\Backend;

use App\Exports\UsersExport;
use App\Imports\UsersImport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class UserExcelController extends Controller
{
    public function export(Request $request)
	{
		$userIds = $request->userIds ?? [];
		$name = $request->name_excel ?? 'Nhan-Vien' . now()->format('Y-m-d');
		return Excel::download(new UsersExport($userIds), $name.'.xlsx');
	}
}
