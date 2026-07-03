<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\TeamMember;
use App\Form\Admin\TeamMemberFormType;
use App\Repository\TeamMemberRepository;
use App\Service\ImageUploadService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/equipe')]
#[IsGranted('ROLE_ADMIN')]
final class TeamMemberAdminController extends AbstractController
{
    public function __construct(
        private readonly ImageUploadService $imageUploadService,
    ) {
    }

    #[Route('', name: 'admin_team_index')]
    public function index(TeamMemberRepository $repository): Response
    {
        return $this->render('admin/team/index.html.twig', [
            'members' => $repository->findBy([], ['position' => 'ASC']),
        ]);
    }

    #[Route('/nouveau', name: 'admin_team_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        return $this->handleForm($request, $entityManager, new TeamMember(), true);
    }

    #[Route('/{id}/modifier', name: 'admin_team_edit', requirements: ['id' => '\d+'])]
    public function edit(TeamMember $member, Request $request, EntityManagerInterface $entityManager): Response
    {
        return $this->handleForm($request, $entityManager, $member, false);
    }

    #[Route('/{id}/supprimer', name: 'admin_team_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(TeamMember $member, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$member->getId(), (string) $request->request->get('_token'))) {
            $this->imageUploadService->deleteIfExists($member->getPhoto());
            $entityManager->remove($member);
            $entityManager->flush();
            $this->addFlash('success', 'Membre supprimé.');
        }

        return $this->redirectToRoute('admin_team_index');
    }

    private function handleForm(Request $request, EntityManagerInterface $entityManager, TeamMember $member, bool $isNew): Response
    {
        $form = $this->createForm(TeamMemberFormType::class, $member);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile|null $photoFile */
            $photoFile = $form->get('photoFile')->getData();

            if ($photoFile instanceof UploadedFile) {
                $this->imageUploadService->deleteIfExists($member->getPhoto());
                $member->setPhoto($this->imageUploadService->upload(
                    $photoFile,
                    ImageUploadService::DIRECTORY_TEAM,
                    $member->getFullName() ?? 'membre',
                ));
            }

            $entityManager->persist($member);
            $entityManager->flush();
            $this->addFlash('success', 'Membre enregistré.');

            return $this->redirectToRoute('admin_team_index');
        }

        return $this->render('admin/team/form.html.twig', [
            'form' => $form,
            'is_new' => $isNew,
            'member' => $member,
        ]);
    }
}
