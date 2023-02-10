<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/auth', name: 'app.auth.')]
class AuthController extends AbstractController
{
    #[Route(path: '/login', name: 'login.view', methods: 'GET')]
    public function indexView(): Response
    {
        return $this->render('auth/login.html.twig');
    }
}
