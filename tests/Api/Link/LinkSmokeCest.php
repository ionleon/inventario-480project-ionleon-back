<?php

declare(strict_types=1);

namespace App\Tests\Api\Link;

use App\Shared\Domain\Model\ErrorCode;
use App\Tests\Support\ApiTester;
use Codeception\Util\HttpCode;

final class LinkSmokeCest
{
    public function _before(ApiTester $I): void
    {
        $I->haveAdminHttpHeaders();
    }

    public function listLinksByProjectReturns200(ApiTester $I): void
    {
        $I->wantTo('list links for a project seeded by fixtures');

        // Grab a project id from the list endpoint (paginated: {items: [{id:...}]})
        $I->sendGet('/480project/projects');
        $I->seeResponseCodeIs(HttpCode::OK);
        $projectIds = $I->grabDataFromResponseByJsonPath('$.items[0].id');
        $projectId = $projectIds[0] ?? null;
        \PHPUnit\Framework\Assert::assertNotNull($projectId, 'No projects found in fixtures');

        $I->haveAdminHttpHeaders();
        $I->sendGet('/480project/projects/' . $projectId . '/links');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
    }

    public function deleteLinkWithInvalidIdReturns400(ApiTester $I): void
    {
        $I->wantTo('reject malformed link id on delete');
        $I->sendDelete('/480project/links/not-a-uuid');
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();
        $I->seeResponseErrorCodeContent(ErrorCode::INVALID_LINK_ID->value);
    }
}
