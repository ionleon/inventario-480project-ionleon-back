<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Model\Aggregate;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;

/**
 * Guards the invariant that the $id property of every aggregate is set ONCE
 * at construction and never re-assigned afterwards by any public method.
 *
 * Background: the spec wants `private readonly XxxId $id`, but Doctrine + the
 * DataFixtures library don't tolerate readonly on $id in our setup (see
 * docs/superpowers/specs/2026-05-16-refactor-deviations.md #4). We dropped
 * `readonly` to unblock fixtures; this test guards the contract at the API
 * level instead.
 */
final class AggregateIdImmutabilityTest extends TestCase
{
    /**
     * @return iterable<string, array{class-string}>
     */
    public static function aggregateClassProvider(): iterable
    {
        $aggregateDir = __DIR__ . '/../../../../../../src/Core/Domain/Model/Aggregate';
        foreach (glob($aggregateDir . '/*.php') as $file) {
            $shortName = basename($file, '.php');
            yield $shortName => ['App\\Core\\Domain\\Model\\Aggregate\\' . $shortName];
        }
    }

    /** @dataProvider aggregateClassProvider */
    public function test_GivenAggregate_WhenInspected_ThenNoPublicMethodAssignsId(string $class): void
    {
        $reflection = new ReflectionClass($class);

        // Skip if the class has no $id property at all (e.g., a non-aggregate value class accidentally in the folder).
        if (!$reflection->hasProperty('id')) {
            self::markTestSkipped("$class has no \$id property; not an aggregate guarded by this test.");
        }

        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            // Skip inherited methods from AggregateRoot and Symfony interfaces.
            if ($method->getDeclaringClass()->getName() !== $class) {
                continue;
            }

            // Constructor is allowed to set $id; that's the only legit path.
            if ($method->isConstructor()) {
                continue;
            }

            $source = $this->methodSource($method);
            if ($source === null) {
                continue;
            }

            $pattern = '/\$this->id\s*=/';
            self::assertDoesNotMatchRegularExpression(
                $pattern,
                $source,
                "$class::{$method->getName()}() assigns \$this->id — id must be immutable post-construction."
            );
        }
    }

    private function methodSource(ReflectionMethod $method): ?string
    {
        $filename = $method->getFileName();
        if ($filename === false) {
            return null;
        }
        $start = $method->getStartLine();
        $end = $method->getEndLine();
        if ($start === false || $end === false) {
            return null;
        }
        $lines = file($filename);
        if ($lines === false) {
            return null;
        }
        return implode('', array_slice($lines, $start - 1, $end - $start + 1));
    }
}
