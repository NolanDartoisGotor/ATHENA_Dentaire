<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Service;
use App\Form\Admin\ServiceFormType;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/services')]
#[IsGranted('ROLE_ADMIN')]
final class ServiceAdminController extends AbstractController
{
    #[Route('', name: 'admin_service_index')]
    public function index(ServiceRepository $repository): Response
    {
        return $this->render('admin/service/index.html.twig', [
            'services' => $repository->findBy([], ['position' => 'ASC']),
        ]);
    }

    #[Route('/nouveau', name: 'admin_service_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        return $this->handleForm($request, $entityManager, new Service(), true);
    }

    #[Route('/{id}/modifier', name: 'admin_service_edit', requirements: ['id' => '\d+'])]
    public function edit(Service $service, Request $request, EntityManagerInterface $entityManager): Response
    {
        return $this->handleForm($request, $entityManager, $service, false);
    }

    #[Route('/{id}/supprimer', name: 'admin_service_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Service $service, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$service->getId(), (string) $request->request->get('_token'))) {
            $entityManager->remove($service);
            $entityManager->flush();
            $this->addFlash('success', 'Service supprimé.');
        }

        return $this->redirectToRoute('admin_service_index');
    }

    private function handleForm(Request $request, EntityManagerInterface $entityManager, Service $service, bool $isNew): Response
    {
        $form = $this->createForm(ServiceFormType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($service);
            $entityManager->flush();
            $this->addFlash('success', 'Service enregistré.');

            return $this->redirectToRoute('admin_service_index');
        }

        return $this->render('admin/service/form.html.twig', [
            'form' => $form,
            'is_new' => $isNew,
        ]);
    }
}
