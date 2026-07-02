<?php

namespace App\Policies;

use App\Enums\Rol;
use App\Models\User;

class TarifaPolicy
{
    public function create(User $user): bool
    {
        return $user->rol === Rol::Admin;
    }

    public function update(User $user): bool
    {
        return $user->rol === Rol::Admin;
    }

    public function delete(User $user): bool
    {
        return $user->rol === Rol::Admin;
    }
}
