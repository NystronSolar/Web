<?php

namespace App\Controller\Admin;

use App\DataProvider\DayGenerationDataProvider;
use App\Entity\Client;
use App\Factory\ClientFactory;
use App\Form\NewClientFormType;
use App\Helper\Formatter;
use App\Repository\ClientRepository;
use App\Repository\DayGenerationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

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
    public function new(Request $request, ClientRepository $clientRepository, UserPasswordHasherInterface $passwordHasher): Response
    {
        $client = new Client();
        $form = $this->createForm(NewClientFormType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cpf = Formatter::removeStyleCPF($client->getCPF());

            $clientFactory = new ClientFactory($passwordHasher);
            $client = $clientFactory->update($client, [
                'Roles' => ['ROLE_USER'],
                'Password' => $client->getPassword(),
                'CPF' => $cpf,
            ]);

            $clientRepository->save($client, true);

            return $this->redirectToRoute('app.admin.clients.show', ['client' => $client->getId()]);
        }

        $errors = $form->getErrors(true);

        return $this->render('admin/clients/new.html.twig', [
            'form' => $form,
            'errors' => $errors,
        ]);
    }

    #[Route(path: '/{client}', name: 'show', methods: 'GET', requirements: ['client' => '\d+'])]
    public function show(Request $request, Client $client): Response
    {
        return $this->render('admin/clients/show.html.twig', [
            'client' => $client,
        ]);
    }

    #[Route(path: '/{client}/generation', name: 'show.generation', methods: 'GET', requirements: ['client' => '\d+'])]
    public function showGeneration(Request $request, Client $client, TranslatorInterface $translator): Response
    {
        $provider = new DayGenerationDataProvider($translator);
        $dayGenerations = $client->getDayGenerations()->map(\Closure::fromCallable([$provider, 'styleDayGeneration']));

        return $this->render('admin/clients/show-generation.html.twig', [
            'client' => $client,
            'dayGenerations' => $dayGenerations,
        ]);
    }

    #[Route(path: '/destroy/{client}', name: 'destroy', methods: ['DELETE', 'GET'])]
    public function destroy(Request $request, Client $client, ClientRepository $clientRepository, DayGenerationRepository $dayGenerationRepository): Response
    {
        $clientName = $client->getName();

        foreach ($client->getDayGenerations() as $dayGeneration) {
            $dayGenerationRepository->remove($dayGeneration);
        }

        $clientRepository->remove($client, true);

        $this->addFlash('success', sprintf('Client %s Deleted.', $clientName));

        return $this->redirectToRoute('app.admin.clients.index');
    }
}
