<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Service\Sector;

use App\Core\Domain\Exception\Sector\DuplicatedSectorNameException;
use App\Core\Domain\Exception\Sector\SectorNotFoundException;
use App\Core\Domain\Model\Repository\SectorRepository;
use App\Core\Domain\Service\Sector\UpdateSector\UpdateSectorService;
use App\Tests\Unit\Core\Domain\Mother\Sector\SectorIdMother;
use App\Tests\Unit\Core\Domain\Mother\Sector\SectorMother;
use App\Tests\Unit\Core\Domain\Mother\Sector\SectorNameMother;
use PHPUnit\Framework\TestCase;

final class UpdateSectorServiceTest extends TestCase
{
    public function test_GivenExistingSector_WhenInvokeWithNewUniqueName_ThenSectorIsRenamed(): void
    {
        $id = SectorIdMother::create();
        $sector = SectorMother::create(id: $id, name: SectorNameMother::create('Finance'));

        $repo = $this->createMock(SectorRepository::class);
        $repo->method('findOneOrFail')->willReturn($sector);
        $repo->method('findOneByName')->willReturn(null);

        $service = new UpdateSectorService($repo);
        $service($id, SectorNameMother::create('Technology'));
    }

    public function test_GivenDuplicatedName_WhenInvoke_ThenThrowsDuplicatedSectorNameException(): void
    {
        $id = SectorIdMother::create();
        $sector = SectorMother::create(id: $id);
        $otherSector = SectorMother::create();

        $repo = $this->createMock(SectorRepository::class);
        $repo->method('findOneOrFail')->willReturn($sector);
        $repo->method('findOneByName')->willReturn($otherSector);

        $this->expectException(DuplicatedSectorNameException::class);

        (new UpdateSectorService($repo))($id, SectorNameMother::create());
    }

    public function test_GivenNonExistentSector_WhenInvoke_ThenThrowsSectorNotFoundException(): void
    {
        $repo = $this->createMock(SectorRepository::class);
        $repo->method('findOneOrFail')->willThrowException(new SectorNotFoundException());

        $this->expectException(SectorNotFoundException::class);

        (new UpdateSectorService($repo))(SectorIdMother::create(), SectorNameMother::create());
    }
}
