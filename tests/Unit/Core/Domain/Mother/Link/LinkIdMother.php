<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Mother\Link;

use App\Core\Domain\Model\VO\Link\LinkId;

final class LinkIdMother
{
    public static function create(?string $value = null): LinkId
    {
        return new LinkId($value ?? LinkId::generate()->__toString());
    }
}
