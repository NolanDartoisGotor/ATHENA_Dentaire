<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\DataFixtures\AppFixtures;
use App\Entity\ContactMessage;
use App\Entity\User;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class AdminPagesTest extends WebTestCase
{
    private const array LIST_PAGES = [
        '/admin',
        '/admin/messages',
        '/admin/parametres',
        '/admin/pages-legales',
        '/admin/sections',
        '/admin/horaires',
        '/admin/plateau',
        '/admin/services',
        '/admin/equipe',
        '/admin/utilisateurs',
    ];

    public function testEveryAdminPageAndFormWorks(): void
    {
        $client = $this->bootClientWithFixtures();

        foreach (self::LIST_PAGES as $url) {
            $crawler = $client->request('GET', $url);
            self::assertResponseIsSuccessful($url);

            // Open every edit/new/show link of the list and resubmit its form unchanged.
            $links = $crawler->filter('.admin-content a')->links();
            foreach ($links as $link) {
                $href = parse_url($link->getUri(), PHP_URL_PATH);
                if (!preg_match('#^/admin/.+/(modifier|nouveau|\d+)$#', (string) $href)) {
                    continue;
                }

                $page = $client->click($link);
                self::assertResponseIsSuccessful($href);

                $form = $page->filter('form button[type=submit]');
                if ($form->count() === 0 || str_ends_with((string) $href, 'nouveau')) {
                    continue;
                }

                $client->submit($form->form());
                self::assertResponseRedirects(null, null, $href.' (submit)');
            }
        }
    }

    public function testDashboardShowsTotals(): void
    {
        $client = $this->bootClientWithFixtures();
        $entityManager = $client->getContainer()->get(EntityManagerInterface::class);
        for ($i = 0; $i < 7; ++$i) {
            $entityManager->persist((new ContactMessage())
                ->setFirstName('Jean')
                ->setLastName('Test'.$i)
                ->setEmail('jean@example.com')
                ->setSubject('Sujet')
                ->setMessage('Message'));
        }
        $entityManager->flush();

        $crawler = $client->request('GET', '/admin');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('[data-stat=messages]', '7');
        self::assertCount(5, $crawler->filter('table.admin-table tbody tr'));
    }

    public function testEditorSeesDashboardButNotAdminPages(): void
    {
        $client = $this->bootClientWithFixtures();
        $editor = $client->getContainer()->get(EntityManagerInterface::class)
            ->getRepository(User::class)->findOneBy(['email' => 'editor@athena-dentaire.fr']);
        self::assertInstanceOf(User::class, $editor);
        $client->loginUser($editor);

        $client->request('GET', '/admin');
        self::assertResponseIsSuccessful();
        self::assertSelectorNotExists('a[href="/admin/sections"]');

        $client->request('GET', '/admin/sections');
        self::assertResponseStatusCodeSame(403);
    }

    private function bootClientWithFixtures(): KernelBrowser
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $entityManager = $container->get(EntityManagerInterface::class);

        $schemaTool = new SchemaTool($entityManager);
        $metadata = $entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);

        (new ORMExecutor($entityManager, new ORMPurger()))->execute([$container->get(AppFixtures::class)]);

        $admin = $entityManager->getRepository(User::class)->findOneBy([], ['id' => 'ASC']);
        self::assertInstanceOf(User::class, $admin);
        self::assertContains(User::ROLE_ADMIN, $admin->getRoles());
        $client->loginUser($admin);

        return $client;
    }
}
