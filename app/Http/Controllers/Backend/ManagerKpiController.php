<?php

namespace App\Http\Controllers\backend;

use App\Models\Company;
use App\Models\Department;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ManagerKpiController extends Controller
{
    public function index(Request $request){
        $query="1=1";
        $page_num = intval($request->input('page_num', \App\Define\Constant::PAGE_NUM_20));
        $user_filter = $request->input('name') ?? '';
        if ($user_filter) $query .= " AND fullname like '%" . $user_filter . "%'";
        $month_filter = $request->input('month') ?? date('m/Y');
        $month = "01-".str_replace('/', '-', $month_filter);
        $targets = User::whereRaw($query)->with('company', 'department')
            ->join('targets as t1', 't1.user_id', '=', 'users.id')
            ->when(auth()->user()->position_id != 5, function($query) {
                $query->when(auth()->user()->position_id == 0,  function($query1) {
                    $query1->where('department_id', auth()->user()->department_id);
                });
            })
            ->whereMonth('t1.timestamp', '=', date('m', strtotime($month)))
            ->whereYear('t1.timestamp', '=', date('Y', strtotime($month)))
            ->orderBy('timestamp', 'DESC')
            ->paginate($page_num);
        $companies = Company::where('status', 1)->get();
        $departments = Department::where('status', 1)->get();
//        $users = User::where('active', 1)
//            ->when(auth()->id()!= 5, function($query) {
//                $query->when(auth()->user()->position_id == 0, function($query) {
//                    $query->where('users.id', auth()->id());
//                }, function($query1) {
//                    $query1->where('department_id', auth()->user()->department_id)->where('users.id', '!=', auth()->id());
//                });
//            }, function($query) {
//                $query->where('users.id', '!=', auth()->id());
//            })
//            ->get();
//        dd($users);
        return view('backend.manager_kpi.index',compact('company_filter', 'department_filter', 'user_filter', 'targets', 'month_filter', 'companies', 'departments', 'users'));
    }
    public function create(Request $request){
        $page_num = intval($request->input('page_num', \App\Define\Constant::PAGE_NUM_20));
        $month_filter = $request->input('month');
        $month = $month_filter ? "01-".str_replace('/', '-', $month_filter) : '';
        $users = User::where('id','>',17)->with(['target' => function($query) use ($month) {
            $query->when($month, function($query) use ($month) {
                $query->whereMonth('timestamp', '=', date('m', strtotime($month)))->whereYear('timestamp', '=', date('Y', strtotime($month)));
            }, function($query1) {
                $query1->whereMonth('timestamp', date('m'))->whereYear('timestamp', date('Y'));
            });
        }])
            ->when(auth()->user()->position_id != 5, function($query) {
                $query->where('department_id', auth()->user()->department_id);
            })
            ->when(auth()->user()->position_id != 5, function($query) {
                $query->when(auth()->user()->position_id == 0, function($query) {
                    $query->where('users.id', auth()->id());
                }, function($query1) {
                    $query1->where('department_id', auth()->user()->department_id);
                });
            })->paginate($page_num);
        $mc = date('m');
        $year = date('Y');
        $companies = Company::where('status', 1)->get();
        $departments = Department::where('status', 1)->get();
       return view('backend.manager_kpi.create', compact('company_filter', 'department_filter', 'companies', 'month_filter', 'departments', 'targets', 'users',  'user_selected', 'mc', 'year'));
    }
}
