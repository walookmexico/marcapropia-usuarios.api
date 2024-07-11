<?php

namespace App\Models;

use App\Traits\HasCustomTimestampsTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Area extends Model{

    use SoftDeletes, HasCustomTimestampsTrait;

    protected $table = "areas";
    protected $primaryKey = 'id';
    protected $guard_name = 'api';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts = [
        'active' => 'boolean',
    ];
    protected $fillable = ['name', 'description', 'active'];
    protected $hidden = [];
}
