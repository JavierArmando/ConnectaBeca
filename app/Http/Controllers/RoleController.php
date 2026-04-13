<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;


class RoleController extends Controller
{
    public function index() 
    {
        $roles = Role::all();
    }
    public function create() 
    {
        $role = new Role();
        $role->name = 'Admin';
        $role->save();
    }
    public function update() 
    {
        $role = Role::find(1); //find solo funciona con id´s
        //$role = Role::where("name","Admin")->first();
        $role->name = 'Administrador';
        $role->save();
    }
    public function delete() 
    {
        $role = Role::find(1)->delete();
    }
}