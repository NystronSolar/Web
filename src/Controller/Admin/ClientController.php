<?php

namespace App\Controller\Admin;

use App\Repository\ClientRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/clients', name: 'app.admin.clients.')]
class ClientController extends AbstractController
{
    #[Route(path: '/', name: 'index', methods: 'GET')]
    public function index(Request $request, ClientRepository $clientRepository): Response
    {
        $clients = $clientRepository->findAll();

        $clients = array_map(fn ($c) => $c->toArray(true), $clients);

        return $this->render('admin/clients/index.html.twig', ['clients' => $clients]);
    }

    #[Route(path: '/new', name: 'new')]
    public function new(Request $request, ClientRepository $clientRepository): Response
    {
    }

    #[Route(path: '/{book}', name: 'show', methods: 'GET')]
    public function show(Request $request, ClientRepository $clientRepository): Response
    {
    }

    #[Route(path: '/{book}', name: 'destroy', methods: 'DELETE')]
    public function destroy(Request $request, ClientRepository $clientRepository): Response
    {
    }
}
