<?php

namespace App\Services;
use App\Models\ExternalUserType;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Summary of ExternalUserTypeService
 */
interface ExternalUserTypeServiceInterface
{
    public function getExternalUserTypeById(int $id): ExternalUserType;
    public function createExternalUserType(array $data): ExternalUserType;
    public function updateExternalUserType(int $id, array $data): ExternalUserType;
    public function deactivateExternalUserType(int $id): void;
    public function activateExternalUserType(int $id): void;
    public function getExternalUserTypesPaginated(int $perPage = 10, string $searchBy = null, string $search = null, 
        string $sortBy = 'name', string $sortDirection = 'asc') : LengthAwarePaginator;

}
