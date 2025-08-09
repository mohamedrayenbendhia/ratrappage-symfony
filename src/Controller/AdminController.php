<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        /** @var User $user */
        $user = $this->getUser();

        return match ($user->isVerified()) {
            true => $this->render("admin/index.html.twig"),
            false => $this->render("admin/please-verify-email.html.twig"),
        };
    }

    #[Route('/admin/test', name: 'app_admin_test')]
    public function test(): Response
    {
        $user = $this->getUser();
        return new Response(
            'Super Admin connecté ! Email: ' . $user->getEmail() . 
            ' - Rôles: ' . implode(', ', $user->getRoles())
        );
    }
}
