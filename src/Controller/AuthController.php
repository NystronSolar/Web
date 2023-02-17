<?php

namespace App\Controller;

use App\Form\LoginFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/auth', name: 'app.auth.')]
class AuthController extends AbstractController
{
    #[Route(path: '/login', name: 'login.view', methods: 'GET|POST')]
    public function loginView(Request $request): Response
    {
        $form = $this->createForm(LoginFormType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var \App\Entity\Client $client */
            $client = $form->getData();
            $name = $client->getName() ?? 'NULL';

            $this->addFlash('success', sprintf('Login Successful! Logged as %s', $name));

            return $this->redirectToRoute('app.dashboard.index');
        }

        return $this->render('auth/login.html.twig', ['loginForm' => $form->createView()]);
    }
}
