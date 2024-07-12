<?php

namespace App\Http\Controllers;

use App\Services\Impl\SubdivisionServiceImpl;
use App\Traits\HttpResponseTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SubdivisionController extends Controller{

    use HttpResponseTrait;
    private $subdivisionService;

    public function __construct(){
        $this->subdivisionService = SubdivisionServiceImpl::getInstance();
    }

    public function getAllSubdivision(Request $request){
        $perPage = $request->input('per_page', 10);
        $searchBy = $request->input('searchBy', 'name');
        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'id');
        $sortDirection = $request->input('sort_direction', 'asc');
        $subdivisionPaginated = $this->subdivisionService->getSubdivisionsPaginated($perPage, $searchBy, $search, $sortBy, $sortDirection);
        return $this->success(trans('subdivision.subdivisions_retrieved'), ['pagination' => $subdivisionPaginated]);
    }

    public function getSubdivision($id){
        try {
            $subdivision = $this->subdivisionService->getSubdivisionById($id);
            return $this->success(trans('subdivision.subdivision_retrieved'), ['subdivision' => $subdivision]);
        } catch (ModelNotFoundException $e) {
            return $this->error(trans('subdivision.subdivision_not_found'), [], Response::HTTP_NOT_FOUND);
        }
    }
}