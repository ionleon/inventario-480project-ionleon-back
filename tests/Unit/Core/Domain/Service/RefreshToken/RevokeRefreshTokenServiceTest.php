<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Service\RefreshToken;

use App\Core\Domain\Exception\RefreshToken\RefreshTokenNotFoundException;
use App\Core\Domain\Model\Repository\RefreshTokenRepository;
use App\Core\Domain\Service\RefreshToken\RevokeRefreshToken\RevokeRefreshTokenService;
use App\Tests\Unit\Core\Domain\Mother\RefreshToken\RefreshTokenMother;
use App\Tests\Unit\Core\Domain\Mother\RefreshToken\RefreshTokenValueMother;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class RevokeRefreshTokenServiceTest extends TestCase
{
    private RefreshTokenRepository&MockObject $repository;
    private RevokeRefreshTokenService $service;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(RefreshTokenRepository::class);
        $this->service = new RevokeRefreshTokenService($this->repository);
    }

    public function test_GivenExistingToken_WhenRevoke_ThenTokenRemoved(): void
    {
        $value = RefreshTokenValueMother::create('tokentorevoke');
        $token = RefreshTokenMother::create('tokentorevoke');
        $token->pullEvents();

        $this->repository->expects(self::once())
            ->method('findOneByValue')
            ->with($value)
            ->willReturn($token);

        $this->repository->expects(self::once())
            ->method('remove')
            ->with($token);

        ($this->service)($value);

        self::assertFalse($token->isValid());
    }

    public function test_GivenNonExistingToken_WhenRevoke_ThenNotFoundException(): void
    {
        $value = RefreshTokenValueMother::create('nonexistent');

        $this->repository->method('findOneByValue')->willReturn(null);

        $this->expectException(RefreshTokenNotFoundException::class);

        ($this->service)($value);
    }
}
