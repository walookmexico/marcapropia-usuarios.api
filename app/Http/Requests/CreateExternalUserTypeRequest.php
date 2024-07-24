<?php

namespace App\Http\Requests;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CreateExternalUserTypeRequest{
    public static function validate(Request $request){
        $rules = [
            'name' => 'required|max:100|unique:external_user_types',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->errors()->count() > 0) {
            throw new ValidationException($validator);
        }
    }
}
