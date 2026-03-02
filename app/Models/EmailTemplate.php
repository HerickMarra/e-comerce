<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'subject',
        'content',
        'type',
        'is_system',
    ];

    protected $casts = [
        'is_system' => 'boolean',
    ];
}
