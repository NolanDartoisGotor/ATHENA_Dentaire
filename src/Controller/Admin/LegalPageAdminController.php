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

#[Route('/admin/pages-legales')]
#[IsGranted('ROLE_ADMIN')]
final class LegalPageAdminController extends AbstractController
{
    private const LEGAL_KEYS = [
        PageSection::KEY_LEGAL_NOTICE,
        PageSection::KEY_PRIVACY_POLICY,
    ];

    #[Route('', name: 'admin_legal_index')]
    public function index(PageSectionRepository $repository): Response
    {
        $sections = [];
        foreach (self::LEGAL_KEYS as $key) {
            $section = $repository->findOneByKey($key);
            if ($section !== null) {
                $sections[] = $section;
            }
        }

        return $this->render('admin/legal/index.html.twig', [
            'sections' => $sections,
        ]);
    }

    #[Route('/{id}/modifier', name: 'admin_legal_edit', requirements: ['id' => '\d+'])]
    public function edit(
        PageSection $section,
        Request $request,
        EntityManagerInterface $entityManager,
    ): Response {
        if (!in_array($section->getSectionKey(), self::LEGAL_KEYS, true)) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(PageSectionFormType::class, $section, [
            'is_legal_page' => true,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Page enregistrée.');

            return $this->redirectToRoute('admin_legal_index');
        }

        return $this->render('admin/legal/form.html.twig', [
            'form' => $form,
            'section' => $section,
        ]);
    }
}
