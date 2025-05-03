<?php

namespace App\Models;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Model;

class CommentImage extends Model
{
    //
    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }
}
