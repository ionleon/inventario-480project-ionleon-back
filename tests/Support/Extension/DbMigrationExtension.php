<?php

declare(strict_types=1);

namespace App\Tests\Support\Extension;

use Codeception\Event\SuiteEvent;
use Codeception\Events;
use Codeception\Extension;

final class DbMigrationExtension extends Extension
{
    public static array $events = [
        Events::SUITE_BEFORE => 'beforeSuite',
    ];

    public function beforeSuite(SuiteEvent $event): void
    {
        if ($event->getSuite()->getName() !== 'Api') {
            return;
        }
        passthru('php bin/console doctrine:migrations:migrate --no-interaction --env=test 2>&1', $exit);
        if ($exit !== 0) {
            throw new \RuntimeException('Migrations failed for test DB (exit ' . $exit . ')');
        }
        passthru('php bin/console doctrine:fixtures:load --no-interaction --env=test 2>&1', $exit);
        if ($exit !== 0) {
            throw new \RuntimeException('Fixtures load failed (exit ' . $exit . ')');
        }
    }
}
