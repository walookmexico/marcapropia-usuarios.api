<?php

namespace App\Models;

use App\Traits\HasCustomTimestampsTrait;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Lumen\Auth\Authorizable;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     description="Schema de usuario",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", example="john.doe@example.com"),
 *     @OA\Property(property="user_type", type="string", example="admin"),
 *     @OA\Property(property="active", type="boolean", example=true),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T00:00:00.0000"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-02T00:00:00.0000"),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", example=null),
 *     required={"name", "email", "password", "user_type", "active"}
 * )
 */
class User extends Model implements AuthenticatableContract, AuthorizableContract, JWTSubject
{
    use Authenticatable, Authorizable, HasFactory, HasRoles, HasCustomTimestampsTrait, SoftDeletes;

    protected $table = "users";
    protected $primaryKey = 'id';
    protected $guard_name = 'api';
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
        'name', 'email', 'password', 'user_type', 'active'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var string[]
     */
    protected $hidden = [
        'password', 'email_verified_at', 'remember_token'
    ];
    /**
     * @inheritDoc
     */
    public function getJWTCustomClaims() {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getJWTIdentifier() {
        return $this->getKey();
    }

    public function externalUserDetail(){
        return $this->hasOne(ExternalUserDetail::class, 'user_id', 'id');
    }

    public function internalUserDetail(){
        return $this->hasOne(InternalUserDetail::class, 'user_id', 'id');
    }

    public function phones(){
        return $this->hasMany(Phone::class, 'user_id', 'id');
    }
}
