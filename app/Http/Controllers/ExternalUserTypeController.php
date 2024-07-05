<?php

namespace App\Http\Controllers;

use App\Exceptions\ExternalUserTypeActivatedException;
use App\Exceptions\ExternalUserTypeDeactivatedException;
use App\Http\Requests\CreateExternalUserTypeRequest;
use App\Http\Requests\UpdateExternalUserTypeRequest;
use App\Services\Impl\ExternalUserTypeServiceImpl;
use App\Traits\HttpResponseTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class ExternalUserTypeController extends Controller{

    use HttpResponseTrait;
    private $externalUserTypeService;

    public function __construct(){
        $this->externalUserTypeService = ExternalUserTypeServiceImpl::getInstance();
    }

    public function createExternalUserType(Request $request){
        CreateExternalUserTypeRequest::validate($request);
        $externalUserType = $this->externalUserTypeService->createExternalUserType($request->all());
        return $this->success('External user type created successfully', ['externalUserType' => $externalUserType], Response::HTTP_CREATED);
    }

    public function getAllExternalUserType(Request $request){
        $perPage = $request->input('per_page', 10);
        $searchBy = $request->input('searchBy', 'name');
        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'id');
        $sortDirection = $request->input('sort_direction', 'asc');
        $externalUserTypePaginated = $this->externalUserTypeService->getExternalUserTypesPaginated($perPage, $searchBy, $search, $sortBy, $sortDirection);
        return $this->success('External user types retrieved (with pagination) successfully', ['pagination' => $externalUserTypePaginated]);
    }

    public function getExternalUserType($id){
        try {
            $externalUserType = $this->externalUserTypeService->getExternalUserTypeById($id);
            return $this->success('External user type retrieved successfully', ['externalUserType' => $externalUserType]);
        } catch (ModelNotFoundException $e) {
            Log::error($e->getMessage());
            return $this->error('External user type not found', [], Response::HTTP_NOT_FOUND);
        }
    }

    public function updateExternalUserType(Request $request, $id){
        try {
            UpdateExternalUserTypeRequest::validate($request, $id);
            $externalUserType = $this->externalUserTypeService->updateExternalUserType($id, $request->all());
            return $this->success('External user type updated successfully', ['externalUserType' => $externalUserType]);
        } catch (ModelNotFoundException $e) {
            return $this->error('External user type not found', [], Response::HTTP_NOT_FOUND);
        }
    }

    public function deactivateExternalUserType($id){
        try {
            $externalUserType = $this->externalUserTypeService->getExternalUserTypeById($id);
            if(!$externalUserType->active){
                throw new ExternalUserTypeDeactivatedException();
            }

            $this->externalUserTypeService->deactivateExternalUserType($id);
            return $this->success('External user type deactivated successfully');
        } catch (ModelNotFoundException $e) {
            return $this->error('External user type not found', [], Response::HTTP_NOT_FOUND);
        } catch (ExternalUserTypeDeactivatedException $e) {
            return $this->error('External user type is deactivated', [], Response::HTTP_CONFLICT);
        }
    }

    public function activateExternalUserType($id){
        try {
            $externalUserType = $this->externalUserTypeService->getExternalUserTypeById($id);
            if($externalUserType->active){
                throw new ExternalUserTypeActivatedException();
            }

            $this->externalUserTypeService->activateExternalUserType($id);
            
            return $this->success('External user type activated successfully');
        } catch (ModelNotFoundException $e) {
            return $this->error('External user type not found', [], Response::HTTP_NOT_FOUND);
        } catch (ExternalUserTypeActivatedException $e) {
            return $this->error('External user type is activated', [], Response::HTTP_CONFLICT);
        }
    }
}
