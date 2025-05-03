<?php

namespace App\Models;

use App\Models\Route;
use Illuminate\Database\Eloquent\Model;

class Terrain extends Model
{
    //
    public function routes()
    {
        return $this->hasMany(Route::class);
    }
}
