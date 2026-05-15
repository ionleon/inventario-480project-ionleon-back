<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Service\Sector;

use App\Core\Domain\Exception\Sector\SectorNotFoundException;
use App\Core\Domain\Model\Repository\SectorRepository;
use App\Core\Domain\Service\Sector\DeleteSector\DeleteSectorService;
use App\Tests\Unit\Core\Domain\Mother\Sector\SectorIdMother;
use App\Tests\Unit\Core\Domain\Mother\Sector\SectorMother;
use PHPUnit\Framework\TestCase;

final class DeleteSectorServiceTest extends TestCase
{
    public function test_GivenExistingSector_WhenInvoke_ThenSectorIsRemoved(): void
    {
        $id = SectorIdMother::create();
        $sector = SectorMother::create(id: $id);

        $repo = $this->createMock(SectorRepository::class);
        $repo->method('findOneOrFail')->willReturn($sector);
        $repo->expects(self::once())->method('remove')->with($sector);

        $service = new DeleteSectorService($repo);
        $service($id);
    }

    public function test_GivenNonExistentSector_WhenInvoke_ThenThrowsSectorNotFoundException(): void
    {
        $repo = $this->createMock(SectorRepository::class);
        $repo->method('findOneOrFail')->willThrowException(new SectorNotFoundException());

        $this->expectException(SectorNotFoundException::class);

        (new DeleteSectorService($repo))(SectorIdMother::create());
    }
}
