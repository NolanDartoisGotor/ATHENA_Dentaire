<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use Scheb\TwoFactorBundle\Mailer\AuthCodeMailerInterface;
use Scheb\TwoFactorBundle\Model\Email\TwoFactorInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

final class AdminTwoFactorMailer implements AuthCodeMailerInterface
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly Environment $twig,
        private readonly string $securityEmailFrom,
    ) {
    }

    public function sendAuthCode(TwoFactorInterface $user): void
    {
        $authCode = $user->getEmailAuthCode();
        $recipient = $user->getEmailAuthRecipient();
        $expiresAt = $user instanceof User ? $user->getAuthCodeExpiresAt() : null;

        $htmlBody = $this->twig->render('emails/admin_2fa_code.html.twig', [
            'authCode' => $authCode,
            'expiresAt' => $expiresAt,
        ]);

        $textBody = sprintf(
            "Votre code de vérification Athéna Dentaire est : %s\n\nCe code expire dans 10 minutes.\n\nSi vous n'êtes pas à l'origine de cette demande, ignorez cet email.",
            $authCode,
        );

        $email = (new Email())
            ->from($this->securityEmailFrom)
            ->to($recipient)
            ->subject('[Athéna Dentaire] Code de vérification admin')
            ->text($textBody)
            ->html($htmlBody);

        $this->mailer->send($email);
    }
}
