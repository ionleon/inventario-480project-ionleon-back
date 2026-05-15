<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Mother\Link;

use App\Core\Domain\Model\VO\Link\LinkUrl;

final class LinkUrlMother
{
    public static function create(?string $value = null): LinkUrl
    {
        return new LinkUrl($value ?? 'https://example.com/' . uniqid());
    }
}
