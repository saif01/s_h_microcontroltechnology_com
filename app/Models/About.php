<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class About extends Model
{
    protected $table = 'about';

    protected $fillable = [
        'hero',
        'story',
        'values_title',
        'values_subtitle',
        'values',
        'team_overline',
        'team_title',
        'team',
    ];

    protected $casts = [
        'hero' => 'array',
        'story' => 'array',
        'values' => 'array',
        'team' => 'array',
    ];
}
