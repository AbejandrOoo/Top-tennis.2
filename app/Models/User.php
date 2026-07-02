<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// MODELO USER: USUARIO DEL SISTEMA CON 2 ROLES (admin, cliente)
// EL CAST 'rol' => Rol::class CONVIERTE EL STRING DE LA BD AL ENUM PHP AUTOMATICAMENTE
// EL CAST 'password' => 'hashed' HASHEA LA CONTRASEÑA (BCRYPT) AL ASIGNARLA
// RELACION: UN USER TIENE MUCHAS RESERVAS (1:N)
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'telefono',
        'password',
        'rol',
        'emoji_perfil',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'rol' => \App\Enums\Rol::class,
    ];

    public function reservas()
    {
        return $this->hasMany(Reserva::class);
    }
}