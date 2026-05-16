<?php

declare(strict_types=1);

namespace App\Tests\Api\ProjectRole;

use App\Shared\Domain\Model\ErrorCode;
use App\Tests\ApiTester;
use Codeception\Util\HttpCode;

final class ProjectRoleSmokeCest
{
    public function _before(ApiTester $I): void
    {
        $I->haveAdminHttpHeaders();
    }

    public function listProjectRolesReturns200(ApiTester $I): void
    {
        $I->wantTo('list project roles as admin');
        $I->sendGet('/480project/project-roles');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
    }

    public function deleteProjectRoleWithInvalidIdReturns400(ApiTester $I): void
    {
        $I->wantTo('reject malformed project role id on delete');
        $I->sendDelete('/480project/project-roles/not-a-uuid');
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();
        $I->seeResponseErrorCodeContent(ErrorCode::INVALID_PROJECT_ROLE_ID->value);
    }
}
