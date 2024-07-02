<?php

namespace App\Models;

use App\Traits\HasCustomTimestampsTrait;
use Illuminate\Database\Eloquent\Model;

class ExternalUserType extends Model{

    use HasCustomTimestampsTrait;

    protected $table = "external_user_types";
    protected $primaryKey = "id";

    protected $fillable = [
        'name'
    ];

    public function externalUserDetails(){
        return $this->hasMany(ExternalUserDetail::class, 'external_user_type_id', 'id');
    }
}