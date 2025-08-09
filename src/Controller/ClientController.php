<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/client')]
class ClientController extends AbstractController
{
    #[Route('/', name: 'app_client_dashboard')]
    public function dashboard(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        
        return $this->render('client/dashboard.html.twig', [
            'user' => $this->getUser(),
        ]);
    }
}
