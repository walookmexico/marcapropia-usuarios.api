<?php

namespace App\Services;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Summary of UserService
 */
interface UserServiceInterface{
   
    public function registerUser(array $data) : User;
    
    public function getUserById(int $id): User;

    public function getUserWithAllRelationsById(int $id): User;
    
    public function updateUser(int $id, array $data): User;

    public function deactivateUser(int $id): void;
    
    public function activateUser(int $id): void;

    public function getUsersPaginated(int $perPage = 10, string $searchBy = null, string $search = null, 
        string $sortBy = 'email', string $sortDirection = 'asc') : LengthAwarePaginator;
    
}
