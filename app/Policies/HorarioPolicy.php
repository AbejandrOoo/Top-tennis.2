<?php

namespace App\Policies;

use App\Enums\Rol;
use App\Models\Horario;
use App\Models\User;

class HorarioPolicy
{
    // Admin ve todos; Cliente solo los suyos (manejado en controller)
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Horario $horario): bool
    {
        if ($user->rol === Rol::Admin) {
            return true;
        }

        return $horario->user_id === $user->id;
    }

    // Cualquier usuario autenticado puede crear un horario
    public function create(User $user): bool
    {
        return true;
    }

    // Admin edita cualquiera; Cliente solo los suyos en estado Reservado
    public function update(User $user, Horario $horario): bool
    {
        if ($user->rol === Rol::Admin) {
            return true;
        }

        return $horario->user_id === $user->id && $horario->estado === 'Reservado';
    }

    // Solo Admin puede eliminar (soft delete)
    public function delete(User $user, Horario $horario): bool
    {
        return $user->rol === Rol::Admin;
    }
}
