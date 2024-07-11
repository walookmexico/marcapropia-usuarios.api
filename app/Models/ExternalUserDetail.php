<?php

namespace App\Models;

use App\Traits\HasCustomTimestampsTrait;
use Illuminate\Database\Eloquent\Model;

class ExternalUserDetail extends Model{

    use HasCustomTimestampsTrait;

    protected $table = "external_user_details";

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'company_name', 'external_user_type_id'
    ];
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function externalUserType(){
        return $this->belongsTo(ExternalUserType::class, 'external_user_type_id', 'id');
    }
}