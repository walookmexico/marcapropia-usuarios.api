<?php

namespace App\Models;

use App\Traits\HasCustomTimestampsTrait;
use Illuminate\Database\Eloquent\Model;

class InternalUserDetail extends Model{

    use HasCustomTimestampsTrait;

    protected $table = "internal_user_details";
    protected $primaryKey = 'user_id';

    protected $fillable = [
        'employee_code'
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}