<?php

namespace App\Models;

use App\Traits\HasCustomTimestampsTrait;
use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends SpatieRole{

    use SoftDeletes, HasCustomTimestampsTrait;

    protected $table = "roles";
    protected $primaryKey = 'id';
    protected $guard_name = 'api';
    protected $dates = ['deleted_at'];
    protected $casts = [
        'active' => 'boolean',
    ];
    protected $fillable = ['name', 'description', 'active', 'guard_name'];   
    protected $hidden = ['guard_name'];
}
