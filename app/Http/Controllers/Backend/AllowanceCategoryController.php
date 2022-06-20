<?php

namespace App\Http\Controllers\Backend;

use App\Models\Contract;
use Illuminate\Http\Request;
use App\Models\AllowanceCategory;
use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Support\Facades\Session;

class AllowanceCategoryController extends Controller
{
    public function index(Request $request)
    {
		$query = '1=1';
		$name = $request->input('name');
		$status     = intval($request->input('status', -1));
		$date_range = $request->input('date_range');
		$page_num   = intval($request->input('page_num', \App\Define\Constant::PAGE_NUM_20));
		if ($name) $query .= " AND name like '%" . $name . "%'";
		if ($status <> -1) $query .= " AND status = {$status}";
		if ($date_range) {
			$date_range = explode(' - ', $date_range);
			if (isset($date_range[0]) && isset($date_range[1])) {
				$query .= " AND created_at >= '" . date("Y-m-d 00:00:00", strtotime(str_replace('/', '-', ($date_range[0] == '' ? '1/1/2015' : $date_range[0]) ))) . "' AND updated_at <= '" . date("Y-m-d 23:59:59", strtotime(str_replace('/', '-', ($date_range[1] == '' ? date("d/m/Y") : $date_range[1]) ))) . "'";
			}
		}
		$allowanceCategories = AllowanceCategory::whereRaw($query)->paginate($page_num);
        return view('backend.allowance-categories.index', compact('allowanceCategories'));
    }

    public function create()
    {
        return view('backend.allowance-categories.create');
    }

    public function store(Request $request)
    {
		$request->merge(['status' => $request->input('status', 1)]);
		$request->merge(['type' => $request->input('type', 0)]);
		$request->merge(['is_social_security' => $request->input('is_social_security', 0)]);
		$request->merge(['is_exemp' => $request->input('is_exemp', 0)]);
		$request->merge(['ot' => $request->input('ot', 0)]);
		$data = $request->only('name', 'name_es', 'status', 'type', 'desc', 'is_social_security', 'is_exemp', 'ot', 'company_id', 'department');
		$validator = \Validator::make($data, AllowanceCategory::rules());
		$validator->setAttributeNames(trans('allowance_categories'));
		if ($validator->fails()) return back()->withErrors($validator)->withInput();
		if (!is_null($data['company_id'])) {
			$data['department'] = implode(', ', $data['department']);
		}
		AllowanceCategory::create($data);
		Session::flash('message', trans('system.success'));
		Session::flash('alert-class', 'success');
		return redirect()->route('admin.allowance-categories.index');
    }

    public function show($id)
    {
        $allowanceCategory = AllowanceCategory::find($id);
        return view('backend.allowance-categories.show', compact('allowanceCategory'));
    }

    public function edit($id)
    {
        $allowanceCategory = AllowanceCategory::find($id);
		if (is_null($allowanceCategory)) {
			Session::flash('message', trans('system.have_an_error'));
			Session::flash('alert-class', 'danger');
			return back();
		}
		return view('backend.allowance-categories.edit', compact('allowanceCategory'));
    }

    public function update(Request $request, $id)
    {
		$allowanceCategory = AllowanceCategory::find($id);
		if (is_null($allowanceCategory)) {
			Session::flash('message', trans('system.have_an_error'));
			Session::flash('alert-class', 'danger');
			return back();
		}
		$request->merge(['status' => $request->input('status', 1)]);
		$request->merge(['type' => $request->input('type', 0)]);
		$request->merge(['is_social_security' => $request->input('is_social_security', 0)]);
		$request->merge(['is_exemp' => $request->input('is_exemp', 0)]);
		$request->merge(['ot' => $request->input('ot', 0)]);
		$data = $request->only(['name', 'name_es', 'status', 'type', 'desc', 'is_social_security', 'is_exemp', 'ot', 'company_id', 'department']);
		if (!is_null($data['company_id'])) {
			$data['department'] = implode(', ', $data['department']);
		}
		$validator = \Validator::make($data, AllowanceCategory::rules($id));
		$validator->setAttributeNames(trans('allowance_categories'));
		if ($validator->fails()) return back()->withErrors($validator)->withInput();
		$allowanceCategory->update($data);
		Session::flash('message', trans('system.success'));
		Session::flash('alert-class', 'success');
		return redirect()->route('admin.allowance-categories.index');

    }

    public function destroy($id)
    {
		$allowanceCategory = AllowanceCategory::find($id);
		if (is_null($allowanceCategory)) {
			Session::flash('message', trans('system.have_an_error'));
			Session::flash('alert-class', 'danger');
			return back();
		}
		if (count($allowanceCategory->allowances) || count($allowanceCategory->appendix_allowances)) {
			Session::flash('message', trans('allowance_categories.error_delete'));
			Session::flash('alert-class', 'danger');
			return back();
		}
		$allowanceCategory->delete();
		Session::flash('message', trans('system.success'));
		Session::flash('alert-class', 'success');
		return redirect()->route('admin.allowance-categories.index');
    }
}
