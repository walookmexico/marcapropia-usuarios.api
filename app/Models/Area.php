<?php

namespace App\Models;

use App\Traits\HasCustomTimestampsTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="Area",
 *     type="object",
 *     description="Schema de área",
 *     @OA\Property(property="id", type="integer", format="int64", example=1),
 *     @OA\Property(property="name", type="string", example="Marketing"),
 *     @OA\Property(property="description", type="string", example="Department responsible for marketing strategies"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-07-21 14:00:00.0000"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-07-21 14:00:00.0000"),
 *     required={"name", "description", "active"}
 * )
 */
class Area extends Model{

    use SoftDeletes, HasCustomTimestampsTrait;

    protected $table = "areas";
    protected $primaryKey = 'id';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts = [
        'active' => 'boolean',
    ];
    protected $fillable = ['name', 'description', 'active'];
    protected $hidden = [];

    public function subdivisions(){
        return $this->hasMany(Subdivision::class, 'area_id', 'id');
    }
}
