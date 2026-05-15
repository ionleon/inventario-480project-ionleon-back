<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Model\Aggregate;

use App\Core\Domain\Model\Aggregate\User;
use App\Core\Domain\Model\Event\User\UserPasswordWasChanged;
use App\Core\Domain\Model\Event\User\UserRoleWasChanged;
use App\Core\Domain\Model\Event\User\UserWasActivated;
use App\Core\Domain\Model\Event\User\UserWasCreated;
use App\Core\Domain\Model\Event\User\UserWasDeactivated;
use App\Core\Domain\Model\Event\User\UserWasUpdated;
use App\Shared\Domain\Enum\SystemRole;
use App\Tests\Unit\Core\Domain\Mother\Common\EmailMother;
use App\Tests\Unit\Core\Domain\Mother\Common\PasswordMother;
use App\Tests\Unit\Core\Domain\Mother\User\UserIdMother;
use App\Tests\Unit\Core\Domain\Mother\User\UserMother;
use App\Tests\Unit\Core\Domain\Mother\User\UserNameMother;
use App\Tests\Unit\Core\Domain\Mother\User\UserSurnameMother;
use PHPUnit\Framework\TestCase;

final class UserTest extends TestCase
{
    public function test_GivenValidVOs_WhenCreate_ThenFiresUserWasCreated(): void
    {
        $user = UserMother::create();
        $events = $user->pullEvents();

        self::assertCount(1, $events);
        self::assertInstanceOf(UserWasCreated::class, $events[0]);
    }

    public function test_GivenNewUser_WhenCreate_ThenIsActiveAndFirstTime(): void
    {
        $user = UserMother::create();

        self::assertTrue($user->isActive());
        self::assertTrue($user->firstTime());
    }

    public function test_GivenUser_WhenGetters_ThenReturnExpectedValues(): void
    {
        $id = UserIdMother::create('00000000-0000-4000-8000-000000000001');
        $email = EmailMother::create('test@example.com');
        $name = UserNameMother::create('Alice');
        $surname = UserSurnameMother::create('Smith');

        $user = User::create(
            id: $id,
            email: $email,
            name: $name,
            surname: $surname,
            password: PasswordMother::create(),
            role: SystemRole::EMPLOYEE,
        );

        self::assertSame('00000000-0000-4000-8000-000000000001', (string) $user->id());
        self::assertSame('test@example.com', (string) $user->email());
        self::assertSame('Alice', (string) $user->name());
        self::assertSame('Smith', (string) $user->surname());
        self::assertSame(SystemRole::EMPLOYEE, $user->role());
    }

    public function test_GivenUser_WhenChangePassword_ThenFiresEventAndSetsFirstTimeFalse(): void
    {
        $user = UserMother::create();
        $user->pullEvents(); // clear

        $user->changePassword(PasswordMother::create('$2y$10$newhash'));

        $events = $user->pullEvents();
        self::assertCount(1, $events);
        self::assertInstanceOf(UserPasswordWasChanged::class, $events[0]);
        self::assertFalse($user->firstTime());
    }

    public function test_GivenActiveUser_WhenDeactivate_ThenFiresDeactivatedEvent(): void
    {
        $user = UserMother::create();
        $user->pullEvents();

        $user->deactivate();

        $events = $user->pullEvents();
        self::assertCount(1, $events);
        self::assertInstanceOf(UserWasDeactivated::class, $events[0]);
        self::assertFalse($user->isActive());
    }

    public function test_GivenInactiveUser_WhenDeactivate_ThenNoEvent(): void
    {
        $user = UserMother::create();
        $user->pullEvents();
        $user->deactivate();
        $user->pullEvents(); // clear deactivate event

        $user->deactivate(); // idempotent

        self::assertCount(0, $user->pullEvents());
    }

    public function test_GivenInactiveUser_WhenActivate_ThenFiresActivatedEvent(): void
    {
        $user = UserMother::create();
        $user->pullEvents();
        $user->deactivate();
        $user->pullEvents();

        $user->activate();

        $events = $user->pullEvents();
        self::assertCount(1, $events);
        self::assertInstanceOf(UserWasActivated::class, $events[0]);
        self::assertTrue($user->isActive());
    }

    public function test_GivenActiveUser_WhenActivate_ThenNoEvent(): void
    {
        $user = UserMother::create();
        $user->pullEvents();

        $user->activate(); // idempotent

        self::assertCount(0, $user->pullEvents());
    }

    public function test_GivenActiveUser_WhenToggleActivation_ThenDeactivates(): void
    {
        $user = UserMother::create();
        $user->pullEvents();

        $user->toggleActivation();

        self::assertFalse($user->isActive());
    }

    public function test_GivenUser_WhenUpdateProfile_ThenFiresUpdatedEvent(): void
    {
        $user = UserMother::create();
        $user->pullEvents();

        $user->updateProfile(UserNameMother::create('Bob'), UserSurnameMother::create('Jones'));

        $events = $user->pullEvents();
        self::assertCount(1, $events);
        self::assertInstanceOf(UserWasUpdated::class, $events[0]);
        self::assertSame('Bob', (string) $user->name());
        self::assertSame('Jones', (string) $user->surname());
    }

    public function test_GivenUser_WhenChangeRole_ThenFiresRoleChangedEvent(): void
    {
        $user = UserMother::create(role: SystemRole::EMPLOYEE);
        $user->pullEvents();

        $user->changeRole(SystemRole::ADMIN);

        $events = $user->pullEvents();
        self::assertCount(1, $events);
        self::assertInstanceOf(UserRoleWasChanged::class, $events[0]);
        self::assertSame(SystemRole::EMPLOYEE, $events[0]->oldRole);
        self::assertSame(SystemRole::ADMIN, $events[0]->newRole);
        self::assertSame(SystemRole::ADMIN, $user->role());
    }

    public function test_GivenUser_WhenMarkAsReturning_ThenFirstTimeFalse(): void
    {
        $user = UserMother::create();
        self::assertTrue($user->firstTime());

        $user->markAsReturning();
        self::assertFalse($user->firstTime());
    }

    public function test_GivenUser_WhenGetUserIdentifier_ThenReturnsEmail(): void
    {
        $user = UserMother::create(email: EmailMother::create('test@example.com'));
        self::assertSame('test@example.com', $user->getUserIdentifier());
    }

    public function test_GivenUser_WhenGetRoles_ThenReturnsRoleValue(): void
    {
        $user = UserMother::create(role: SystemRole::ADMIN);
        self::assertSame(['ROLE_ADMIN'], $user->getRoles());
    }

    public function test_GivenUser_WhenResetPasswordByAdmin_ThenFiresEventAndFirstTimeTrue(): void
    {
        $user = UserMother::create();
        $user->changePassword(PasswordMother::create()); // firstTime becomes false
        $user->pullEvents();

        $user->resetPasswordByAdmin(PasswordMother::create('$2y$10$adminhash'));

        $events = $user->pullEvents();
        self::assertCount(1, $events);
        self::assertInstanceOf(UserPasswordWasChanged::class, $events[0]);
        self::assertTrue($user->firstTime());
    }
}
