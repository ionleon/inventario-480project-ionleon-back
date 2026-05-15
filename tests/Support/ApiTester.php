<?php

declare(strict_types=1);

namespace App\Tests\Support;

use Codeception\Actor;

/**
 * @SuppressWarnings(PHPMD)
 */
class ApiTester extends Actor
{
    use _generated\ApiTesterActions;

    public function haveAdminHttpHeaders(string $language = 'es'): void
    {
        $this->haveHttpHeader('Content-Type', 'application/json');
        $this->haveHttpHeader('Api-Language', $language);
        // JWT generation helper added later in slice plans
    }

    public function seeResponseErrorCodeContent(string $expectedCode): void
    {
        $this->seeResponseContainsJson(['code' => $expectedCode]);
    }
}
