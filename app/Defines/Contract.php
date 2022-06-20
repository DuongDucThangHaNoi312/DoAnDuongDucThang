<?php

namespace App\Defines;

class Contract
{
    const TYPE_6_MONTH 	= 1;
    const TYPE_1_YEAR 	= 2;
    const TYPE_3_YEAR 	= 3;
    const TYPE_UNLIMITED= 4;

    const NOT_VALID 	= 1;
    const NOT_YET_VALID = 2;
	const IS_VALID 		= 3;

	const TRANSFER 	= 2; //ĐIỀU CHUYỂN
	const LEAVE_WORK= 3; //NGHỈ VIỆC
	const ACTIVE 	= 1; //ĐANG HOẠT ĐỘNG
	const DISMISSAL = 4; //MIỄN NHIỆM
	const APPOINT 	= 5; //BỔ NHIỆM
	const END_PART_TIME = 6; // HẾT HẠN THỬ VIỆC
	const CHO_NGHI_VIEC = 7;
	const EXPIRED = 8; //HẾT HẠN HỢP ĐỒNG
    const FUTURE = 9; //CHỜ CÓ HIỆU LỰC

	const DRIVER = ['DR1', 'DR2'];
	const DRIVER_ID = [4, 14];
	const SUBSIDIZE = [1, 2, 6, 5, 7, 8, 9]; // id Trợ cấp dùng trng sắp xếp phụ cấp ở báo cáo
	const CODE_DRIVER = ['DR1', 'DR2']; // chức danh lái xe

    public static function getTypeStatusForSelectDayOffTimeKeeping()
    {
        return [self::ACTIVE, self::TRANSFER, self::CHO_NGHI_VIEC, self::EXPIRED, self::END_PART_TIME];
    }

    const VND = 'VND';
    const USD = 'USD';

    public static function getCurrencyOptions()
    {
        return [
            self::VND => self::VND,
            self::USD => self::USD,
        ];
    }
  
	public static function getType()
    {
        return [
			6     => '6 tháng',
			1      => '1 năm',
			3      => '3 năm',
			4      => 'Vô thời hạn',
        ];
    }



	public static function getTypesForOption()
    {
        return [self::TYPE_6_MONTH => trans('contracts.types.' . self::TYPE_6_MONTH), self::TYPE_1_YEAR => trans('contracts.types.' . self::TYPE_1_YEAR),
            self::TYPE_3_YEAR => trans('contracts.types.' . self::TYPE_3_YEAR), self::TYPE_UNLIMITED => trans('contracts.types.' . self::TYPE_UNLIMITED)];
    }

	public static function getTypeStatusForOption()
	{
		return [
			self::ACTIVE => trans('contracts.type_status.' . self::ACTIVE),
			self::FUTURE => trans('contracts.type_status.' . self::FUTURE),
            self::APPOINT => trans('contracts.type_status.' . self::APPOINT),
			self::TRANSFER => trans('contracts.type_status.' . self::TRANSFER),
			self::DISMISSAL => trans('contracts.type_status.' . self::DISMISSAL),
			self::END_PART_TIME => trans('contracts.type_status.' . self::END_PART_TIME),
			self::CHO_NGHI_VIEC => trans('contracts.type_status.' . self::CHO_NGHI_VIEC),
            self::LEAVE_WORK => trans('contracts.type_status.' . self::LEAVE_WORK),
            self::EXPIRED => trans('contracts.type_status.' . self::EXPIRED),

        ];
	}
}