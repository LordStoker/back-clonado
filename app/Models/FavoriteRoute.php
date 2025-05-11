<?php

namespace App\Models;

use App\Models\User;
use App\Models\Route;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FavoriteRoute extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'route_id',
    ];
    
    /**
     * Obtener el usuario al que pertenece este favorito
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Obtener la ruta marcada como favorita
     */
    public function route()
    {
        return $this->belongsTo(Route::class);
    }
}
