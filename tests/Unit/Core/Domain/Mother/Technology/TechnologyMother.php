<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Mother\Technology;

use App\Core\Domain\Model\Aggregate\Technology;
use App\Core\Domain\Model\VO\Technology\TechnologyId;
use App\Core\Domain\Model\VO\Technology\TechnologyName;

final class TechnologyMother
{
    public static function create(
        ?TechnologyId $id = null,
        ?TechnologyName $name = null,
    ): Technology {
        return Technology::create(
            id: $id ?? TechnologyIdMother::create(),
            name: $name ?? TechnologyNameMother::create(),
        );
    }
}
