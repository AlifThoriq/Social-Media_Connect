<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',      // <-- Tambahkan baris ini
        'email',
        'password',
        'avatar_url',    // <-- Sekalian tambahkan ini untuk nanti
        'bio',           // <-- Sekalian tambahkan ini untuk nanti
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
        return $this->hasMany(Post::class);
    }

    /**
     * Relasi: Satu User memiliki banyak Komentar
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Relasi: Satu User memiliki banyak Like
     */
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function following()
{
    return $this->belongsToMany(
        User::class,
        'follows',
        'follower_id',
        'following_id'
    );
}

// User yang follow aku
public function followers()
{
    return $this->belongsToMany(
        User::class,
        'follows',
        'following_id',
        'follower_id'
    );
}
}


