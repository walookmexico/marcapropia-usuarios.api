<?php

namespace App\Models;

use App\Traits\HasCustomTimestampsTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="DirectBoss",
 *     type="object",
 *     description="Schema de jefe directo",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example="2"),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="active", type="boolean", example=true),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T00:00:00.0000"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-02T00:00:00.0000"),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", example=null),
 *     required={"user_id", "active"}
 * )
 */
class DirectBoss extends Model{

    use HasCustomTimestampsTrait, SoftDeletes;

    protected $table = "direct_bosses";
    protected $primaryKey = 'id';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'user_id', 'active'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var string[]
     */
    protected $hidden = [

    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
