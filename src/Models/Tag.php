<?php

namespace Danir\MediaLib\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    public function pictures()
    {
        return $this->belongsToMany(Picture::class);
    }
}
