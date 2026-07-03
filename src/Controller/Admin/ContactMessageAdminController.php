<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\ContactMessage;
use App\Repository\ContactMessageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/messages')]
#[IsGranted('ROLE_EDITOR')]
final class ContactMessageAdminController extends AbstractController
{
    #[Route('', name: 'admin_message_index')]
    public function index(ContactMessageRepository $repository): Response
    {
        return $this->render('admin/message/index.html.twig', [
            'messages' => $repository->findLatest(50),
        ]);
    }

    #[Route('/{id}', name: 'admin_message_show', requirements: ['id' => '\d+'])]
    public function show(ContactMessage $message): Response
    {
        return $this->render('admin/message/show.html.twig', [
            'message' => $message,
        ]);
    }
}
