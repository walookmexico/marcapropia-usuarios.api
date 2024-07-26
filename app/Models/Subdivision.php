<?php

namespace App\Models;

use App\Traits\HasCustomTimestampsTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="Subdivision",
 *     type="object",
 *     description="Schema de subdivisión",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Division A"),
 *     @OA\Property(property="description", type="string", example="Descripción de la división A"),
 *     @OA\Property(property="active", type="boolean", example=true),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T00:00:00.0000"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-02T00:00:00.0000"),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", example=null),
 *     required={"name", "description", "active"}
 * )
 */
class Subdivision extends Model{

    use SoftDeletes, HasCustomTimestampsTrait;

    protected $table = "subdivisions";
    protected $primaryKey = 'id';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts = [
        'active' => 'boolean',
    ];
    protected $fillable = ['name', 'description', 'active'];
    protected $hidden = [];

    public function area(){
        return $this->belongsTo(Area::class, 'area_id', 'id');
    }
}
