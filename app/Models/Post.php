<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    // Izinkan Laravel mengisi kolom-kolom ini
    protected $fillable = ['user_id', 'content', 'image_url'];

    // Relasi: Sebuah postingan dimiliki oleh satu User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 👇 TAMBAHKAN INI 👇
    // Relasi: Sebuah postingan bisa memiliki banyak Like
    public function likes()
    {
        return $this->hasMany(Like::class);
    }
    // Relasi: Sebuah postingan bisa memiliki banyak Komentar
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

}

