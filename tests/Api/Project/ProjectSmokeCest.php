<?php

declare(strict_types=1);

namespace App\Tests\Api\Project;

use App\Shared\Domain\Model\ErrorCode;
use App\Tests\Support\ApiTester;
use Codeception\Util\HttpCode;

final class ProjectSmokeCest
{
    public function _before(ApiTester $I): void
    {
        $I->haveAdminHttpHeaders();
    }

    public function listProjectsReturns200(ApiTester $I): void
    {
        $I->wantTo('list projects as admin');
        $I->sendGet('/480project/projects');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
    }

    public function getProjectWithInvalidIdReturns400(ApiTester $I): void
    {
        $I->wantTo('reject malformed project id');
        $I->sendGet('/480project/projects/not-a-uuid');
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();
        $I->seeResponseErrorCodeContent(ErrorCode::INVALID_PROJECT_ID->value);
    }
}
