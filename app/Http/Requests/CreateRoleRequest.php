<?php

namespace App\Http\Requests;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CreateRoleRequest{
    public static function validate(Request $request){
        $rules = [
            'name' => 'required|max:100',
            'description' => 'required|max:200',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->errors()->count() > 0) {
            throw new ValidationException($validator);
        }
    }
}
