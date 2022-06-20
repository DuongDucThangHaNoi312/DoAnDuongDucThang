<?php

namespace App\Http\Controllers\backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Insurance;
use App\Models\InsuranceDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class InsuranceController extends Controller
{
    public function index()
    {
        $insurances = Insurance::with('company')->orderBy('year', 'DESC')->orderBy('month', 'DESC')->orderBy('id', 'DESC')->get();
        return view('backend.insurances.index', compact('insurances'));
    }

    public function createBulk()
    {
        return view('backend.insurances.create-bulk');
    }

    public function download()
    {
        $file = public_path() . "/assets/media/files/templates/bang-phan-bo-chi-phi-luong-lai-xe.xlsx";
        $headers = [
            'Content-Type: application/xls',
        ];
        return response()->download($file, 'bao-hiem' . time() . '.xlsx', $headers);
    }

    public function readBulk(Request $request)
    {
        ini_set('memory_limit', '4096M');
        $response = ['message' => trans('system.have_an_error')];
        $statusCode = 200;
        if ($request->ajax()) {
            try {
                $file = $request->file;
                switch ($file->getClientOriginalExtension()) {
                    case 'xlsx':
                        $data = \Excel::toArray(new \App\Imports\InsuranceImport, $file);
                        if ($data) $data = $data[0];
                        
                        $response['message'] = view('backend.insurances.excel_im', compact('data'))->render();
                        break;
                    default:
                        throw new \Exception("Không hỗ trợ định dạng", 1);
                }
            } catch (\Exception $e) {
                if ($statusCode == 200) $statusCode = 500;
                $response['message'] = $e->getMessage();
            } finally {
                return response()->json($response, $statusCode);
            }
        } else {
            $statusCode = 405;
            return response()->json($response, $statusCode);
        }
    }

    public function saveBulk(Request $request)
    {
        $response = ['message' => trans('system.have_an_error')];
        $statusCode = 200;
        if ($request->ajax()) {
            
            try {
                $data = $request->data;
                $month = $request->month;
                $year = $request->year;
                $title = $request->title;
                $company_id = $request->company_id;
                
                if (!is_array($data) || count($data) == 0) {
                    $statusCode = 400;
                    throw new \Exception(trans('system.have_an_error'), 1);
                }
                
                // $key_last = array_key_last($data);
                unset($data[0]);
                // unset($data[$key_last]);

                $data = array_values($data);
                $companies = Company::all();
                foreach ($data as $k => $d) {
                    if ($d[2] == null || $d[3] == null) continue;
                    $license_plates = trim($d[1]);
                    $company_name = trim($d[2]);
                    $job_code = trim($d[3]);
                    $total_salary_vans = trim($d[4]);
                    $total_wharf = trim($d[5]);
                    $bhxh_drive = trim($d[6]);
                    $bhyt_drive = trim($d[7]);
                    $union_drive = trim($d[8]);
                    $bhtn_drive = trim($d[9]);
                    $basic_salary_allowance = trim($d[10]);
                    $bhxh_other = trim($d[11]);
                    $bhyt_other = trim($d[13]);
                    $union_other = trim($d[13]);
                    $bhtn_other = trim($d[14]);
                    $total_salary = trim($d[15]);
                    
                    $company = $companies->where('shortened_name', $company_name)->first();

                    if (is_null($company)) {
                        throw new \Exception("Kiểm tra lại công ty tại dòng số " . (($d[0] - 1) ?? "") . "");
                    }

                    $insert[] = [
                        'license_plates' => $license_plates,
                        'company_id' => $company->id,
                        'job_code' => $job_code,
                        'total_salary_vans' => $total_salary_vans == '' ? 0 : $total_salary_vans,
                        'total_wharf' => $total_wharf == '' ? 0 : $total_wharf,
                        'bhxh_drive' => $bhxh_drive == '' ? 0 : $bhxh_drive,
                        'bhyt_drive' => $bhyt_drive == '' ? 0 : $bhyt_drive,
                        'union_drive' => $union_drive == '' ? 0 : $union_drive,
                        'bhtn_drive' => $bhtn_drive == '' ? 0 : $bhtn_drive,
                        'basic_salary_allowance' => $basic_salary_allowance == '' ? 0 : $basic_salary_allowance,
                        'bhxh_other' => $bhxh_other == '' ? 0 : $bhxh_other,
                        'bhyt_other' => $bhyt_other == '' ? 0 : $bhyt_other,
                        'union_other' => $union_other == '' ? 0 : $union_other,
                        'bhtn_other' => $bhtn_other == '' ? 0 : $bhtn_other,
                        'total_salary' => $total_salary == '' ? 0 : $total_salary,
                    ];
                    
                    $insurance = [
                        'month' => $month,
                        'year' => $year,
                        'created_by' => Auth::user()->id,
                        'title' => $title,
                        'company_id' => $company_id
                    ];
                }

                DB::beginTransaction();

                $insurance = Insurance::create($insurance);
                foreach ($insert as $item) {
                    $item['insurance_id'] = $insurance->id;
                    InsuranceDetail::insert($item);
                }
                DB::commit();

                $response['message'] = trans('system.success');
                Session::flash('message', $response['message']);
                Session::flash('alert-class', 'success');

            } catch (\Exception $e) {
                DB::rollBack();
                if ($statusCode == 200) $statusCode = 500;
                $response['message'] = $e->getMessage();
            } finally {
                return response()->json($response, $statusCode);
            }
        } else {
            $statusCode = 405;
            return response()->json($response, $statusCode);
        }
    }

    public function detail($id)
    {
        $insurance = Insurance::find(intval($id))->load('insurance_detail');

        if (is_null($insurance)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.insurances.index');
        }

        return view('backend.insurances.detail', compact('insurance'));
    }

    public function destroy($id)
    {
        $insurance = Insurance::find(intval($id));
        if (is_null($insurance)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.insurances.index');
        }
		try {
			DB::beginTransaction();
			$insurance->insurance_detail()->delete();
			$insurance->delete();
			DB::commit();
		} catch (\Exception $e) {
			DB::rollBack();
			return back()->withErrors($e)->withInput();
		}
        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');
        return redirect()->route('admin.insurances.index');
    }
}
