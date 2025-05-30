<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gift extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'message',
        'image_url',
        'video_url',
        'other_link',
        'age',
    ];
}