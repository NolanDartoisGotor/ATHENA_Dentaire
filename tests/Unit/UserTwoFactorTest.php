<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

final class UserTwoFactorTest extends TestCase
{
    public function testEmailAuthCodeIsExpiredWhenDeadlinePassed(): void
    {
        $user = new User();
        $user->setEmail('admin@test.fr');
        $user->setFullName('Admin');
        $user->setEmailAuthCode('123456');
        $user->setAuthCodeExpiresAt(new \DateTimeImmutable('-1 minute'));

        self::assertTrue($user->isEmailAuthCodeExpired());
    }

    public function testEmailAuthCodeIsValidBeforeDeadline(): void
    {
        $user = new User();
        $user->setEmail('admin@test.fr');
        $user->setFullName('Admin');
        $user->setEmailAuthCode('123456');
        $user->setAuthCodeExpiresAt(new \DateTimeImmutable('+5 minutes'));

        self::assertFalse($user->isEmailAuthCodeExpired());
    }

    public function testTwoFactorIsForcedForAdminsOnly(): void
    {
        $admin = (new User())->setRoles([User::ROLE_ADMIN])->setEmailAuthEnabled(false);
        $editor = (new User())->setRoles([User::ROLE_EDITOR])->setEmailAuthEnabled(false);

        self::assertTrue($admin->isEmailAuthEnabled());
        self::assertFalse($editor->isEmailAuthEnabled());
    }
}
