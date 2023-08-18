<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Survey extends Model
{
    
    use SoftDeletes;

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'title', 
        'type',
        'description',
        'user_id',
        'user_ids', 
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
