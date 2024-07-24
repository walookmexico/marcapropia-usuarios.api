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
/**
     * @OA\Get(
     *     path="/api/areas",
     *     tags={"areas"},
     *     summary="Obtiene una lista paginada de áreas",
     *     operationId="areas-all-retrieved",
     *     @OA\Parameter(
     *         name="perPage",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer", example=10),
     *         description="Número de áreas por página."
     *     ),
     *     @OA\Parameter(
     *         name="searchBy",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", example="name"),
     *         description="Campo por el que se realizará la búsqueda (por ejemplo, 'name' o 'id')."
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", example="Marketing"),
     *         description="Texto de búsqueda."
     *     ),
     *     @OA\Parameter(
     *         name="sortBy",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", example="name"),
     *         description="Campo por el que se realizará la ordenación (por ejemplo, 'name' o 'id')."
     *     ),
     *     @OA\Parameter(
     *         name="sortDirection",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", example="asc"),
     *         description="Dirección de la ordenación, puede ser 'asc' o 'desc'."
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Áreas recuperadas (con paginación) correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="pagination",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Area")),
     *                 @OA\Property(property="first_page_url", type="string", example="http://example.com/api/areas?page=1"),
     *                 @OA\Property(property="from", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=10),
     *                 @OA\Property(property="last_page_url", type="string", example="http://example.com/api/areas?page=10"),
     *                 @OA\Property(property="link", type="array",
     *                      @OA\Items(
     *                     type="object",
     *                     @OA\Property(
     *                         property="url",
     *                         type="string",
     *                         format="uri",
     *                         example="http://example.com/api/areas?page=1",
     *                         description="URL for the link"
     *                     ),
     *                     @OA\Property(
     *                         property="label",
     *                         type="string",
     *                         example="1",
     *                         description="Label for the link, e.g., page number or a special label"
     *                     ),
     *                     @OA\Property(
     *                         property="active",
     *                         type="boolean",
     *                         example=true,
     *                         description="Whether the link is active for the current page"
     *                     )
     *                 ),
     *                 description="Pagination links including previous, next, and page numbers"
     *              ),
     *                 @OA\Property(property="next_page_url", type="string", example="http://example.com/api/areas?page=2"),
     *                 @OA\Property(property="path", type="string", example="http://example.com/api/areas"),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="prev_page_url", type="string", example=null),
     *                 @OA\Property(property="to", type="integer", example=10),
     *                 @OA\Property(property="total", type="integer", example=100)
     *             ),
     *             @OA\Property(property="message", type="string", example="Áreas recuperadas (con paginación) correctamente"),
     *             @OA\Property(property="code", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *          response="500",
     *          description="Ocurrió un error inesperado en el servidor. Por favor, inténtelo de nuevo más tarde o contacte con soporte si el problema persiste",
     *          @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="Ocurrió un error inesperado en el servidor. Por favor, inténtelo de nuevo más tarde o contacte con soporte si el problema persiste"),
     *             @OA\Property(property="code", type="integer", example=500)
     *         )
     *     )
     * )
     */
    public function getAllArea(Request $request){
        $perPage = $request->input('perPage', 10);
        $searchBy = $request->input('searchBy', 'name');
        $search = $request->input('search');
        $sortBy = $request->input('sortBy', 'id');
        $sortDirection = $request->input('sortDirection', 'asc');
        $areaPaginated = $this->areaService->getAreasPaginated($perPage, $searchBy, $search, $sortBy, $sortDirection);
        return $this->success(trans('area.areas_retrieved'), ['pagination' => $areaPaginated]);
    }

    /**
     * @OA\Get(
     *     path="/api/areas/{id}",
     *     summary="Obtiene un área",
     *     tags={"areas"},
     *     operationId="areas-retrieved",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="Área recuperada correctamente",
     *          @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="area", type="object", ref="#/components/schemas/Area"),
     *             ),
     *             @OA\Property(property="message", type="string", example="Área recuperada correctamente"),
     *             @OA\Property(property="code", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *          response="404",
     *          description="Área no encontrada",
     *          @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="Área no encontrada"),
     *             @OA\Property(property="code", type="integer", example=404)
     *         )
     *     ),
     *     @OA\Response(
     *          response="500",
     *          description="Ocurrió un error inesperado en el servidor. Por favor, inténtelo de nuevo más tarde o contacte con soporte si el problema persiste",
     *          @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="Ocurrió un error inesperado en el servidor. Por favor, inténtelo de nuevo más tarde o contacte con soporte si el problema persiste"),
     *             @OA\Property(property="code", type="integer", example=500)
     *         )
     *     )
     * )
     */
    public function getArea($id){
        try {
            $area = $this->areaService->getAreaById($id);
            return $this->success(trans('area.area_retrieved'), ['area' => $area]);
        } catch (ModelNotFoundException $e) {
            return $this->error(trans('area.area_not_found'), [], Response::HTTP_NOT_FOUND);
        }
    }
}