<?php

namespace App\Models;

use App\Traits\HasCustomTimestampsTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="ExternalUserType",
 *     type="object",
 *     description="Schema de tipo de usuario externo",
 *     @OA\Property(property="id", type="integer", format="int64", example=1),
 *     @OA\Property(property="name", type="string", example="Laboratorio"),
 *     @OA\Property(property="active", type="boolean", example=true),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-07-21 14:00:00.0000"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-07-21 14:00:00.0000"),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", example=null),
 *     required={"name", "active"}
 * )
 */
class ExternalUserType extends Model{

    use SoftDeletes, HasCustomTimestampsTrait;

    protected $table = "external_user_types";
    protected $primaryKey = "id";
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts = [
        'active' => 'boolean',
    ];
    protected $fillable = [
        'name', 'active'
    ];

    public function externalUserDetails(){
        return $this->hasMany(ExternalUserDetail::class, 'external_user_type_id', 'id');
    }
}