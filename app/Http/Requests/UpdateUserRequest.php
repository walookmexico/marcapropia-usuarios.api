<?php

namespace App\Http\Requests;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UpdateUserRequest{
    public static function validate(Request $request, int $id){
        $rules = [
            'fullName' => 'string|max:255',
            'password' => [ 
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
            'userType' => ['sometimes', 'required', 'integer','exists:external_user_types,id']
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->errors()->count() > 0) {
            throw new ValidationException($validator);
        }
    }
}
