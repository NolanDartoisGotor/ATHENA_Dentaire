<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\TechnicalFacility;
use App\Form\Admin\TechnicalFacilityFormType;
use App\Repository\TechnicalFacilityRepository;
use App\Service\ImageUploadService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/plateau')]
#[IsGranted('ROLE_ADMIN')]
final class TechnicalFacilityAdminController extends AbstractController
{
    public function __construct(
        private readonly ImageUploadService $imageUploadService,
    ) {
    }

    #[Route('', name: 'admin_facility_index')]
    public function index(TechnicalFacilityRepository $repository): Response
    {
        return $this->render('admin/facility/index.html.twig', [
            'facilities' => $repository->findBy([], ['position' => 'ASC']),
        ]);
    }

    #[Route('/nouveau', name: 'admin_facility_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        return $this->handleForm($request, $entityManager, new TechnicalFacility(), true);
    }

    #[Route('/{id}/modifier', name: 'admin_facility_edit', requirements: ['id' => '\d+'])]
    public function edit(TechnicalFacility $facility, Request $request, EntityManagerInterface $entityManager): Response
    {
        return $this->handleForm($request, $entityManager, $facility, false);
    }

    #[Route('/{id}/supprimer', name: 'admin_facility_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(TechnicalFacility $facility, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$facility->getId(), (string) $request->request->get('_token'))) {
            $this->imageUploadService->deleteIfExists($facility->getImage());
            $entityManager->remove($facility);
            $entityManager->flush();
            $this->addFlash('success', 'Élément supprimé.');
        }

        return $this->redirectToRoute('admin_facility_index');
    }

    private function handleForm(Request $request, EntityManagerInterface $entityManager, TechnicalFacility $facility, bool $isNew): Response
    {
        $form = $this->createForm(TechnicalFacilityFormType::class, $facility);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile|null $imageFile */
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile instanceof UploadedFile) {
                $this->imageUploadService->deleteIfExists($facility->getImage());
                $facility->setImage($this->imageUploadService->upload(
                    $imageFile,
                    ImageUploadService::DIRECTORY_FACILITY,
                    $facility->getTitle() ?? 'facility',
                ));
            }

            $entityManager->persist($facility);
            $entityManager->flush();
            $this->addFlash('success', 'Élément enregistré.');

            return $this->redirectToRoute('admin_facility_index');
        }

        return $this->render('admin/facility/form.html.twig', [
            'form' => $form,
            'is_new' => $isNew,
            'facility' => $facility,
        ]);
    }
}
