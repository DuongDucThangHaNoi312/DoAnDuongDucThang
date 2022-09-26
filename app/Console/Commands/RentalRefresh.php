<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Rental;
use App\Models\RentalService;
use App\Models\RentalEquipment;

class RentalRefresh extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rentals:refresh';
    protected $msgNoData = "Không tìm thấy dữ liệu";
    protected $success = "Thành công";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $dateCurrent = date("Y-m-d H:i:s");
        $rentals = Rental::where('rental_end', '<',  $dateCurrent)->get();
        
        if (count($rentals) < 1) {
            return 1;
        }
        
        foreach ($rentals as $key => $rental) {
            $id = intval($rental->id);
            $rental->delete();
    
            RentalService::where('rental_history_id', $id)->delete();
            RentalEquipment::where('rental_history_id', $id)->delete();
        }
        
        return  response()->json([
            'status' => 200,
            'message' => $this->success,
        ]);
    }
}
