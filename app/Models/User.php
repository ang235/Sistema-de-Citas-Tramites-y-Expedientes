<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Proxy / alias de compatibilidad con Laravel para el modelo Usuario.
 */
class User extends Usuario
{
    // Hereda todas las propiedades y métodos de Usuario adaptado a SETC
}
