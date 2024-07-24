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

    /**
     * @OA\Post(
     *     path="/api/external-user-types",
     *     summary="Crea un tipo de usuario externo",
     *     tags={"tipos-usuarios-externos"},
     *     operationId="external-user-types-create",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Otro"),
     *         )
     *     ),
     *     @OA\Response(
     *          response="201",
     *          description="Tipo de usuario externo creado correctamente",
     *          @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="role", type="object", ref="#/components/schemas/ExternalUserType"),
     *                 @OA\Property(property="message", type="string", example="Tipo de usuario externo creado correctamente"),
     *                 @OA\Property(property="code", type="integer", example=201)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error en los campos",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="errors", type="object"),
     *                 @OA\Property(property="message", type="string", example="Error en los campos"),
     *                 @OA\Property(property="code", type="integer", example=400)
     *             )
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
    public function createExternalUserType(Request $request){
        CreateExternalUserTypeRequest::validate($request);
        $externalUserType = $this->externalUserTypeService->createExternalUserType($request->all());
        return $this->success(trans('external_user_type.external_user_type_created'), ['externalUserType' => $externalUserType], Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/api/external-user-types",
     *     tags={"tipos-usuarios-externos"},
     *     summary="Obtiene una lista paginada de tipos de usuarios externos",
     *     operationId="external-user-types-all-retrieved",
     *     @OA\Parameter(
     *         name="perPage",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer", example=10),
     *         description="Número de tipos de usuarios externos por página."
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
     *         @OA\Schema(type="string", example="Proveedor"),
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
     *         description="Tipos de usuarios externos recuperados (con paginación) correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="pagination",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/ExternalUserType")),
     *                 @OA\Property(property="first_page_url", type="string", example="http://example.com/api/external-user-types?page=1"),
     *                 @OA\Property(property="from", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=10),
     *                 @OA\Property(property="last_page_url", type="string", example="http://example.com/api/external-user-types?page=10"),
     *                 @OA\Property(property="link", type="array",
     *                      @OA\Items(
     *                     type="object",
     *                     @OA\Property(
     *                         property="url",
     *                         type="string",
     *                         format="uri",
     *                         example="http://example.com/api/external-user-types?page=1",
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
     *                 @OA\Property(property="next_page_url", type="string", example="http://example.com/api/external-user-types?page=2"),
     *                 @OA\Property(property="path", type="string", example="http://example.com/api/external-user-types"),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="prev_page_url", type="string", example=null),
     *                 @OA\Property(property="to", type="integer", example=10),
     *                 @OA\Property(property="total", type="integer", example=100)
     *             ),
     *             @OA\Property(property="message", type="string", example="Tipos de usuarios externos recuperados (con paginación) correctamente"),
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
    public function getAllExternalUserType(Request $request){
        $perPage = $request->input('perPage', 10);
        $searchBy = $request->input('searchBy', 'name');
        $search = $request->input('search');
        $sortBy = $request->input('sortBy', 'id');
        $sortDirection = $request->input('sortDirection', 'asc');
        $externalUserTypePaginated = $this->externalUserTypeService->getExternalUserTypesPaginated($perPage, $searchBy, $search, $sortBy, $sortDirection);
        return $this->success(trans('external_user_type.external_user_types_retrieved'), ['pagination' => $externalUserTypePaginated]);
    }

    /**
     * @OA\Get(
     *     path="/api/external-user-types/{id}",
     *     summary="Obtiene un tipo de usuario externo",
     *     tags={"tipos-usuarios-externos"},
     *     operationId="external-user-types-retrieved",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="Tipo de usuario externo recuperado correctamente",
     *          @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="externalUserType", type="object", ref="#/components/schemas/ExternalUserType"),
     *             ),
     *             @OA\Property(property="message", type="string", example="Tipo de usuario externo recuperado correctamente"),
     *             @OA\Property(property="code", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *          response="404",
     *          description="Tipo de usuario externo no encontrado",
     *          @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="Tipo de usuario externo no encontrado"),
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
    public function getExternalUserType($id){
        try {
            $externalUserType = $this->externalUserTypeService->getExternalUserTypeById($id);
            return $this->success(trans('external_user_type.external_user_type_retrieved'), ['externalUserType' => $externalUserType]);
        } catch (ModelNotFoundException $e) {
            Log::error($e->getMessage());
            return $this->error(trans('external_user_type.external_user_type_not_found'), [], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/external-user-types/{id}",
     *     summary="Actualiza un tipo de usuario externo",
     *     tags={"tipos-usuarios-externos"},
     *     operationId="external-user-types-update",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Proveedor"),
     *         )
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="Tipo de usuario externo actualizado correctamente",
     *          @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="externalUserType", type="object", ref="#/components/schemas/ExternalUserType"),
     *             ),
     *             @OA\Property(property="message", type="string", example="Tipo de usuario externo actualizado correctamente"),
     *             @OA\Property(property="code", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *          response="404",
     *          description="Tipo de usuario externo no encontrado",
     *          @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="Tipo de usuario externo no encontrado"),
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
    public function updateExternalUserType(Request $request, $id){
        try {
            UpdateExternalUserTypeRequest::validate($request, $id);
            $externalUserType = $this->externalUserTypeService->updateExternalUserType($id, $request->all());
            return $this->success(trans('external_user_type.external_user_type_updated'), ['externalUserType' => $externalUserType]);
        } catch (ModelNotFoundException $e) {
            return $this->error(trans('external_user_type.external_user_type_not_found'), [], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/external-user-types/{id}",
     *     summary="Desactiva un tipo de usuario externo",
     *     tags={"tipos-usuarios-externos"},
     *     operationId="external-user-types-deactivate",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="Tipo de usuario externo desactivado correctamente",
     *          @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="Tipo de usuario externo desactivado correctamente"),
     *             @OA\Property(property="code", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *          response="404",
     *          description="Tipo de usuario externo no encontrado",
     *          @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="Tipo de usuario externo no encontrado"),
     *             @OA\Property(property="code", type="integer", example=404)
     *         )
     *     ),
     *     @OA\Response(
     *          response="409",
     *          description="El tipo de usuario externo ya está desactivado",
     *          @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="El tipo de usuario externo ya está desactivado"),
     *             @OA\Property(property="code", type="integer", example=409)
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
    public function deactivateExternalUserType($id){
        try {
            $externalUserType = $this->externalUserTypeService->getExternalUserTypeById($id);
            if(!$externalUserType->active){
                throw new ExternalUserTypeDeactivatedException();
            }

            $this->externalUserTypeService->deactivateExternalUserType($id);
            return $this->success(trans('external_user_type.external_user_type_deactivated'));
        } catch (ModelNotFoundException $e) {
            return $this->error(trans('external_user_type.external_user_type_not_found'), [], Response::HTTP_NOT_FOUND);
        } catch (ExternalUserTypeDeactivatedException $e) {
            return $this->error(trans('external_user_type.external_user_type_already_deactivated'), [], Response::HTTP_CONFLICT);
        }
    }

    /**
     * @OA\Patch(
     *     path="/api/external-user-types/{id}",
     *     summary="Activa un tipo de usuario externo",
     *     tags={"tipos-usuarios-externos"},
     *     operationId="external-user-types-activate",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="Tipo de usuario externo activado correctamente",
     *          @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="Tipo de usuario externo activado correctamente"),
     *             @OA\Property(property="code", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *          response="404",
     *          description="Tipo de usuario externo no encontrado",
     *          @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="Tipo de usuario externo no encontrado"),
     *             @OA\Property(property="code", type="integer", example=404)
     *         )
     *     ),
     *     @OA\Response(
     *          response="409",
     *          description="El tipo de usuario externo ya está desactivado",
     *          @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="El tipo de usuario externo ya está desactivado"),
     *             @OA\Property(property="code", type="integer", example=409)
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
    public function activateExternalUserType($id){
        try {
            $externalUserType = $this->externalUserTypeService->getExternalUserTypeById($id);
            if($externalUserType->active){
                throw new ExternalUserTypeActivatedException();
            }

            $this->externalUserTypeService->activateExternalUserType($id);

            return $this->success(trans('external_user_type.external_user_type_activated'));
        } catch (ModelNotFoundException $e) {
            return $this->error(trans('external_user_type.external_user_type_not_found'), [], Response::HTTP_NOT_FOUND);
        } catch (ExternalUserTypeActivatedException $e) {
            return $this->error(trans('external_user_type.external_user_type_already_activated'), [], Response::HTTP_CONFLICT);
        }
    }
}