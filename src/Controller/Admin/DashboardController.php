<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Repository\ContactMessageRepository;
use App\Repository\OpeningHourRepository;
use App\Repository\PageSectionRepository;
use App\Repository\ServiceRepository;
use App\Repository\TeamMemberRepository;
use App\Repository\TechnicalFacilityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_EDITOR')]
final class DashboardController extends AbstractController
{
    #[Route('', name: 'admin_dashboard')]
    public function index(
        ContactMessageRepository $contactMessageRepository,
        PageSectionRepository $pageSectionRepository,
        TechnicalFacilityRepository $technicalFacilityRepository,
        ServiceRepository $serviceRepository,
        TeamMemberRepository $teamMemberRepository,
        OpeningHourRepository $openingHourRepository,
    ): Response {
        return $this->render('admin/dashboard.html.twig', [
            'latestMessages' => $contactMessageRepository->findLatest(5),
            'messagesCount' => $contactMessageRepository->count([]),
            'sectionsCount' => $pageSectionRepository->count([]),
            'facilitiesCount' => $technicalFacilityRepository->count([]),
            'servicesCount' => $serviceRepository->count([]),
            'teamCount' => $teamMemberRepository->count([]),
            'hoursCount' => $openingHourRepository->count([]),
        ]);
    }
}
