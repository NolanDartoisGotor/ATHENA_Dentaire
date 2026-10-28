<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Security\TwoFactor\AdminTwoFactorEnabledCondition;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Scheb\TwoFactorBundle\Security\TwoFactor\AuthenticationContextInterface;

final class AdminTwoFactorEnabledConditionTest extends TestCase
{
    #[DataProvider('flagValues')]
    public function testOnlyExactFalseDisablesTwoFactor(string $flag, bool $expected): void
    {
        $condition = new AdminTwoFactorEnabledCondition($flag);

        self::assertSame($expected, $condition->shouldPerformTwoFactorAuthentication($this->createStub(AuthenticationContextInterface::class)));
    }

    /** @return iterable<string, array{string, bool}> */
    public static function flagValues(): iterable
    {
        yield 'true' => ['true', true];
        yield 'false' => ['false', false];
        yield 'faute de frappe' => ['fals', true];
        yield 'valeur vide' => ['', true];
        yield 'zéro' => ['0', true];
    }
}
