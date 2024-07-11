<?php

namespace App\Http\Controllers;

use App\Services\Impl\AreaServiceImpl;
use App\Traits\HttpResponseTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AreaController extends Controller{

    use HttpResponseTrait;
    private $areaService;

    public function __construct(){
        $this->areaService = AreaServiceImpl::getInstance();
    }

    public function getAllArea(Request $request){
        $perPage = $request->input('per_page', 10);
        $searchBy = $request->input('searchBy', 'name');
        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'id');
        $sortDirection = $request->input('sort_direction', 'asc');
        $areaPaginated = $this->areaService->getAreasPaginated($perPage, $searchBy, $search, $sortBy, $sortDirection);
        return $this->success(trans('area.areas_retrieved'), ['pagination' => $areaPaginated]);
    }

    public function getArea($id){
        try {
            $area = $this->areaService->getAreaById($id);
            return $this->success(trans('area.area_retrieved'), ['area' => $area]);
        } catch (ModelNotFoundException $e) {
            return $this->error(trans('area.area_not_found'), [], Response::HTTP_NOT_FOUND);
        }
    }
}