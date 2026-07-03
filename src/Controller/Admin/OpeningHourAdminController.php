<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\OpeningHour;
use App\Form\Admin\OpeningHourFormType;
use App\Repository\OpeningHourRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/horaires')]
#[IsGranted('ROLE_ADMIN')]
final class OpeningHourAdminController extends AbstractController
{
    #[Route('', name: 'admin_opening_hour_index')]
    public function index(OpeningHourRepository $repository): Response
    {
        return $this->render('admin/opening_hour/index.html.twig', [
            'hours' => $repository->findAllOrdered(),
        ]);
    }

    #[Route('/nouveau', name: 'admin_opening_hour_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        return $this->handleForm($request, $entityManager, new OpeningHour(), true);
    }

    #[Route('/{id}/modifier', name: 'admin_opening_hour_edit', requirements: ['id' => '\d+'])]
    public function edit(OpeningHour $openingHour, Request $request, EntityManagerInterface $entityManager): Response
    {
        return $this->handleForm($request, $entityManager, $openingHour, false);
    }

    #[Route('/{id}/supprimer', name: 'admin_opening_hour_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(OpeningHour $openingHour, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$openingHour->getId(), (string) $request->request->get('_token'))) {
            $entityManager->remove($openingHour);
            $entityManager->flush();
            $this->addFlash('success', 'Horaire supprimé.');
        }

        return $this->redirectToRoute('admin_opening_hour_index');
    }

    private function handleForm(Request $request, EntityManagerInterface $entityManager, OpeningHour $openingHour, bool $isNew): Response
    {
        $form = $this->createForm(OpeningHourFormType::class, $openingHour);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($openingHour);
            $entityManager->flush();
            $this->addFlash('success', 'Horaire enregistré.');

            return $this->redirectToRoute('admin_opening_hour_index');
        }

        return $this->render('admin/opening_hour/form.html.twig', [
            'form' => $form,
            'is_new' => $isNew,
        ]);
    }
}
