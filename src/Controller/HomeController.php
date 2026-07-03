<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\ContactMessage;
use App\Entity\PageSection;
use App\Form\ContactFormType;
use App\Repository\OpeningHourRepository;
use App\Repository\ServiceRepository;
use App\Repository\SiteSettingsRepository;
use App\Repository\TeamMemberRepository;
use App\Repository\TechnicalFacilityRepository;
use App\Service\ContactMailer;
use App\Service\PageSectionProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(
        Request $request,
        SiteSettingsRepository $siteSettingsRepository,
        PageSectionProvider $pageSectionProvider,
        OpeningHourRepository $openingHourRepository,
        TechnicalFacilityRepository $technicalFacilityRepository,
        ServiceRepository $serviceRepository,
        TeamMemberRepository $teamMemberRepository,
        EntityManagerInterface $entityManager,
        ContactMailer $contactMailer,
        #[Autowire(service: 'limiter.contact_form')]
        RateLimiterFactory $contactFormLimiter,
    ): Response {
        $settings = $siteSettingsRepository->getSettings();
        $message = new ContactMessage();
        $form = $this->createForm(ContactFormType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $limiter = $contactFormLimiter->create($request->getClientIp() ?? 'unknown');
            if (!$limiter->consume()->isAccepted()) {
                $this->addFlash('error', 'Trop de demandes. Veuillez réessayer plus tard.');

                return $this->redirectToRoute('home', ['_fragment' => 'contact']);
            }

            $entityManager->persist($message);
            $entityManager->flush();
            $contactMailer->send($message, $settings);

            $this->addFlash('success', 'Votre message a bien été envoyé. Nous vous répondrons dans les meilleurs délais.');

            return $this->redirectToRoute('home', ['_fragment' => 'contact']);
        }

        return $this->render('pages/home.html.twig', [
            'settings' => $settings,
            'appointmentSection' => $pageSectionProvider->get(PageSection::KEY_APPOINTMENT),
            'emergencySection' => $pageSectionProvider->get(PageSection::KEY_EMERGENCY),
            'beforeConsultationSection' => $pageSectionProvider->get(PageSection::KEY_BEFORE_CONSULTATION),
            'technicalIntro' => $pageSectionProvider->get(PageSection::KEY_TECHNICAL_INTRO),
            'servicesIntro' => $pageSectionProvider->get(PageSection::KEY_SERVICES_INTRO),
            'teamIntro' => $pageSectionProvider->get(PageSection::KEY_TEAM_INTRO),
            'openingHours' => $openingHourRepository->findAllOrdered(),
            'facilities' => $technicalFacilityRepository->findPublishedOrdered(),
            'services' => $serviceRepository->findPublishedOrdered(),
            'teamMembers' => $teamMemberRepository->findPublishedOrdered(),
            'form' => $form,
        ]);
    }
}
