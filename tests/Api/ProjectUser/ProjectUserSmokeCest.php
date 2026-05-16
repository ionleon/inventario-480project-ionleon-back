<?php

declare(strict_types=1);

namespace App\Tests\Api\ProjectUser;

use App\Shared\Domain\Model\ErrorCode;
use App\Tests\ApiTester;
use Codeception\Util\HttpCode;

final class ProjectUserSmokeCest
{
    public function _before(ApiTester $I): void
    {
        $I->haveAdminHttpHeaders();
    }

    public function listProjectUsersReturns200(ApiTester $I): void
    {
        $I->wantTo('list users assigned to a project seeded by fixtures');

        // Grab a project id from the list endpoint (paginated: {items: [{id:...}]})
        $I->sendGet('/480project/projects');
        $I->seeResponseCodeIs(HttpCode::OK);
        $projectIds = $I->grabDataFromResponseByJsonPath('$.items[0].id');
        $projectId = $projectIds[0] ?? null;
        \PHPUnit\Framework\Assert::assertNotNull($projectId, 'No projects found in fixtures');

        $I->haveAdminHttpHeaders();
        $I->sendGet('/480project/projects/' . $projectId . '/users');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
    }

    public function listProjectUsersWithInvalidProjectIdReturns400(ApiTester $I): void
    {
        $I->wantTo('reject malformed project id when listing project users');
        $I->sendGet('/480project/projects/not-a-uuid/users');
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();
        $I->seeResponseErrorCodeContent(ErrorCode::INVALID_PROJECT_ID->value);
    }
}
