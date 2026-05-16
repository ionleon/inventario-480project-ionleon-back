<?php

declare(strict_types=1);

namespace App\Tests\Api\Technology;

use App\Shared\Domain\Model\ErrorCode;
use App\Tests\Support\ApiTester;
use Codeception\Util\HttpCode;

final class TechnologySmokeCest
{
    public function _before(ApiTester $I): void
    {
        $I->haveAdminHttpHeaders();
    }

    public function listTechnologiesReturns200(ApiTester $I): void
    {
        $I->wantTo('list technologies as admin');
        $I->sendGet('/480project/technologies');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
    }

    public function getTechnologyWithInvalidIdReturns400(ApiTester $I): void
    {
        $I->wantTo('reject malformed technology id');
        $I->sendDelete('/480project/technologies/not-a-uuid');
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();
        $I->seeResponseErrorCodeContent(ErrorCode::INVALID_TECHNOLOGY_ID->value);
    }
}
