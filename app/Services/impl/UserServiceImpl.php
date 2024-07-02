<?php

namespace App\Services\Impl;

use App\Models\User;
use App\Services\Impl\AbstractBaseService;
use App\Services\UserServiceInterface;
use App\Utils\UserConstants;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserServiceImpl extends AbstractBaseService implements UserServiceInterface{

    /**
     * @inheritDoc
     */
    public function registerUser(array $data) {
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

    private function getUserType(string $email){
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

    private function createUser(array $data){
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

    private function createPhoneIfNeccesary(array $data, User $user){
        if(isset($data['phone']) && isset($data['areaCode'])){
            $phoneNumber = $data['phone'];
            $areaCode = $data['areaCode'];

            $user->phones()->create([
                'phone' => $phoneNumber, 
                'area_code' => $areaCode
            ]);
        }
    }

    private function createUserByType(array $data, User $user){
        $email = $data['email'];
        $userType = $this->getUserType($email);

        if ($userType == UserConstants::INTERNAL_USER_TYPE) {
            $job = $data['job'];
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
}