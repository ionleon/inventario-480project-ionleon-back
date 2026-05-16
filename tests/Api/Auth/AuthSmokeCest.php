<?php

declare(strict_types=1);

namespace App\Tests\Api\Auth;

use App\Tests\ApiTester;
use Codeception\Util\HttpCode;

final class AuthSmokeCest
{
    /**
     * Login returns JWT token and refresh_token.
     * Fixtures seed admin@example.com with password 'password1234'.
     */
    public function loginReturnsTokenAndRefreshToken(ApiTester $I): void
    {
        $I->wantTo('obtain JWT and refresh_token via login');
        $I->haveHttpHeader('Content-Type', 'application/json');

        $I->sendPost('/480project/login', [
            'email' => 'admin@example.com',
            'password' => 'password1234',
        ]);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        // Assert that a 'token' field exists in the response
        $token = $I->grabDataFromResponseByJsonPath('$.token');
        \PHPUnit\Framework\Assert::assertNotEmpty($token, 'Expected a JWT token in login response');

        // Extract refresh token and use it
        $refreshToken = $I->grabDataFromResponseByJsonPath('$.refresh_token')[0] ?? null;
        if ($refreshToken !== null) {
            $I->haveHttpHeader('Content-Type', 'application/json');
            $I->sendPost('/480project/token/refresh', [
                'refresh_token' => $refreshToken,
            ]);
            $I->seeResponseCodeIs(HttpCode::OK);
            $I->seeResponseIsJson();
        }
    }

    public function tokenRefreshWithBogusTokenReturnsUnauthorized(ApiTester $I): void
    {
        $I->wantTo('reject bogus refresh token');
        $I->haveHttpHeader('Content-Type', 'application/json');

        $I->sendPost('/480project/token/refresh', [
            'refresh_token' => 'bogus-invalid-token-value',
        ]);

        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
    }
}
