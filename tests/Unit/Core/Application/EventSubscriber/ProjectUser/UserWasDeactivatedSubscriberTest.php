<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Application\EventSubscriber\ProjectUser;

use App\Core\Application\EventSubscriber\ProjectUser\UserWasDeactivatedSubscriber;
use App\Core\Domain\Model\Event\User\UserWasDeactivated;
use App\Core\Domain\Model\Repository\ProjectUserRepository;
use App\Tests\Unit\Core\Domain\Mother\ProjectUser\ProjectUserMother;
use App\Tests\Unit\Core\Domain\Mother\User\UserIdMother;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

final class UserWasDeactivatedSubscriberTest extends TestCase
{
    public function test_GivenEvent_WhenInvoke_ThenAllActiveProjectUsersAreDeactivated(): void
    {
        $userId = UserIdMother::create();
        $pu1 = ProjectUserMother::create(userId: $userId);
        $pu2 = ProjectUserMother::create(userId: $userId);

        self::assertTrue($pu1->isActive(), 'ProjectUser should start active');
        self::assertTrue($pu2->isActive(), 'ProjectUser should start active');

        $repo = $this->createMock(ProjectUserRepository::class);
        $repo->method('findActiveByUser')->with($userId)->willReturn([$pu1, $pu2]);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::once())->method('flush');

        (new UserWasDeactivatedSubscriber($repo, $em))(
            new UserWasDeactivated(id: $userId, occurredAt: new DateTimeImmutable()),
        );

        self::assertFalse($pu1->isActive());
        self::assertFalse($pu2->isActive());
    }

    public function test_GivenEventWithNoProjectUsers_WhenInvoke_ThenFlushIsStillCalled(): void
    {
        $userId = UserIdMother::create();

        $repo = $this->createMock(ProjectUserRepository::class);
        $repo->method('findActiveByUser')->willReturn([]);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::once())->method('flush');

        (new UserWasDeactivatedSubscriber($repo, $em))(
            new UserWasDeactivated(id: $userId, occurredAt: new DateTimeImmutable()),
        );

        $this->addToAssertionCount(1);
    }
}
