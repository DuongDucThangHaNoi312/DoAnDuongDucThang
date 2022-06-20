<?php

namespace App\Http\Controllers\Backend;

use App\Define\ChooseContainer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CostHandling;
use App\Models\JobDetailBooking;
use App\Models\SalaryChooseCont;
use App\Models\SalaryChooseContainer;
use App\Models\SalaryChooseContDetail;
use App\Models\TypeCostHandling;
use App\Models\WfThreadBooking;
use App\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class SalaryChooseContainerController extends Controller
{
    protected $typeCostHandling;

    public function __construct(TypeCostHandling $typeCostHandling)
    {
        $this->typeCostHandling = $typeCostHandling; 
    }

    public function index()
    {
        $user = Auth::user();
        if ($user->hasRole('TGD') || $user->hasRole('system') || in_array($user->qualification_id, \App\Defines\User::KT)) {
            $query = '1=1';
        } else {
            $query = 'company_id = "' .$user->company_id. '"';
            $query .= 'AND department_id = "' .$user->department_id. '"';
        }

        $salaryChooseConts = SalaryChooseContainer::with('company', 'deparment', 'createdBy', 'tpApproved', 'ktApproved')
            ->orderBy('year', 'DESC')
            ->orderBy('month', 'DESC')
            ->whereRaw($query)
            ->get();
        return view('backend.salary-choose-cont.index', compact('salaryChooseConts'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $validate = [];
        $queryDetail = $queryJob = $queryThread = '1=1';
        $companyId = $request->company_id;
        $departmentId = $request->department_id;
        $month = $request->month;
        $year = $request->year;
        array_push($validate, $companyId, $departmentId, $month, $year);
        foreach ($validate as $key => $value) {
            if (is_null($value)) {
                Session::flash('message', trans('system.have_an_error'));
                Session::flash('alert-class', 'danger');
                return back();
            }
        }

        $alreadySalaryChooseCont = SalaryChooseContainer::where('company_id', $companyId)
            ->where('department_id', $departmentId)
            ->where('month', $month)
            ->where('year', $year)
            ->first();

        if (!is_null($alreadySalaryChooseCont)) {
            Session::flash('message', 'Dữ liệu công ty đã tồn tại');
            Session::flash('alert-class', 'danger');
            return back();
        }                                     
        
        $start = $year. '-'. str_pad(($month - 1), 2, "0", STR_PAD_LEFT). '-26 00:00:00';
        if ($month == 1) {
            $start = ($year - 1). '-12-26 00:00:00';
        }
        $end = $year. '-'. str_pad($month, 2, "0", STR_PAD_LEFT). '-25 23:59:59' ;
        $queryDetail .= " AND update_date_cont >= '{$start}'";
        $queryDetail .= " AND update_date_cont <= '{$end}'";
        $created_by = $this->getListUser($companyId, $departmentId);
       
        // \DB::connection('mysql_booking')->enableQueryLog(); 
        // \DB::connection('mysql_booking')->table('job_details')
        //         ->join('jobs', 'jobs.id', 'job_details.job_id')
        //         ->join('wf_threads', 'wf_threads.job_detail_id', 'job_details.id')
        //         ->join('cost_detail', 'cost_detail.booking_detail_id', 'job_details.id')
        //         ->where('wf_threads.wf_def_detail_id', \App\Defines\NodeBooking::CHON_VO)
        //         ->where('wf_threads.state', \App\Defines\NodeBooking::STATE_APPROVED)
        //         ->whereIn('wf_threads.action_by', $created_by)
        //         ->where('job_details.update_date_cont', '>=', $start)
        //         ->where('job_details.update_date_cont', '<=', $end)
        //         ->where('cost_detail.total_money', '<>', NULL)
        //         ->get(); 
        // dd(\DB::connection('mysql_booking')->getQueryLog());       

        $jobDetailIds = WfThreadBooking::where('wf_def_detail_id', \App\Defines\NodeBooking::CHON_VO)
            ->where('state', \App\Defines\NodeBooking::STATE_APPROVED)
            ->whereIn('action_by', $created_by)
            ->pluck('job_detail_id')
            ->toArray();

        $jobDetailBookings = JobDetailBooking::with('job', 'costDetailChooseContainers', 'threadChonVo')
            ->whereIntegerInRaw('id', $jobDetailIds)
            ->where('status_choose_container', \App\Defines\ChooseContainer::CONFIRM)
            ->whereRaw($queryDetail)
            ->get()
            ->toArray();

        $costDetailChooseContainers = array_column($jobDetailBookings, 'cost_detail_choose_containers');
       
        DB::beginTransaction();
        try {
            $temp2 = $totalUser = [];
            $temp2['company_id'] = $companyId;
            $temp2['department_id'] = $departmentId;
            $temp2['month'] = $month;
            $temp2['year'] = $year;
            $temp2['created_by'] = Auth::id();
            $temp2['total_user'] = 0;
            $temp2['total_money'] = array_sum(array_column($costDetailChooseContainers, 'total_money'));
            $salaryChooseCont = SalaryChooseContainer::create($temp2);

            if (is_null($salaryChooseCont)) {
                Session::flash('message', trans('system.have_an_error'));
                Session::flash('alert-class', 'danger');
                return redirect()->route('admin.salary-choose-containers.index');
            }
            
            foreach ($jobDetailBookings as $key => $job) {
                $temp1 = [];
                $cost = $job['cost_detail_choose_containers'];
                $threadChonVo = $job['thread_chon_vo'];
                if (is_null($cost) || is_null($cost['total_money']) || is_null($threadChonVo)) continue;
                $temp1['user_id'] = $threadChonVo['action_by'];
                $temp1['department_id'] = $departmentId;
                $temp1['company_id'] = $companyId;
                $temp1['month'] = $month;
                $temp1['year'] = $year;
                $temp1['money'] = $cost['total_money'];
                $temp1['cont_no'] = $job['cont_no'];
                $temp1['customer_id'] = $job['customer_id'];
                $temp1['booking_detail'] = ($job['booking_detail']. '_'. $job['order']);
                $temp1['type_cost'] = 'CK16 - CP Chọn Vỏ';
                $temp1['id_salary_choose_cont'] = $salaryChooseCont->id;
                $salaryChooseContDetailCreate = SalaryChooseContDetail::create($temp1);
                if (!in_array($salaryChooseContDetailCreate->user_id, $totalUser)) array_push($totalUser, $salaryChooseContDetailCreate->user_id);
            }
            $salaryChooseCont->update(['total_user' => count($totalUser)]);

            DB::commit();
            Session::flash('message', trans('system.success'));
            Session::flash('alert-class', 'success');
            return back();
        } catch (Exception $e) {
            DB::rollBack();
            // dd($e->getMessage());
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return back();
        }
         
    }

    public function show($id)
    {
        $salaryChooseCont = SalaryChooseContainer::with('company', 'deparment')->where('id', $id)->first();
        if (is_null($salaryChooseCont)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return back();
        }  
        $salaryChooseContDetails = SalaryChooseContDetail::with('user', 'company', 'deparment')
            ->where('id_salary_choose_cont',  $id)
            ->get()
            ->groupBy('user_id')
            ->toArray();
        if (count($salaryChooseContDetails) < 1) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return back();
        }                 
        return view('backend.salary-choose-cont.detail-deparment', compact('salaryChooseContDetails', 'salaryChooseCont'));
    }
    

    public function edit($id, Request $request)
    {

    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        $salaryChooseCont = SalaryChooseContainer::find($id);
        if (is_null($salaryChooseCont)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return back();
        }
        try {
            DB::beginTransaction();     
            $salaryChooseCont->salaryChooseContDetail()->delete();
            $salaryChooseCont->delete();
            DB::commit();

            Session::flash('message', trans('system.success'));
            Session::flash('alert-class', 'success');
            return back();
        } catch (\Exception $e) {
            
            DB::rollBack();
            return back()->withErrors($e)->withInput();
        }
    }

    public function getListUser($companyId, $departmentId) {
        $query = '1=1';
        if (!is_null($companyId)) {
            $query .= " AND company_id = '{$companyId}'";
        } 
        if (!is_null($departmentId)) {
            $query .= " AND department_id = '{$departmentId}'";
        } 
        return User::where('active', 1)->whereRaw($query)->select('id')->get()->pluck('id')->toArray();
    }

    public function detailWithDep(Request $request)
    {
        $salaryChooseContDetails = SalaryChooseContDetail::with('user', 'company', 'deparment', 'customer')
            ->where('user_id', $request->user_id)
            ->where('company_id', $request->company_id)
            ->where('department_id', $request->department_id)
            ->where('month', $request->month)
            ->where('year', $request->year)
            ->orderBy('booking_detail')
            ->get();
        // $customers = DB::table('partners')->where('is_customer', 1)->pluck('code', 'id')->toArray();
        if (is_null($salaryChooseContDetails)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return back();
        }            
        return view('backend.salary-choose-cont.detail-user', compact('salaryChooseContDetails'));
    }

    public function exportExcel(Request $request) {
        $validate = [];
        $companyId = $request->company_id;
        $departmentId = $request->department_id;
        $month = $request->month;
        $year = $request->year;

        array_push($validate, $companyId, $departmentId, $month, $year);
        foreach ($validate as $key => $value) {
            if (is_null($value)) {
                Session::flash('message', trans('system.have_an_error'));
                Session::flash('alert-class', 'danger');
                return back();
            }
        }

        $salaryChooseContDetails = SalaryChooseContDetail::with('user', 'company', 'deparment', 'customer')
            ->where('company_id',  $companyId)
            ->where('department_id',  $departmentId)
            ->where('month',  $month)
            ->where('year',  $year)
            ->orderBy('user_id')
            ->get()
            ->toArray();
        return \Excel::download(new \App\Exports\SalaryChooseContainerExport($salaryChooseContDetails), 'khoan_to_khai.xlsx');
        
    }
   

    // tính lại
    public function restart(Request $request) {
        $validate = $totalUser = [];
        $companyId = $request->company_id;
        $departmentId = $request->department_id;
        $month = $request->month;
        $year = $request->year;
        $queryDetail = '1=1';

        array_push($validate, $companyId, $departmentId, $month, $year);
        foreach ($validate as $key => $value) {
            if (is_null($value)) {
                Session::flash('message', trans('system.have_an_error'));
                Session::flash('alert-class', 'danger');
                return back();
            }
        }

        try {
            DB::beginTransaction();     
            // xóa đi
            $salaryChooseCont = SalaryChooseContainer::where('company_id', $companyId)
                ->where('department_id', $departmentId)
                ->where('month', $month)
                ->where('year', $year)
                ->first();
            if (is_null($salaryChooseCont)) {
                Session::flash('message', trans('system.have_an_error'));
                Session::flash('alert-class', 'danger');
                return redirect()->route('admin.salary-choose-containers.index');
            }

            $salaryChooseCont->salaryChooseContDetail()->delete();
            $salaryChooseCont->delete();
            // lấy lại từ handling

            $start = $year. '-'. str_pad(($month - 1), 2, "0", STR_PAD_LEFT). '-26 00:00:00';
            if ($month == 1) {
                $start = ($year - 1). '-12-26 00:00:00';
            }
            $end = $year. '-'. str_pad($month, 2, "0", STR_PAD_LEFT). '-25 23:59:59' ;
            $queryDetail .= " AND update_date_cont >= '{$start}'";
            $queryDetail .= " AND update_date_cont <= '{$end}'";
            $created_by = $this->getListUser($companyId, $departmentId);    
            
            $jobDetailIds = WfThreadBooking::where('wf_def_detail_id', \App\Defines\NodeBooking::CHON_VO)
                ->where('state', \App\Defines\NodeBooking::STATE_APPROVED)
                ->whereIn('action_by', $created_by)
                ->pluck('job_detail_id')
                ->toArray();

            $jobDetailBookings = JobDetailBooking::with('job', 'costDetailChooseContainers', 'threadChonVo')
                ->whereIntegerInRaw('id', $jobDetailIds)
                ->where('status_choose_container', \App\Defines\ChooseContainer::CONFIRM)
                ->whereRaw($queryDetail)
                ->get()
                ->toArray();
            $costDetailChooseContainers = array_column($jobDetailBookings, 'cost_detail_choose_containers');
       

            $temp2 = [];
            $temp2['company_id'] = $companyId;
            $temp2['department_id'] = $departmentId;
            $temp2['month'] = $month;
            $temp2['year'] = $year;
            $temp2['created_by'] = Auth::id();
            $temp2['total_user'] = 0;
            $temp2['total_money'] = array_sum(array_column($costDetailChooseContainers, 'total_money'));
            $salaryChooseCont = SalaryChooseContainer::create($temp2);

            if (is_null($salaryChooseCont)) {
                Session::flash('message', trans('system.have_an_error'));
                Session::flash('alert-class', 'danger');
                return redirect()->route('admin.salary-choose-containers.index');
            }
            
            foreach ($jobDetailBookings as $key => $job) {
                $temp1 = [];
                $cost = $job['cost_detail_choose_containers'];
                $threadChonVo = $job['thread_chon_vo'];
                if (is_null($cost) || is_null($cost['total_money']) || is_null($threadChonVo)) continue;
                $temp1['user_id'] = $threadChonVo['action_by'];
                $temp1['department_id'] = $departmentId;
                $temp1['company_id'] = $companyId;
                $temp1['month'] = $month;
                $temp1['year'] = $year;
                $temp1['money'] = $cost['total_money'];
                $temp1['cont_no'] = $job['cont_no'];
                $temp1['customer_id'] = $job['customer_id'];
                $temp1['booking_detail'] = ($job['booking_detail']. '_'. $job['order']);
                $temp1['type_cost'] = 'CK16 - CP Chọn Vỏ';
                $temp1['id_salary_choose_cont'] = $salaryChooseCont->id;
                $salaryChooseContDetailCreate = SalaryChooseContDetail::create($temp1);
                if (!in_array($salaryChooseContDetailCreate->user_id, $totalUser)) array_push($totalUser, $salaryChooseContDetailCreate->user_id);
            }
            $salaryChooseCont->update(['total_user' => count($totalUser)]);   

            DB::commit();
            Session::flash('message', trans('system.success'));
            Session::flash('alert-class', 'success');
            return redirect()->route('admin.salary-choose-containers.index');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors($e)->withInput();
        }
    }

    public function approved(Request $request) {
        $month = $request->month;
        $year = $request->year;
        $departmentId = $request->department;
        $companyId = $request->company;

        $truongPhong = false;
        $keToan = false;
        
        if (Auth::user()->hasRole('TP')) $truongPhong = true;
        if (in_array(Auth::user()->qualification_id, \App\Defines\User::KT) || Auth::user()->hasRole('TGD') || Auth::user()->hasRole('system') ) $keToan = true;

        if (is_null($month) || is_null($departmentId || is_null($year) || is_null($companyId))) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return back();
        }

        $salaryChooseCont = SalaryChooseContainer::where('month', $month)
            ->where('year', $year)                       
            ->where('department_id', $departmentId)                       
            ->where('company_id', $companyId)
            ->first();    

        if (is_null($salaryChooseCont)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return back();
        }              
        try {
            DB::beginTransaction();
            if ($truongPhong) {
                $salaryChooseContUpdated = $salaryChooseCont->update([
                    'tp_approved_by' => Auth::id(),
                    'tp_approved_date' => date("Y-m-d H:i:s"),
                ]);
                $salaryChooseContDetailUpdated = $salaryChooseCont->salaryChooseContDetail()->update([
                    'tp_approved_by' => Auth::id(),
                    'tp_approved_date' => date("Y-m-d H:i:s"),
                ]);
                if (is_null($salaryChooseContUpdated) || is_null($salaryChooseContDetailUpdated))   throw new \Exception(trans('system.have_an_error'));
            } else {
                if ($keToan) {
                    $salaryChooseContUpdated = $salaryChooseCont->update([
                        'kt_approved_by' => Auth::id(),
                        'kt_approved_date' => date("Y-m-d H:i:s"),
                    ]);
                    $salaryChooseContDetailUpdated = $salaryChooseCont->salaryChooseContDetail()->update([
                        'kt_approved_by' => Auth::id(),
                        'kt_approved_date' => date("Y-m-d H:i:s"),
                    ]);
                    if (is_null($salaryChooseContUpdated) || is_null($salaryChooseContDetailUpdated))   throw new \Exception(trans('system.have_an_error')); 
                }
            }
                                    
            DB::commit();
            Session::flash('message', trans('system.success'));
            Session::flash('alert-class', 'success');
            return back();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors($e)->withInput();
        }
    }

}
