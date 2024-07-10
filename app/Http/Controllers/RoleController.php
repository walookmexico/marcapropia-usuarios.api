<?php

namespace App\Http\Controllers;

use App\Exceptions\RoleActivatedException;
use App\Exceptions\RoleDeactivatedException;
use App\Http\Requests\CreateRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Services\Impl\RoleServiceImpl;
use App\Traits\HttpResponseTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RoleController extends Controller{

    use HttpResponseTrait;
    private $roleService;

    public function __construct(){
        $this->roleService = RoleServiceImpl::getInstance();
    }

    public function createRole(Request $request){
        CreateRoleRequest::validate($request);
        $role = $this->roleService->createRole($request->all());
        return $this->success(trans('role.role_created'), ['role' => $role], Response::HTTP_CREATED);
    }

    public function getAllRole(Request $request){
        $perPage = $request->input('per_page', 10);
        $searchBy = $request->input('searchBy', 'name');
        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'id');
        $sortDirection = $request->input('sort_direction', 'asc');
        $rolePaginated = $this->roleService->getRolesPaginated($perPage, $searchBy, $search, $sortBy, $sortDirection);
        return $this->success(trans('role.roles_retrieved'), ['pagination' => $rolePaginated]);
    }

    public function getRole($id){
        try {
            $role = $this->roleService->getRoleById($id);
            return $this->success(trans('role.role_retrieved'), ['role' => $role]);
        } catch (ModelNotFoundException $e) {
            return $this->error(trans('role.role_not_found'), [], Response::HTTP_NOT_FOUND);
        }
    }

    public function updateRole(Request $request, $id){
        try {
            UpdateRoleRequest::validate($request, $id);
            $role = $this->roleService->updateRole($id, $request->all());
            return $this->success(trans('role.role_updated'), ['role' => $role]);
        } catch (ModelNotFoundException $e) {
            return $this->error(trans('role.role_not_found'), [], Response::HTTP_NOT_FOUND);
        }
    }

    public function deactivateRole($id){
        try {
            $role = $this->roleService->getRoleById($id);
            if(!$role->active){
                throw new RoleDeactivatedException();
            }

            $this->roleService->deactivateRole($id);
            return $this->success(trans('role.role_deactivated'));
        } catch (ModelNotFoundException $e) {
            return $this->error(trans('role.role_not_found'), [], Response::HTTP_NOT_FOUND);
        } catch (RoleDeactivatedException $e) {
            return $this->error(trans('role.role_already_deactivated'), [], Response::HTTP_CONFLICT);
        }
    }

    public function activateRole($id){
        try {
            $role = $this->roleService->getRoleById($id);
            if($role->active){
                throw new RoleActivatedException();
            }

            $this->roleService->activateRole($id);
            
            return $this->success(trans('role.role_activated'));
        } catch (ModelNotFoundException $e) {
            return $this->error(trans('role.role_not_found'), [], Response::HTTP_NOT_FOUND);
        } catch (RoleActivatedException $e) {
            return $this->error(trans('role.role_already_activated'), [], Response::HTTP_CONFLICT);
        }
    }
}