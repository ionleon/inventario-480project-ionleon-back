<?php

namespace App\Shared\Domain\Enum;

enum SystemRole: string
{
    case ADMIN = 'ROLE_ADMIN';
    case EMPLOYEE = 'ROLE_EMPLOYEE';
}
