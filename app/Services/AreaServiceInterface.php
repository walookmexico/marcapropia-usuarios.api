<?php

namespace App\Services;
use App\Models\Area;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Summary of AreaService
 */
interface AreaServiceInterface
{
    public function getAreaById(int $id): Area;
    public function createArea(array $data): Area;
    public function updateArea(int $id, array $data): Area;
    public function deactivateArea(int $id): void;
    public function activateArea(int $id): void;
    public function getAreasPaginated(int $perPage = 10, string $searchBy = null, string $search = null, 
        string $sortBy = 'name', string $sortDirection = 'asc') : LengthAwarePaginator;

}
