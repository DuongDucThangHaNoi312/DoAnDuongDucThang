<?php

namespace App\Exports;

use App\User;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class UsersExport implements FromView, ShouldAutoSize, WithColumnFormatting, WithTitle
{
	use Exportable;
	protected $userIds;

	public function __construct($userIds = [])
	{
		$this->userIds = $userIds;
	}

	public function view(): View
	{
//		$allColumns = \Schema::getColumnListing('users');
//		$listColumns = array_diff($allColumns, ['id']);
		$users = [];
		if ($this->userIds) {
			$users = User::with('families')->whereNotIn('fullname', ['System', 'Administrator', 'KT LOG', 'KT PAC'])->whereIn('id', $this->userIds)->orderBy('id', 'desc')->get();
		}
		$maxCountFamily = 0;
		foreach ($users as $user) {
			if (count($user->families) > $maxCountFamily) $maxCountFamily = count($user->families);
		}
		return view('backend.staffs._table_export', [
			'users' => $users,
			'maxCountFamily' => $maxCountFamily
		]);
	}

	public function columnFormats(): array
	{
		return [
			'J' => NumberFormat::FORMAT_NUMBER,
			'T' => NumberFormat::FORMAT_NUMBER,
			'V' => NumberFormat::FORMAT_NUMBER,
			'R' => NumberFormat::FORMAT_NUMBER,
		];
	}

	public function title(): string
	{
		return 'Nhân viên';
	}
}
