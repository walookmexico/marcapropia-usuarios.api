<?php

namespace App\Models;

use App\Traits\HasCustomTimestampsTrait;
use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="Role",
 *     type="object",
 *     description="Schema de rol",
 *     @OA\Property(property="id", type="integer", format="int64", example=1),
 *     @OA\Property(property="name", type="string", example="Admin"),
 *     @OA\Property(property="description", type="string", example="Administrator role with full permissions"),
 *     @OA\Property(property="active", type="boolean", example=true),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-07-21 14:00:00.0000"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-07-21 14:00:00.0000"),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", example=null),
 *     required={"name", "description", "active"}
 * )
 */
class Role extends SpatieRole{

    use SoftDeletes, HasCustomTimestampsTrait;

    protected $table = "roles";
    protected $primaryKey = 'id';
    protected $guard_name = 'api';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts = [
        'active' => 'boolean',
    ];
    protected $fillable = ['name', 'description', 'active', 'guard_name'];
    protected $hidden = ['guard_name'];
}
