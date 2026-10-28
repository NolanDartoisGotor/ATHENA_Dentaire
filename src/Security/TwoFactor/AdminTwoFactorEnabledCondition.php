<?php

declare(strict_types=1);

namespace App\Security\TwoFactor;

use Scheb\TwoFactorBundle\Security\TwoFactor\AuthenticationContextInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Condition\TwoFactorConditionInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * Seule la valeur exacte "false" désactive le 2FA : toute autre valeur (y compris une faute de frappe) le laisse actif.
 */
final class AdminTwoFactorEnabledCondition implements TwoFactorConditionInterface
{
    public function __construct(
        #[Autowire(env: 'ADMIN_TWO_FACTOR_ENABLED')]
        private readonly string $enabled,
    ) {
    }

    public function shouldPerformTwoFactorAuthentication(AuthenticationContextInterface $context): bool
    {
        return $this->enabled !== 'false';
    }
}
