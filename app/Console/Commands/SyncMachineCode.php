<?php

namespace App\Console\Commands;

use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SyncMachineCode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:machine_code';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Machine code';

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
        User::syncAttendanceMachine();
		$this->info('Successfully sync machine code.');
    }
}
