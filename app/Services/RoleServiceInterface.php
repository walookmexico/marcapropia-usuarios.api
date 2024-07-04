<?php

namespace App\Services;
use App\Models\Role;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Summary of RoleService
 */
interface RoleServiceInterface
{
    public function getRoleById(int $id): Role;
    public function createRole(array $data): Role;
    public function updateRole(int $id, array $data): Role;
    public function deactivateRole(int $id): void;
    public function activateRole(int $id): void;
    public function getRolesPaginated(int $perPage = 10, string $searchBy = null, string $search = null, 
        string $sortBy = 'name', string $sortDirection = 'asc') : LengthAwarePaginator;

}
