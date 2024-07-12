<?php

namespace App\Services\Impl;

use App\Models\Subdivision;
use App\Services\Impl\AbstractBaseService;
use App\Services\SubdivisionServiceInterface;
use App\Utils\UserConstants;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubdivisionServiceImpl extends AbstractBaseService implements SubdivisionServiceInterface{


    public function getSubdivisionById(int $id): Subdivision {
        return Subdivision::withTrashed()->findOrFail($id);
    }

    public function createSubdivision(array $data): Subdivision {
        $name = $data["name"];
        $description = $data["description"];
        $areaId = $data["areaId"];

        return Subdivision::create([
            'name' => $name,
            'description' => $description,
            'active' => UserConstants::ACTIVE_STATUS,
            'area_id' => $areaId,
        ]);
    }

    public function updateSubdivision(int $id, array $data): Subdivision {
        $subdivision = $this->getSubdivisionById($id);

        $name = $data["name"];
        $description = $data["description"];
        $areaId = $data["areaId"];

        $subdivision->update([
            'name' => $name,
            'description' => $description,
            'area_id' => $areaId,
        ]);
        return $subdivision;
    }

    public function deactivateSubdivision(int $id): void {

        try {
            DB::beginTransaction();

            $subdivision = $this->getSubdivisionById($id);
            $subdivision->active = UserConstants::INACTIVE_STATUS;
            $subdivision->save();
            $subdivision->delete();

            DB::commit();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            throw $e;
        }
    }

    public function activateSubdivision(int $id): void {
        try {
            DB::beginTransaction();

            $subdivision = $this->getSubdivisionById($id);
            $subdivision->active = UserConstants::ACTIVE_STATUS;
            $subdivision->save();
            $subdivision->restore();

            DB::commit();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            throw $e;
        }
    }

    public function getSubdivisionsPaginated(int $perPage = 10, string $searchBy = null, string $search = null,
        string $sortBy = 'name', string $sortDirection = 'asc') : LengthAwarePaginator{
        $query = Subdivision::query();
        $query->withTrashed();

        // Aplicar filtros si existe la búsqueda
        if ($searchBy && $search) {
            $query->where($searchBy, 'like', '%' . $search . '%')
            ->orWhere($searchBy, $search);
        }

        // Aplicar ordenamiento
        $query->orderBy($sortBy, $sortDirection);

        // Obtener subdivisions paginados
        return $query->paginate($perPage);
    }
}