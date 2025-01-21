<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    /** @use HasFactory<\Database\Factories\CommentFactory> */
    use HasFactory;

    protected $fillable = ['comment', 'user_id', 'commentable_type', 'commentable_id'];
    // to disable mass assignment user -> public $guarded = [];
    //morph commentable
    public function commentable()
    {
        return $this->morphTo();
    }
    //to return the user 
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //comments also can has comments
    public function comments()
    { //call morph commentable function in comments class
        return $this->morphMany(Comment::class, 'commentable');
    }
}
