<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use App\Service\SiteContentLoader;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly SiteContentLoader $siteContentLoader,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $this->siteContentLoader->load($manager);
        $this->loadUsers($manager);

        $manager->flush();
    }

    private function loadUsers(ObjectManager $manager): void
    {
        $admin = (new User())
            ->setEmail('admin@athena-dentaire.fr')
            ->setFullName('Administrateur Athéna')
            ->setRoles([User::ROLE_ADMIN]);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'AdminAthena2026!'));
        $manager->persist($admin);

        $editor = (new User())
            ->setEmail('editor@athena-dentaire.fr')
            ->setFullName('Éditeur Athéna')
            ->setRoles([User::ROLE_EDITOR]);
        $editor->setPassword($this->passwordHasher->hashPassword($editor, 'EditorAthena2026!'));
        $manager->persist($editor);
    }
}
