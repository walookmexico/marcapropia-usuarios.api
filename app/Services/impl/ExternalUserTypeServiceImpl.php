<?php

namespace App\Services\Impl;

use App\Models\ExternalUserType;
use App\Services\Impl\AbstractBaseService;
use App\Services\ExternalUserTypeServiceInterface;
use App\Utils\UserConstants;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExternalUserTypeServiceImpl extends AbstractBaseService implements ExternalUserTypeServiceInterface{


    public function getExternalUserTypeById(int $id): ExternalUserType {
        return ExternalUserType::withTrashed()->findOrFail($id);
    }

    public function createExternalUserType(array $data): ExternalUserType {
        $name = $data["name"];

        return ExternalUserType::create([
            'name' => $name
        ]);
    }

    public function updateExternalUserType(int $id, array $data): ExternalUserType {
        $ExternalUserType = $this->getExternalUserTypeById($id);
        
        $name = $data["name"];

        $ExternalUserType->update([
            'name' => $name
        ]);
        return $ExternalUserType;
    }

    public function deactivateExternalUserType(int $id): void {

        try {
            DB::beginTransaction();

            $ExternalUserType = $this->getExternalUserTypeById($id);
            $ExternalUserType->active = UserConstants::INACTIVE_STATUS;
            $ExternalUserType->save();
            $ExternalUserType->delete();

            DB::commit();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            throw $e;
        }
    }

    public function activateExternalUserType(int $id): void {
        try {
            DB::beginTransaction();

            $ExternalUserType = ExternalUserType::withTrashed()->findOrFail($id);
            $ExternalUserType->active = UserConstants::ACTIVE_STATUS;
            $ExternalUserType->save();
            $ExternalUserType->restore();
           
            DB::commit();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            throw $e;
        }
    }

    public function getExternalUserTypesPaginated(int $perPage = 10, string $searchBy = null, string $search = null, 
        string $sortBy = 'name', string $sortDirection = 'asc') : LengthAwarePaginator{
        $query = ExternalUserType::query();

        // Aplicar filtros si existe la búsqueda
        if ($searchBy && $search) {
            $query->where($searchBy, 'like', '%' . $search . '%')
            ->orWhere($searchBy, $search);
        }

        // Aplicar ordenamiento
        $query->orderBy($sortBy, $sortDirection);

        // Obtener ExternalUserTypes paginados
        return $query->paginate($perPage);
    }
}