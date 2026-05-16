<?php

declare(strict_types=1);

namespace App\Tests\Api\Contact;

use App\Shared\Domain\Model\ErrorCode;
use App\Tests\Support\ApiTester;
use Codeception\Util\HttpCode;

final class ContactSmokeCest
{
    public function _before(ApiTester $I): void
    {
        $I->haveAdminHttpHeaders();
    }

    public function listContactsByClientReturns200(ApiTester $I): void
    {
        $I->wantTo('list contacts for a client seeded by fixtures');

        // Grab a client id from the list endpoint (paginated: {items: [{id:...}]})
        $I->sendGet('/480project/clients');
        $I->seeResponseCodeIs(HttpCode::OK);
        $clients = $I->grabDataFromResponseByJsonPath('$.items[0].id');
        $clientId = $clients[0] ?? null;
        \PHPUnit\Framework\Assert::assertNotNull($clientId, 'No clients found in fixtures');

        $I->haveAdminHttpHeaders();
        $I->sendGet('/480project/clients/' . $clientId . '/contacts');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
    }

    public function listContactsWithInvalidClientIdReturns400(ApiTester $I): void
    {
        $I->wantTo('reject malformed client id when listing contacts');
        $I->sendGet('/480project/clients/not-a-uuid/contacts');
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();
        $I->seeResponseErrorCodeContent(ErrorCode::INVALID_CLIENT_ID->value);
    }
}
