<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['user_id', 'post_id', 'content'];

    // Relasi: Komentar ini milik siapa?
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}