<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    const ROLE_ADMIN = 'ADMIN'; //1 //we can use numbers, they say its a little bit faster
    const ROLE_EDITOR = 'EDITOR'; //2
    const ROLE_USER = 'USER'; //3

    const ROLE_DEFAULT = self::ROLE_USER;


    const ROLES = [
        self::ROLE_ADMIN => "Admin",
        self::ROLE_EDITOR => "Editor",
        self::ROLE_USER => "User",
    ];

    public function canAccessPanel(Panel $panel): bool
    { //this to are same
        //return $this->isAdmin() ||  $this->isEditor();
        //from User::class we know which policy has this method viewPanel
        return $this->can('viewPanel', User::class);
    }

    public function isAdmin()
    {
        return str_ends_with($this->email, '@admin.com') /* && $this->email, hasVerifiedEmail() */ &&  $this->role == self::ROLE_ADMIN;
    }

    public function isEditor()
    {
        return str_ends_with($this->email, '@admin.com') /* && $this->email, hasVerifiedEmail() */ &&  $this->role == self::ROLE_EDITOR;
    }


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    // using this command we are adding roles to users table. we can also say --table=users if we did other name at last 
    // php artisan make:migration add_role_to_users  
    protected $fillable = [
        'name',
        'email',
        'password',
        'role'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function posts()
    {
        //post users is the tables we want to define many to many relationship in
        return $this->belongsToMany(Post::class, 'post_users')->withPivot(['order'])->withTimestamps();
    }
}
