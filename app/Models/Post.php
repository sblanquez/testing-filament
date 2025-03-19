<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{

    protected $fillable = [
        'user_id',
        'thumbnail',
        'title',
        'color',
        'slug',
        'category_id',
        'content',
        'tags',
        'published'
    ];

    protected $casts = [
        'tags' => 'array',
        'published' => 'boolean'
    ];



    public function user(): BelongsTo
        {
            return $this->belongsTo(User::class);
        }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}