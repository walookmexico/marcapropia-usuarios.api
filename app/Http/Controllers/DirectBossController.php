<?php

namespace App\Http\Controllers;

use App\Services\Impl\UserServiceImpl;
use App\Traits\HttpResponseTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DirectBossController extends Controller{

    use HttpResponseTrait;
    private $userService;

    public function __construct(){
        $this->userService = UserServiceImpl::getInstance();
    }
/**
     * @OA\Get(
     *     path="/api/users/direct-bosses",
     *     tags={"usuarios"},
     *     summary="Obtiene una lista paginada de jefes directos",
     *     operationId="direct-boss-all-retrieved",
     *     @OA\Parameter(
     *         name="perPage",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer", example=10),
     *         description="Número de jefes directos por página."
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
     *     @OA\Parameter(
     *         name="active",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", example="true"),
     *         description="Indica si el registro esta activo o no, puede ser 'true' o 'false'."
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Jefes directos recuperadas (con paginación) correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="pagination",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/DirectBoss")),
     *                 @OA\Property(property="first_page_url", type="string", example="http://example.com/api/users/direct-bosses?page=1"),
     *                 @OA\Property(property="from", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=10),
     *                 @OA\Property(property="last_page_url", type="string", example="http://example.com/api/users/direct-bosses?page=10"),
     *                 @OA\Property(property="link", type="array",
     *                     @OA\Items(
     *                     type="object",
     *                     @OA\Property(
     *                         property="url",
     *                         type="string",
     *                         format="uri",
     *                         example="http://example.com/api/users/direct-bosses?page=1",
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
     *                 @OA\Property(property="next_page_url", type="string", example="http://example.com/api/users/direct-bosses?page=2"),
     *                 @OA\Property(property="path", type="string", example="http://example.com/api/users/direct-bosses"),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="prev_page_url", type="string", example=null),
     *                 @OA\Property(property="to", type="integer", example=10),
     *                 @OA\Property(property="total", type="integer", example=100)
     *             ),
     *             @OA\Property(property="message", type="string", example="Jefes directos recuperadas (con paginación) correctamente"),
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
    public function getAllDirectBoss(Request $request){
        $perPage = $request->input('perPage', 10);
        $searchBy = $request->input('searchBy', 'name');
        $search = $request->input('search');
        $sortBy = $request->input('sortBy', 'id');
        $sortDirection = $request->input('sortDirection', 'asc');
        $active = $request->input('active', true);
        $directBosses = $this->userService->getDirectBossesPaginated($perPage, $searchBy, $search, $sortBy, $sortDirection, $active);
        return $this->success(trans('user.direct_bosses_retrieved'), ['pagination' => $directBosses]);
    }
}