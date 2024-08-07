<?php

namespace App\Models;

use App\Traits\HasCustomTimestampsTrait;
use Illuminate\Database\Eloquent\Model;

class Phone extends Model{

    use HasCustomTimestampsTrait;

    protected $table = "phones";

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $fillable = [
        'area_code', 'phone'
    ];


    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}