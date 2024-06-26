<?php

namespace App\Http\Controllers;

use App\Services\Impl\RoleServiceImpl;
use Illuminate\Http\Request;


class RoleController extends Controller{

    private $roleService;

    public function __construct(){
        $this->roleService = RoleServiceImpl::getInstance();
    }

    public function createRole(Request $request){
        $this->validate($request, [
            'name' => 'required|Max:100',
        ]);

        $this->roleService->createRole($request->name);
        return response('', 201);
    }

    public function addRoleToProjectTeam(Request $request){
       
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users'
        ]);
    }
}
