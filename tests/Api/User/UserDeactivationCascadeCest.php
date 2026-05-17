<?php

declare(strict_types=1);

namespace App\Tests\Api\User;

use App\Core\Domain\Model\Aggregate\ProjectUser;
use App\Core\Domain\Model\Aggregate\User;
use App\Core\Domain\Model\Repository\ProjectUserRepository;
use App\Core\Domain\Model\Repository\UserRepository;
use App\Core\Domain\Model\VO\User\UserId;
use App\Shared\Domain\Enum\SystemRole;
use App\Tests\ApiTester;
use Codeception\Util\HttpCode;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Guards Plan 6's cross-aggregate event flow:
 * UserWasDeactivated → UserWasDeactivatedSubscriber → ProjectUser::deactivate()
 *
 * Setup: pick an active employee user that has at least one active ProjectUser
 * row. Toggle the user via PATCH /users/{id}. Assert all their active
 * ProjectUser rows are now inactive.
 */
final class UserDeactivationCascadeCest
{
    public function _before(ApiTester $I): void
    {
        $I->haveAdminHttpHeaders();
    }

    public function deactivatingAUserCascadesToTheirProjectUsers(ApiTester $I): void
    {
        $I->wantTo('cascade deactivation from User to ProjectUser via domain event');

        /** @var UserRepository $userRepo */
        $userRepo = $I->grabService(UserRepository::class);
        /** @var ProjectUserRepository $puRepo */
        $puRepo = $I->grabService(ProjectUserRepository::class);
        /** @var EntityManagerInterface $em */
        $em = $I->grabService(EntityManagerInterface::class);

        // Find ANY employee with ProjectUser rows (active or not) and prepare scenario.
        $targetUser = null;
        foreach ($userRepo->all() as $u) {
            if ($u->role() !== SystemRole::EMPLOYEE) {
                continue;
            }
            $allPus = $em->getRepository(ProjectUser::class)
                ->findBy(['userId' => $u->id()]);
            if ($allPus === []) {
                continue;
            }
            $targetUser = $u;
            break;
        }

        if ($targetUser === null) {
            throw new \RuntimeException('No employee with ProjectUser rows found in fixtures — cascade test cannot run.');
        }

        // Ensure the user is active and ALL its ProjectUsers are active for a meaningful cascade.
        if (!$targetUser->isActive()) {
            $targetUser->activate();
        }
        $allPus = $em->getRepository(ProjectUser::class)->findBy(['userId' => $targetUser->id()]);
        foreach ($allPus as $pu) {
            if (!$pu->isActive()) {
                $pu->activate();
            }
        }
        $em->flush();
        $em->clear();

        $userId = (string) $targetUser->id();

        // Sanity: confirm at least one active PU exists now
        $beforeActive = count($puRepo->findActiveByUser(new UserId($userId)));
        if ($beforeActive === 0) {
            throw new \RuntimeException("Setup failed: user $userId still has zero active ProjectUser rows before cascade test.");
        }

        // Act: deactivate via API
        $I->sendPatch('/480project/users/' . $userId);
        $I->seeResponseCodeIs(HttpCode::NO_CONTENT);

        // Assert: subscriber cascaded to deactivate all ProjectUser rows
        $em->clear();
        $afterActive = count($puRepo->findActiveByUser(new UserId($userId)));
        \PHPUnit\Framework\Assert::assertSame(
            0,
            $afterActive,
            "Cascade failed: $beforeActive ProjectUser rows were active before, expected 0 after — got $afterActive."
        );
    }
}
