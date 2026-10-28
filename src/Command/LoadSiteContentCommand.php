<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\PageSectionRepository;
use App\Service\SiteContentLoader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:load-content', description: 'Charge le contenu de base du site (sans comptes ni données de test)')]
final class LoadSiteContentCommand
{
    public function __construct(
        private readonly SiteContentLoader $siteContentLoader,
        private readonly PageSectionRepository $pageSectionRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(SymfonyStyle $io): int
    {
        if ($this->pageSectionRepository->count([]) > 0) {
            $io->error('Du contenu existe déjà : rien n\'a été chargé. Vider les tables de contenu pour le recharger.');

            return 1;
        }

        $this->siteContentLoader->load($this->entityManager);
        $this->entityManager->flush();

        $io->success('Contenu de base chargé.');

        return 0;
    }
}
