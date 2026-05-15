<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Service\Technology;

use App\Core\Domain\Exception\Technology\DuplicatedTechnologyNameException;
use App\Core\Domain\Model\Repository\TechnologyRepository;
use App\Core\Domain\Service\Technology\CreateTechnology\CreateTechnologyService;
use App\Tests\Unit\Core\Domain\Mother\Technology\TechnologyIdMother;
use App\Tests\Unit\Core\Domain\Mother\Technology\TechnologyMother;
use App\Tests\Unit\Core\Domain\Mother\Technology\TechnologyNameMother;
use PHPUnit\Framework\TestCase;

final class CreateTechnologyServiceTest extends TestCase
{
    public function test_GivenUniqueName_WhenInvoke_ThenAggregateIsAdded(): void
    {
        $repo = $this->createMock(TechnologyRepository::class);
        $repo->method('findOneByName')->willReturn(null);
        $repo->expects(self::once())->method('add');

        $service = new CreateTechnologyService($repo);
        $service(TechnologyIdMother::create(), TechnologyNameMother::create());
    }

    public function test_GivenDuplicatedName_WhenInvoke_ThenThrowsDuplicatedTechnologyNameException(): void
    {
        $existing = TechnologyMother::create();
        $repo = $this->createMock(TechnologyRepository::class);
        $repo->method('findOneByName')->willReturn($existing);
        $repo->expects(self::never())->method('add');

        $this->expectException(DuplicatedTechnologyNameException::class);

        (new CreateTechnologyService($repo))(
            TechnologyIdMother::create(),
            TechnologyNameMother::create(),
        );
    }
}
