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
use Illuminate\Support\Facades\Log;

class RoleController extends Controller{

    use HttpResponseTrait;
    private $roleService;

    public function __construct(){
        $this->roleService = RoleServiceImpl::getInstance();
    }

    public function createRole(Request $request){
        CreateRoleRequest::validate($request);
        $role = $this->roleService->createRole($request->all());
        return $this->success('Role created successfully', ['role' => $role], Response::HTTP_CREATED);
    }

    public function getAllRole(Request $request){
        $perPage = $request->input('per_page', 10);
        $searchBy = $request->input('searchBy', 'name');
        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'id');
        $sortDirection = $request->input('sort_direction', 'asc');
        $rolePaginated = $this->roleService->getRolesPaginated($perPage, $searchBy, $search, $sortBy, $sortDirection);
        return $this->success('Roles retrieved (with pagination) successfully', ['pagination' => $rolePaginated]);
    }

    public function getRole($id){
        try {
            $role = $this->roleService->getRoleById($id);
            return $this->success('Role retrieved successfully', ['role' => $role]);
        } catch (ModelNotFoundException $e) {
            Log::error($e->getMessage());
            return $this->error('Role not found', [], Response::HTTP_NOT_FOUND);
        }
    }

    public function updateRole(Request $request, $id){
        UpdateRoleRequest::validate($request, $id);
        $role = $this->roleService->updateRole($id, $request->all());
        return $this->success('Role updated successfully', ['role' => $role]);
    }

    public function deactivateRole($id){
        try {
            $role = $this->roleService->getRoleById($id);
            if(!$role->active){
                throw new RoleDeactivatedException();
            }

            $this->roleService->deactivateRole($id);
            return $this->success('Role deactivated successfully');
        } catch (ModelNotFoundException $e) {
            return $this->error('Role not found', [], Response::HTTP_NOT_FOUND);
        } catch (RoleDeactivatedException $e) {
            return $this->error('Role is deactivated', [], Response::HTTP_CONFLICT);
        }
    }

    public function activateRole($id){
        try {
            $role = $this->roleService->getRoleById($id);
            if($role->active){
                throw new RoleActivatedException();
            }

            $this->roleService->activateRole($id);
            
            return $this->success('Role activated successfully');
        } catch (ModelNotFoundException $e) {
            return $this->error('Role not found', [], Response::HTTP_NOT_FOUND);
        } catch (RoleActivatedException $e) {
            return $this->error('Role is activated', [], Response::HTTP_CONFLICT);
        }
    }
}
