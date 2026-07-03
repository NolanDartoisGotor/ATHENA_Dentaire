<?php

declare(strict_types=1);

namespace App\Security\TwoFactor;

use App\Entity\User;
use Scheb\TwoFactorBundle\Mailer\AuthCodeMailerInterface;
use Scheb\TwoFactorBundle\Model\Email\TwoFactorInterface;
use Scheb\TwoFactorBundle\Model\PersisterInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Email\Generator\CodeGeneratorInterface;

use function random_int;

final class ExpiringEmailCodeGenerator implements CodeGeneratorInterface
{
    public function __construct(
        private readonly PersisterInterface $persister,
        private readonly AuthCodeMailerInterface $mailer,
        private readonly int $digits = 6,
        private readonly int $ttlSeconds = 600,
    ) {
    }

    public function generateAndSend(TwoFactorInterface $user): void
    {
        $min = 10 ** ($this->digits - 1);
        $max = 10 ** $this->digits - 1;
        $code = (string) random_int($min, $max);

        $user->setEmailAuthCode($code);

        if ($user instanceof User) {
            $user->setAuthCodeExpiresAt(new \DateTimeImmutable(sprintf('+%d seconds', $this->ttlSeconds)));
        }

        $this->persister->persist($user);
        $this->mailer->sendAuthCode($user);
    }
}
