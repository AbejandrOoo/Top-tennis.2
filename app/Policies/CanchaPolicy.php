<?php

namespace App\Policies;

use App\Enums\Rol;
use App\Models\User;

class CanchaPolicy
{
    public function create(User $user): bool
    {
        return in_array($user->rol, [Rol::Admin, Rol::Recepcionista]);
    }

    public function update(User $user): bool
    {
        return in_array($user->rol, [Rol::Admin, Rol::Recepcionista]);
    }

    public function delete(User $user): bool
    {
        return $user->rol === Rol::Admin;
    }
}
