<?php

namespace App\Http\Requests;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class RegisterUserRequest
{
    public function validate(Request $request){
        $rules = [
            'fullName' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => [ 
                'required',
                'string',
                'min:8',
                'max:12',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,12}$/',
                'confirmed'
            ],
            'areaCode' => ['sometimes', 'required', 'string', 'max: 10'],
            'phone' => ['nullable', 'string', 'max: 25'],
            'job' => ['sometimes', 'required', 'integer', 'exists:roles,id'],
            'employeeCode' => ['sometimes', 'required', 'string', 'max: 25'],
            'company' => ['sometimes', 'required', 'string', 'max: 200'],
            'userType' => ['sometimes', 'required', 'integer']
        ];

        $validator = Validator::make($request->all(), $rules);
        $this->withValidator($validator, $request);

        // Verificar si la validación falla
        if ($validator->errors()->count() > 0) {
            throw new ValidationException($validator);
        }
    }

    protected function withValidator($validator, $request){
          
        $email = $request->input('email');
        self::validateDomains($email, $validator);
        
        $phone = $request->input('phone');
        $areaCode = $request->input('areaCode');
        self::validateAreaCode($areaCode, $phone, $validator);

        $job = $request->input('job');
        self::validateJob($email, $job, $validator);

        $externalUserType = $request->input('userType');
        self::validateExternalUserType($email, $externalUserType, $validator);

        $employeeCode = $request->input('employeeCode');
        self::validateEmployeeCode($email, $employeeCode, $validator);

        $company = $request->input('company');
        self::validateCompany($email, $company, $validator);
        
    }

    protected function validateDomains($email, $validator){
        $prohibitedDomains = explode(',', env('PROHIBITED_EMAIL_DOMAINS', ''));
        foreach ($prohibitedDomains as $domain) {
            if (str_ends_with($email, $domain)) {
                $validator->errors()->add('email', 'Emails from the domains ' . implode(' or ', $prohibitedDomains) . ' are not allowed.');
                return;
            }
        }
    }

    protected function validateAreaCode($areaCode, $phone, $validator){
        if (!empty($phone)) {
            if (empty($areaCode)) {
                $validator->errors()->add('areaCode', 'The areaCode field is required when providing a phone number.');
            } elseif (!is_string($areaCode) || strlen($areaCode) > 5) {
                $validator->errors()->add('areaCode', 'The areaCode field must be a string with a maximum length of 5 characters.');
            }
        }
    }

    protected function validateJob($email, $job, $validator){
        $internalDomains = explode(',', env('INTERNAL_EMAIL_DOMAINS', ''));
        foreach ($internalDomains as $domain) {
            if (str_ends_with($email, $domain)) {
                if (empty($job)) {
                    $validator->errors()->add('job', 'The job field is required when email ends with internal domain.');
                }
                
                break;
            }
        }
    }

    protected function validateExternalUserType($email, $externalUserType, $validator){
        $internalDomains = explode(',', env('INTERNAL_EMAIL_DOMAINS', ''));
        foreach ($internalDomains as $domain) {
            if (!str_ends_with($email, $domain)) {
                if (empty($externalUserType)) {
                    $validator->errors()->add('userType', 'The userType field is required when email not ends with internal domain.');
                }
                
                break;
            }
        }
    }

    protected function validateEmployeeCode($email, $employeCode, $validator){
        $internalDomains = explode(',', env('INTERNAL_EMAIL_DOMAINS', ''));
        foreach ($internalDomains as $domain) {
            if (str_ends_with($email, $domain)) {
                if (empty($employeCode)) {
                    $validator->errors()->add('employeeCode', 'The employeeCode field is required when email ends with internal domain.');
                }
                
                break;
            }
        }
    }

    protected function validateCompany($email, $company, $validator){
        $internalDomains = explode(',', env('INTERNAL_EMAIL_DOMAINS', ''));
        foreach ($internalDomains as $domain) {
            if (!str_ends_with($email, $domain)) {
                if (empty($company)) {
                    $validator->errors()->add('company', 'The company field is required when email not ends with internal domain.');
                }
                
                break;
            }
        }
    }
}
