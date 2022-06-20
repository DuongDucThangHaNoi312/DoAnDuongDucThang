<?php

use Illuminate\Database\Seeder;

use App\Commons\Pemission as Pemission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Pemission::sync();
    }
}
