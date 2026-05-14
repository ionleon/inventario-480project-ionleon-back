<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Service\Sector;

use App\Core\Domain\Exception\Sector\DuplicatedSectorNameException;
use App\Core\Domain\Model\Repository\SectorRepository;
use App\Core\Domain\Service\Sector\CreateSector\CreateSectorService;
use App\Tests\Unit\Core\Domain\Mother\Sector\SectorIdMother;
use App\Tests\Unit\Core\Domain\Mother\Sector\SectorMother;
use App\Tests\Unit\Core\Domain\Mother\Sector\SectorNameMother;
use PHPUnit\Framework\TestCase;

final class CreateSectorServiceTest extends TestCase
{
    public function test_GivenUniqueName_WhenInvoke_ThenAggregateIsAdded(): void
    {
        $repo = $this->createMock(SectorRepository::class);
        $repo->method('findOneByName')->willReturn(null);
        $repo->expects(self::once())->method('add');

        $service = new CreateSectorService($repo);
        $service(SectorIdMother::create(), SectorNameMother::create());
    }

    public function test_GivenDuplicatedName_WhenInvoke_ThenThrowsDuplicatedSectorNameException(): void
    {
        $existing = SectorMother::create();
        $repo = $this->createMock(SectorRepository::class);
        $repo->method('findOneByName')->willReturn($existing);
        $repo->expects(self::never())->method('add');

        $this->expectException(DuplicatedSectorNameException::class);

        (new CreateSectorService($repo))(
            SectorIdMother::create(),
            SectorNameMother::create(),
        );
    }
}
