<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Scheb\TwoFactorBundle\Security\Authentication\Token\TwoFactorTokenInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class AdminTwoFactorAuthenticationTest extends WebTestCase
{
    private const string ADMIN_EMAIL = 'admin-2fa@test.athena-dentaire.fr';
    private const string ADMIN_PASSWORD = 'TestPassword2026!';

    protected function tearDown(): void
    {
        parent::tearDown();
        static::ensureKernelShutdown();
    }

    public function testAdminLoginRequiresTwoFactorCode(): void
    {
        $client = $this->bootClientWithDatabase();
        $this->createAdminUser($client);

        $entityManager = $client->getContainer()->get(EntityManagerInterface::class);
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => self::ADMIN_EMAIL]);
        self::assertInstanceOf(User::class, $user);
        self::assertTrue($user->isEmailAuthEnabled());

        $client->request('GET', '/admin/login');
        $client->submitForm('Se connecter', [
            '_username' => self::ADMIN_EMAIL,
            '_password' => self::ADMIN_PASSWORD,
        ]);

        $token = $client->getContainer()->get('security.token_storage')->getToken();
        self::assertInstanceOf(TwoFactorTokenInterface::class, $token);

        $client->followRedirect();
        if ($client->getResponse()->isRedirect()) {
            $client->followRedirect();
        }

        self::assertSelectorTextContains('h1', 'Vérification en deux étapes');

        $authCode = $this->getAuthCodeForUser($client);
        $client->submitForm('Valider', [
            '_auth_code' => $authCode,
        ]);

        self::assertResponseRedirects('/admin');
        $client->followRedirect();
        self::assertResponseIsSuccessful();
    }

    public function testInvalidTwoFactorCodeIsRejected(): void
    {
        $client = $this->bootClientWithDatabase();
        $this->createAdminUser($client);
        $this->loginUntilTwoFactorForm($client);

        $client->submitForm('Valider', [
            '_auth_code' => '000000',
        ]);

        self::assertResponseRedirects('/admin/2fa');
        $client->followRedirect();
        self::assertResponseIsSuccessful();
        self::assertSelectorExists('div.text-red-800');
    }

    public function testExpiredTwoFactorCodeIsRejected(): void
    {
        $client = $this->bootClientWithDatabase();
        $this->createAdminUser($client);
        $this->loginUntilTwoFactorForm($client);

        $entityManager = $client->getContainer()->get(EntityManagerInterface::class);
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => self::ADMIN_EMAIL]);
        self::assertInstanceOf(User::class, $user);

        $user->setAuthCodeExpiresAt(new \DateTimeImmutable('-1 minute'));
        $entityManager->flush();

        $client->submitForm('Valider', [
            '_auth_code' => $user->getEmailAuthCode(),
        ]);

        self::assertResponseRedirects('/admin/2fa');
        $client->followRedirect();
        self::assertResponseIsSuccessful();
        self::assertSelectorExists('div.text-red-800');
    }

    public function testAdminAlwaysRequiresTwoFactorEvenWhenDisabled(): void
    {
        $client = $this->bootClientWithDatabase();
        $this->createAdminUser($client, false);

        $client->request('GET', '/admin/login');
        $client->submitForm('Se connecter', [
            '_username' => self::ADMIN_EMAIL,
            '_password' => self::ADMIN_PASSWORD,
        ]);
        $client->followRedirect();

        self::assertResponseRedirects('/admin/2fa');
    }

    private function bootClientWithDatabase(): KernelBrowser
    {
        $client = static::createClient();
        $this->resetDatabase($client);

        return $client;
    }

    private function loginUntilTwoFactorForm(KernelBrowser $client): void
    {
        $client->request('GET', '/admin/login');
        $client->submitForm('Se connecter', [
            '_username' => self::ADMIN_EMAIL,
            '_password' => self::ADMIN_PASSWORD,
        ]);
        $client->followRedirect();
        if ($client->getResponse()->isRedirect()) {
            $client->followRedirect();
        }
    }

    private function getAuthCodeForUser(KernelBrowser $client): string
    {
        $entityManager = $client->getContainer()->get(EntityManagerInterface::class);
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => self::ADMIN_EMAIL]);
        self::assertInstanceOf(User::class, $user);

        return $user->getEmailAuthCode();
    }

    private function createAdminUser(KernelBrowser $client, bool $twoFactorEnabled = true): void
    {
        $entityManager = $client->getContainer()->get(EntityManagerInterface::class);
        $passwordHasher = $client->getContainer()->get(UserPasswordHasherInterface::class);

        $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => self::ADMIN_EMAIL]);
        if ($existingUser instanceof User) {
            $entityManager->remove($existingUser);
            $entityManager->flush();
        }

        $user = (new User())
            ->setEmail(self::ADMIN_EMAIL)
            ->setFullName('Admin Test 2FA')
            ->setRoles([User::ROLE_ADMIN])
            ->setEmailAuthEnabled($twoFactorEnabled);

        $user->setPassword($passwordHasher->hashPassword($user, self::ADMIN_PASSWORD));

        $entityManager->persist($user);
        $entityManager->flush();
    }

    private function resetDatabase(KernelBrowser $client): void
    {
        $entityManager = $client->getContainer()->get(EntityManagerInterface::class);
        $metadata = $entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($entityManager);
        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);
    }
}
