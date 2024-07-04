<?php

namespace App\Models;

use App\Traits\HasCustomTimestampsTrait;
use Faker\Provider\en_AU\PhoneNumber;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Model implements AuthenticatableContract, AuthorizableContract, JWTSubject
{
    use Authenticatable, Authorizable, HasFactory, HasRoles, HasCustomTimestampsTrait;

    protected $table = "users";
    protected $primaryKey = 'id';
    protected $guard_name = 'api';
    protected $dates = ['deleted_at'];
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
        'password',
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
