<?php

namespace App\Models;

use App\Models\User;
use App\Models\Route;
use App\Models\CommentImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model
{
    use HasFactory, Notifiable;
    //
    protected $fillable = [
        'comment',
        'score',
        'user_id',
        'route_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function route()
    {
        return $this->belongsTo(Route::class);
    }
    public function commentImages()
    {
        return $this->hasMany(CommentImage::class);
    }
    
}
