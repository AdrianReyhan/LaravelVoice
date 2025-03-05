<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voice extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'voice_path'];

    // Relasi ke user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
