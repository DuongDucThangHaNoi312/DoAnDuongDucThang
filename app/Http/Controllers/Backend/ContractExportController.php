<?php

namespace App\Http\Controllers\Backend;

use App\Exports\AppendixExport;
use App\Exports\ConcurrentExport;
use App\Exports\ContractExport;
use App\Models\AppendixAllowance;
use App\Models\ConcurrentContract;
use App\Qualification;
use App\User;
use App\Defines\Staff;
use App\Helper\HString;
use App\Models\Contract;
use App\Helpers\HandleDate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpWord\TemplateProcessor;
use function React\Promise\all;

class ContractExportController extends Controller
{
	public function export($id)
	{
		$contract = Contract::with('user','company', 'department', 'position', 'qualification', 'allowances')->find(intval($id));
		if (is_null($contract)) {
			Session::flash('message', trans('system.have_an_error'));
			Session::flash('alert-class', 'danger');
			return back();
		}
		$user = $contract->user;
		$company = $contract->company;
		$signer = User::find($company->user_id);
        $signerPosition = Qualification::find($company->qualification_id)->name;
		$values = [];
		if ($contract->is_main == Staff::STATUS_PROBATIONARY) {
            $salaryDriver = $salaryNo = [];
            array_push($salaryDriver, [
                'salary' => '1/ 150.000VNĐ/01 Ngày nếu đi theo lái xe khác để học việc.',
            ]);
            array_push($salaryDriver, [
                'salary' => '2/ 80% Lương khoán chuyến theo các Quyết định hiện hành nếu đi xe độc lập tự chạy chuyến riêng.',
            ]);
            array_push($salaryNo, [
                'salary' => HString::currencyFormat($contract->basic_salary),
            ]);
            $salary = in_array($contract->qualification->code, ['DR1', 'DR2']) ? $salaryDriver : $salaryNo;
                $values = [
                'code'				=> $contract->code,
                'year'				=> Carbon::parse($contract->valid_from)->year,
                'fullname' 			=> mb_strtoupper($signer->fullname),
                'company' 			=> mb_strtoupper($company->name),
                'company_lowercase' => mb_convert_case($company->name, MB_CASE_TITLE, "UTF-8"),
                'staff' 			=> mb_strtoupper($user->fullname),
                'phone' 			=> $user->phone,
                'email' 			=> $user->email,
                'valid_from'		=> HandleDate::formatDate($contract->valid_from),
                'to_temp'           => Carbon::parse($contract->valid_from)->addDays(30)->format('d/m/Y'),
                'to_temp2'          => Carbon::parse($contract->valid_from)->addDays(31)->format('d/m/Y'),
                'qualification' 	=> $contract->qualification->name,
//                'salary'		    => $salary,
                'company_name_es'	=> $company->name_es ?? '',
                'company_name_es1'	=> $company->name_es ? mb_convert_case($company->name_es, MB_CASE_TITLE, "UTF-8") : '',
                'd'                 => Carbon::parse($contract->valid_from)->format('d'),
                'm'                 => Carbon::parse($contract->valid_from)->format('m'),
            ];
            $templateProcessor = new TemplateProcessor("assets/media/files/templates/contract_template_parttime.docx");
            $templateProcessor->setValues($values);
            $templateProcessor->cloneBlock('s_block', 0, true, false, $salary);
            header("Content-Disposition: attachment; filename=HDTV_". Str::slug($user->fullname) . ".docx");
            $templateProcessor->saveAs('php://output');
//            $nameFile = "HDTV_" . Str::slug($user->fullname). now()->timestamp . ".docx";
//            $public_path  = public_path('assets/media/files/contracts/' . $nameFile);
//            $templateProcessor->saveAs($public_path);
//            $url = "https://docs.google.com/viewerng/viewer?url=" . \URL::to('/') ."/assets/media/files/contracts/" . $nameFile;
//            return \Redirect::to($url);
        } else {
            $allowancesArr = [];
            $allowances = $contract->allowances;
            $check = false;
            foreach ($allowances as $item) {
                $temp = $item->allowanceCategory;
                $checkSort = in_array($temp->id, \App\Defines\Contract::SUBSIDIZE) ? 3 : ($temp->is_social_security ? 1 : 2);
                if ($temp->id == 10) {
                    $checkSort = 4;
                    $check = true;
                }
                $type = $temp->type ? 'VNĐ /100 điểm KPI /tháng' : 'VNĐ /tháng';
                if ($item->type_dept && $temp->id == 1) $type = 'VNĐ /1 ngày làm';
                $name = $temp->id == 6 && in_array($contract->qualification->code, ['DR1', 'DR2']) ? $temp->name . ' +"4G" /4G + Telephone subsidize' : $temp->name;
                $namEs = $temp->id == 6 && in_array($contract->qualification->code, ['DR1', 'DR2']) ? '' : '(' . $temp->name_es . ')';
                array_push($allowancesArr, [
                    'check_sort' => $checkSort,
                    'name' => $name,
                    'name_es' => $namEs,
                    'type_allowance' => $type,
                    'expense' => HString::currencyFormat($item->expense),
                ]);
            }
//            dd($allowancesArr);
            usort($allowancesArr, function ($a, $b) { return strcmp($a["check_sort"], $b["check_sort"]); });
            $codeAppendix = str_replace('HDLD', 'PLHD', $contract->code);
            $textArr = [];
            if ($check) array_push($textArr, ['text' => '(Nếu người lao động nghỉ phép quá 1,5 ngày/01 tháng thì sẽ không được chi trả trợ cấp chuyên cần)']);
            $values = [
                'code'				=> $contract->code,
                'year'				=> Carbon::parse($contract->valid_from)->year,
                'fullname' 			=> mb_strtoupper($signer->fullname),
                'nationality' 		=> $signer->nationality,
                'company' 			=> mb_strtoupper($company->name),
                'company_lowercase' => mb_convert_case($company->name, MB_CASE_TITLE, "UTF-8"),
                'company_address' 	=> $company->address,
                'staff' 			=> mb_strtoupper($user->fullname),
                'signer_position' 	=> mb_strtoupper($signerPosition),
                'signer_phone' 		=> $signer->phone,
                'staff_nationality'	=> $user->nationality,
                'birthday'			=> HandleDate::formatDate($user->date_of_birth),
                'staff_address'		=> $user->addresses,
                'domicile'			=> $user->domicile,
                'id_card_no'		=> $user->id_card_no,
                'issued_on'			=> HandleDate::formatDate($user->issued_on),
                'issued_at'			=> $user->issued_at,
                'type_contract'		=> $contract->type ? ($contract->type != \App\Defines\Contract::TYPE_UNLIMITED ? 'Hợp đồng xác định thời hạn - ' . trans('contracts.types.' . $contract->type) : trans('contracts.types.' . $contract->type)) : trans('staffs.status.' . Staff::STATUS_PROBATIONARY),
                'valid_from'		=> HandleDate::formatDate($contract->valid_from),
                'desc_valid_to'     => $contract->type != \App\Defines\Contract::TYPE_UNLIMITED ? 'đến ngày/ Until:' : '',
                'valid_to'			=> $contract->valid_to ? HandleDate::formatDate($contract->valid_to) : '',
                'qualification' 	=> $contract->qualification->name,
                'desc_qualification'=> $contract->desc_qualification,
                'department_type'	=> trans('shifts.type_exports.' . $contract->department->type),
                'basic_salary'		=> HString::currencyFormat($contract->basic_salary),
                'company_name_es'	=> $company->name_es ?? '',
                'company_name_es1'	=> $company->name_es ? mb_convert_case($company->name_es, MB_CASE_TITLE, "UTF-8") : '',
                'company_short'		=> $contract->company->shortened_name,
                'sign_date'         => Carbon::parse($contract->valid_from)->format('d/m/Y'),
                'sign_date_es'      => Carbon::parse($contract->valid_from)->format('d/m/Y'),
                'day'               => Carbon::parse($contract->valid_from)->format('d'),
                'month'             => Carbon::parse($contract->valid_from)->format('m'),
                'codeAppendix'      => $codeAppendix
            ];
            $templateName = in_array($contract->qualification->code, ['DR1', 'DR2']) ? 'contract_template_driver.docx' : 'contract_template_old.docx';
            $templateProcessor = new TemplateProcessor("assets/media/files/templates/" . $templateName);
            $templateProcessor->setValues($values);
            ini_set("pcre.backtrack_limit", 2000000);
            $templateProcessor->cloneBlock('allowance_block', 0, true, false, $allowancesArr);
            $templateProcessor->cloneBlock('text_block', 0, true, false, $textArr);
            header("Content-Disposition: attachment; filename=HDLD_". Str::slug($user->fullname) . ".docx");
            $templateProcessor->saveAs('php://output');
//            $nameFile = "HDLD_" . Str::slug($user->fullname). now()->timestamp . ".docx";
//            $public_path  = public_path('assets/media/files/contracts/' . $nameFile);
//            $templateProcessor->saveAs($public_path);
//            $url = "https://docs.google.com/viewerng/viewer?url=" . \URL::to('/') ."/assets/media/files/contracts/" . $nameFile;
//            return \Redirect::to($url);
        }
	}

	public function exportTransfer($id)
	{
		$contractOld = Contract::with('user','company', 'department', 'position', 'qualification', 'allowances')->find(intval($id));
		$contractNew = Contract::checkTransfer($id);
		if (is_null($contractOld) || $contractNew === 1 || $contractNew === 2) {
			Session::flash('message', trans('system.have_an_error'));
			Session::flash('alert-class', 'danger');
			return back();
		}
        $signerPosition = mb_strtoupper(Qualification::find($contractOld->company->qualification_id)->name);
        $contains = str_contains($signerPosition, 'CHI NHÁNH');
        if ($contains) $signerPosition = trim(str_replace('CHI NHÁNH', '', $signerPosition));
		$transferValid = $contractOld->report_valid ? Carbon::parse($contractOld->report_valid)->format('d/m/Y') : Carbon::parse($contractNew->valid_from)->format('d/m/Y');
        $d = Carbon::createFromFormat('d/m/Y', $transferValid)->subDays(10)->format('d');
        $m = Carbon::createFromFormat('d/m/Y', $transferValid)->subDays(10)->format('m');
        $y = Carbon::createFromFormat('d/m/Y', $transferValid)->subDays(10)->year;
		$values = [
			'company_old' 			=> $contractOld->company->name,
			'company_old_lowercase' =>  mb_convert_case($contractOld->company->name, MB_CASE_TITLE, "UTF-8"),
			'transfer_code'			=> HandleDate::formatDateDMY($transferValid) . '-' . $contractOld->user->code . '-' . $contractOld->company->shortened_name .  '/QĐĐCNS',
			'year' 					=> $y,
			// 'position_signer'
			'position_old'			=> $contractOld->qualification->name,
			'position_new'			=> $contractNew->qualification->name,
			'staff'					=> $contractOld->user->fullname,
			'company_new' 			=> $contractNew->company->name,
			'company_new_lowercase' =>  mb_convert_case($contractNew->company->name, MB_CASE_TITLE, "UTF-8"),
			'valid_from_new'		=> $contractNew->valid_from->format('d/m/Y'),
			'department'			=> $contractNew->department->name,
			'tranfer_valid'		    => $transferValid,
            'm'                     => $m,
            'd'                     => $d,
            'position'              => $signerPosition
		];
		$templateProcessor = new TemplateProcessor("assets/media/files/templates/tranfer_template.docx");
		$templateProcessor->setValues($values);
		header("Content-Disposition: attachment; filename=QDDC_" . Str::slug($contractOld->user->fullname). ".docx");
		$templateProcessor->saveAs('php://output');
	}

	public function exportQuitJob(Request $request, $id)
	{
		$contract = Contract::with('user', 'company', 'department', 'position', 'qualification')->find(intval($id));
		if (is_null($contract)) {
			Session::flash('message', trans('system.have_an_error'));
			Session::flash('alert-class', 'danger');
			return back();
		}
		$company = $contract->company;
		$user = $contract -> user;
        $signerPosition = mb_strtoupper(Qualification::find($company->qualification_id)->name);
        $contains = str_contains($signerPosition, 'CHI NHÁNH');
        if ($contains) $signerPosition = trim(str_replace('CHI NHÁNH', '', $signerPosition));
		$quitJobValid = $request->transfer_valid ? date('d/m/Y', strtotime($request->transfer_valid)) : Carbon::parse($contract->report_valid)->format('d/m/Y');
		$d = Carbon::createFromFormat('d/m/Y', $quitJobValid)->subDays(10)->format('d');
		$m = Carbon::createFromFormat('d/m/Y', $quitJobValid)->subDays(10)->format('m');
        $y = Carbon::createFromFormat('d/m/Y', $quitJobValid)->subDays(10)->year;
		$values = [
			'company_old' 			=> $company->name,
			'company_old_lowercase' =>  mb_convert_case($company->name, MB_CASE_TITLE, "UTF-8"),
			'quit_job_code'			=> HandleDate::formatDateDMY($quitJobValid) . '-' . $contract->user->code . '-' . $contract->company->shortened_name .  '/CDHĐLD',
			'year' 					=> $y,
			// 'position_signer'
			'staff'					=> $user->fullname,
			'notvalid_date' 		=> $request->transfer_date ? date('d/m/Y', strtotime($request->transfer_date)) : Carbon::parse($contract->set_notvalid_on)->format('d/m/Y'),
			'paid_salary_to'		=> $request->transfer_valid ? date('d/m/Y', strtotime($request->transfer_valid)) : Carbon::parse($contract->set_notvalid_on)->subDay()->format('d/m/Y'),
			'staff_submit_date'		=> $request->transfer_date ? date('d/m/Y', strtotime($request->transfer_date)) : Carbon::parse($contract->staff_submit_date)->format('d/m/Y'),
			'department'			=> $contract->department->name,
			'quit_job_valid'		=>  $request->transfer_valid ? date('d/m/Y', strtotime($request->transfer_valid)) : Carbon::parse($contract->report_valid)->format('d/m/Y'),
            'm'                     => $m,
            'd'                     => $d,
            'paid'                 => $request->transfer_valid ? date('d/m/Y', strtotime($request->transfer_valid)) : Carbon::parse($contract->report_valid)->format('d/m/Y'),
            'position'              => mb_strtoupper($signerPosition),
		];
		$templateProcessor = new TemplateProcessor("assets/media/files/templates/quit_job_template.docx");
		$templateProcessor->setValues($values);
		header("Content-Disposition: attachment; filename=QDThoiViec_" . Str::slug($user->fullname). ".docx");
		$templateProcessor->saveAs('php://output');
	}

	public function exportAppoint(Request $request, $id)
	{
		$contract = Contract::with('user','company', 'department', 'position', 'qualification', 'allowances')->find(intval($id));
		$contractNew = Contract::checkAppoint($id);
		if (is_null($contract) || $contractNew === 1 || $contractNew === 2) {
			Session::flash('message', trans('system.have_an_error'));
			Session::flash('alert-class', 'danger');
			return back();
		}
		$company = $contract->company;
		$user = $contract -> user;
        $signerPosition = mb_strtoupper(Qualification::find($company->qualification_id)->name);
        $contains = str_contains($signerPosition, 'CHI NHÁNH');
        if ($contains) $signerPosition = trim(str_replace('CHI NHÁNH', '', $signerPosition));
        $appointValid = Carbon::parse($contract->report_valid)->format('d/m/Y');
        $d = Carbon::createFromFormat('d/m/Y', $appointValid)->subDays(10)->format('d');
        $m = Carbon::createFromFormat('d/m/Y', $appointValid)->subDays(10)->format('m');
        $y = Carbon::createFromFormat('d/m/Y', $appointValid)->subDays(10)->year;
		$values = [
			'company_old' 			=> $company->name,
			'company_old_lowercase' =>  mb_convert_case($company->name, MB_CASE_TITLE, "UTF-8"),
			'appoint_code'			=> date('dmy', strtotime($contract->report_valid)) . '-' . $contract->user->code . '-' . $contract->company->shortened_name .  '/QĐBNCB',
			'year' 					=> $y,
			// 'position_signer'
			'staff'					=> $user->gender == 1 ? 'Ông ' . $user->fullname : 'Bà ' . $user->fullname,
			'department'			=> $contract->department->name,
			'qualification_old'		=> $contract->qualification->name,
			'qualification_new'		=> $contractNew->qualification->name,
			'valid_from'			=> $contractNew->valid_from->format('d/m/Y'),
			'appoint_valid'			=> $appointValid,
            'm'                     => $m,
            'd'                     => $d,
            'position'              => $signerPosition
		];
		$templateProcessor = new TemplateProcessor("assets/media/files/templates/appoint_template.docx");
		$templateProcessor->setValues($values);
		header("Content-Disposition: attachment; filename=QDBoNhiem_" . Str::slug($user->fullname). ".docx");
		$templateProcessor->saveAs('php://output');
	}

	public function exportDismissal($id)
	{
		$contract = Contract::with('user','company', 'department', 'position', 'qualification', 'allowances')->find(intval($id));
		$contractNew = $contract;
		if (is_null($contract)) {
			Session::flash('message', trans('system.have_an_error'));
			Session::flash('alert-class', 'danger');
			return back();
		}
		// dd($contract);
		$user = $contract->user;
		$company = $contract->company;
		$department = $contract->department;
        $signerPosition = mb_strtoupper(Qualification::find($company->qualification_id)->name);
        $contains = str_contains($signerPosition, 'CHI NHÁNH');
        if ($contains) $signerPosition = trim(str_replace('CHI NHÁNH', '', $signerPosition));
        $dismissalValid = Carbon::parse($contract->report_valid)->format('d/m/Y');
        $d = Carbon::createFromFormat('d/m/Y', $dismissalValid)->subDays(10)->format('d');
        $m = Carbon::createFromFormat('d/m/Y', $dismissalValid)->subDays(10)->format('m');
        $y = Carbon::createFromFormat('d/m/Y', $dismissalValid)->subDays(10)->year;
		$values = [
			'company_old' 			=> $company->name,
			'company_old_lowercase' =>  mb_convert_case($company->name, MB_CASE_TITLE, "UTF-8"),
			'dismissal_code'		=>  date('dmy', strtotime($contract->report_valid)) . '-' . $contract->user->code . '-' . $contract->company->shortened_name .  '/QĐMNCB',
			'year' 					=> $y,
			// 'position_signer'
			'staff'					=> $user->gender == 1 ? 'Ông ' . $user->fullname : 'Bà ' . $user->fullname,
			'staff_new'				=> '',
			'position'				=> $contract->position->name,
			'department1'			=> $department->name,
			'department'			=> Str::slug($department->name) == 'phong-hanh-chinh-nhan-su' ? '' : $department->name . ', ',
			'notvalid_date'			=> date('d/m/Y', strtotime($contract->set_notvalid_on)),
			'dismissal_valid'		=> $dismissalValid,
            'm'                     => $m,
            'd'                     => $d,
            'positionSign'          => $signerPosition
		];
		$templateProcessor = new TemplateProcessor("assets/media/files/templates/dismissal_template.docx");
		$templateProcessor->setValues($values);
		header("Content-Disposition: attachment; filename=QDMienNhiem_" . Str::slug($user->fullname). ".docx");
		$templateProcessor->saveAs('php://output');
	}

    public function exportExcel(Request $request)
    {
        $type = $request->input('type_export') ?? 1;
        $contracts = $request->contractIds ?? [];
        if (!$contracts) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return back();
        }
        $name = $request->name_excel ?? 'HOPDONG' . now()->format('Y-m-d');
        if ($type == 2) return Excel::download(new AppendixExport($contracts), $name.'.xlsx');
        if ($type == 3) return Excel::download(new ConcurrentExport($contracts), $name.'.xlsx');
        return Excel::download(new ContractExport($contracts), $name.'.xlsx');
	}

    public function exportAppendix($contractId, $code)
    {
        $appendixes = AppendixAllowance::where('code', $code)->get();
        $contract = Contract::with('user','company', 'department', 'position', 'qualification', 'allowances')->find(intval($contractId));
        if (is_null($contract)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return back();
        }
        $user = $contract->user;
        $company = $contract->company;
        $signer = User::find($company->user_id);
        $allowancesArr = [];
        foreach ($appendixes as $item) {
            $type = $item->category->type ? 'VNĐ /100 điểm KPI /tháng' : 'VNĐ /tháng';
            array_push($allowancesArr, [
                'name' => $item->category->name,
                'name_es' => $item->category->name_es ?? '',
                'type_allowance' => $type,
                'expense' => HString::currencyFormat($item->expense),
            ]);
        }
        $salary = $appendixes[0]['salary'];
        $d = Carbon::parse($appendixes[0]['valid_from'])->format('d');
        $m = Carbon::parse($appendixes[0]['valid_from'])->format('m');
        $y = Carbon::parse($appendixes[0]['valid_from'])->year;
        $codeAppendix = $contract->is_main == Staff::STATUS_OFFICIAL ? str_replace('HDLD', 'PLHD', $contract->code) : str_replace('HDTV', 'PLHD', $contract->code);
        $values = [
            'code'				=> $contract->code,
            'year'				=> $y,
            'day'               => $d,
            'month'             => $m,
            'code_appendix'     => $codeAppendix,
            'fullname' 			=> mb_strtoupper($signer->fullname),
            'nationality' 		=> $signer->nationality,
            'signer_position' 	=> mb_strtoupper($signer->position->name),
            'company' 			=> mb_strtoupper($company->name),
            'company_lowercase' => mb_convert_case($company->name, MB_CASE_TITLE, "UTF-8"),
            'company_phone' 	=> $company->telephone,
            'company_address' 	=> $company->address,
            'staff' 			=> mb_strtoupper($user->fullname),
            'staff_nationality'	=> $user->nationality,
            'birthday'			=> HandleDate::formatDate($user->date_of_birth),
            'staff_address'		=> $user->addresses,
            'domicile'			=> $user->domicile,
            'id_card_no'		=> $user->id_card_no,
            'issued_on'			=> HandleDate::formatDate($user->issued_on),
            'issued_at'			=> $user->issued_at,
            'code_contract_last'=> $contract->code,
            'code_appendix_before' 	=> $codeAppendix,
            'qualification' 	=> $contract->qualification->name,
            'salary'		    => HString::currencyFormat($salary),
            'company_name_es'	=> $company->name_es ?? '',
            'company_name_es1'	=> $company->name_es ? mb_convert_case($company->name_es, MB_CASE_TITLE, "UTF-8") : '',
            'company_short'		=> $company->shortened_name,
        ];
        $templateProcessor = new TemplateProcessor("assets/media/files/templates/appendix_template_old.docx");
        $templateProcessor->setValues($values);
        ini_set("pcre.backtrack_limit", 2000000);
        $templateProcessor->cloneBlock('allowance_block', 0, true, false, $allowancesArr);
        header("Content-Disposition: attachment; filename=PLHD_". Str::slug($user->fullname) . '-' . $codeAppendix . ".docx");
        $templateProcessor->saveAs('php://output');
    }

    public function exportConcurrent($contractId, $id)
    {
        $contract = Contract::with('user','company', 'department', 'qualification')->find(intval($contractId));
        $contractConcurrent = ConcurrentContract::find($id);
        if (is_null($contract) || is_null($contractConcurrent)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return back();
        }
        $user = $contract->user;
        $company = $contractConcurrent->company;
        $signer = User::find($company->user_id);
        $signerPositionData = Qualification::find($company->qualification_id);
        $signerPosition = is_null($signerPositionData) ? '' : $signerPositionData->name;

        $qualificationData = Qualification::find($contractConcurrent->qualification_id);
        $qualification = is_null($qualificationData) ? '' : $qualificationData->name;
        $descQualification = is_null($qualificationData) ? '' : $qualificationData->description;
        $values = [
            'code'				=> date('dmy', strtotime($contractConcurrent->valid_from)) . '-' . $contract->user->code . '-' . $contract->company->shortened_name .  '/HĐKN',
            'year'				=> Carbon::parse($contractConcurrent->valid_from)->year,
            'fullname' 			=> mb_strtoupper($signer->fullname),
            'nationality' 		=> $signer->nationality,
            'company' 			=> mb_strtoupper($company->name),
            'company_lowercase' => mb_convert_case($company->name, MB_CASE_TITLE, "UTF-8"),
            'company_address' 	=> $company->address,
            'company_phone' 	=> $company->telephone,
            'company_fax' 	    => $company->fax,
            'staff' 			=> mb_strtoupper($user->fullname),
            'signer_position' 	=> mb_strtoupper($signerPosition),
            'signer_phone' 		=> $signer->phone,
            'Snationality'	    => $user->nationality,
            'birthday'			=> HandleDate::formatDate($user->date_of_birth),
            'staff_address'		=> $user->addresses,
            'domicile'			=> $user->domicile,
            'id_card_no'		=> $user->id_card_no,
            'issued_on'			=> HandleDate::formatDate($user->issued_on),
            'issued_at'			=> $user->issued_at,
            'qualification' 	=> $qualification,
            'desc_qualification'=> $descQualification,
            'salary'		    => HString::currencyFormat($contractConcurrent->salary),
            'company_name_es'	=> $company->name_es ?? '',
            'company_name_es1'	=> $company->name_es ? mb_convert_case($company->name_es, MB_CASE_TITLE, "UTF-8") : '',
            'company_short'		=> $company->shortened_name,
            'sign_date'         => Carbon::parse($contractConcurrent->valid_from)->format('d/m/Y'),
            'sign_date_es'      => Carbon::parse($contractConcurrent->valid_from)->format('d F Y'),
            'd'                 => Carbon::parse($contractConcurrent->valid_from)->format('d'),
            'm'                 => Carbon::parse($contractConcurrent->valid_from)->format('m'),
        ];
        $templateProcessor = new TemplateProcessor("assets/media/files/templates/concurrent_template.docx");
        $templateProcessor->setValues($values);
        header("Content-Disposition: attachment; filename=HDKN_". Str::slug($user->fullname) . ".docx");
        $templateProcessor->saveAs('php://output');
    }
}
