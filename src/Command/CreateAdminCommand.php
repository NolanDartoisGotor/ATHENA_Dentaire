<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\PasswordStrength;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommand(name: 'app:create-admin', description: 'Crée un compte administrateur (2FA par email activée)')]
final class CreateAdminCommand
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly ValidatorInterface $validator,
    ) {
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Argument('Adresse email de l\'administrateur')] string $email,
        #[Argument('Nom complet')] string $fullName,
    ): int {
        $password = (string) $io->askHidden('Mot de passe (12 caractères minimum)');

        $violations = $this->validator->validate($email, [new Email()]);
        $violations->addAll($this->validator->validate($password, [
            new Length(min: 12),
            new PasswordStrength(minScore: PasswordStrength::STRENGTH_MEDIUM),
        ]));

        if (\count($violations) > 0) {
            foreach ($violations as $violation) {
                $io->error((string) $violation->getMessage());
            }

            return Command::FAILURE;
        }

        if ($this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]) !== null) {
            $io->error('Un utilisateur avec cet email existe déjà.');

            return Command::FAILURE;
        }

        $user = (new User())
            ->setEmail($email)
            ->setFullName($fullName)
            ->setRoles([User::ROLE_ADMIN])
            ->setEmailAuthEnabled(true);
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success(sprintf('Administrateur %s créé.', $email));

        return Command::SUCCESS;
    }
}
