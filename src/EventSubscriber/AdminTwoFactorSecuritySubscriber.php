<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\User;
use Psr\Log\LoggerInterface;
use Scheb\TwoFactorBundle\Model\PersisterInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Event\EmailCodeEvents;
use Scheb\TwoFactorBundle\Security\TwoFactor\Event\TwoFactorAuthenticationEvent;
use Scheb\TwoFactorBundle\Security\TwoFactor\Event\TwoFactorAuthenticationEvents;
use Scheb\TwoFactorBundle\Security\TwoFactor\Event\TwoFactorCodeEvent;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

final class AdminTwoFactorSecuritySubscriber
{
    public function __construct(
        #[Autowire(service: 'limiter.admin_two_factor')]
        private readonly RateLimiterFactory $twoFactorLimiter,
        private readonly PersisterInterface $persister,
        private readonly LoggerInterface $logger,
    ) {
    }

    #[AsEventListener(event: TwoFactorAuthenticationEvents::ATTEMPT)]
    public function onAttempt(TwoFactorAuthenticationEvent $event): void
    {
        $ipLimiter = $this->twoFactorLimiter->create('ip:'.($event->getRequest()->getClientIp() ?? 'unknown'));
        // Per-account limit so rotating IPs does not allow brute-forcing a single code.
        $userLimiter = $this->twoFactorLimiter->create('user:'.$event->getToken()->getUserIdentifier());

        if (!$ipLimiter->consume()->isAccepted() || !$userLimiter->consume()->isAccepted()) {
            $this->logger->warning('Limite de tentatives 2FA atteinte', [
                'ip' => $event->getRequest()->getClientIp(),
                'user' => $event->getToken()->getUserIdentifier(),
            ]);

            throw new TooManyRequestsHttpException(900, 'Trop de tentatives. Réessayez dans 15 minutes.');
        }
    }

    #[AsEventListener(event: EmailCodeEvents::CHECK, priority: 256)]
    public function onEmailCodeCheck(TwoFactorCodeEvent $event): void
    {
        $user = $event->getUser();

        if (!$user instanceof User || !$user->isEmailAuthCodeExpired()) {
            return;
        }

        $user->clearEmailAuthCode();
        $this->persister->persist($user);

        throw new CustomUserMessageAuthenticationException('Le code de vérification a expiré. Reconnectez-vous pour en recevoir un nouveau.');
    }

    #[AsEventListener(event: EmailCodeEvents::INVALID)]
    public function onInvalidCode(TwoFactorCodeEvent $event): void
    {
        $this->logger->warning('Code 2FA invalide', [
            'user' => $event->getUser() instanceof User ? $event->getUser()->getUserIdentifier() : 'unknown',
        ]);
    }
}
