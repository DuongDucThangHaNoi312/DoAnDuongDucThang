<?php

namespace App\Imports;

use App\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Events\BeforeImport;

class UsersImport implements ToModel,
	WithHeadingRow,
	WithValidation,
	WithEvents
{
	public function headingRow() : int
	{
		return 1;
	}

    public function model(array $row)
    {
		if ($row['ma_nhan_vien']) {
			$data = [
				'code' => $row['ma_nhan_vien'],
				'fullname' => $row['ho_ten'],
				'email' => $row['email'],
				'addresses' => $row['dia_chi'],
				'nationality' => $row['quoc_tich'],
				'date_of_birth' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['ngay_sinh'])->format('Y-m-d'),
				'phone' => $row['so_dien_thoai'],
				'gender' => $row['gioi_tinh'],
				'id_card_no' => $row['so_cccd'],
				'issued_on' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['ngay_cap'])->format('Y-m-d'),
				'issued_at' => $row['noi_cap'],
				'activated' => 1,
				'active' => 0,
				'created_by' => 1,
			];
//		$validator = \Validator::make($data , User::rules());
//		$validator->setAttributeNames(trans('contracts'));
//		if ($validator->fails()) {
//			return response()->json([
//				'data' => '',
//				'message' => 'Lỗi validate',
//				'errors' => $validator->errors()->all(),
//			]);
//		}
			return User::create($data);
		}
    }

	public function registerEvents() :array
	{
		return [
			BeforeImport::class => function (BeforeImport $event) {
				$totalRows = $event->getReader()->getTotalRows();

				if (!empty($totalRows)) {
//					dd($totalRows['Worksheet']);
				}
			}
		];
    }

	public function rules(): array
	{
		return [
			'ma_nhan_vien' 	=> 'required|max:50|regex:/^[A-Za-z0-9_.-]+$/',
			'ho_ten' 		=> 'required|max:255',
			'email'         => 'required|max:50|min:10|email',
			'dia_chi' 		=> 'required|max:255',
			'quoc_tich' 	=> 'required|max:255',
			'ngay_sinh' 	=> 'required',
			'so_dien_thoai'	=> 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
			'gioi_tinh'		=> 'required',
			'so_cccd' 		=> 'required|numeric|digits:12',
			'ngay_cap' 		=> 'required',
			'noi_cap' 		=> 'required|max:255',
		];
	}

	public function customValidationAttributes()
	{
		return [
			'ma_nhan_vien'  => 'Mã nhân viên',
			'ho_ten' 		=> 'Họ tên',
			'email'         => 'Email',
			'dia_chi' 		=> 'Địa chỉ',
			'quoc_tich' 	=> 'Quốc tịch',
			'ngay_sinh' 	=> 'Ngày sinh',
			'so_dien_thoai'	=> 'Số điện thoại',
			'gioi_tinh'		=> 'Giới tính',
			'so_cccd' 		=> 'Số CCCD',
			'ngay_cap' 		=> 'Ngày cấp',
			'noi_cap' 		=> 'Nơi cấp',
		];
	}
}
