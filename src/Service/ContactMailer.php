<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\ContactMessage;
use App\Entity\SiteSettings;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

final class ContactMailer
{
    public function __construct(
        private readonly MailerInterface $mailer,
    ) {
    }

    public function send(ContactMessage $message, SiteSettings $settings): void
    {
        $email = (new Email())
            ->from($settings->getContactEmail())
            ->replyTo((string) $message->getEmail())
            ->to($settings->getRecipientEmail())
            ->subject(sprintf('[Athéna Dentaire] %s', $message->getSubject()))
            ->text(sprintf(
                "Nom : %s\nPrénom : %s\nEmail : %s\n\n%s",
                $message->getLastName(),
                $message->getFirstName(),
                $message->getEmail(),
                $message->getMessage(),
            ));

        $this->mailer->send($email);
    }
}
