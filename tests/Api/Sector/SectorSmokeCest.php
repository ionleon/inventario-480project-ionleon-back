<?php

declare(strict_types=1);

namespace App\Tests\Api\Sector;

use App\Shared\Domain\Model\ErrorCode;
use App\Tests\Support\ApiTester;
use Codeception\Util\HttpCode;

final class SectorSmokeCest
{
    public function _before(ApiTester $I): void
    {
        $I->haveAdminHttpHeaders();
    }

    public function listSectorsReturns200(ApiTester $I): void
    {
        $I->wantTo('list sectors as admin');
        $I->sendGet('/480project/sectors');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
    }

    public function getSectorWithInvalidIdReturns400(ApiTester $I): void
    {
        $I->wantTo('reject malformed sector id');
        $I->sendGet('/480project/sectors/not-a-uuid');
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();
        $I->seeResponseErrorCodeContent(ErrorCode::INVALID_SECTOR_ID->value);
    }
}
