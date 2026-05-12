<?php

declare(strict_types=1);

use App\Enum\SystemRole;
use App\Service\ProjectManager;
use App\Service\UserManager;

require dirname(__DIR__) . '/vendor/autoload.php';

function assert_true(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

$controllerContents = file_get_contents(dirname(__DIR__) . '/src/Controller/ProjectAssignmentController.php');
assert_true(
    is_string($controllerContents) && str_contains($controllerContents, 'assignUser($project, $user, ['),
    'BF-01 regression: assignUser is not called with array payload in ProjectAssignmentController::addUser().'
);

$corsConfigContents = file_get_contents(dirname(__DIR__) . '/config/packages/nelmio_cors.yaml');
assert_true(
    is_string($corsConfigContents) && preg_match("/\^\/480project\/[\s\S]*allow_methods:\s*\[[^\]]*'PATCH'/", $corsConfigContents) === 1,
    'BF-02 regression: PATCH is missing from /480project CORS allow_methods.'
);

$projectManagerReflection = new ReflectionClass(ProjectManager::class);
$projectManager = $projectManagerReflection->newInstanceWithoutConstructor();
$normalizeStartDate = $projectManagerReflection->getMethod('normalizeStartDate');
$normalizeStartDate->setAccessible(true);

$parsedDate = $normalizeStartDate->invoke($projectManager, '2026-05-12');
assert_true($parsedDate instanceof DateTime, 'BF-05 regression: start_date string does not normalize to DateTime.');
assert_true(
    $normalizeStartDate->invoke($projectManager, null) === null,
    'BF-05 regression: null start_date should normalize to null.'
);

try {
    $normalizeStartDate->invoke($projectManager, ['invalid']);
    throw new RuntimeException('BF-05 regression: non-string start_date should throw InvalidArgumentException.');
} catch (InvalidArgumentException) {
}

$userManagerReflection = new ReflectionClass(UserManager::class);
$userManager = $userManagerReflection->newInstanceWithoutConstructor();
$resolveRole = $userManagerReflection->getMethod('resolveRole');
$resolveRole->setAccessible(true);

assert_true(
    $resolveRole->invoke($userManager, 'ROLE_ADMIN') === SystemRole::ADMIN,
    'BF-06 regression: ROLE_ADMIN is not mapped to SystemRole::ADMIN.'
);
assert_true(
    $resolveRole->invoke($userManager, 'employee') === SystemRole::EMPLOYEE,
    'BF-06 regression: employee is not mapped to SystemRole::EMPLOYEE.'
);

try {
    $resolveRole->invoke($userManager, 'ROLE_UNKNOWN');
    throw new RuntimeException('BF-06 regression: invalid role should throw InvalidArgumentException.');
} catch (InvalidArgumentException) {
}

echo "P8.1 regression checks passed.\n";
