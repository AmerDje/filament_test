<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post_User extends Model
{
    /** @use HasFactory<\Database\Factories\PostUserFactory> */
    use HasFactory;

    protected $table = 'post_users';

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
