<?php

namespace App\Shared\Domain\Enum;

enum Enviroment : string
{
    case STAGE = 'STAGE';
    case PREPRODUCTION = 'PREPRODUCTION';
    case PRODUCTION = 'PRODUCTION';
}
