<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Service\RefreshToken;

use App\Core\Domain\Model\Repository\RefreshTokenRepository;
use App\Core\Domain\Service\RefreshToken\RevokeAllRefreshTokensForUser\RevokeAllRefreshTokensForUserService;
use App\Tests\Unit\Core\Domain\Mother\RefreshToken\RefreshTokenMother;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class RevokeAllRefreshTokensForUserServiceTest extends TestCase
{
    private RefreshTokenRepository&MockObject $repository;
    private RevokeAllRefreshTokensForUserService $service;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(RefreshTokenRepository::class);
        $this->service = new RevokeAllRefreshTokensForUserService($this->repository);
    }

    public function test_GivenUserWithTokens_WhenRevokeAll_ThenAllTokensRemoved(): void
    {
        $token1 = RefreshTokenMother::create('token1', 'user@example.com');
        $token2 = RefreshTokenMother::create('token2', 'user@example.com');
        $token1->pullEvents();
        $token2->pullEvents();

        $this->repository->expects(self::once())
            ->method('findByUsername')
            ->with('user@example.com')
            ->willReturn([$token1, $token2]);

        $this->repository->expects(self::exactly(2))
            ->method('remove');

        ($this->service)('user@example.com');

        self::assertFalse($token1->isValid());
        self::assertFalse($token2->isValid());
    }

    public function test_GivenUserWithNoTokens_WhenRevokeAll_ThenNoop(): void
    {
        $this->repository->method('findByUsername')->willReturn([]);
        $this->repository->expects(self::never())->method('remove');

        ($this->service)('unknown@example.com');
    }
}
