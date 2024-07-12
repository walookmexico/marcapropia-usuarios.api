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
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'max:12',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,12}$/',
                'confirmed'
            ],
            'areaCode' => ['sometimes', 'nullable', 'string', 'min:2','max: 6', 'regex:/^\+\d{1,5}$/'],
            'phone' => ['sometimes','nullable', 'string', 'max: 10', 'regex:/^\d{1,10}$/'],
            'job' => ['sometimes', 'required', 'integer', 'exists:roles,id'],
            'employeeCode' => ['sometimes', 'required', 'string', 'max: 25'],
            'company' => ['sometimes', 'required', 'string', 'max: 200'],
            'userType' => ['sometimes', 'required', 'integer','exists:external_user_types,id']
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
        self::validatePhone($areaCode, $phone, $validator);

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
                $validator->errors()->add('email', __('validation.custom.email.prohibited_domain',
                [
                    'domains' => implode(' or ', $prohibitedDomains),
                    'attribute' => __('validation.attributes.email')
                ]));
                return;
            }
        }
    }

    protected function validatePhone($areaCode, $phone, $validator){
        if (!empty($areaCode)) {
            if (empty($phone)) {
                $validator->errors()->add('phone', __('validation.custom.phone.required_if_area_code',
                ['attribute' => __('validation.attributes.phone')]));
            }
        }
    }

    protected function validateAreaCode($areaCode, $phone, $validator){
        if (!empty($phone)) {
            if (empty($areaCode)) {
                $validator->errors()->add('areaCode', __('validation.custom.areaCode.required_if_phone',
                ['attribute' => __('validation.attributes.areaCode')]));
            }
        }
    }

    protected function validateJob($email, $job, $validator){
        $internalDomains = explode(',', env('INTERNAL_EMAIL_DOMAINS', ''));
        foreach ($internalDomains as $domain) {
            if (str_ends_with($email, $domain)) {
                if (empty($job)) {
                    $validator->errors()->add('job', __('validation.custom.job.required_if_internal_domain',
                    ['attribute' => __('validation.attributes.job')]));
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
                    $validator->errors()->add('userType', __('validation.custom.userType.required_if_external_user',
                    ['attribute' => __('validation.attributes.userType')]));
                }
                break;
            }
        }
    }

    protected function validateEmployeeCode($email, $employeeCode, $validator){
        $internalDomains = explode(',', env('INTERNAL_EMAIL_DOMAINS', ''));
        foreach ($internalDomains as $domain) {
            if (str_ends_with($email, $domain)) {
                if (empty($employeeCode)) {
                    $validator->errors()->add('employeeCode', __('validation.custom.employeeCode.required_if_internal_user',
                    ['attribute' => __('validation.attributes.employeeCode')]));
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
                    $validator->errors()->add('company', __('validation.custom.company.required_if_external_user',
                    ['attribute' => __('validation.attributes.company')]));
                }
                break;
            }
        }
    }
}
