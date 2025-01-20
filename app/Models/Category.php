<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'slug'];
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    // to create a resource php artisan make:filament-resource Post
    //the below function should be available to create relationship manager using this command 
    //?php artisan make:filament-relation-manager CategoryResource posts title
    // Category resource indicated which table we want add the relation to
    // posts is the method defined below
    // title is which column we want to show
    //works on one to many relationships
    // this gonna show us all the post related to that category
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
    use HasFactory;
}
