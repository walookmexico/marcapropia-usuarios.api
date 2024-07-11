<?php

namespace App\Traits;

use Carbon\Carbon;

trait HasCustomTimestampsTrait
{
    public $timestamps = true;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    public function getCreatedAtAttribute($value){
        if($value){
            return Carbon::parse($value)->format('Y-m-d H:i:s.u');
        }
        return null;
    }

    public function getUpdatedAtAttribute($value){
        if($value){
            return Carbon::parse($value)->format('Y-m-d H:i:s.u');
        }
        return null;
    }

    public function getDeletedAtAttribute($value){
        if($value){
            return Carbon::parse($value)->format('Y-m-d H:i:s.u');
        }
        return null;
    }
}
