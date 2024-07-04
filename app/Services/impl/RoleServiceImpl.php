<?php

namespace App\Services\Impl;

use App\Models\Role;
use App\Services\Impl\AbstractBaseService;
use App\Services\RoleServiceInterface;
use App\Utils\UserConstants;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RoleServiceImpl extends AbstractBaseService implements RoleServiceInterface{


    public function getRoleById(int $id): Role {
        return Role::withTrashed()->findOrFail($id);
    }

    public function createRole(array $data): Role {
        $name = $data["name"];
        $description = $data["description"];

        return Role::create([
            'name' => $name,
            'description' => $description,
        ]);
    }

    public function updateRole(int $id, array $data): Role {
        $role = $this->getRoleById($id);
        
        $name = $data["name"];
        $description = $data["description"];

        $role->update([
            'name' => $name,
            'description' => $description,
        ]);
        return $role;
    }

    public function deactivateRole(int $id): void {

        try {
            DB::beginTransaction();

            $role = $this->getRoleById($id);
            $role->active = UserConstants::INACTIVE_STATUS;
            $role->save();
            $role->delete();

            DB::commit();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            throw $e;
        }
    }

    public function activateRole(int $id): void {
        try {
            DB::beginTransaction();

            $role = Role::withTrashed()->findOrFail($id);
            $role->active = UserConstants::ACTIVE_STATUS;
            $role->save();
            $role->restore();
           
            DB::commit();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            throw $e;
        }
    }

    public function getRolesPaginated(int $perPage = 10, string $searchBy = null, string $search = null, 
        string $sortBy = 'name', string $sortDirection = 'asc') : LengthAwarePaginator{
        $query = Role::query();

        // Aplicar filtros si existe la búsqueda
        if ($searchBy && $search) {
            $query->where($searchBy, 'like', '%' . $search . '%')
            ->orWhere($searchBy, $search);
        }

        // Aplicar ordenamiento
        $query->orderBy($sortBy, $sortDirection);

        // Obtener roles paginados
        return $query->paginate($perPage);
    }
}