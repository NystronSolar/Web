<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\LoginFormType;
use App\Repository\ClientRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/auth', name: 'app.auth.')]
class AuthController extends AbstractController
{
    #[Route(path: '/login', name: 'login', methods: 'GET|POST')]
    public function loginView(Request $request, ClientRepository $clientRepository, UserPasswordHasherInterface $passwordHasher): Response
    {
        $form = $this->createForm(LoginFormType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var \App\Entity\Client $formClient */
            $formClient = $form->getData();
            $databaseClient = $clientRepository->findOneByEmail($formClient->getEmail());

            $validateLogin = $this->validateLogin($formClient, $databaseClient, $passwordHasher);

            if (!$validateLogin) {
                $this->sendDangerFlash('Login Failed! Email or Password Incorrect');

                return $this->redirectToRoute('app.auth.login');
            }

            $this->sendSuccessFlash(sprintf('Login Successful! Logged as **%s**', $databaseClient->getName()));

            return $this->redirectToRoute('app.dashboard.index');
        }

        return $this->render('auth/login.html.twig', ['loginForm' => $form->createView()]);
    }

    protected function validateLogin(Client $formClient, ?Client $databaseClient, UserPasswordHasherInterface $passwordHasher): bool
    {
        return !is_null($databaseClient) && $passwordHasher->isPasswordValid($databaseClient, $formClient->getPassword());
    }

    protected function sendSuccessFlash(string $message): void
    {
        $this->addFlash('success', $message);

        return;
    }

    protected function sendDangerFlash(string $message): void
    {
        $this->addFlash('danger', $message);

        return;
    }
}