<?php

namespace App\Services;
use App\Models\Subdivision;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Summary of SubdivisionService
 */
interface SubdivisionServiceInterface
{
    public function getSubdivisionById(int $id): Subdivision;
    public function createSubdivision(array $data): Subdivision;
    public function updateSubdivision(int $id, array $data): Subdivision;
    public function deactivateSubdivision(int $id): void;
    public function activateSubdivision(int $id): void;
    public function getSubdivisionsPaginated(int $perPage = 10, string $searchBy = null, string $search = null,
        string $sortBy = 'name', string $sortDirection = 'asc') : LengthAwarePaginator;

}
