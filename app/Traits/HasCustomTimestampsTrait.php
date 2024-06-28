<?php

namespace App\Traits;

trait HasCustomTimestampsTrait
{
    public $timestamps = true;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
}
