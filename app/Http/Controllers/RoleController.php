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

     /**
     * @OA\Post(
     *     path="/api/roles",
     *     summary="Crea un rol",
     *     tags={"roles"},
     *     operationId="roles-create",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Director Marca Propia"),
     *             @OA\Property(property="description", type="string", example="Director Marca Propia")
     *         )
     *     ),
     *     @OA\Response(
     *          response="201",
     *          description="Rol creado correctamente",
     *          @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="role", type="object", ref="#/components/schemas/Role"),
     *                 @OA\Property(property="message", type="string", example="Rol creado correctamente"),
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
    public function createRole(Request $request){
        CreateRoleRequest::validate($request);
        $role = $this->roleService->createRole($request->all());
        return $this->success(trans('role.role_created'), ['role' => $role], Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/api/roles",
     *     tags={"roles"},
     *     summary="Obtiene una lista paginada de roles",
     *     operationId="roles-all-retrieved",
     *     @OA\Parameter(
     *         name="perPage",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer", example=10),
     *         description="Número de roles por página."
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
     *         @OA\Schema(type="string", example="Admin"),
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
     *         description="Roles recuperados (con paginación) correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="pagination",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Role")),
     *                 @OA\Property(property="first_page_url", type="string", example="http://example.com/api/roles?page=1"),
     *                 @OA\Property(property="from", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=10),
     *                 @OA\Property(property="last_page_url", type="string", example="http://example.com/api/roles?page=10"),
     *                 @OA\Property(property="link", type="array",
     *                      @OA\Items(
     *                     type="object",
     *                     @OA\Property(
     *                         property="url",
     *                         type="string",
     *                         format="uri",
     *                         example="http://example.com/api/roles?page=1",
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
     *                 @OA\Property(property="next_page_url", type="string", example="http://example.com/api/roles?page=2"),
     *                 @OA\Property(property="path", type="string", example="http://example.com/api/roles"),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="prev_page_url", type="string", example=null),
     *                 @OA\Property(property="to", type="integer", example=10),
     *                 @OA\Property(property="total", type="integer", example=100)
     *             ),
     *             @OA\Property(property="message", type="string", example="Roles recuperados (con paginación) correctamente"),
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
    public function getAllRole(Request $request){
        $perPage = $request->input('perPage', 10);
        $searchBy = $request->input('searchBy', 'name');
        $search = $request->input('search');
        $sortBy = $request->input('sortBy', 'id');
        $sortDirection = $request->input('sortDirection', 'asc');
        $rolePaginated = $this->roleService->getRolesPaginated($perPage, $searchBy, $search, $sortBy, $sortDirection);
        return $this->success(trans('role.roles_retrieved'), ['pagination' => $rolePaginated]);
    }

     /**
     * @OA\Get(
     *     path="/api/roles/{id}",
     *     summary="Obtiene un rol",
     *     tags={"roles"},
     *     operationId="roles-retrieved",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="Rol recuperado correctamente",
     *          @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="role", type="object", ref="#/components/schemas/Role"),
     *             ),
     *             @OA\Property(property="message", type="string", example="Rol recuperado correctamente"),
     *             @OA\Property(property="code", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *          response="404",
     *          description="Rol no encontrado",
     *          @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="Rol no encontrado"),
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
    public function getRole($id){
        try {
            $role = $this->roleService->getRoleById($id);
            return $this->success(trans('role.role_retrieved'), ['role' => $role]);
        } catch (ModelNotFoundException $e) {
            return $this->error(trans('role.role_not_found'), [], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/roles/{id}",
     *     summary="Actualiza un rol",
     *     tags={"roles"},
     *     operationId="roles-update",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Director Marca Propia"),
     *             @OA\Property(property="description", type="string", example="Director Marca Propia")
     *         )
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="Rol actualizado correctamente",
     *          @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="role", type="object", ref="#/components/schemas/Role"),
     *             ),
     *             @OA\Property(property="message", type="string", example="Rol actualizado correctamente"),
     *             @OA\Property(property="code", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *          response="404",
     *          description="Rol no encontrado",
     *          @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="Rol no encontrado"),
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
    public function updateRole(Request $request, $id){
        try {
            UpdateRoleRequest::validate($request, $id);
            $role = $this->roleService->updateRole($id, $request->all());
            return $this->success(trans('role.role_updated'), ['role' => $role]);
        } catch (ModelNotFoundException $e) {
            return $this->error(trans('role.role_not_found'), [], Response::HTTP_NOT_FOUND);
        }
    }

/**
     * @OA\Delete(
     *     path="/api/roles/{id}",
     *     summary="Desactiva un rol",
     *     tags={"roles"},
     *     operationId="roles-deactivate",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="Rol desactivado correctamente",
     *          @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="Rol desactivado correctamente"),
     *             @OA\Property(property="code", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *          response="404",
     *          description="Rol no encontrado",
     *          @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="Rol no encontrado"),
     *             @OA\Property(property="code", type="integer", example=404)
     *         )
     *     ),
     *     @OA\Response(
     *          response="409",
     *          description="El rol ya está desactivado",
     *          @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="El rol ya está desactivado"),
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

    /**
     * @OA\Patch(
     *     path="/api/roles/{id}",
     *     summary="Activa un rol",
     *     tags={"roles"},
     *     operationId="roles-activate",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="Rol activado correctamente",
     *          @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="Rol activado correctamente"),
     *             @OA\Property(property="code", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *          response="404",
     *          description="Rol no encontrado",
     *          @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="Rol no encontrado"),
     *             @OA\Property(property="code", type="integer", example=404)
     *         )
     *     ),
     *     @OA\Response(
     *          response="409",
     *          description="El rol ya está activado",
     *          @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="El rol ya está activado"),
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