<?php

namespace App\Models;

use App\Models\Route;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array
     */
    protected $fillable = ['name', 'code'];
    
    /**
     * Indica si el modelo debe ser marcado con fechas automáticamente.
     *
     * @var bool
     */
    public $timestamps = false;
    
    /**
     * Obtiene las rutas asociadas a este país.
     */
    public function routes()
    {
        return $this->hasMany(Route::class);
    }
}
