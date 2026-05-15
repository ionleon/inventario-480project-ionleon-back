<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Service\Link;

use App\Core\Domain\Exception\Link\LinkNotFoundException;
use App\Core\Domain\Model\Repository\LinkRepository;
use App\Core\Domain\Service\Link\DeleteLink\DeleteLinkService;
use App\Tests\Unit\Core\Domain\Mother\Link\LinkIdMother;
use App\Tests\Unit\Core\Domain\Mother\Link\LinkMother;
use PHPUnit\Framework\TestCase;

final class DeleteLinkServiceTest extends TestCase
{
    public function test_GivenExistingLink_WhenInvoke_ThenLinkIsRemoved(): void
    {
        $link = LinkMother::create();

        $linkRepo = $this->createMock(LinkRepository::class);
        $linkRepo->method('findOneOrFail')->willReturn($link);
        $linkRepo->expects(self::once())->method('remove')->with($link);

        (new DeleteLinkService($linkRepo))(LinkIdMother::create());
    }

    public function test_GivenLinkNotFound_WhenInvoke_ThenThrowsException(): void
    {
        $linkRepo = $this->createMock(LinkRepository::class);
        $linkRepo->method('findOneOrFail')->willThrowException(new LinkNotFoundException());

        $this->expectException(LinkNotFoundException::class);

        (new DeleteLinkService($linkRepo))(LinkIdMother::create());
    }
}
