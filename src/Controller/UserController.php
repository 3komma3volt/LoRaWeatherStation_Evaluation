<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


final class UserController extends AbstractController
{
  #[Route('/login', name: 'app_login')]
    public function login(Request $request, AuthenticationUtils $utils): Response
    {
         if ($this->getUser()) {
            return $this->redirectToRoute('dashboard');        
        }

        $lastUsername = $utils->getLastUsername();
        $error = $utils->getLastAuthenticationError();
        return $this->render('user/login.html.twig', [
            'lastusername' => $lastUsername,
            'error' => $error,
        ]);
    }
    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        
    }
}
    