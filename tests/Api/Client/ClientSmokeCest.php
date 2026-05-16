<?php

declare(strict_types=1);

namespace App\Tests\Api\Client;

use App\Shared\Domain\Model\ErrorCode;
use App\Tests\Support\ApiTester;
use Codeception\Util\HttpCode;

final class ClientSmokeCest
{
    public function _before(ApiTester $I): void
    {
        $I->haveAdminHttpHeaders();
    }

    public function listClientsReturns200(ApiTester $I): void
    {
        $I->wantTo('list clients as admin');
        $I->sendGet('/480project/clients');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
    }

    public function getClientWithInvalidIdReturns400(ApiTester $I): void
    {
        $I->wantTo('reject malformed client id');
        $I->sendGet('/480project/clients/not-a-uuid');
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();
        $I->seeResponseErrorCodeContent(ErrorCode::INVALID_CLIENT_ID->value);
    }
}
