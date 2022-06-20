<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LaratrustSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return  void
     */
    public function run()
    {
        $this->truncateLaratrustTables();

        $config = config('laratrust_seeder.role_structure');
        $mapPermission = collect(config('laratrust_seeder.permissions_map'));

        foreach ($config as $key => $modules) {
            // Create a new role
            $role = \App\Role::create([
                'name' => $key,
                'display_name' => ucfirst($key),
                'description' => ucfirst($key)
            ]);

            $this->command->info('Creating Role '. strtoupper($key));

            // Reading role permission modules
            foreach ($modules as $module => $value) {
                $permissions = explode(',', $value);

                foreach ($permissions as $p => $perm) {
                    $permissionValue = $mapPermission->get($perm);

                    $permission = \App\Permission::firstOrCreate([
                        'name'          => $module . '.' . $permissionValue,
                        'display_name'  => (\Lang::has('system.action.' . $permissionValue) ? trans('system.action.' . $permissionValue) : ucfirst($permissionValue)) . ' ' . (\Lang::has($module . '.label') ? trans($module . '.label') : ucfirst($module)),
                        'description'   => ucfirst($permissionValue) . ' ' . ucfirst($module),
                        'action'        => $permissionValue,
                    ]);

                    $this->command->info('Creating Permission to '.$permissionValue.' for '. $module);

                    if (!$role->hasPermission($permission->name)) {
                        $role->attachPermission($permission);
                    } else {
                        $this->command->info($key . ': ' . $p . ' ' . $permissionValue . ' already exist');
                    }
                }
            }

            // Create default user for each role
////            $user = \App\User::create([
//                'fullname'  => ucfirst($key),
//                'email'     => $key . '@' . env('APP_NAME', 'bctech.vn'),
//                'password'  => bcrypt('123@123'),
//                'activated' => 1,
//                'remember_token'    => str_random(10),
//            ]);
//            $user->attachRole($role);
        }
    }

    /**
     * Truncates all the laratrust tables and the users table
     * @return    void
     */
    public function truncateLaratrustTables()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        DB::table('permission_role')->truncate();
        DB::table('role_user')->truncate();
//        \App\User::truncate();
        \App\Role::truncate();
        \App\Permission::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
