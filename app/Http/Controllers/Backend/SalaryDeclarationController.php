<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\DepartmentGroup;
use App\Models\JobDetailBooking;
use App\Models\SalaryDeclaration;
use App\Models\SalaryDeclarationDetail;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class SalaryDeclarationController extends Controller
{
    protected $declarationWithPoint;
    protected $companyCode;
    protected $departmentGroupCode;

    public function __construct()
    {
        $this->declarationWithPoint = DB::table('type_declarations')->pluck('point', 'name')->toArray(); 
        $this->companyCode = Company::where('status', 1)->pluck('shortened_name', 'id')->toArray();
        $this->departmentGroupCode = DepartmentGroup::where('status', 1)->where('type', \App\Define\Department::DECLARATION_OFFICE)->pluck('name', 'id')->toArray();
    }

    public function index()
    {
        $user = Auth::user();
        if ($user->hasRole('TGD') || $user->hasRole('system') || in_array($user->qualification_id, \App\Defines\User::KT)) {
            $query = '1=1';
        } else {
            $query = 'department_group_id = "' .$user->dept_group_id. '"';
        }

        $data = [];
        $salaryDeclarations = SalaryDeclaration::with('departmentGroup', 'user', 'tpApproved', 'ktApproved')
                                ->whereRaw($query)
                                ->orderBy('year', 'DESC')
                                ->orderBy('month', 'DESC')
                                ->get()
                                ->groupBy(['month_year', 'department_group_id'])
                                ->toArray();
        
        $departmentGroups = $this->departmentGroupCode;
        // dd($salaryDeclarations);
        foreach ($salaryDeclarations as $key1 => $salaryDeclaration) {
            $data[$key1] = [];
            foreach ($salaryDeclaration as $key2 => $value) {
                $data[$key1][$key2] = [];
                $data[$key1][$key2]['CREATED'] = [];
                $data[$key1][$key2]['declaration_main'] = $data[$key1][$key2]['declaration_branch'] = $data[$key1][$key2]['declaration_self'] = $data[$key1][$key2]['point'] = 0;
                $data[$key1][$key2]['tp_approved'] = $data[$key1][$key2]['kt_approved'] = false;
                foreach ($value as $key3 => $item) {
                    if (is_null($data[$key1][$key2]['CREATED'][$item['company_id']])) $data[$key1][$key2]['CREATED'][$item['company_id']] = [];
                    $data[$key1][$key2]['declaration_main'] += $item['declaration_main'];
                    $data[$key1][$key2]['declaration_branch'] += $item['declaration_branch'];
                    $data[$key1][$key2]['declaration_self'] += $item['declaration_self'];
                    $data[$key1][$key2]['point'] += $this->getPoint($item['declaration_main'], $item['declaration_branch'], $item['declaration_self'], $item['type_declaration']);
                    $data[$key1][$key2]['created_by'] = $item['user']['fullname'];
                    $data[$key1][$key2]['department_group'] = $item['department_group']['name'];
                    $data[$key1][$key2]['month_year'] = $item['month_year'];
                    $data[$key1][$key2]['month_year'] = $item['month_year'];
                    
                    // nhân viên công ty khác vẫn cộng
                    if (!in_array($item['user_mo_tk'], $data[$key1][$key2]['CREATED'][$item['company_id']])) {
                        array_push($data[$key1][$key2]['CREATED'][$item['company_id']], $item['user_mo_tk']);
                    }
                    $data[$key1][$key2]['tp_approved_date'] = $item['tp_approved_date'];
                    $data[$key1][$key2]['kt_approved_date'] = $item['kt_approved_date'];
                    $data[$key1][$key2]['tp_approved_by'] = $item['tp_approved']['fullname'];
                    $data[$key1][$key2]['kt_approved_by'] = $item['kt_approved']['fullname'];
                    if (!is_null($item['tp_approved_date'])) $data[$key1][$key2]['tp_approved'] = true;
                    if (!is_null($item['kt_approved_date'])) $data[$key1][$key2]['kt_approved'] = true;
                    
                }              
            }
        }
        $departmentGroupCode = $this->departmentGroupCode;
        // dd($data);
        return view('backend.salary-declaration.index', compact('departmentGroups', 'data', 'departmentGroupCode'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $departmentGroupId = $request->department_group_id;   
        $month = $request->month;   
        $year = $request->year;   
        $query = '1=1';
        if (is_null($departmentGroupId)) {
            Session::flash('message', 'Chưa chọn nhóm phòng ban');
            Session::flash('alert-class', 'danger');
            return back();
        }
        
        if (is_null($year)) {
            Session::flash('message', 'Chưa chọn năm');
            Session::flash('alert-class', 'danger');
            return back();
        }

        $monthYear = $month. '/'. $year;
        $firstSalaryDeclaration = SalaryDeclaration::where('month_year', $monthYear)
                                            ->where('department_group_id', $departmentGroupId)
                                            ->first();
        if (!is_null($firstSalaryDeclaration)) {
            Session::flash('message', 'Đã có dữ liệu của nhóm phòng ban');
            Session::flash('alert-class', 'danger');
            return back();
        }
        
        $start = $year. '-'. str_pad(($month - 1), 2, "0", STR_PAD_LEFT). '-26 00:00:00';
        if ($month == 1) {
            $start = ($year - 1). '-12-26 00:00:00';
        }
        $end = $year. '-'. str_pad($month, 2, "0", STR_PAD_LEFT). '-25 23:59:59' ;

        $query .= " AND created_at >= '{$start}'";
        $query .= " AND created_at <= '{$end}'";
        $query .= " AND department_group_id = '{$departmentGroupId}'";

        $jobDetails = JobDetailBooking::with('job')
                        ->whereRaw($query)
                        ->orderBy('job_id')
                        ->get()
                        // ->sortBy('job.company_id')
                        ->toArray();
        $soTkMain = $soTkBranch = $soTkSelf = [];
        $typeDeclarations = DB::table('type_declarations')->pluck('name')->toArray();
        $jobId =  null;
        

        try {
            DB::beginTransaction();     
            foreach ($jobDetails as $key => $jobDetail) {
                if (is_null($jobDetail['declaration_form_no']) || $jobDetail['declaration_form_no'] == '' || !in_array($jobDetail['declaration_form_type'], $typeDeclarations)) continue;
                if ($jobId != $jobDetail['job_id'])  $soTkMain = $soTkBranch = $soTkSelf = [];
                $temp = [];
                $temp['department_group_id'] =  $departmentGroupId;
                $temp['month'] =  $month;
                $temp['year'] =  $year;
                $temp['created_by'] =  Auth::id();;
                $temp['department_group_id'] =  $departmentGroupId;
                $temp['month_year'] = $monthYear;
                $temp['user_mo_tk'] = $jobDetail['created_by'];
                $temp['type_declaration'] = $jobDetail['declaration_form_type'];
                $temp['company_id'] = $jobDetail['job']['company_id'];

                $check = false;
                // có mở tk
                if ($jobDetail['is_declaration_opening_services'] == 1) {
                    // tk chính
                    if ($jobDetail['declaration_form_branch'] == 1 || $jobDetail['declaration_form_branch'] == null || $jobDetail['declaration_form_branch'] == '') {
                        if (!in_array($jobDetail['declaration_form_no'], $soTkMain)) {
                            array_push($soTkMain, $jobDetail['declaration_form_no']);
                            $temp['declaration_main'] = 1;
                            $temp['declaration_branch'] = 0;
                            $temp['declaration_self'] = 0;
                        } else {
                            $check = true;
                        }
                    } else { // tk nhanh
                        if (!in_array($jobDetail['declaration_form_no'], $soTkBranch)) {
                            array_push($soTkBranch, $jobDetail['declaration_form_no']);
                            $temp['declaration_main'] = 0;
                            $temp['declaration_branch'] = 1;
                            $temp['declaration_self'] = 0;
                        } else {
                            $check = true;
                        }
                    }
                } else { // không mở (tự mở)
                    if (!in_array($jobDetail['declaration_form_no'], $soTkSelf)) {
                        array_push($soTkSelf, $jobDetail['declaration_form_no']);
                        $temp['declaration_main'] = 0;
                            $temp['declaration_branch'] = 0;
                            $temp['declaration_self'] = 1;
                    } else {
                        $check = true;
                    }
                }
                $jobId = $jobDetail['job_id'];
                if (!$check) SalaryDeclaration::create($temp);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            return back()->withErrors($e)->withInput();
        }

        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');
        return back();

    }

    public function show($id)
    {
        //
    }
    
    // xem theo phòng ban
    public function detailWithDep(Request $request)
    {
        $data = $company =  $total = [];
        $monthYear = $request->month_year;
        $departmentGroup = $request->department_group_id;

        if (is_null($monthYear) || is_null($departmentGroup)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return back();
        }
        
        $salaryDeclarations = SalaryDeclaration::with('departmentGroup')
                                        ->where('month_year', $monthYear)
                                        ->where('department_group_id', $departmentGroup)
                                        ->get()
                                        ->groupBy(['type_declaration', 'company_id'])
                                        ->toArray();
        $declarationWithPoints = $this->declarationWithPoint;
        
        foreach ($salaryDeclarations as $key1 => $salaryDeclaration) {
            if (is_null($data[$key1])) $data[$key1] = [];
            foreach ($salaryDeclaration as $key2 => $value) {
                if (is_null($total[$key2])) {
                    $total[$key2] = [];
                    $total[$key2]['POINT'] = 0;
                    $total[$key2]['MAIN'] = 0;
                    $total[$key2]['BRANCH'] = 0;
                    $total[$key2]['SELF'] = 0;
                    $total[$key2]['CREATED'] = [];
                }
                array_push($company, $key2);

                if (is_null($data[$key1][$key2])) {
                    $data[$key1][$key2] = [];
                    $data[$key1][$key2]['declaration_main'] = $data[$key1][$key2]['declaration_branch'] = $data[$key1][$key2]['declaration_self'] = 0;
                }
                foreach ($value as $key3 => $item) {
                    if ($item['declaration_main'] == 1) {
                        $data[$key1][$key2]['declaration_main']++ ;
                        $total[$key2]['MAIN']++;
                    } else {
                        if ($item['declaration_branch'] == 1) {
                            $data[$key1][$key2]['declaration_branch']++ ;
                            $total[$key2]['BRANCH']++;
                        } else {
                            if ($item['declaration_self'] == 1) {
                                $data[$key1][$key2]['declaration_self']++ ;
                                $total[$key2]['SELF']++;
                            } 
                        }
                    }
                    $total[$key2]['POINT'] += $this->getPoint($item['declaration_main'], $item['declaration_branch'], $item['declaration_self'], $item['type_declaration']);
                    if (!in_array($item['user_mo_tk'], $total[$key2]['CREATED'])) array_push($total[$key2]['CREATED'], $item['user_mo_tk']); 
                }
            }
        }

        $company = (array_unique($company));
        $companyCode = $this->companyCode;
        $departmentGroupCode = $this->departmentGroupCode;
        return view('backend.salary-declaration.detail-deparment', compact('salaryDeclarations', 'declarationWithPoints', 'data', 'company', 'companyCode', 'total', 'monthYear', 'monthYear', 'departmentGroup', 'departmentGroupCode'));
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy(Request $request, $id)
    {
        $monthYear = $request->month_year;
        $departmentGroup = $id;

        if (is_null($monthYear) || is_null($departmentGroup)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return back();
        }

        $salaryDeclarations = SalaryDeclaration::where('month_year', $monthYear)
                                    ->where('department_group_id', $departmentGroup)
                                    ->select('id')
                                    ->pluck('id')
                                    ->toArray();
        try {
            DB::beginTransaction();                                                               
            if (count($salaryDeclarations) > 0) {
                // foreach ($salaryDeclarations as $key => $value) {
                //     $value->delete();
                // }
                SalaryDeclaration::whereIn('id', $salaryDeclarations)->delete();
            }	
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors($e)->withInput();
        }
        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');
        return back();

    }

    // tính điểm thực tế
    public function getPoint($soTkChinh, $soTkNhanh, $soTkTuMo, $loaiHinhToKhai) 
    {
        $typeDeclarations = $this->declarationWithPoint;
        $point = 0;
        if ($typeDeclarations[$loaiHinhToKhai]) $point = $typeDeclarations[$loaiHinhToKhai] ;
        return  ($soTkChinh +  $soTkNhanh*0.3 + $soTkTuMo*0.2)* $point;
    }

    // xem theo cty
    public function detailWithCom(Request $request)
    {
        $data = [];
        $monthYear = $request->month_year;
        $departmentGroup = $request->department_group_id;

        if (is_null($monthYear) || is_null($departmentGroup)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return back();
        }
        
        $salaryDeclarations = SalaryDeclaration::where('month_year', $monthYear)
                                        ->where('department_group_id', $departmentGroup)
                                        ->get()
                                        ->groupBy(['company_id', 'type_declaration'])
                                        ->toArray();
        $declarationWithPoints = $this->declarationWithPoint;
        foreach ($salaryDeclarations as $key => $value) {
            $data[$key] = [];
            $data[$key]['NV'] = [];
            $data[$key]['MAIN'] = $data[$key]['BRANCH'] = $data[$key]['SELF'] = $data[$key]['POINT'] = 0;
            foreach ($value as $key1 => $item) {
               foreach ($item as $key2 => $vl) {
                    if ($vl['declaration_main'] == 1) {
                        $data[$key]['MAIN'] ++;
                    } else {
                        if (($vl['declaration_branch'] == 1)) {
                            $data[$key]['BRANCH'] ++;
                        } else {
                            $data[$key]['SELF'] ++;
                        }
                    }
                    if (!in_array($vl['user_mo_tk'], $data[$key]['NV'])) array_push($data[$key]['NV'], $vl['user_mo_tk']);
                    $data[$key]['POINT'] += $this->getPoint($vl['declaration_main'], $vl['declaration_branch'], $vl['declaration_self'], $vl['type_declaration']); 
               }
            }
        }
        $companyCode = $this->companyCode;
        $departmentGroupCode = $this->departmentGroupCode;
        return view('backend.salary-declaration.detail-company', compact('companyCode', 'data', 'monthYear', 'departmentGroupCode', 'departmentGroup'));
    }
   
    // xem theo user
    public function detailWithUser(Request $request)
    {
        $data = [];
        $monthYear = $request->month_year;
        $departmentGroup = $request->department_group_id;
        $company = $request->company_id;

        if (is_null($monthYear) || is_null($departmentGroup) || is_null($company)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return back();
        }
        
        $salaryDeclarations = SalaryDeclaration::where('month_year', $monthYear)
                                        ->where('department_group_id', $departmentGroup)
                                        ->where('company_id', $company)
                                        ->get()
                                        ->groupBy(['type_declaration'])
                                        ->toArray();
        $declarationWithPoints = $this->declarationWithPoint;
        foreach ($salaryDeclarations as $key => $value) {
            $data[$key] = [];
            $data[$key]['MAIN'] = $data[$key]['BRANCH'] = $data[$key]['SELF'] = $data[$key]['POINT'] = 0;
            foreach ($value as $key1 => $item) {
                if ($item['declaration_main'] == 1) {
                    $data[$key]['MAIN'] ++;
                } else {
                    if (($item['declaration_branch'] == 1)) {
                        $data[$key]['BRANCH'] ++;
                    } else {
                        $data[$key]['SELF'] ++;
                    }
                }
                $data[$key]['POINT'] += $this->getPoint($item['declaration_main'], $item['declaration_branch'], $item['declaration_self'], $item['type_declaration']); 
            }
        }
        $companyCode = $this->companyCode;
        $departmentGroupCode = $this->departmentGroupCode;
        return view('backend.salary-declaration.detail-user', compact('company', 'declarationWithPoints', 'companyCode', 'data', 'monthYear', 'departmentGroupCode', 'departmentGroup'));
    }

    // phân bổ thưởng cho user
    public function createSalaryUser(Request $request)
    {
        $data = $user = [];
        $rewardPoint = 0;
        $hasMoney = $ktApproved = false;
        $companyCode = $this->companyCode;
        $departmentGroupCode = $this->departmentGroupCode;
        $monthYear = $request->month_year;
        $departmentGroup = $request->department_group_id;
        $company = $request->company_id;
        $totalNV = $request->input('total_nv', 0);
        $totalPoint = $request->input('total_point', 0);
        $point = $request->input('point', 0);

        if (is_null($monthYear) || is_null($departmentGroup) || is_null($company)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return back();
        }
        
        $salaryDeclarations = SalaryDeclaration::where('month_year', $monthYear)
                                        ->where('department_group_id', $departmentGroup)
                                        ->where('company_id', $company)
                                        ->select('user_mo_tk')
                                        ->pluck('user_mo_tk')
                                        ->toArray();
        $user = (array_unique($salaryDeclarations));
        $date = explode('/', $monthYear);
        $month = $date[0]; $year = $date[1];

        if ($totalPoint > $totalNV*100) {
            $totalMoney =  intval(($totalPoint - $totalNV*100)*40000);
            $ratio = round(($point/ $totalPoint) *100, 2, PHP_ROUND_HALF_DOWN);
            $rewardPoint = intval(($ratio/100)*$totalMoney);
        }
        
        // $declarationWithPoints = $this->declarationWithPoint;
        // foreach ($salaryDeclarations as $key => $value) {
        //     $data[$key] = [];
        //     $data[$key]['MAIN'] = $data[$key]['BRANCH'] = $data[$key]['SELF'] = $data[$key]['POINT'] = 0;
        //     foreach ($value as $key1 => $item) {
        //         if ($item['declaration_main'] == 1) {
        //             $data[$key]['MAIN'] ++;
        //         } else {
        //             if (($item['declaration_branch'] == 1)) {
        //                 $data[$key]['BRANCH'] ++;
        //             } else {
        //                 $data[$key]['SELF'] ++;
        //             }
        //         }
        //         $data[$key]['POINT'] += $this->getPoint($item['declaration_main'], $item['declaration_branch'], $item['declaration_self'], $item['type_declaration']); 
        //         if (!in_array($item['user_mo_tk'], $user)) array_push($user, $item['user_mo_tk']);
        //     }
        // }

        // $pointThucte = array_sum(array_column($data, 'POINT'));
        // $pointTarget = count($user) * 100;
        // if ($pointThucte > $pointTarget)  $rewardPoint = ($pointThucte - $pointTarget)*40000;
        
        $userCodes = User::whereIn('id', $user)->selectRaw('id, CONCAT(fullname, "-" ,code) as text')->pluck('text', 'id')->toArray();
        $money = SalaryDeclarationDetail::whereIn('user_id', $user)
                                    ->where('month_year', $monthYear)
                                    ->where('company_id', $company)
                                    ->get(['ratio', 'money', 'user_id', 'note', 'tp_approved_date', 'kt_approved_date'])
                                    ->keyBy('user_id')
                                    ->toArray();
        if (!in_array(null, array_column($money, 'kt_approved_date'))) $ktApproved = true;
        return view('backend.salary-declaration.create-salary-user', compact('ktApproved', 'hasMoney', 'money', 'userCodes', 'rewardPoint', 'company', 'declarationWithPoints', 'companyCode', 'data', 'monthYear', 'departmentGroupCode', 'departmentGroup', 'user', 'month', 'year'));
    }
    
    // lưu lương cho nhân viên
    public function saveSalaryUser(Request $request)
    {
        $response = ['message' => trans('system.have_an_error')];
        $statusCode = 400;
        if ($request->ajax()) {
            try {
                DB::beginTransaction();  
                $company = $request->company_id;
                $departmentGroup = $request->department_group_id;
                $month = $request->month;
                $year = $request->year;
                $month = $request->month;
                $user = $request->user;
                $money = $request->money;
                $ratio =  $request->ratio;
                $note =  $request->note;
                if (floatval(array_sum($ratio)) > 100.00) {
                    throw new \Exception('Tổng tỉ lệ lớn hơn 100 %');
                } 
                SalaryDeclarationDetail::whereIn('user_id', $user)
                                ->where('month', $month)
                                ->where('year', $year)
                                ->where('company_id', $company)
                                ->where('department_group_id', $departmentGroup)
                                ->delete();

                foreach ($user as $key => $value) {
                    $temp = [];
                    $temp['user_id'] = $value;
                    $temp['month'] = $month;
                    $temp['year'] = $year;
                    $temp['department_group_id'] = $departmentGroup;
                    $temp['company_id'] = $company;
                    $temp['money'] = str_replace(',', '', $money[$key]);
                    $temp['ratio'] = str_replace(',', '', $ratio[$key]);
                    $temp['note'] = $note[$key];
                    $temp['month_year'] = $month.'/'.$year;
                    $temp['created_by'] = Auth::id();
                    SalaryDeclarationDetail::create($temp);
                }
                
                DB::commit();
                $statusCode = 200;
                $response['message'] = 'Tạo thành công';
            } catch (\Exception $e) {
                DB::rollBack();
                dd($e);
                $response['message'] = $e->getMessage();
            } finally {
                return response()->json($response, $statusCode);
            }
        } else {
            $statusCode = 405;
            return response()->json($response, $statusCode);
        }

    }

    public function exportExcel(Request $request) {
        $data = $variable = [] ; 
        $departmentGroup = $request->department_group_id;
        $month = $request->month;
        $year = $request->year;

        array_push($variable, $departmentGroup, $month, $year);
        foreach ($variable as $value) {
            if (is_null($value)) {
                Session::flash('message', trans('system.have_an_error'));
                Session::flash('alert-class', 'danger');
                return back();
            }
        }

        $salaryDeclarations = SalaryDeclaration::where('month', $month)
                            ->where('year', $year)
                            ->where('department_group_id', $departmentGroup)
                            ->get()
                            ->groupBy(['company_id', 'type_declaration'])
                            ->toArray();
                            
        $data['TOTAL'] = $infor = $total = $userInDep = [];
        $total['MAIN'] = $total['BRANCH'] = $total['SELF'] = $total['POINT'] = 0;
        $total['USER']= [];
        $infor['month'] = $month;
        $infor['year'] = $year;
        $infor['departmentGroup'] = $departmentGroup;

        foreach ($salaryDeclarations as $key1 => $value) {
            $data[$key1] = [];
            $userInDep[$key1] = [];
            foreach ($value as $key2 => $item) {
                $data[$key1][$key2] = [];
                $data[$key1][$key2]['MAIN'] = 0;
                $data[$key1][$key2]['BRANCH'] = 0;
                $data[$key1][$key2]['SELF'] = 0;
                $data[$key1][$key2]['POINT'] = 0;
                $data[$key1][$key2]['USER'] = [];
                
                foreach ($item as $key3 => $vl) {
                    if ($vl['declaration_main'] == 1) {
                        $data[$key1][$key2]['MAIN']++ ;
                        $total['MAIN']++;
                    } else {
                        if ($vl['declaration_branch'] == 1) {
                            $data[$key1][$key2]['BRANCH']++ ;
                            $total['BRANCH']++ ;
                        } else {
                            $data[$key1][$key2]['SELF']++ ;
                            $total['SELF']++ ;
                        }
                    }
                    $data[$key1][$key2]['POINT'] += $this->getPoint($vl['declaration_main'], $vl['declaration_branch'], $vl['declaration_self'], $vl['type_declaration']);
                    $total['POINT'] += $this->getPoint($vl['declaration_main'], $vl['declaration_branch'], $vl['declaration_self'], $vl['type_declaration']);
                    if (!in_array($vl['user_mo_tk'],  $data[$key1][$key2]['USER'])) {
                        array_push($data[$key1][$key2]['USER'], $vl['user_mo_tk']);
                    }

                    if (!in_array($vl['user_mo_tk'],  $userInDep[$key1])) {
                        array_push($userInDep[$key1], $vl['user_mo_tk']);
                    }
                }
            }
        }

        $total['USER'] = array_merge(...$userInDep);
        return \Excel::download(new \App\Exports\SalaryDeclarationExport($data, $infor, $total), 'JPT-HRM_' . '_' . date('Hmi-dm'). '.xlsx');


    }
    
    // duyệt
    public function approved(Request $request) {
        $monthYear = $request->month_year;
        $departmentGroup = $request->department_group_id;
        $truongPhong = false;
        $keToan = false;
        if (Auth::user()->hasRole('TP')) $truongPhong = true;
        if (in_array(Auth::user()->qualification_id, \App\Defines\User::KT) || Auth::user()->hasRole('TGD') || Auth::user()->hasRole('system') ) $keToan = true;

        if (is_null($monthYear) || is_null($departmentGroup)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return back();
        }
        try {
            DB::beginTransaction();
            if ($truongPhong) {
                SalaryDeclaration::where('month_year', $monthYear)
                        ->where('department_group_id', $departmentGroup)
                        ->update([
                            'tp_approved_by' => Auth::id(),
                            'tp_approved_date' => date("Y-m-d H:i:s"),
                        ]);

                SalaryDeclarationDetail::where('month_year', $monthYear)
                        ->where('department_group_id', $departmentGroup)
                        ->update([
                            'tp_approved_by' => Auth::id(),
                            'tp_approved_date' => date("Y-m-d H:i:s"),
                        ]);        
            } else {
                if ($keToan) {
                    SalaryDeclaration::where('month_year', $monthYear)
                        ->where('department_group_id', $departmentGroup)
                        ->update([
                            'kt_approved_by' => Auth::id(),
                            'kt_approved_date' => date("Y-m-d H:i:s"),
                        ]);

                    SalaryDeclarationDetail::where('month_year', $monthYear)
                        ->where('department_group_id', $departmentGroup)
                        ->update([
                            'kt_approved_by' => Auth::id(),
                            'kt_approved_date' => date("Y-m-d H:i:s"),
                        ]);       
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

    // tính lại
    public function restart(Request $request) {
        $query = '1=1';
        $monthYear = $request->month_year;
        $departmentGroup = $request->department_group_id;
        if (is_null($monthYear) || is_null($departmentGroup)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return back();
        }
        
        $date = (explode('/', $monthYear));
        $month = $date[0];
        $year = $date[1];
        $salaryDeclarations = SalaryDeclaration::where('month_year', $monthYear)
                                    ->where('department_group_id', $departmentGroup)
                                    ->select('id')
                                    ->pluck('id')
                                    ->toArray();
        try {
            DB::beginTransaction();                                                               
            if (count($salaryDeclarations) > 0) {
                // xóa đi
                SalaryDeclaration::whereIn('id', $salaryDeclarations)->delete();
                // kéo db từ handling lại
                $firstSalaryDeclaration = SalaryDeclaration::where('month_year', $monthYear)
                                            ->where('department_group_id', $departmentGroup)
                                            ->first();
                if (!is_null($firstSalaryDeclaration)) {
                    Session::flash('message', 'Đã có dữ liệu của nhóm phòng ban');
                    Session::flash('alert-class', 'danger');
                    return back();
                }
                
                $start = $year. '-'. str_pad(($month - 1), 2, "0", STR_PAD_LEFT). '-26 00:00:00';
                if ($month == 1) {
                    $start = ($year - 1). '-12-26 00:00:00';
                }
                $end = $year. '-'. str_pad($month, 2, "0", STR_PAD_LEFT). '-25 23:59:59' ;

                $query .= " AND created_at >= '{$start}'";
                $query .= " AND created_at <= '{$end}'";
                $query .= " AND department_group_id = '{$departmentGroup}'";

                $jobDetails = JobDetailBooking::with('job')
                                ->whereRaw($query)
                                ->orderBy('job_id')
                                ->get()
                                // ->sortBy('job.company_id')
                                ->toArray();
                $soTkMain = $soTkBranch = $soTkSelf = [];
                $typeDeclarations = DB::table('type_declarations')->pluck('name')->toArray();
                $jobId =  null;
            
                foreach ($jobDetails as $key => $jobDetail) {
                    if (is_null($jobDetail['declaration_form_no']) || $jobDetail['declaration_form_no'] == '' || !in_array($jobDetail['declaration_form_type'], $typeDeclarations)) continue;
                    if ($jobId != $jobDetail['job_id'])  $soTkMain = $soTkBranch = $soTkSelf = [];
                    $temp = [];
                    $temp['department_group_id'] =  $departmentGroup;
                    $temp['month'] =  $month;
                    $temp['year'] =  $year;
                    $temp['created_by'] =  Auth::id();;
                    $temp['month_year'] = $monthYear;
                    $temp['user_mo_tk'] = $jobDetail['created_by'];
                    $temp['type_declaration'] = $jobDetail['declaration_form_type'];
                    $temp['company_id'] = $jobDetail['job']['company_id'];
    
                    $check = false;
                    // có mở tk
                    if ($jobDetail['is_declaration_opening_services'] == 1) {
                        // tk chính
                        if ($jobDetail['declaration_form_branch'] == 1 || $jobDetail['declaration_form_branch'] == null || $jobDetail['declaration_form_branch'] == '') {
                            if (!in_array($jobDetail['declaration_form_no'], $soTkMain)) {
                                array_push($soTkMain, $jobDetail['declaration_form_no']);
                                $temp['declaration_main'] = 1;
                                $temp['declaration_branch'] = 0;
                                $temp['declaration_self'] = 0;
                            } else {
                                $check = true;
                            }
                        } else { // tk nhanh
                            if (!in_array($jobDetail['declaration_form_no'], $soTkBranch)) {
                                array_push($soTkBranch, $jobDetail['declaration_form_no']);
                                $temp['declaration_main'] = 0;
                                $temp['declaration_branch'] = 1;
                                $temp['declaration_self'] = 0;
                            } else {
                                $check = true;
                            }
                        }
                    } else { // không mở (tự mở)
                        if (!in_array($jobDetail['declaration_form_no'], $soTkSelf)) {
                            array_push($soTkSelf, $jobDetail['declaration_form_no']);
                            $temp['declaration_main'] = 0;
                                $temp['declaration_branch'] = 0;
                                $temp['declaration_self'] = 1;
                        } else {
                            $check = true;
                        }
                    }
                    $jobId = $jobDetail['job_id'];
                    if (!$check) SalaryDeclaration::create($temp);
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
