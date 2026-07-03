<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Form\Admin\SiteSettingsFormType;
use App\Repository\SiteSettingsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/parametres')]
#[IsGranted('ROLE_ADMIN')]
final class SiteSettingsAdminController extends AbstractController
{
    #[Route('', name: 'admin_settings')]
    public function edit(
        Request $request,
        SiteSettingsRepository $repository,
        EntityManagerInterface $entityManager,
    ): Response {
        $settings = $repository->getSettings();
        $form = $this->createForm(SiteSettingsFormType::class, $settings);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Paramètres enregistrés.');

            return $this->redirectToRoute('admin_settings');
        }

        return $this->render('admin/settings/form.html.twig', [
            'form' => $form,
        ]);
    }
}
