<?php

namespace App\Models;

use App\Casts\MoneyCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Post extends Model
{
    /** @use HasFactory<\Database\Factories\PostFactory> */
    use HasFactory, LogsActivity;
    // to add activity 
    /*
    first install //?composer require spatie/laravel-activitylog
    then past //? this php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-migrations"
    then do migrate //? php artisan migrate
    then past this //?php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-config"
    then use LogsActivity
    then use getActivitylogOptions
    then do the reset as pxlrbt tells 
    */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'title',
                'slug',
                'content',
                'category.name'
            ]);
        //->logAll();
    }
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
        //'price' => MoneyCast::class,
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
}
