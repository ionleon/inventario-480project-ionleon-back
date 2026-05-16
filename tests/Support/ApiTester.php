<?php

declare(strict_types=1);

namespace App\Tests\Support;

use App\Core\Domain\Model\Aggregate\User;
use App\Core\Domain\Model\Repository\UserRepository;
use App\Shared\Domain\Enum\SystemRole;
use Codeception\Actor;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

/**
 * @SuppressWarnings(PHPMD)
 */
class ApiTester extends Actor
{
    use _generated\ApiTesterActions;

    public function haveAdminHttpHeaders(string $language = 'es'): void
    {
        $this->haveHttpHeader('Content-Type', 'application/json');
        $this->haveHttpHeader('Api-Language', $language);
        $this->amBearerAuthenticated($this->createTestJwtForRole(SystemRole::ADMIN));
    }

    public function haveEmployeeHttpHeaders(string $language = 'es'): void
    {
        $this->haveHttpHeader('Content-Type', 'application/json');
        $this->haveHttpHeader('Api-Language', $language);
        $this->amBearerAuthenticated($this->createTestJwtForRole(SystemRole::EMPLOYEE));
    }

    public function seeResponseErrorCodeContent(string $expectedCode): void
    {
        $this->seeResponseContainsJson(['code' => $expectedCode]);
    }

    private function createTestJwtForRole(SystemRole $role): string
    {
        /** @var JWTTokenManagerInterface $jwtManager */
        $jwtManager = $this->grabService(JWTTokenManagerInterface::class);

        /** @var UserRepository $userRepo */
        $userRepo = $this->grabService(UserRepository::class);

        // Find first active user with the requested role.
        // Fixtures should seed at least one admin and one employee.
        $users = $userRepo->all();
        $user = null;
        foreach ($users as $u) {
            if ($u instanceof User && $u->role() === $role && $u->isActive()) {
                $user = $u;
                break;
            }
        }

        if (!$user instanceof User) {
            throw new \RuntimeException('No active user with role ' . $role->value . ' found in test fixtures');
        }

        return $jwtManager->create($user);
    }
}
