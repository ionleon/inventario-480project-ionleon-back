<?php

declare(strict_types=1);

namespace App\Tests\Api\TimeEntry;

use App\Shared\Domain\Model\ErrorCode;
use App\Tests\Support\ApiTester;
use Codeception\Util\HttpCode;

final class TimeEntrySmokeCest
{
    public function _before(ApiTester $I): void
    {
        $I->haveAdminHttpHeaders();
    }

    public function listTimeEntriesByProjectReturns200(ApiTester $I): void
    {
        $I->wantTo('list time entries for a project seeded by fixtures');

        // Grab a project id from the list endpoint (paginated: {items: [{id:...}]})
        $I->sendGet('/480project/projects');
        $I->seeResponseCodeIs(HttpCode::OK);
        $projectIds = $I->grabDataFromResponseByJsonPath('$.items[0].id');
        $projectId = $projectIds[0] ?? null;
        \PHPUnit\Framework\Assert::assertNotNull($projectId, 'No projects found in fixtures');

        $I->haveAdminHttpHeaders();
        $I->sendGet('/480project/projects/' . $projectId . '/time-entries');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
    }

    public function getTimeEntryWithInvalidIdReturns400(ApiTester $I): void
    {
        $I->wantTo('reject malformed time entry id');
        $I->sendGet('/480project/time-entries/not-a-uuid');
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();
        $I->seeResponseErrorCodeContent(ErrorCode::INVALID_TIME_ENTRY_ID->value);
    }
}
