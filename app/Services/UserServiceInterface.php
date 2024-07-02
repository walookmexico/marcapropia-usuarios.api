<?php

namespace App\Services;

/**
 * Summary of UserService
 */
interface UserServiceInterface{
    /**
     * Summary of createUser
     * @param mixed $name
     * @return void
     */
    public function registerUser(array $data);
}
