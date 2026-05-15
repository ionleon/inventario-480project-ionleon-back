<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Service\Technology;

use App\Core\Domain\Exception\Technology\TechnologyNotFoundException;
use App\Core\Domain\Model\Repository\TechnologyRepository;
use App\Core\Domain\Service\Technology\DeleteTechnology\DeleteTechnologyService;
use App\Tests\Unit\Core\Domain\Mother\Technology\TechnologyIdMother;
use App\Tests\Unit\Core\Domain\Mother\Technology\TechnologyMother;
use PHPUnit\Framework\TestCase;

final class DeleteTechnologyServiceTest extends TestCase
{
    public function test_GivenExistingTechnology_WhenInvoke_ThenAggregateIsRemoved(): void
    {
        $technology = TechnologyMother::create();
        $repo = $this->createMock(TechnologyRepository::class);
        $repo->method('findOneOrFail')->willReturn($technology);
        $repo->expects(self::once())->method('remove');

        $service = new DeleteTechnologyService($repo);
        $service(TechnologyIdMother::create());
    }

    public function test_GivenNonExistingTechnology_WhenInvoke_ThenThrowsTechnologyNotFoundException(): void
    {
        $repo = $this->createMock(TechnologyRepository::class);
        $repo->method('findOneOrFail')->willThrowException(new TechnologyNotFoundException('some-id'));
        $repo->expects(self::never())->method('remove');

        $this->expectException(TechnologyNotFoundException::class);

        (new DeleteTechnologyService($repo))(TechnologyIdMother::create());
    }
}
