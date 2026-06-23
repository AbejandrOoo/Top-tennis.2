<?php

namespace App\Enums;

enum Rol: string {
    case Admin = 'admin';
    case Recepcionista = 'recepcionista';
    case Cliente = 'cliente';
}