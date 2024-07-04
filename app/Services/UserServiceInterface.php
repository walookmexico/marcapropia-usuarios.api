<?php

namespace App\Services;
use App\Models\User;

/**
 * Summary of UserService
 */
interface UserServiceInterface{
   
    
    public function registerUser(array $data) : User;
    
}
