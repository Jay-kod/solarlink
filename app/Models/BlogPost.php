<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    protected $fillable = [
        'title',
        'author',
        'category',
        'content',
        'status',
        'image',
        'views',
    ];
    use HasFactory;
}
