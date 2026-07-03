<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\Admin\UserFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/utilisateurs')]
#[IsGranted('ROLE_ADMIN')]
final class UserAdminController extends AbstractController
{
    #[Route('', name: 'admin_user_index')]
    public function index(UserRepository $repository): Response
    {
        return $this->render('admin/user/index.html.twig', [
            'users' => $repository->findAll(),
        ]);
    }

    #[Route('/nouveau', name: 'admin_user_new')]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
    ): Response {
        return $this->handleForm($request, $entityManager, $passwordHasher, new User(), true);
    }

    #[Route('/{id}/modifier', name: 'admin_user_edit', requirements: ['id' => '\d+'])]
    public function edit(
        User $user,
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
    ): Response {
        return $this->handleForm($request, $entityManager, $passwordHasher, $user, false);
    }

    #[Route('/{id}/supprimer', name: 'admin_user_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(User $user, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser() instanceof User && $this->getUser()->getId() === $user->getId()) {
            $this->addFlash('error', 'Vous ne pouvez pas supprimer votre propre compte.');

            return $this->redirectToRoute('admin_user_index');
        }

        if ($this->isCsrfTokenValid('delete'.$user->getId(), (string) $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
            $this->addFlash('success', 'Utilisateur supprimé.');
        }

        return $this->redirectToRoute('admin_user_index');
    }

    private function handleForm(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        User $user,
        bool $isNew,
    ): Response {
        $form = $this->createForm(UserFormType::class, $user, ['is_new' => $isNew]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            if (is_string($plainPassword) && $plainPassword !== '') {
                $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));
            }

            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Utilisateur enregistré.');

            return $this->redirectToRoute('admin_user_index');
        }

        return $this->render('admin/user/form.html.twig', [
            'form' => $form,
            'is_new' => $isNew,
        ]);
    }
}
