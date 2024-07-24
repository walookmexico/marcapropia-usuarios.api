<?php

namespace App\Http\Requests;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UpdateRoleRequest{
    public static function validate(Request $request, int $id){
        $rules = [
            'name' => 'required|string|max:255|unique:roles,name,' . $id,
            'description' => 'required|max:200',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->errors()->count() > 0) {
            throw new ValidationException($validator);
        }
    }
}
