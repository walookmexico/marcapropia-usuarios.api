<?php

namespace App\Traits;

trait HttpResponseTrait{

    protected function success($message, $data = [], $status = 200){
        return response([
            'status' => 'success',
            'data' => $data,
            'message' => $message,
            'code' => $status
        ], $status);
    }

    protected function error($message, $data = [], $status = 422){
        return response([
            'status' => 'error',
            'data' => $data,
            'message' => $message,
            'code' => $status
        ], $status);
    }

}