<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RouteImage extends Model
{
    function route()
    {
        return $this->belongsTo(Route::class);
    }
}
