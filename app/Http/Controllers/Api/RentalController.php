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
        }
        $data = $data->get();
        // $data = $data->where('rental_start', '<=', $dateCurrent)
        //     ->where('renral_end', '>=', $dateCurrent)
        //     ->get();
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
        $data = $request->all();
       
        $equipments = Equipment::selectRaw("CONCAT(name, '-', price) as text, id")->pluck('text', 'id')->toArray();
        $services = Service::selectRaw("CONCAT(name, '-', price) as text, id")->pluck('text', 'id')->toArray();
        $meetingRooms = MeetingRoom::selectRaw("CONCAT(name, '-', price, '-', path_img) as text, id")->pluck('text', 'id')->toArray();

        $detailMeetingRoom =  explode('-', $meetingRooms[$data['meeting_room_id']]);


        $dataRental = [
            'meeting_room_id' => $data['meeting_room_id'],
            'user_id' => $data['user_id'],
            'rental_start' => $data['rental_start'],
            'renral_end' => $data['renral_end'],
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $data = $request->all();
        $data = $data['data']['0'];


        $dataRental = [
            'meeting_room_id' => $data['meeting_room_id'],
            'user_id' => $data['user_id'],
            'rental_start' => $data['rental_start'],
            'renral_end' => $data['renral_end'],
            'status' => 1,
        ];

        $rental = Rental::create($dataRental);
        $rentalServices = $data['rental_services'];
        $rentalEquipments = $data['rental_equipments'];



        foreach ($rentalServices as $rentalService) {
            $rentalService['rental_history_id'] = $rental->id;
            RentalService::create($rentalService);
        }

        foreach ($rentalEquipments as $rentalEquipment) {
            $rentalEquipment['rental_history_id'] = $rental->id;
            RentalEquipment::create($rentalEquipment);
        }

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

        RentalService::where('rental_history_id', 20)->delete();
        RentalEquipment::where('rental_history_id', 20)->delete();

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
}
