<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $fillable = ['type', 'path'];

    // 所属用户
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
