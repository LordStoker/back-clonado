<?php

namespace App\Models;

use App\Models\User;
use App\Models\Comment;
use App\Models\Country;
use App\Models\Terrain;
use App\Models\Landscape;
use App\Models\Difficulty;
use App\Models\RouteImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Testing\Fluent\Concerns\Has;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Route extends Model
{
    //
    use HasFactory, Notifiable;
    protected $fillable = [
        'name',
        'route_map',
        'description',
        'distance',
        'duration',
        'totalScore',
        'countScore',
        'country_id',
        'terrain_id',
        'difficulty_id',
        'landscape_id',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function country()
    {
        return $this->belongsTo(Country::class);
    }
    public function terrain()
    {
        return $this->belongsTo(Terrain::class);
    }
    public function difficulty()
    {
        return $this->belongsTo(Difficulty::class);
    }
    public function landscape()
    {
        return $this->belongsTo(Landscape::class);
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    public function routeImages()
    {
        return $this->hasOne(RouteImage::class);
    }
    

}
