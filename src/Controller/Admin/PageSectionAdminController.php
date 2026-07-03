<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\PageSection;
use App\Form\Admin\PageSectionFormType;
use App\Repository\PageSectionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/sections')]
#[IsGranted('ROLE_ADMIN')]
final class PageSectionAdminController extends AbstractController
{
    #[Route('', name: 'admin_section_index')]
    public function index(PageSectionRepository $repository): Response
    {
        $legalKeys = [PageSection::KEY_LEGAL_NOTICE, PageSection::KEY_PRIVACY_POLICY];
        $sections = array_values(array_filter(
            $repository->findBy([], ['sectionKey' => 'ASC']),
            static fn (PageSection $section): bool => !in_array($section->getSectionKey(), $legalKeys, true),
        ));

        return $this->render('admin/section/index.html.twig', [
            'sections' => $sections,
        ]);
    }

    #[Route('/{id}/modifier', name: 'admin_section_edit', requirements: ['id' => '\d+'])]
    public function edit(
        PageSection $section,
        Request $request,
        EntityManagerInterface $entityManager,
    ): Response {
        $form = $this->createForm(PageSectionFormType::class, $section);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Section enregistrée.');

            return $this->redirectToRoute('admin_section_index');
        }

        return $this->render('admin/section/form.html.twig', [
            'form' => $form,
            'section' => $section,
        ]);
    }
}
