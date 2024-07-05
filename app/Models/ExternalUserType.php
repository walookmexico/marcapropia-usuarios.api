<?php

namespace App\Models;

use App\Traits\HasCustomTimestampsTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExternalUserType extends Model{

    use SoftDeletes, HasCustomTimestampsTrait;

    protected $table = "external_user_types";
    protected $primaryKey = "id";
    protected $dates = ['deleted_at'];
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