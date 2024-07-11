<?php

namespace App\Services\Impl;

use App\Models\Area;
use App\Services\Impl\AbstractBaseService;
use App\Services\AreaServiceInterface;
use App\Utils\UserConstants;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AreaServiceImpl extends AbstractBaseService implements AreaServiceInterface{


    public function getAreaById(int $id): Area {
        return Area::withTrashed()->findOrFail($id);
    }

    public function createArea(array $data): Area {
        $name = $data["name"];
        $description = $data["description"];

        return Area::create([
            'name' => $name,
            'description' => $description,
            'active' => UserConstants::ACTIVE_STATUS
        ]);
    }

    public function updateArea(int $id, array $data): Area {
        $area = $this->getAreaById($id);

        $name = $data["name"];
        $description = $data["description"];

        $area->update([
            'name' => $name,
            'description' => $description,
        ]);
        return $area;
    }

    public function deactivateArea(int $id): void {

        try {
            DB::beginTransaction();

            $area = $this->getAreaById($id);
            $area->active = UserConstants::INACTIVE_STATUS;
            $area->save();
            $area->delete();

            DB::commit();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            throw $e;
        }
    }

    public function activateArea(int $id): void {
        try {
            DB::beginTransaction();

            $area = $this->getAreaById($id);
            $area->active = UserConstants::ACTIVE_STATUS;
            $area->save();
            $area->restore();

            DB::commit();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            throw $e;
        }
    }

    public function getAreasPaginated(int $perPage = 10, string $searchBy = null, string $search = null,
        string $sortBy = 'name', string $sortDirection = 'asc') : LengthAwarePaginator{
        $query = Area::query();
        $query->withTrashed();

        // Aplicar filtros si existe la búsqueda
        if ($searchBy && $search) {
            $query->where($searchBy, 'like', '%' . $search . '%')
            ->orWhere($searchBy, $search);
        }

        // Aplicar ordenamiento
        $query->orderBy($sortBy, $sortDirection);

        // Obtener areas paginados
        return $query->paginate($perPage);
    }
}