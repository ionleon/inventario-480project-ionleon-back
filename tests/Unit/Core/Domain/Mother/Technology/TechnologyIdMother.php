<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Mother\Technology;

use App\Core\Domain\Model\VO\Technology\TechnologyId;

final class TechnologyIdMother
{
    public static function create(?string $value = null): TechnologyId
    {
        return new TechnologyId($value ?? TechnologyId::generate()->__toString());
    }
}
