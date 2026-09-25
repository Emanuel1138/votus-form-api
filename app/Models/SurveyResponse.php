<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyResponse extends Model
{
    protected $fillable = ['device_id', 'answers', 'answered_at'];

    protected $casts = [
        'answers' => 'array',
        'answered_at' => 'datetime',
    ];
}