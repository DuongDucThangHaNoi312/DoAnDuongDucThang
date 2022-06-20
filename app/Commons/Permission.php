<?php

namespace App\Commons;

use App\Role;
use App\Permission;

class Pemission
{
    public static function sync()
    {

        $routes = \Route::getRoutes();
        $permissions = [];
        foreach($routes as $route)
        {
            $name = $route->getName();
            if (substr($name, 0, 5) == 'admin' && count(explode('.', $name)) > 2) {
                $action = $route->getAction();
                if (isset($action['role'])) {
                    $role = $action['role'];
                    $role = substr($role, 6, strlen($role));
                    $oneDot = explode('.', $role);
                    if(isset($oneDot[1])) {
                        $permissions[ str_replace('-', '_', substr($role, 0, strlen($role) - strlen(end($oneDot)) - 1)) ][] = end($oneDot);
                    }
                } else {
                    $routeName = substr($name, 6, strlen($name));
                    $oneDot = explode('.', $routeName);
                    if(isset($oneDot[1])) {
                        $permissions[ str_replace('-', '_', substr($routeName, 0, strlen($routeName) - strlen(end($oneDot)) - 1)) ][] = end($oneDot);
                    }
                }
            }
        }

        foreach ($permissions as $key => $value) {
            foreach ($value as $k => $v) {
                if ($v == 'view' || $v == 'index' || $v == 'show' )
                    $value[$k] = 'read';
                if ($v == 'delete' || $v == 'destroy' )
                    $value[$k] = 'delete';
                if ($v == 'create' || $v == 'store' )
                    $value[$k] = 'create';
                if ($v == 'edit' || $v == 'update' )
                    $value[$k] = 'update';
            }
            $permissions[$key] = array_unique(($value) );
        }

        foreach ($permissions as $key => $value) {
            foreach ($value as $v) {
                if (is_null(Permission::where('name', $key . '.' . $v)->first())) {
                    $permission = Permission::create([
                        'name'          => $key . '.' . $v,
                        'display_name'  => (\Lang::has('system.action.' . $v) ? trans('system.action.' . $v) : ucfirst($v)) . ' ' . (\Lang::has($key . '.label') ? trans($key . '.label') : ucfirst($key)),
                        'description'   => ucfirst($v) . ' ' . ucfirst($key),
                        'module'        => $key,
                        'action'        => $v
                    ]);
                    Role::first()->attachPermission($permission);
                }
            }
        }
    }
}