<?php

namespace App\Services\Impl;

use App\Models\User;
use App\Services\Impl\AbstractBaseService;
use App\Services\Impl\RoleServiceImpl;
use App\Services\UserServiceInterface;
use App\Utils\UserConstants;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserServiceImpl extends AbstractBaseService implements UserServiceInterface{

    protected $roleService;

    public function __construct(){
        $this->roleService = RoleServiceImpl::getInstance();
    }

    /**
     * @inheritDoc
     */
    public function registerUser(array $data) : User{
        try {
            DB::beginTransaction();

            $user = $this->createUser($data);
            $this->createPhoneIfNeccesary($data, $user);
            $this->createUserByType($data, $user);

            DB::commit();

            return $user;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            throw $e;
        }
    }

    private function getUserType(string $email) : string{
        $userType = UserConstants::EXTERNAL_USER_TYPE;
        $internalDomains = explode(',', env('INTERNAL_EMAIL_DOMAINS', ''));
        foreach ($internalDomains as $domain) {
            if (str_ends_with($email, $domain)) {
                $userType = UserConstants::INTERNAL_USER_TYPE;
                break;
            }
        }

        return $userType;
    }

    private function createUser(array $data) : User{
        $email = $data['email'];
        $fullName = $data['fullName'];
        $pass = $data['password'];
        $userType = $this->getUserType($email);

        $user = User::create([
            'name' => $fullName,
            'email' => $email,
            'password' => app('hash')->make($pass),
            'user_type' => $userType,
            'active' => UserConstants::ACTIVE_STATUS,
        ]);

        return $user;
    }

    private function createPhoneIfNeccesary(array $data, User $user) : void{
        if(isset($data['phone']) && isset($data['areaCode'])){
            $phoneNumber = $data['phone'];
            $areaCode = $data['areaCode'];

            if(!empty(trim($phoneNumber)) && !empty(trim($areaCode))){
                $user->phones()->create([
                    'phone' => $phoneNumber,
                    'area_code' => $areaCode
                ]);
            }
        }
    }

    private function createUserByType(array $data, User $user) : void{
        $email = $data['email'];
        $userType = $this->getUserType($email);

        if ($userType == UserConstants::INTERNAL_USER_TYPE) {
            $job = $data['job'];
            $role = $this->roleService->getRoleById($job);
            $user->assignRole($role);

            $employeeCode = $data['employeeCode'];
            $user->internalUserDetail()->create([
                'employee_code' => $employeeCode
            ]);
        }else{
            $companyName = $data['company'];
            $externalUserType = $data['userType'];

            $user->externalUserDetail()->create([
                'company_name' => $companyName,
                'external_user_type_id' => $externalUserType
            ]);
        }
    }


    public function getUserById(int $id): User {
        return User::with("phones")->withTrashed()->findOrFail($id);
    }

    public function getUserWithAllRelationsById(int $id): User {
        return User::with(['phones' => function($query) {
                $query->select('area_code', 'phone', 'user_id'); // Especifica los campos que deseas obtener
            }])
            ->with(['internalUserDetail' => function($query) {
                $query->select('employee_code', 'user_id'); // Especifica los campos que deseas obtener
            }])
            ->with(['externalUserDetail' => function($query) {
                $query->select('external_user_type_id', 'company_name', 'user_id')->with(['externalUserType' => function($query) {
                    $query->select('name', 'id'); // Especifica los campos que deseas obtener
                }]); // Especifica los campos que deseas obtener
            }])
            ->withTrashed()->findOrFail($id);
    }

    public function updateUser(int $id, array $data): User {
        try{

            DB::beginTransaction();

            $user = $this->getUserById($id);

            if(isset($data['name'])){
                $name = $data["name"];
                $user->name = $name;
            }

            if(isset($data["password"])){
                $pass = $data["password"];
                $user->password = app('hash')->make($pass);
            }

            $this->updatePhoneIfNecessary($data, $user);

            if($user->user_type == UserConstants::INTERNAL_USER_TYPE){
                $this->updateJobIfNecessary($data, $user);
                $this->updateEmployeeCodeIfNecessary($data, $user);
            }else{
                $this->updateCompanyNameIfNecessary($data, $user);
                $this->updateUserTypeIfNecessary($data, $user);
            }

            $user->save();

            DB::commit();

            return $user;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            throw $e;
        }
    }

    public function deactivateUser(int $id): void {

        try {
            DB::beginTransaction();

            $user = $this->getUserById($id);
            $user->active = UserConstants::INACTIVE_STATUS;
            $user->save();
            $user->delete();

            DB::commit();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            throw $e;
        }
    }

    public function activateUser(int $id): void {
        try {
            DB::beginTransaction();

            $user = $this->getUserById($id);
            $user->active = UserConstants::ACTIVE_STATUS;
            $user->save();
            $user->restore();

            DB::commit();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            throw $e;
        }
    }

    public function getUsersPaginated(int $perPage = 10, string $searchBy = null, string $search = null,
        string $sortBy = 'email', string $sortDirection = 'asc') : LengthAwarePaginator{
        $query = User::query();
        $query->withTrashed();

        $query->with(['phones' => function($query) {
            $query->select('area_code', 'phone', 'user_id'); // Especifica los campos que deseas obtener
        }])
        ->with(['internalUserDetail' => function($query) {
            $query->select('employee_code', 'user_id'); // Especifica los campos que deseas obtener
        }])
        ->with(['externalUserDetail' => function($query) {
            $query->select('external_user_type_id', 'company_name', 'user_id')->with(['externalUserType' => function($query) {
                $query->select('name', 'id'); // Especifica los campos que deseas obtener
            }]); // Especifica los campos que deseas obtener
        }]);

        // Aplicar filtros si existe la búsqueda
        if ($searchBy && $search) {
            $query->where($searchBy, 'like', '%' . $search . '%')
            ->orWhere($searchBy, $search);
        }

        // Aplicar ordenamiento
        $query->orderBy($sortBy, $sortDirection);

        // Obtener roles paginados
        return $query->paginate($perPage);
    }

    private function updatePhoneIfNecessary(array $data, User $user) : void{
        if(isset($data['phone']) && isset($data['areaCode'])){
            $phoneNumber = $data['phone'];
            $areaCode = $data['areaCode'];

            if(!empty(trim($phoneNumber)) && !empty(trim($areaCode))){
                $user->phones()->update([
                    'phone' => $phoneNumber,
                    'area_code' => $areaCode
                ]);
            }
        }
    }

    private function updateJobIfNecessary(array $data, User $user) : void {
        if(isset($data['job'])) {
            $job = $data['job'];
            $role = $this->roleService->getRoleById($job);
            $user->assignRole($role);
        }
    }

    private function updateEmployeeCodeIfNecessary(array $data, User $user) : void {
        if(isset($data['employeeCode'])) {
            $employeeCode = $data['employeeCode'];
            $user->internalUserDetail()->update([
                'employee_code' => $employeeCode
            ]);
        }
    }
    private function updateCompanyNameIfNecessary(array $data, User $user) : void {
        if(isset($data['company'])) {
            $companyName = $data['company'];
            $user->externalUserDetail()->update([
                'company_name' => $companyName,
            ]);
        }
    }

    private function updateUserTypeIfNecessary(array $data, User $user) : void {
        if(isset($data['userType'])) {
            $externalUserType = $data['userType'];
            $user->externalUserDetail()->update([
                'external_user_type_id' => $externalUserType
            ]);
        }
    }
}