<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\PageSection;
use App\Repository\SiteSettingsRepository;
use App\Service\PageSectionProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LegalController extends AbstractController
{
    #[Route('/mentions-legales', name: 'legal_notice')]
    public function legalNotice(
        SiteSettingsRepository $siteSettingsRepository,
        PageSectionProvider $pageSectionProvider,
    ): Response {
        return $this->renderLegalPage(
            $siteSettingsRepository,
            $pageSectionProvider,
            PageSection::KEY_LEGAL_NOTICE,
        );
    }

    #[Route('/politique-de-confidentialite', name: 'privacy_policy')]
    public function privacyPolicy(
        SiteSettingsRepository $siteSettingsRepository,
        PageSectionProvider $pageSectionProvider,
    ): Response {
        return $this->renderLegalPage(
            $siteSettingsRepository,
            $pageSectionProvider,
            PageSection::KEY_PRIVACY_POLICY,
        );
    }

    private function renderLegalPage(
        SiteSettingsRepository $siteSettingsRepository,
        PageSectionProvider $pageSectionProvider,
        string $sectionKey,
    ): Response {
        return $this->render('pages/legal.html.twig', [
            'settings' => $siteSettingsRepository->getSettings(),
            'section' => $pageSectionProvider->get($sectionKey),
        ]);
    }
}
