<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Role;
use App\Models\Route;
use App\Models\Comment;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'last_name',
        'email',
        'password',
        'role_id',        
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function routes()
    {
        return $this->hasMany(Route::class);
    }

    public function comments(){
        
        return $this->hasMany(Comment::class);
    }
    
    /**
     * Rutas favoritas del usuario
     */
    public function favoriteRoutes()
    {
        return $this->hasMany(FavoriteRoute::class);
    }
    
    /**
     * Relación muchos a muchos para obtener las rutas favoritas del usuario
     */
    public function favorites()
    {
        return $this->belongsToMany(Route::class, 'favorite_routes')->withTimestamps();
    }

    /**
     * Envía la notificación de recuperación de contraseña.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
