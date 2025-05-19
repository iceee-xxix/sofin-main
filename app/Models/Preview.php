<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Preview extends Model
{
    use HasFactory;

    public function category()
    {
        return $this->belongsTo(Categories::class, 'categories_id')->withTrashed();
    }

    public function files()
    {
        return $this->hasMany(PreviewFile::class, 'preview_id');
    }
}
