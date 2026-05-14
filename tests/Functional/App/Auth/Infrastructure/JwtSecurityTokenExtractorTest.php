<?php

declare(strict_types=1);

namespace App\Tests\Functional\App\Auth\Infrastructure;

use App\App\Auth\Domain\Service\SecurityTokenExtractorInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class JwtSecurityTokenExtractorTest extends KernelTestCase
{
    public function test_GivenNoAuthenticatedUser_WhenInvoke_ThenReturnsAnonymousToken(): void
    {
        self::bootKernel();
        $extractor = self::getContainer()->get(SecurityTokenExtractorInterface::class);

        $token = $extractor();

        self::assertSame('anonymous', $token->authUserId);
    }
}
