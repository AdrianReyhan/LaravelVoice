<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Face extends Model
{
    use HasFactory;

    protected $table = 'images';
    protected $fillable = ['user_id', 'filename'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
