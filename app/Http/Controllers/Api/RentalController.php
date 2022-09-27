<?php

namespace App\Http\Controllers\Api;

use App\Models\Rental;
use App\Models\RentalService;
use App\Models\RentalEquipment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\Service;
use App\Models\MeetingRoom;


class RentalController extends Controller
{
    protected $msgNoData = "Không tìm thấy dữ liệu";
    protected $success = "Thành công";
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $meetingRoomId = $request->meeting_room_id;
        $dateCurrent = date("Y-m-d H:i:s");
        $data = Rental::query();
        $data->with('rentalServices', 'rentalEquipments');
        
        if ($meetingRoomId) {
            $data->where('meeting_room_id', $meetingRoomId);

            $data = $data->where('rental_start', '<=', $dateCurrent)
                ->where('rental_end', '>=', $dateCurrent);
        }
            
        $data = $data->get();

        
        if (count($data) < 1 ) {
            return  response()->json([
                'status'  => 200,
                'message' => $this->msgNoData,
                'data'    => [],
            ]);
        }


        return  response()->json([
            'status' => 200,
            'message' => $this->success,
            'data'=> $data,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $equipments = Equipment::selectRaw("CONCAT(name, '-', price) as text, id")->pluck('text', 'id')->toArray();
        $services = Service::selectRaw("CONCAT(name, '-', price) as text, id")->pluck('text', 'id')->toArray();
        $meetingRooms = MeetingRoom::selectRaw("CONCAT(name, '-', price, '-', path_img) as text, id")->pluck('text', 'id')->toArray();
        $detailMeetingRoom =  explode('-', $meetingRooms[$data['meeting_room_id']]);
        $data = $request->all();

        if (!isset($data['meeting_room_id'])) {
            return  response()->json([
                'message' => 'Chọn phòng thuê',
                'status' => 406,
                'data' => null,
            ]);
        }
        
        if (!isset($data['date'])) {
            return  response()->json([
                'message' => 'Nhập ngày thuê phòng',
                'status' => 406,
                'data' => null,
            ]);
        }

        if (!isset($data['hour_start'])) {
            return  response()->json([
                'message' => 'Nhập thời gian bắt đầu thuê phòng',
                'status' => 406,
                'data' => null,
            ]);
        }
        
        if (!isset($data['hour_end'])) {
            return  response()->json([
                'message' => 'Nhập thời gian kết thúc thuê phòng',
                'status' => 406,
                'data' => null,
            ]);
        }

        $data['rental_start'] = $data['date'] . ' ' . $data['hour_start'];
        $data['rental_end'] = $data['date'] . ' ' . $data['hour_end'];

        if ($data['rental_start'] >= $data['rental_end']) {
            return  response()->json([
                'message' => 'Thời gian kết thúc phải lớn hơn thời gian bắt đầu',
                'status' => 406,
                'data' => null,
            ]);
        }

        // thêm mới data
        if (isset($data['id'])) {
            $rentalDeleted = Rental::find($data['id']);
            
            if (is_null($rentalDeleted)) {
                return  response()->json([
                    'message' => 'Không tìm thấy dữ liệu !',
                    'status' => 200,
                    'data' => null,
                ]);
            }

            $rentalDeleted->delete();
            RentalService::where('rental_history_id', $data['id'])->delete();
            RentalEquipment::where('rental_history_id', $data['id'])->delete();
        
        }

        $checkRentaled1 = $this->checkAvaibleMeetingRoom1($data['rental_start'], $data['rental_end'], $data['meeting_room_id']);
        $checkRentaled2 = $this->checkAvaibleMeetingRoom2($data['rental_start'], $data['rental_end'], $data['meeting_room_id']);
        $checkRentaled3 = $this->checkAvaibleMeetingRoom3($data['rental_start'], $data['rental_end'], $data['meeting_room_id']);
        $checkRentaled = [];
        $checkRentaled[] = $checkRentaled1; 
        $checkRentaled[] = $checkRentaled2; 
        $checkRentaled[] = $checkRentaled3; 
        $checkRentaled = array_filter($checkRentaled);
        
        if(!empty($checkRentaled)) {
            return  response()->json([
                'message' => 'Phòng đã được thuê !',
                'status' => 200,
                'data' => null,
            ]);
        } 

        $dataRental = [
            'meeting_room_id' => $data['meeting_room_id'],
            'user_id' => $data['user_id'],
            'rental_start' => $data['rental_start'],
            'rental_end' => $data['rental_end'],
            'status' => 1,
            'price_meeting_room' => $detailMeetingRoom[1],
            'name_meeting_room' => $detailMeetingRoom[0],
            'path_img_meeting_room' => $detailMeetingRoom[2],
        ];

        $rentalServices = $data['rental_services'];
        $rentalEquipments = $data['rental_equipments'];

        $rental = Rental::create($dataRental);
        $totalMoneyService = $totalMoneyEquipment = 0;

        foreach ($rentalServices as $rentalService) {
            $detailService =  explode('-', $services[$rentalService['service_id']]);
            $rentalService['rental_history_id'] = $rental->id;
            $rentalService['price_service'] = $detailService[1];
            $rentalService['name_service'] =  $detailService[0];
            $rentalService['total_money']   = intval($rentalService['quantity'])*intval($rentalService['price_service']);
            $totalMoneyService += $rentalService['total_money'];
            RentalService::create($rentalService);
        }

        foreach ($rentalEquipments as $rentalEquipment) {
            $rentalEquipment['rental_history_id'] = $rental->id;
            $detailEquipment =  explode('-', $equipments[$rentalEquipment['equipment_id']]);
            $rentalEquipment['rental_history_id'] = $rental->id;
            $rentalEquipment['price_equipment'] = $detailEquipment[1];
            $rentalEquipment['name_equipment'] =  $detailEquipment[0];
            $rentalEquipment['total_money']   = intval($rentalEquipment['quantity'])*intval($rentalEquipment['price_equipment']);
            $totalMoneyEquipment += $rentalEquipment['total_money'];
            RentalEquipment::create($rentalEquipment);
        }

        $rentalHistory = Rental::find($rental->id);
        $rentalHistory->update([
            'total_money' => intval($detailMeetingRoom[1]) + $totalMoneyService + $totalMoneyEquipment,
        ]);

        return  response()->json([
            'message' => $this->success,
            'status' => 200,
        ]);


        
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $data = $request->all();
        $id = intval($data['id']);

        $rental = Rental::find($id);
        
        if (is_null($rental)) {
            return  response()->json([
                'status' => 200,
                'message' => $this->msgNoData,
                'data'=> [],
            ]);
        }

        $rental->delete();

        RentalService::where('rental_history_id', $id)->delete();
        RentalEquipment::where('rental_history_id', $id)->delete();

        return  response()->json([
            'status' => 200,
            'message' => $this->success,
            'data'=> [],
        ]);
    
    }

    public function getMettingRoomOfUser(Request $request)
    {
        $userId = $request->user_id;
        $data = Rental::query();
        $data->with('rentalServices', 'rentalEquipments');
        $data->where(function($q) use ($userId) {
            $q->where('user_id', 'LIKE', '%' . ',' . $userId . '%')
            ->orWhere('user_id', 'LIKE', '%' . $userId . ',' . '%');
        });
        $data = $data->get();
        if (count($data) < 1 ) {
            return  response()->json([
                'status'  => 200,
                'message' => $this->msgNoData,
                'data'    => [],
            ]);
        }

        return  response()->json([
            'status' => 200,
            'message' => $this->success,
            'data'=> $data,
        ]);
    }

    public function checkAvaibleMeetingRoom1($rental_start, $rental_end, $meeting_room_id) {
        
        $rental = Rental::where('meeting_room_id', $meeting_room_id)
            ->where('rental_start', '=', $rental_start)
            ->where('rental_end', '=', $rental_end)
            ->first();

        return $rental;    
    }
    
    public function checkAvaibleMeetingRoom2($rental_start, $rental_end, $meeting_room_id) {
        
        $rental = Rental::where('meeting_room_id', $meeting_room_id)
            ->where('rental_start', '<=', $rental_start)
            ->where('rental_end', '>', $rental_start)
            ->first();

        return $rental;    
    }
    
    public function checkAvaibleMeetingRoom3($rental_start, $rental_end, $meeting_room_id) {
        
        $rental = Rental::where('meeting_room_id', $meeting_room_id)
            ->where('rental_start', '>=', $rental_start)
            ->where('rental_start', '<', $rental_end)
            ->first();

        return $rental;    
    }
    
}
