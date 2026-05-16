<?php

declare(strict_types=1);

namespace App\Tests\Api\User;

use App\Shared\Domain\Model\ErrorCode;
use App\Tests\ApiTester;
use Codeception\Util\HttpCode;

final class UserSmokeCest
{
    public function _before(ApiTester $I): void
    {
        $I->haveAdminHttpHeaders();
    }

    public function listUsersReturns200(ApiTester $I): void
    {
        $I->wantTo('list users as admin');
        $I->sendGet('/480project/users');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
    }

    public function getUserWithInvalidIdReturns400(ApiTester $I): void
    {
        $I->wantTo('reject malformed user id');
        $I->sendGet('/480project/users/not-a-uuid');
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();
        $I->seeResponseErrorCodeContent(ErrorCode::INVALID_USER_ID->value);
    }
}
