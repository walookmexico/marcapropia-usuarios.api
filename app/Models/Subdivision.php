<?php

namespace App\Models;

use App\Traits\HasCustomTimestampsTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
