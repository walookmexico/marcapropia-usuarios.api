<?php

namespace App\Services\Impl;

use App\Services\Impl\AbstractBaseService;
use App\Services\RoleServiceInterface;
use Spatie\Permission\Models\Role;

class RoleServiceImpl extends AbstractBaseService implements RoleServiceInterface{

    /**
     *
     * @param mixed $name
     */
    public function createRole($name) {
       return Role::create(["name"=> $name]);
    }

    private function isRoleExists($roleName){
        return Count(Role::findByName($roleName)->get()) > 0;
    }
}