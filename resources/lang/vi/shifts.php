<?php
return [
    'create'=>'Thêm mới lịch làm việc',
    'label'=>'Lịch làm việc',
    'shift'=>'Ca làm',
    'shift_and_ot'=>'Làm theo kíp',
    'shift_one'=>'Ca 1 - N',
    'shift_and_ot_one'=>'Kíp 1',
    'shift_two'=>'Ca 2 - N',
    'shift_and_ot_two'=>'Kíp 2',
    'shift_three'=>'Ca 3 - Đ',
    'people'=>'Nhân viên (để trống nếu ca nghỉ)',
    'people_shift_and_ot'=>'Nhân viên (để trống nếu kíp nghỉ)',
    'start_date'=>'Ngày bắt đầu',
    'end_date'=>'Ngày kết thúc',
    'start_date_placeholder'=>'Nhập ngày bắt đầu',
    'end_date_placeholder'=>'Nhập ngày kết thúc',
    'error_start_date_required'=>'Vui lòng nhập ngày bắt đầu',
    'error_out_date'=>'Ngày bắt đầu phải lớn hơn ngày hiện tại',
    'error_end_date_required'=>'Vui lòng nhập ngày kết thúc',
    'error_end_date_bigger'=>'Ngày kết thúc phải lớn hơn ngày bắt đầu',
    'edit_day'=>'Làm việc theo ca',
    'error_create'=>'Lỗi tạo lịch làm việc',
    'error_update'=>'Lỗi cập nhật lịch làm việc',
    'error_delete'=>'Lỗi xóa lịch làm việc',
    'error_date_in'=>'Đã có lịch làm việc trong khoảng thời gian đã chọn',
    'cannot_required_all'=>'Không thể để trống tất cả các ca',
    'cannot_required_shift_all'=>'Không thể để trống tất cả các kíp',
    'success_create'=>'Tạo lịch làm việc thành công',
    'success_update'=>'Cập nhật lịch làm việc thành công',
    'success_delete'=>'Xóa lịch làm việc thành công',
    'success'=>'Lấy dữ liệu thành công',
    'errors'=>'Lỗi lấy dữ liệu vui lòng thử lại sau',
    'confirm'=>'Cảnh báo',
    'title_confirm_all'=>'Bạn chắn chắn muốn xóa toàn bộ lịch làm việc trong khoảng thời gian này ?',
    'title_confirm_one_1'=>'Bạn chắn chắn muốn xóa ngày',
    'title_confirm_one_2'=>'trong lịch làm việc ?',
    'must_one'=>'Mỗi nhân viên chỉ làm 1 ca trong ngày',
    'note'=>'Thay đổi lịch làm việc',
    'confirm_edit'=>'Bạn có chắc chắn muốn chỉnh sửa lịch làm việc',
    'edit_all'=>'Chỉnh sửa toàn bộ',
    'edit_one'=>'Chỉnh sửa khoảng thời gian đã chọn',
    'cancel'=>'Hủy bỏ',
	'types' => [
		\App\Define\Shift::OFFICE_TIME => 'Hành chính',
		\App\Define\Shift::SHIFT_TIME => 'Làm theo ca',
		\App\Define\Shift::SHIFT_TIME_AND_OT => 'Làm theo kíp',
		\App\Define\Shift::NINE_HOUR => 'Đủ 9 tiếng',
	],

    'type_exports' => [
        \App\Define\Shift::OFFICE_TIME => 'Hành chính',
        \App\Define\Shift::SHIFT_TIME => 'Làm theo ca',
        \App\Define\Shift::SHIFT_TIME_AND_OT => 'Làm theo kíp',
        \App\Define\Shift::NINE_HOUR => 'Làm theo ca',
    ],

    'choose'=>[
        'user'=>'Chọn nhân viên',
        'shifts'=>'Chọn ca làm việc',
    ],
    'shifts'=>[
        \App\Define\Shift::FIRST_SHIFT => 'Ca 1',
        \App\Define\Shift::SECOND_SHIFT => 'Ca 2',
        \App\Define\Shift::THREE_SHIFT => 'Ca 3',
        \App\Define\Shift::FOUR_SHIFT => 'Ca 4',
    ],
    'color_types'=>[
        \App\Define\Shift::FIRST_SHIFT => '#3CB371',
        \App\Define\Shift::SECOND_SHIFT => '#1E90FF',
        \App\Define\Shift::THREE_SHIFT => '#F4A460',
        \App\Define\Shift::FOUR_SHIFT => 'rgb(244 103 96)',
    ],
    'required_start' => 'Ngày bắt đầu không để trống.',
    'required_end' => 'Ngày kết thúc không để trống.',
    'past_date' => 'Chỉ có thể xét lịch làm trong tương lai',
    'same_day_off' => 'Ngày chọn đã có lịch làm.',
    'error_past_day_off' => 'Lịch làm đã bắt đầu.',
    'apply'=>'Áp dụng lịch làm của tháng cho',
    'shift_four' => 'Ca 4 - HC',
    'user_id' => 'Nhân viên',
    'user_required' => 'Cần chọn nhân viên'

];