<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'thumbnail',
        'title',
        'color',
        'slug',
        'category_id',
        'content',
        'tags',
        'published'
    ];
    //should cast to array
    protected $casts = [
        'tags' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    //post users is the tables we want to define many to many relationship in
    //when using pivot we should tell which table 
    public function authors()
    {
        return $this->belongsToMany(User::class, 'post_users')->withPivot(['order'])->withTimestamps();
    }

    public function comments()
    { //call morph commentable function in comments class
        return $this->morphMany(Comment::class, 'commentable');
    }
    /** @use HasFactory<\Database\Factories\PostFactory> */
    use HasFactory;
}
